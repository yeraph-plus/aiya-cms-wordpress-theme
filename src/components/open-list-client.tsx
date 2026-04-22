import { __, sprintf } from '@wordpress/i18n';

import * as React from "react"

import {
    Folder,
    FolderOpen,
    FileText,
    FileArchive,
    Image,
    Music,
    Video,
    FileCode,
    Database,
    File,
    Lock,
    Download,
    ChevronLeft,
    ChevronRight,
    Settings,
    Forward,
    Send,
    ArrowUpDown
} from "lucide-react"
import { toast } from "sonner"
import { getConfig } from "@/lib/utils"
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    CardAction
} from "@/components/ui/card"
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { Button } from "@/components/ui/button"
import { Spinner } from "@/components/ui/spinner"
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationEllipsis,
} from "@/components/ui/pagination"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Label } from "@/components/ui/label"
import { Input } from "@/components/ui/input"
import { Checkbox } from "@/components/ui/checkbox"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import {
    flexRender,
    getCoreRowModel,
    getSortedRowModel,
    useReactTable,
    type ColumnDef,
    type SortingState,
} from "@tanstack/react-table"

// `oplist_fs` API contract
type OpenListApiRequest = {
    post_id: number;
    page: number;
};
type OpenListApiFile = {
    name: string;
    type: string;
    size: number;
    modified: string;
    url: string;
};
type OpenListApiSuccessResponse = {
    content: OpenListApiFile[];
    per_page: number;
    page: number;
    total: number;
    folder_name: string;
    description: string;
};
type OpenListPagination = Pick<OpenListApiSuccessResponse, "total" | "per_page" | "page">;
interface Aria2Config {
    rpcUrl: string;
    token: string;
}
type Aria2BatchResult = {
    total: number;
    successCount: number;
    failCount: number;
    failNames: string[];
};

type StableJson = null | boolean | number | string | StableJson[] | { [key: string]: StableJson };

function stableStringify(value: unknown): string {
    const toStable = (v: unknown): StableJson => {
        if (v === null || v === undefined) return null;
        if (typeof v === "string" || typeof v === "number" || typeof v === "boolean") return v;
        if (Array.isArray(v)) return v.map(toStable);
        if (typeof v === "object") {
            const obj = v as Record<string, unknown>;
            const out: Record<string, StableJson> = {};
            Object.keys(obj).sort().forEach((k) => {
                out[k] = toStable(obj[k]);
            });
            return out;
        }
        return String(v);
    };

    return JSON.stringify(toStable(value));
}

function getOpenListClientCache() {
    const w = window as any;
    if (!w.__AIYA_OPENLIST_CLIENT_CACHE__) {
        w.__AIYA_OPENLIST_CLIENT_CACHE__ = {
            inflight: new Map<string, Promise<NormalizedOpenListResponse>>(),
            data: new Map<string, { ts: number; data: NormalizedOpenListResponse }>(),
            logged: new Set<string>(),
        };
    }
    return w.__AIYA_OPENLIST_CLIENT_CACHE__ as {
        inflight: Map<string, Promise<NormalizedOpenListResponse>>;
        data: Map<string, { ts: number; data: NormalizedOpenListResponse }>;
        logged: Set<string>;
    };
}

type NormalizedOpenListResponse =
    | {
        ok: true;
        data: OpenListApiSuccessResponse;
    }
    | {
        ok: false;
        message: string;
    };

const PRESET_ARIA2_CONFIGS = [
    { name: "Aria2Core", url: "http://localhost:6800/jsonrpc" },
    { name: "MotrixNext", url: "http://localhost:16800/jsonrpc" },
    { name: "Motrix", url: "http://localhost:16800/jsonrpc" },
];

type OpenListClientProps = {
    postId?: number;
};

function cleanUrl(url: unknown): string {
    if (typeof url !== "string") return "";
    const trimmed = url.trim();
    const noBackticks = trimmed.replace(/^`+/, "").replace(/`+$/, "").trim();
    const noQuotes = noBackticks.replace(/^"+/, "").replace(/"+$/, "").trim();
    return noQuotes;
}

function normalizeOpenListResponse(raw: any, pageFallback: number): NormalizedOpenListResponse {
    const rawData = raw?.data && typeof raw.data === "object" ? raw.data : null;
    const payload = rawData ?? raw;

    const contentRaw = payload?.content;
    if (Array.isArray(contentRaw)) {
        const content: OpenListApiFile[] = contentRaw.map((f: any) => ({
            name: typeof f?.name === "string" ? f.name : "",
            type: typeof f?.type === "string" ? f.type : "file",
            size: typeof f?.size === "number" ? f.size : Number(f?.size) || 0,
            modified: typeof f?.modified === "string" ? f.modified : "",
            url: cleanUrl(f?.url),
        }));

        return {
            ok: true,
            data: {
                content,
                total: typeof payload?.total === "number" ? payload.total : Number(payload?.total) || content.length,
                per_page: typeof payload?.per_page === "number" ? payload.per_page : Number(payload?.per_page) || 0,
                page: typeof payload?.page === "number" ? payload.page : Number(payload?.page) || pageFallback,
                folder_name: typeof payload?.folder_name === "string" ? payload.folder_name : "",
                description: typeof payload?.description === "string" ? payload.description : "",
            },
        };
    }

    const message =
        (typeof raw?.message === "string" && raw.message) ||
        (typeof raw?.data?.detail === "string" && raw.data.detail) ||
        (typeof raw?.data?.message === "string" && raw.data.message) ||
        (typeof raw?.detail === "string" && raw.detail) ||
        "请求失败";

    return { ok: false, message };
}

function createAria2AddUriPayload(config: Aria2Config, url: string, filename: string) {
    const id = `aiya-cms-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    const params: unknown[] = [];
    if (config.token) {
        params.push(`token:${config.token}`);
    }
    params.push([url]);
    params.push({ out: filename });
    return {
        jsonrpc: "2.0",
        method: "aria2.addUri",
        id,
        params,
    };
}

async function sendToLocalAria2(config: Aria2Config, url: string, filename: string): Promise<boolean> {
    const safeUrl = cleanUrl(url);
    if (!safeUrl) return false;

    const payload = createAria2AddUriPayload(config, safeUrl, filename);
    try {
        const res = await fetch(config.rpcUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            return false;
        }

        const data = await res.json();
        if (data?.error) {
            return false;
        }

        return true;
    } catch {
        return false;
    }
}

async function batchSendToLocalAria2(config: Aria2Config, files: OpenListApiFile[]): Promise<Aria2BatchResult> {
    const result: Aria2BatchResult = {
        total: files.length,
        successCount: 0,
        failCount: 0,
        failNames: [],
    };

    for (const file of files) {
        if (file.type === "folder") continue;
        const success = await sendToLocalAria2(config, file.url, file.name);
        if (success) {
            result.successCount += 1;
        } else {
            result.failCount += 1;
            result.failNames.push(file.name);
        }
    }

    return result;
}

function hasValidPostId(props: OpenListClientProps | undefined | null): props is OpenListClientProps {
    return !!props && typeof props.postId === "number" && props.postId > 0;
}

export default function OpenListClient(props: OpenListClientProps) {
    const request: number | null = React.useMemo(() => {
        if (hasValidPostId(props)) {
            return props.postId as number;
        }

        return null;
    }, [props]);

    const requestKey = React.useMemo(() => (request ? stableStringify(request) : ""), [request]);

    const [loading, setLoading] = React.useState(false);
    const [_error, setError] = React.useState<string | null>(null);
    const [files, setFiles] = React.useState<OpenListApiFile[]>([]);
    const [pagination, setPagination] = React.useState<OpenListPagination | null>(null);
    const [folderName, setFolderName] = React.useState<string>("");
    const [description, setDescription] = React.useState<string>("");
    const [_clickedFiles, setClickedFiles] = React.useState<Set<string>>(new Set());
    const [sorting, setSorting] = React.useState<SortingState>([])
    const [rowSelection, setRowSelection] = React.useState({})
    const [aria2Config, setAria2Config] = React.useState<Aria2Config>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('aiya_aria2_config');
            if (saved) {
                try {
                    return JSON.parse(saved);
                } catch { }
            }
        }
        return {
            rpcUrl: 'http://localhost:6800/jsonrpc',
            token: ''
        };
    });

    React.useEffect(() => {
        localStorage.setItem('aiya_aria2_config', JSON.stringify(aria2Config));
    }, [aria2Config]);

    const mountedRef = React.useRef(false);

    const fetchData = React.useCallback(async (page: number = 1) => {
        if (!request) return;
        if (mountedRef.current) {
            setLoading(true);
            setError(null);
        }

        const { apiUrl, apiNonce } = getConfig()
        if (!apiNonce) {
            if (mountedRef.current) {
                setError(__('缺少安全 nonce', 'aiya-cms'))
                setLoading(false)
            }
            return
        }

        const applyNormalized = (normalized: NormalizedOpenListResponse) => {
            if (!mountedRef.current) return;

            if (!normalized.ok) {
                setError(normalized.message || __('请求失败', 'aiya-cms'));
                setFiles([]);
                setPagination(null);
                setFolderName("");
                setDescription("");
                return;
            }

            const data = normalized.data;
            if (data.content.length === 0) {
                setError(null);
                setFiles([]);
                setFolderName(data.folder_name);
                setDescription(data.description);
                setPagination({
                    total: data.total || 0,
                    per_page: data.per_page || 0,
                    page: data.page || page,
                });
                return;
            }

            setError(null);
            setFiles(data.content);
            setFolderName(data.folder_name);
            setDescription(data.description);
            setPagination({
                total: data.total || 0,
                per_page: data.per_page || 0,
                page: data.page || page,
            });
        };

        try {
            const payload: OpenListApiRequest = { post_id: request, page };
            const cacheKey = stableStringify({ apiUrl, payload });

            const cache = getOpenListClientCache();
            const cached = cache.data.get(cacheKey);
            if (cached && Date.now() - cached.ts < 3000) {
                applyNormalized(cached.data);
                return;
            }

            const inflight = cache.inflight.get(cacheKey);
            if (inflight) {
                const normalized = await inflight;
                applyNormalized(normalized);
                return;
            }

            const reqPromise = fetch(`${apiUrl}/aiya/v1/oplist_fs`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": apiNonce,
                },
                body: JSON.stringify(payload),
            }).then(async (res) => {
                const data = await res.json();
                return { res, data };
            });

            cache.inflight.set(
                cacheKey,
                reqPromise.then(({ data }) => normalizeOpenListResponse(data, page))
            );

            const { res: response, data: raw } = await reqPromise;
            cache.inflight.delete(cacheKey);

            const normalized = normalizeOpenListResponse(raw, page);
            if (response.ok && normalized.ok) {
                cache.data.set(cacheKey, { ts: Date.now(), data: normalized });
            }
            applyNormalized(normalized);
        } catch (err) {
            try {
                const payload: OpenListApiRequest = { post_id: request, page };
                const cacheKey = stableStringify({ apiUrl, payload });
                getOpenListClientCache().inflight.delete(cacheKey);
            } catch {
            }
            console.error("Fetch error:", err);
            if (mountedRef.current) {
                setError(__('网络连接错误，请稍后重试', 'aiya-cms'));
            }
        } finally {
            if (mountedRef.current) {
                setLoading(false);
            }
        }
    }, [request]);

    React.useEffect(() => {
        mountedRef.current = true;
        return () => {
            mountedRef.current = false;
        };
    }, []);

    React.useEffect(() => {
        if (request) {
            fetchData(1);
        }
    }, [fetchData, request]);

    React.useEffect(() => {
        if (!request || !import.meta.env.DEV) return;
        const cache = getOpenListClientCache();
        if (cache.logged.has(requestKey)) return;
        cache.logged.add(requestKey);
    }, [request, requestKey]);

    const handlePageChange = (page: number) => {
        if (!pagination) return;
        if (pagination.per_page <= 0) return;
        const totalPages = Math.ceil(pagination.total / pagination.per_page);
        if (page < 1 || page > totalPages) return;
        fetchData(page);
    };

    const getFileIcon = (type: string) => {
        switch (type) {
            case 'folder': return <Folder className="h-5 w-5" />;
            case 'archive': return <FileArchive className="h-5 w-5" />;
            case 'image': return <Image className="h-5 w-5" />;
            case 'audio': return <Music className="h-5 w-5" />;
            case 'video': return <Video className="h-5 w-5" />;
            case 'text':
            case 'document': return <FileText className="h-5 w-5" />;
            case 'code': return <FileCode className="h-5 w-5" />;
            case 'db': return <Database className="h-5 w-5" />;
            case 'encryption': return <Lock className="h-5 w-5" />;
            default: return <File className="h-5 w-5" />;
        }
    };

    const formatFileSize = (bytes: number) => {
        if (!bytes) return "-";
        const k = 1024;
        const sizes = ["B", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return "-";
        return new Date(dateString).toLocaleDateString();
    };

    const sendToAria2 = React.useCallback(async (url: string, filename: string) => {
        const success = await sendToLocalAria2(aria2Config, url, filename);
        if (success) {
            toast.success(sprintf(__('已发送到 Aria2: %s', 'aiya-cms'), filename));
        } else {
            toast.error(sprintf(__('发送失败: %s', 'aiya-cms'), filename));
        }
    }, [aria2Config]);

    const handleBatchSendToAria2 = async () => {
        const selectedRows = table.getFilteredSelectedRowModel().rows
        if (selectedRows.length === 0) return

        const filesToSend = selectedRows.map((row) => row.original)
        const total = filesToSend.length

        // Clear selection immediately
        setRowSelection({})

        toast.promise(
            (async () => {
                const result = await batchSendToLocalAria2(aria2Config, filesToSend);
                if (result.failCount > 0) {
                    throw new Error(sprintf(__('共 %d 个文件发送失败', 'aiya-cms'), result.failCount));
                }
                return result.successCount;
            })(),
            {
                loading: sprintf(__('正在发送 %d 个文件到 Aria2...', 'aiya-cms'), total),
                success: (count) => {
                    return sprintf(__('成功发送 %d 个文件到 Aria2', 'aiya-cms'), count);
                },
                error: (err) => sprintf(__('发送完成，但在 %d 个文件中: %s', 'aiya-cms'), total, err.message),
            }
        )
    };

    const columns: ColumnDef<OpenListApiFile>[] = React.useMemo(() => [
        {
            id: "select",
            header: ({ table }) => {
                const isAllPageRowsSelected = table.getIsAllPageRowsSelected()
                const isSomePageRowsSelected = table.getIsSomePageRowsSelected()
                return (
                    <Checkbox
                        checked={isAllPageRowsSelected || (isSomePageRowsSelected && "indeterminate")}
                        onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
                        aria-label="Select all"
                    />
                )
            },
            cell: ({ row }) => (
                <Checkbox
                    checked={row.getIsSelected()}
                    onCheckedChange={(value) => row.toggleSelected(!!value)}
                    aria-label="Select row"
                    disabled={row.original.type === 'folder'}
                />
            ),
            enableSorting: false,
            enableHiding: false,
        },
        {
            accessorKey: "name",
            header: ({ column }) => {
                return (
                    <Button
                        variant="ghost"
                        onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                        className="px-0 hover:bg-transparent"
                    >
                        {__('文件名', 'aiya-cms')}
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                )
            },
            cell: ({ row }) => {
                const file = row.original
                return (
                    <div className="flex items-center gap-2 max-w-[200px] sm:max-w-[300px] md:max-w-[400px]">
                        {getFileIcon(file.type)}
                        {file.type === 'folder' ? (
                            <a href={file.url} className="truncate hover:underline font-medium text-primary">
                                {file.name}
                            </a>
                        ) : (
                            <span
                                className="truncate cursor-pointer hover:underline"
                                onClick={() => handleDownload(file.url)}
                                title={file.name}
                            >
                                {file.name}
                            </span>
                        )}
                    </div>
                )
            },
        },
        {
            accessorKey: "size",
            header: __('大小', 'aiya-cms'),
            cell: ({ row }) => <div className="text-sm text-muted-foreground whitespace-nowrap">{formatFileSize(row.getValue("size"))}</div>,
        },
        {
            accessorKey: "modified",
            header: __('修改时间', 'aiya-cms'),
            cell: ({ row }) => <div className="text-sm text-muted-foreground whitespace-nowrap">{formatDate(row.getValue("modified"))}</div>,
        },
        {
            id: "actions",
            header: __('操作', 'aiya-cms'),
            cell: ({ row }) => {
                const file = row.original
                return (
                    <div className="flex items-center gap-2">
                        {file.type === 'folder' ? (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleOpenLink(file.url)}
                                title={__('新窗口打开', 'aiya-cms')}
                            >
                                <FolderOpen className="h-4 w-4 mr-1" />
                                {__('打开', 'aiya-cms')}
                            </Button>
                        ) : (
                            <>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => handleDownload(file.url)}
                                    title={__('使用浏览器下载', 'aiya-cms')}
                                >
                                    <Download className="h-4 w-4" />
                                    {__('下载', 'aiya-cms')}
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => sendToAria2(file.url, file.name)}
                                    title={__('发送下载到 Aria2 客户端', 'aiya-cms')}
                                >
                                    <Forward className="h-4 w-4" />
                                    {__('推送', 'aiya-cms')}
                                </Button>
                            </>
                        )}
                    </div>
                )
            },
            enableSorting: false,
        },
    ], [sendToAria2])

    const table = useReactTable({
        data: files,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        onSortingChange: setSorting,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            rowSelection,
        },
        getRowId: (row) => cleanUrl(row.url),
    })

    const handleDownload = (url: string) => {
        const safeUrl = cleanUrl(url);
        if (!safeUrl) return;
        setClickedFiles(prev => new Set(prev).add(safeUrl));

        // Create invisible iframe for download
        const iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = safeUrl;
        document.body.appendChild(iframe);
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 5000);
    };

    const handleOpenLink = (url: string) => {
        const safeUrl = cleanUrl(url);
        if (!safeUrl) return;
        window.open(safeUrl, "_blank");
    };

    // Calculate pagination pages
    const getPageNumbers = () => {
        if (!pagination || pagination.per_page <= 0) return [];
        const totalPages = Math.ceil(pagination.total / pagination.per_page);
        if (totalPages <= 1) return [1];

        const pages: (number | string)[] = [];
        const current = pagination.page;

        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            if (current <= 4) {
                for (let i = 1; i <= 5; i++) pages.push(i);
                pages.push("...");
                pages.push(totalPages);
            } else if (current >= totalPages - 3) {
                pages.push(1);
                pages.push("...");
                for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                pages.push("...");
                for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                pages.push("...");
                pages.push(totalPages);
            }
        }
        return pages;
    };

    if (!request) return null;

    return (
        <Card className="w-full rounded-lg">
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <FolderOpen className="h-5 w-5" />
                    {folderName || __('加载中', 'aiya-cms')}
                </CardTitle>
                <CardDescription>
                    {description && (
                        <span dangerouslySetInnerHTML={{ __html: String(description) }} />
                    )}
                </CardDescription>
                <CardAction>
                    {Object.keys(rowSelection).length > 0 && (
                        <Button variant="outline" size="default" onClick={handleBatchSendToAria2} className="mr-2" title={sprintf(__('批量推送 %d 个文件到下载客户端', 'aiya-cms'), Object.keys(rowSelection).length)}>
                            <Send className="h-4 w-4" />
                            {__('推送下载', 'aiya-cms')}
                        </Button>
                    )}
                    <Popover>
                        <PopoverTrigger asChild>
                            <Button variant="outline" size="default" title={__('连接到下载客户端', 'aiya-cms')}>
                                <Settings className="h-4 w-4" />
                                {__('使用客户端下载', 'aiya-cms')}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-80">
                            <div className="grid gap-4">
                                <div className="space-y-2">
                                    <h4 className="font-medium leading-none">{__('连接到下载客户端', 'aiya-cms')}</h4>
                                    <p className="text-sm text-muted-foreground">
                                        {__('配置 Aria2 客户端 RPC 连接以远程推送下载', 'aiya-cms')}
                                    </p>
                                </div>
                                <div className="grid gap-2">
                                    <div className="grid grid-cols-3 items-center gap-4">
                                        <Label htmlFor="preset">{__('选择预设', 'aiya-cms')}</Label>
                                        <Select
                                            onValueChange={(value) => {
                                                const preset = PRESET_ARIA2_CONFIGS.find(p => p.name === value);
                                                if (preset) {
                                                    setAria2Config(prev => ({ ...prev, rpcUrl: preset.url }));
                                                }
                                            }}
                                        >
                                            <SelectTrigger className="col-span-2 h-8 w-full">
                                                <SelectValue placeholder={__('选择预设配置', 'aiya-cms')} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {PRESET_ARIA2_CONFIGS.map((preset) => (
                                                    <SelectItem key={preset.name} value={preset.name}>
                                                        {preset.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="grid grid-cols-3 items-center gap-4">
                                        <Label htmlFor="rpcUrl">{__('RPC URL', 'aiya-cms')}</Label>
                                        <Input
                                            id="rpcUrl"
                                            value={aria2Config.rpcUrl}
                                            onChange={(e) => setAria2Config({ ...aria2Config, rpcUrl: e.target.value })}
                                            className="col-span-2 h-8"
                                        />
                                    </div>
                                    <div className="grid grid-cols-3 items-center gap-4">
                                        <Label htmlFor="token">{__('Secret', 'aiya-cms')}</Label>
                                        <Input
                                            id="token"
                                            type="password"
                                            value={aria2Config.token}
                                            onChange={(e) => setAria2Config({ ...aria2Config, token: e.target.value })}
                                            className="col-span-2 h-8"
                                        />
                                    </div>
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>
                </CardAction>
            </CardHeader>
            <CardContent className="">
                {loading ? (
                    <div className="flex justify-center items-center h-32">
                        <Spinner className="h-8 w-8" />
                    </div>
                ) : _error ? (
                    <div className="h-24 flex items-center justify-center text-destructive">
                        {_error}
                    </div>
                ) : (
                    <Table className="">
                        <TableHeader>
                            {table.getHeaderGroups().map((headerGroup) => (
                                <TableRow key={headerGroup.id}>
                                    {headerGroup.headers.map((header) => {
                                        return (
                                            <TableHead key={header.id} className={header.id === "actions" ? "sticky right-0" : ""}>
                                                {header.isPlaceholder
                                                    ? null
                                                    : flexRender(
                                                        header.column.columnDef.header,
                                                        header.getContext()
                                                    )}
                                            </TableHead>
                                        )
                                    })}
                                </TableRow>
                            ))}
                        </TableHeader>
                        <TableBody>
                            {table.getRowModel().rows?.length ? (
                                table.getRowModel().rows.map((row) => (
                                    <TableRow
                                        key={row.id}
                                        data-state={row.getIsSelected() && "selected"}
                                    >
                                        {row.getVisibleCells().map((cell) => (
                                            <TableCell key={cell.id} className={cell.column.id === "actions" ? "sticky right-0" : ""}>
                                                {flexRender(
                                                    cell.column.columnDef.cell,
                                                    cell.getContext()
                                                )}
                                            </TableCell>
                                        ))}
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell
                                        colSpan={columns.length}
                                        className="h-24 text-center"
                                    >
                                        {__('当前目录下没有文件', 'aiya-cms')}
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                )}

                {/* Pagination */}
                {!loading && !_error && pagination && (
                    <div className="p-4 border-t space-y-3">
                        <div className="text-sm text-muted-foreground text-center">
                            {sprintf(__('共 %d 个文件', 'aiya-cms'), pagination.total)}
                            {pagination.per_page > 0 && pagination.total > pagination.per_page && (
                                <span>{sprintf(__('，第 %d / %d 页', 'aiya-cms'), pagination.page, Math.ceil(pagination.total / pagination.per_page))}</span>
                            )}
                        </div>
                        {pagination.per_page > 0 && pagination.total > pagination.per_page && (
                            <div className="flex justify-center">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationLink
                                                href="#"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    if (pagination.page > 1) handlePageChange(pagination.page - 1);
                                                }}
                                                className={`gap-1 pl-2.5 ${pagination.page <= 1 ? "pointer-events-none opacity-50" : ""}`}
                                                aria-label="Previous Page"
                                                size="default"
                                            >
                                                <ChevronLeft className="h-4 w-4" />
                                                <span className="hidden sm:block">{__('上一页', 'aiya-cms')}</span>
                                            </PaginationLink>
                                        </PaginationItem>

                                        {getPageNumbers().map((page, i) => (
                                            <PaginationItem key={i}>
                                                {page === "..." ? (
                                                    <PaginationEllipsis />
                                                ) : (
                                                    <PaginationLink
                                                        href="#"
                                                        isActive={page === pagination.page}
                                                        onClick={(e) => {
                                                            e.preventDefault();
                                                            handlePageChange(page as number);
                                                        }}
                                                    >
                                                        {page}
                                                    </PaginationLink>
                                                )}
                                            </PaginationItem>
                                        ))}

                                        <PaginationItem>
                                            <PaginationLink
                                                href="#"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    const totalPages = Math.ceil(pagination.total / pagination.per_page);
                                                    if (pagination.page < totalPages) {
                                                        handlePageChange(pagination.page + 1);
                                                    }
                                                }}
                                                className={`gap-1 pr-2.5 ${pagination.page >= Math.ceil(pagination.total / pagination.per_page) ? "pointer-events-none opacity-50" : ""}`}
                                                aria-label="Next Page"
                                                size="default"
                                            >
                                                <span className="hidden sm:block">{__('下一页', 'aiya-cms')}</span>
                                                <ChevronRight className="h-4 w-4" />
                                            </PaginationLink>
                                        </PaginationItem>
                                    </PaginationContent>
                                </Pagination>
                            </div>
                        )}
                    </div>
                )}
            </CardContent>
        </Card>
    )
}
