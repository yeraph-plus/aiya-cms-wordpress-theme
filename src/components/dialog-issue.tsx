import * as React from "react"

import { toast } from "sonner"
import { Badge } from "@/components/ui/badge"
import { Spinner } from "@/components/ui/spinner"
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { Textarea } from "@/components/ui/textarea"
import { joinTranslations } from "@/lib/i18n"
import { cn, getConfig } from "@/lib/utils"

const { t } = joinTranslations()

const CUSTOM_ISSUE_TITLE = "__custom__"
const RELATED_ISSUES_PAGE_SIZE = 5

type UserSummary = {
    id: number
    name: string
    avatar: string
    url: string
}

type IssueRecord = {
    id: number
    post_id: number
    user_id: number
    type: string
    status: string
    title: string
    content: string
    comment_count: number
    created_at: string | null
    permalink: string
    user: UserSummary | null
}

type IssueListResponse = {
    items: IssueRecord[]
}

function getResponseError(data: any, fallback: string) {
    return data?.message || data?.detail || data?.data?.detail || fallback
}

function formatDate(value: string | null) {
    if (!value) {
        return t("unknown_time")
    }

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) {
        return value
    }

    return date.toLocaleString(undefined, {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    })
}

function issueStatusBadgeClass(status: string) {
    const classes: Record<string, string> = {
        open: "bg-primary",
        closed: "bg-gray-700",
        accepted: "bg-green-700",
        progress: "bg-violet-700",
        resolved: "bg-teal-700",
        pending: "bg-amber-700",
    }

    return classes[status] || "bg-muted-foreground text-foreground"
}

function issueStatusLabel(status: string) {
    const labels: Record<string, string> = {
        open: t("issue_status_open"),
        closed: t("issue_status_closed"),
        accepted: t("issue_status_accepted"),
        progress: t("issue_status_progress"),
        resolved: t("issue_status_resolved"),
        pending: t("issue_status_pending"),
    }

    return labels[status] || status
}

function issueTypeLabel(type: string) {
    const labels: Record<string, string> = {
        issue: t("issue_type_issue"),
        discussion: t("issue_type_discussion"),
        question: t("issue_type_question"),
        feedback: t("issue_type_feedback"),
    }

    return labels[type] || type
}

interface IssueDialogProps {
    open: boolean
    onOpenChange: (open: boolean) => void
    postId: number
    issueTitleContent?: string[]
}

export function IssueDialog({
    open,
    onOpenChange,
    postId,
    issueTitleContent = [],
}: IssueDialogProps) {
    const normalizedIssueTitles = React.useMemo(
        () => issueTitleContent.map((title) => title.trim()).filter(Boolean),
        [issueTitleContent]
    )
    const defaultIssueTitleValue = normalizedIssueTitles[0] || CUSTOM_ISSUE_TITLE
    const [selectedIssueTitle, setSelectedIssueTitle] = React.useState(defaultIssueTitleValue)
    const [issueTitle, setIssueTitle] = React.useState("")
    const [issueContent, setIssueContent] = React.useState("")
    const [isSubmitting, setIsSubmitting] = React.useState(false)
    const [relatedIssues, setRelatedIssues] = React.useState<IssueRecord[]>([])
    const [isRelatedIssuesLoading, setIsRelatedIssuesLoading] = React.useState(false)
    const [relatedIssuesError, setRelatedIssuesError] = React.useState<string | null>(null)

    const resetForm = React.useCallback(() => {
        setSelectedIssueTitle(defaultIssueTitleValue)
        setIssueTitle("")
        setIssueContent("")
    }, [defaultIssueTitleValue])

    React.useEffect(() => {
        if (!open) {
            resetForm()
        }
    }, [open, resetForm])

    const loadRelatedIssues = React.useCallback(async () => {
        if (postId <= 0) {
            setRelatedIssues([])
            setRelatedIssuesError(null)
            return
        }

        const { apiUrl } = getConfig()
        if (!apiUrl) {
            setRelatedIssues([])
            setRelatedIssuesError(t("api_endpoint_not_configured", "API Endpoint Not Configured."))
            return
        }

        setIsRelatedIssuesLoading(true)
        setRelatedIssuesError(null)

        try {
            const params = new URLSearchParams({
                post_id: String(postId),
                per_page: String(RELATED_ISSUES_PAGE_SIZE),
                orderby: "updated_at",
                order: "DESC",
            })
            const response = await fetch(`${apiUrl}/aiya/v1/issue/by-post?${params.toString()}`)

            let payload: unknown = null
            try {
                payload = await response.json()
            } catch {
                payload = null
            }

            if (!response.ok) {
                throw new Error(getResponseError(payload, t("issue_list_load_failed")))
            }

            const data = payload && typeof payload === "object" && "data" in (payload as Record<string, unknown>)
                ? (payload as { data: IssueListResponse }).data
                : (payload as IssueListResponse)

            setRelatedIssues(Array.isArray(data?.items) ? data.items : [])
        } catch (error) {
            setRelatedIssues([])
            setRelatedIssuesError(error instanceof Error ? error.message : t("issue_list_load_failed"))
        } finally {
            setIsRelatedIssuesLoading(false)
        }
    }, [postId])

    React.useEffect(() => {
        if (!open) {
            return
        }

        void loadRelatedIssues()
    }, [loadRelatedIssues, open])

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault()

        const finalIssueTitle = selectedIssueTitle === CUSTOM_ISSUE_TITLE
            ? issueTitle.trim()
            : selectedIssueTitle.trim()

        if (!finalIssueTitle) {
            toast.error(t("issue_title_required"))
            return
        }

        const { apiUrl, apiNonce } = getConfig()
        if (!apiNonce) {
            toast.error(t("page_expired"))
            return
        }

        setIsSubmitting(true)

        try {
            const response = await fetch(`${apiUrl}/aiya/v1/issue/create`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": apiNonce,
                },
                body: JSON.stringify({
                    post_id: Number(postId),
                    type: "feedback",
                    status: "open",
                    title: finalIssueTitle,
                    content: issueContent.trim(),
                }),
            })

            const data = await response.json()

            if (!response.ok) {
                throw new Error(getResponseError(data, t("submit_issue_failed")))
            }

            toast.success(data?.data?.message || t("issue_created"))
            onOpenChange(false)

            const issuePermalink = data?.data?.issue?.permalink
            if (typeof issuePermalink === "string" && issuePermalink) {
                window.location.href = issuePermalink
            }
        } catch (error) {
            console.error("Create issue failed:", error)
            toast.error(error instanceof Error ? error.message : t("submit_issue_failed"))
        } finally {
            setIsSubmitting(false)
        }
    }

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[560px]">
                <DialogHeader>
                    <DialogTitle>{t("create_feedback")}</DialogTitle>
                    <DialogDescription>{t("create_feedback_description")}</DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="issue-title-select">{t("related_post_issues")}</Label>
                        <div className="flex flex-col gap-2">
                            {isRelatedIssuesLoading ? <Spinner className="h-4 w-4" /> : relatedIssuesError ? (
                                <div className="flex items-center justify-between gap-3 rounded-md border border-destructive/20 bg-background p-3">
                                    <p className="text-sm text-destructive">{relatedIssuesError}</p>
                                    <Button type="button" variant="outline" size="sm" onClick={() => void loadRelatedIssues()}>
                                        {t("retry")}
                                    </Button>
                                </div>
                            ) : relatedIssues.length > 0 ? (
                                <>
                                    {relatedIssues.map((issue) => (
                                        <a
                                            key={issue.id}
                                            href={issue.permalink}
                                            className="grid gap-2 rounded-md border bg-background p-3 transition-colors hover:bg-muted/50"
                                        >
                                            <div className="flex flex-wrap items-center gap-2">
                                                <Badge
                                                    variant="default"
                                                    className={cn("text-xs text-white", issueStatusBadgeClass(issue.status))}
                                                >
                                                    {issueStatusLabel(issue.status)}
                                                </Badge>
                                                <Badge variant="secondary" className="text-xs">
                                                    {issueTypeLabel(issue.type)}
                                                </Badge>
                                                <span className="text-sm font-medium text-foreground">
                                                    #{issue.id}
                                                </span>
                                                <span className="text-sm font-medium leading-6 text-foreground line-clamp-1">
                                                    {issue.title}
                                                </span>
                                            </div>
                                            <div className="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                                <span>{issue.user?.name || t("anonymous", "匿名")}</span>
                                                <span>{t("created_at")} {formatDate(issue.created_at)}</span>
                                                <span>{issue.comment_count} {t("issue_reply_count_suffix")}</span>
                                            </div>
                                        </a>
                                    ))}
                                </>
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    {t("related_post_issues_empty")}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="issue-title-select">{t("issue_title")}</Label>
                        <Select
                            value={selectedIssueTitle}
                            onValueChange={(value) => {
                                setSelectedIssueTitle(value)
                                if (value !== CUSTOM_ISSUE_TITLE) {
                                    setIssueTitle("")
                                }
                            }}
                            disabled={isSubmitting}
                        >
                            <SelectTrigger id="issue-title-select" className="w-full">
                                <SelectValue placeholder={t("issue_title_placeholder")} />
                            </SelectTrigger>
                            <SelectContent>
                                {normalizedIssueTitles.map((title) => (
                                    <SelectItem key={title} value={title}>
                                        {title}
                                    </SelectItem>
                                ))}
                                <SelectItem value={CUSTOM_ISSUE_TITLE}>{t("manual_input")}...</SelectItem>
                            </SelectContent>
                        </Select>
                        {selectedIssueTitle === CUSTOM_ISSUE_TITLE && (
                            <Input
                                id="issue-title-custom"
                                value={issueTitle}
                                onChange={(event) => setIssueTitle(event.target.value)}
                                placeholder={t("issue_title_placeholder")}
                                disabled={isSubmitting}
                                required
                            />
                        )}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="issue-content">{t("issue_content")}</Label>
                        <Textarea
                            id="issue-content"
                            value={issueContent}
                            onChange={(event) => setIssueContent(event.target.value)}
                            placeholder={t("issue_content_placeholder")}
                            rows={8}
                            disabled={isSubmitting}
                        />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={isSubmitting}
                        >
                            {t("cancel")}
                        </Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting && <Spinner className="mr-2 h-4 w-4" />}
                            {t("create_issue")}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    )
}
