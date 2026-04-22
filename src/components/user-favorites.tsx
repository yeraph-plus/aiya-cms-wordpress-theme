import { __, sprintf } from '@wordpress/i18n';

import { useState } from "react"
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { Checkbox } from "@/components/ui/checkbox"
import { Button } from "@/components/ui/button"
import { Trash2, ExternalLink, Loader2, Bookmark, Archive, ArrowUpDown } from "lucide-react"
import { getConfig } from "@/lib/utils"
import { toast } from "sonner"
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import {
    type ColumnDef,
    flexRender,
    getCoreRowModel,
    getSortedRowModel,
    type SortingState,
    useReactTable,
} from "@tanstack/react-table"

interface FavPost {
    id: number
    url: string
    date: string
    date_iso: string
    modified: string
    thumbnail: string
    title: string
    attr_title: string
    author_name: string
}

interface UserFavListProps {
    initialPosts: FavPost[]
}

export default function UserFavorites({ initialPosts }: UserFavListProps) {
    const [posts, setPosts] = useState<FavPost[]>(initialPosts)
    const [sorting, setSorting] = useState<SortingState>([])
    const [rowSelection, setRowSelection] = useState({})
    const [isLoading, setIsLoading] = useState(false)

    const columns: ColumnDef<FavPost>[] = [
        {
            id: "select",
            header: ({ table }) => (
                <Checkbox
                    checked={
                        table.getIsAllPageRowsSelected() ||
                        (table.getIsSomePageRowsSelected() && "indeterminate")
                    }
                    onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
                    aria-label="Select all"
                />
            ),
            cell: ({ row }) => (
                <Checkbox
                    checked={row.getIsSelected()}
                    onCheckedChange={(value) => row.toggleSelected(!!value)}
                    aria-label="Select row"
                />
            ),
            enableSorting: false,
            enableHiding: false,
        },
        {
            accessorKey: "thumbnail",
            header: __('封面', 'aiya-cms'),
            cell: ({ row }) => {
                const thumbnail = row.original.thumbnail
                const title = row.original.title
                return thumbnail ? (
                    <img
                        src={thumbnail}
                        alt={title}
                        className="w-10 h-10 object-cover rounded"
                    />
                ) : (
                    <div className="w-10 h-10 bg-muted rounded flex items-center justify-center text-xs text-muted-foreground">
                        {__('无', 'aiya-cms')}
                    </div>
                )
            },
            enableSorting: false,
        },
        {
            accessorKey: "title",
            header: ({ column }) => {
                return (
                    <Button
                        variant="ghost"
                        onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                        className="px-0"
                    >
                        {__('标题', 'aiya-cms')}
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                )
            },
            cell: ({ row }) => {
                const post = row.original
                return (
                    <a href={post.url} target="_blank" rel="noopener noreferrer" className="font-medium hover:underline hover:text-primary transition-colors line-clamp-2">
                        {post.title}
                    </a>
                )
            }
        },
        {
            accessorKey: "author_name",
            header: __('作者', 'aiya-cms'),
            cell: ({ row }) => <div className="text-muted-foreground">{row.getValue("author_name")}</div>,
        },
        {
            accessorKey: "date",
            header: ({ column }) => {
                return (
                    <Button
                        variant="ghost"
                        onClick={() => column.toggleSorting(column.getIsSorted() === "asc")}
                        className="px-0"
                    >
                        {__('时间', 'aiya-cms')}
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                )
            },
            cell: ({ row }) => <div className="text-muted-foreground text-sm">{row.getValue("date")}</div>,
        },
        {
            id: "actions",
            header: () => <div className="text-center">{__('操作', 'aiya-cms')}</div>,
            cell: ({ row }) => {
                const post = row.original
                return (
                    <div className="text-center">
                        <Button variant="outline" size="sm" asChild>
                            <a href={post.url} target="_blank" rel="noopener noreferrer" title={__('查看文章', 'aiya-cms')}>
                                {__('查看', 'aiya-cms')}
                                <ExternalLink className="h-4 w-4" />
                            </a>
                        </Button>
                    </div>
                )
            },
        },
    ]

    const table = useReactTable({
        data: posts,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        onSortingChange: setSorting,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            rowSelection,
        },
        getRowId: (row) => String(row.id),
    })

    const handleRemoveSelected = async () => {
        const selectedRows = table.getFilteredSelectedRowModel().rows
        if (selectedRows.length === 0 || isLoading) return

        if (!confirm(__('确定要取消收藏选中的文章吗？', 'aiya-cms'))) {
            return
        }

        setIsLoading(true)
        const { apiUrl, apiNonce } = getConfig()
        const selectedIds = selectedRows.map(row => row.original.id)

        const promises = selectedIds.map(async (id) => {
            try {
                const response = await fetch(`${apiUrl}/aiya/v1/post_favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': apiNonce || ''
                    },
                    body: JSON.stringify({ post_id: id })
                })
                const data = await response.json()
                return { id, success: data.status === 'done' && data.action === 'removed' }
            } catch (error) {
                console.error(`Failed to remove post ${id}`, error)
                return { id, success: false }
            }
        })

        const results = await Promise.all(promises)
        const removedIds = results.filter(r => r.success).map(r => r.id)
        const failedIds = results.filter(r => !r.success).map(r => r.id)

        if (removedIds.length > 0) {
            setPosts(prev => prev.filter(post => !removedIds.includes(post.id)))
            setRowSelection({})
            toast.success(sprintf(__('成功取消收藏 %d 篇文章', 'aiya-cms'), removedIds.length))
        }

        if (failedIds.length > 0) {
            toast.error(sprintf(__('取消收藏 %d 篇文章失败', 'aiya-cms'), failedIds.length))
        }

        setIsLoading(false)
    }

    return (
        <>
            <div className="flex items-center gap-2 pl-2 mb-4">
                <Bookmark className="w-6 h-6 text-primary" />
                <h2 className="text-xl font-bold tracking-tight">{__('我的收藏夹', 'aiya-cms')}</h2>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Archive className="w-6 h-6" />
                        <h4 className="font-bold tracking-tight">{__('查看或管理收藏列表', 'aiya-cms')}</h4>
                    </CardTitle>
                    <CardDescription>
                        {__('查看或管理您收藏的文章', 'aiya-cms')}
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                    <div className="flex justify-between items-center bg-muted/50 p-2 rounded-lg">
                        <div className="text-sm text-muted-foreground pl-2">
                            {sprintf(__('已选择 %d 项', 'aiya-cms'), Object.keys(rowSelection).length)}
                        </div>
                        <Button
                            variant="destructive"
                            size="sm"
                            onClick={handleRemoveSelected}
                            disabled={Object.keys(rowSelection).length === 0 || isLoading}
                            className="gap-2"
                        >
                            {isLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Trash2 className="h-4 w-4" />}
                            {__('取消收藏', 'aiya-cms')}
                        </Button>
                    </div>

                    <div className="rounded-md border">
                        <Table>
                            <TableHeader>
                                {table.getHeaderGroups().map((headerGroup) => (
                                    <TableRow key={headerGroup.id}>
                                        {headerGroup.headers.map((header) => {
                                            return (
                                                <TableHead key={header.id}>
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
                                                <TableCell key={cell.id}>
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
                                            className="h-24 text-center text-muted-foreground"
                                        >
                                            {__('暂无收藏', 'aiya-cms')}
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </>
    )
}
