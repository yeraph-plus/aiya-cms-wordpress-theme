import * as React from "react"
import {
  AlertCircle,
  ArrowLeft,
  CircleDot,
  MessageSquareText,
  Pencil,
  Plus,
  Send,
  Trash2,
} from "lucide-react"
import ReactMarkdown from "react-markdown"
import rehypeRaw from "rehype-raw"
import rehypeSanitize from "rehype-sanitize"
import remarkBreaks from "remark-breaks"
import remarkGfm from "remark-gfm"
import { toast } from "sonner"
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Separator } from "@/components/ui/separator"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from "@/components/ui/empty"
import { Input } from "@/components/ui/input"
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
} from "@/components/ui/pagination"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Spinner } from "@/components/ui/spinner"
import { Textarea } from "@/components/ui/textarea"
import { joinTranslations } from "@/lib/i18n"
import { cn, getConfig } from "@/lib/utils"

type UserSummary = {
  id: number
  name: string
  avatar: string
  url: string
}

type CurrentUser = {
  id: number
  name: string
  avatar: string
  email: string
  role: string
}

type PostSummary = {
  id: number
  title: string
  url: string
  type: string
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
  last_comment_id: number
  last_comment_user_id: number
  last_comment_at: string | null
  created_at: string | null
  updated_at: string | null
  permalink: string
  user: UserSummary | null
  post: PostSummary | null
  last_comment_user: UserSummary | null
  can_edit: boolean
  can_delete: boolean
  can_reply: boolean
}

type IssueComment = {
  id: number
  issue_id: number
  post_id: number
  user_id: number
  status: string
  content: string
  created_at: string | null
  updated_at: string | null
  user: UserSummary | null
  can_edit: boolean
  can_delete: boolean
}

type IssueListResponse = {
  items: IssueRecord[]
  count: number
  total: number
  paged: number
  per_page: number
  orderby?: string
  order?: string
}

type CommentListResponse = {
  items: IssueComment[]
  count: number
  total: number
  paged: number
  per_page: number
}

type IssueDetailResponse = {
  issue: IssueRecord
}

type CreateOrUpdateIssueResponse = {
  message?: string
  issue: IssueRecord
}

type CreateCommentResponse = {
  message?: string
  comment: IssueComment
}

type IssuesProps = {
  currentUser?: CurrentUser | null
  allowedTypes?: string[]
  allowedStatuses?: string[]
  pageUrl?: string
}

type RouteState = {
  view: "list" | "detail" | "edit" | "create"
  issueId: number | null
  page: number
  type: string
  status: string
  sortBy: IssueSortField
  sortOrder: IssueSortOrder
}

type IssueFormState = {
  title: string
  content: string
  type: string
}

type IssueSortField = "created_at" | "comment_count" | "updated_at"

type IssueSortOrder = "ASC" | "DESC"

type IssueSortTypeBy = "issue" | "discussion" | "question" | "feedback" | "all"

type IssueSortStatusBy = "open" | "closed" | "accepted" | "progress" | "resolved" | "pending" | "all"

const PAGE_SIZE = 20
const { locale, t, sprintf } = joinTranslations()

function getResponseError(payload: unknown, fallback: string) {
  if (!payload || typeof payload !== "object") {
    return fallback
  }

  const record = payload as Record<string, unknown>
  const data = record.data && typeof record.data === "object"
    ? (record.data as Record<string, unknown>)
    : null

  const directMessage = typeof record.message === "string" ? record.message : ""
  const directDetail = typeof record.detail === "string" ? record.detail : ""
  const dataDetail = data && typeof data.detail === "string" ? data.detail : ""

  return directDetail || dataDetail || directMessage || fallback
}

function toApiUrl(path: string) {
  const { apiUrl } = getConfig()
  if (!apiUrl) {
    throw new Error(t("api_endpoint_not_configured", "API Endpoint Not Configured."))
  }

  return `${apiUrl}/aiya/v1/${path.replace(/^\/+/, "")}`
}

async function apiRequest<T>(path: string, init?: RequestInit): Promise<T> {
  const { apiNonce } = getConfig()
  const headers = new Headers(init?.headers)

  if (init?.body && !headers.has("Content-Type")) {
    headers.set("Content-Type", "application/json")
  }

  if (apiNonce && !headers.has("X-WP-Nonce")) {
    headers.set("X-WP-Nonce", apiNonce)
  }

  const response = await fetch(toApiUrl(path), {
    ...init,
    headers,
  })

  let payload: unknown = null
  try {
    payload = await response.json()
  } catch {
    payload = null
  }

  if (!response.ok) {
    throw new Error(getResponseError(payload, t("request_failed")))
  }

  const data = payload && typeof payload === "object" && "data" in (payload as Record<string, unknown>)
    ? (payload as { data: T }).data
    : (payload as T)

  return data
}

function parsePositiveInt(value: string | null) {
  if (!value) {
    return null
  }

  const next = Number.parseInt(value, 10)
  return Number.isFinite(next) && next > 0 ? next : null
}

function formatDate(value: string | null, withTime = true) {
  if (!value) {
    return t("unknown_time")
  }

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) {
    return value
  }

  return withTime
    ? date.toLocaleString(locale.replace(/_/g, "-"), {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
    })
    : date.toLocaleDateString(locale.replace(/_/g, "-"), {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
    })
}

function htmlToPlainText(value: string) {
  if (!value) {
    return ""
  }

  if (typeof window === "undefined") {
    return value
  }

  const doc = new DOMParser().parseFromString(value, "text/html")
  const text = doc.body.textContent || ""
  return text.replace(/\s+/g, " ").trim()
}

function issueTypeLabel(type: IssueSortTypeBy) {
  const labels = {
    issue: t("issue_type_issue"),
    discussion: t("issue_type_discussion"),
    question: t("issue_type_question"),
    feedback: t("issue_type_feedback"),
  }

  return labels[type as keyof typeof labels] || type
}

function issueStatusLabel(status: IssueSortStatusBy) {
  const labels = {
    open: t("issue_status_open"),
    closed: t("issue_status_closed"),
    accepted: t("issue_status_accepted"),
    progress: t("issue_status_progress"),
    resolved: t("issue_status_resolved"),
    pending: t("issue_status_pending"),
  }

  return labels[status as keyof typeof labels] || status
}

function issueStatusBadgeClass(status: IssueSortStatusBy) {
  const classes: Record<Exclude<IssueSortStatusBy, "all">, string> = {
    open: "bg-primary",
    closed: "bg-gray-700",
    accepted: "bg-green-700",
    progress: "bg-violet-700",
    resolved: "bg-teal-700",
    pending: "bg-amber-700",
  }
  return classes[status as keyof typeof classes] || "bg-muted-foreground text-foreground"
}

function StatusBadge({ status }: { status: string }) {
  return (
    <Badge
      variant="default"
      className={cn("text-sm text-white", issueStatusBadgeClass(status as IssueSortStatusBy))}
    >
      <CircleDot className="size-3.5" />
      {issueStatusLabel(status as IssueSortStatusBy)}
    </Badge>
  )
}

function TypeBadge({ type }: { type: string }) {
  return (
    <Badge variant="secondary" className="text-sm" >
      {issueTypeLabel(type as IssueSortTypeBy)}
    </Badge>
  )
}

function getUserInitials(name: string | undefined) {
  if (!name) {
    return "BOT"
  }

  return name
    .trim()
    .slice(0, 2)
    .toUpperCase()
}

function getPageNumbers(currentPage: number, totalPages: number) {
  if (totalPages <= 7) {
    return Array.from({ length: totalPages }, (_, index) => index + 1)
  }

  const pages: Array<number | "ellipsis"> = []

  if (currentPage <= 4) {
    pages.push(1, 2, 3, 4, 5, "ellipsis", totalPages)
    return pages
  }

  if (currentPage >= totalPages - 3) {
    pages.push(1, "ellipsis", totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages)
    return pages
  }

  pages.push(1, "ellipsis", currentPage - 1, currentPage, currentPage + 1, "ellipsis", totalPages)
  return pages
}

function fetchIssueList(route: Pick<RouteState, "page" | "type" | "status" | "sortBy" | "sortOrder">) {
  const params = new URLSearchParams()
  params.set("paged", String(route.page))
  params.set("per_page", String(PAGE_SIZE))
  params.set("orderby", route.sortBy)
  params.set("order", route.sortOrder)

  if (route.type !== "all") {
    params.set("type", route.type)
  }

  if (route.status !== "all") {
    params.set("status", route.status)
  }
  return apiRequest<IssueListResponse>(`issue/list?${params.toString()}`)
}

function fetchIssueDetail(issueId: number) {
  return apiRequest<IssueDetailResponse>(`issue/get?issue_id=${issueId}`)
}

function fetchIssueComments(issueId: number) {
  return apiRequest<CommentListResponse>(`issue/comment/list?issue_id=${issueId}&per_page=100&order=ASC`)
}

function requestIssueStatusUpdate(issueId: number, status: string) {
  return apiRequest<CreateOrUpdateIssueResponse>("issue/update", {
    method: "POST",
    body: JSON.stringify({
      issue_id: issueId,
      status,
    }),
  })
}

function requestIssueDelete(issueId: number) {
  return apiRequest<{ message?: string }>("issue/delete", {
    method: "POST",
    body: JSON.stringify({ issue_id: issueId }),
  })
}

type IssueListFilterPatch = Partial<Pick<RouteState, "type" | "status" | "page" | "sortBy" | "sortOrder">>
type IssueListQuery = Pick<RouteState, "page" | "type" | "status" | "sortBy" | "sortOrder">
type IssueListMeta = {
  total: number
  totalPages: number
}

type IssueListPageProps = {
  query: IssueListQuery
  onOpenDetail: (issueId: number) => void
  onListMetaChange: (meta: IssueListMeta) => void
}

type IssueDetailPageProps = {
  currentUser: CurrentUser | null
  issueId: number
  refreshKey: number
  onDetailChange: (detail: IssueRecord | null) => void
}

type ssueCreateFormProps = {
  allowedTypes: string[]
  form: IssueFormState
  issueSubmitting: boolean
  submitLabel: string
  onChange: React.Dispatch<React.SetStateAction<IssueFormState>>
  onSubmit: () => void
}

type IssueCreatePageProps = {
  currentUser: CurrentUser | null
  allowedTypes: string[]
  initialType: string
  onOpenDetail: (issueId: number, replace?: boolean) => void
}

type IssueEditPageProps = {
  currentUser: CurrentUser | null
  allowedTypes: string[]
  issueId: number
  onOpenList: () => void
  onOpenDetail: (issueId: number, replace?: boolean) => void
}

type IssueListPaginationProps = {
  currentPage: number
  totalPages: number
  onPageChange: (page: number) => void
}

function IssueListPagination({
  currentPage,
  totalPages,
  onPageChange,
}: IssueListPaginationProps) {
  if (totalPages <= 1) {
    return null
  }

  return (
    <div className="flex justify-center ">
      <Pagination>
        <PaginationContent>
          {getPageNumbers(currentPage, totalPages).map((page, index) => (
            <PaginationItem key={`${page}-${index}`}>
              {page === "ellipsis" ? (
                <PaginationEllipsis />
              ) : (
                <PaginationLink
                  href="#"
                  isActive={currentPage === page}
                  onClick={(event) => {
                    event.preventDefault()
                    onPageChange(page)
                  }}
                >
                  {page}
                </PaginationLink>
              )}
            </PaginationItem>
          ))}
        </PaginationContent>
      </Pagination>
    </div>
  )
}

function IssueListPage({
  query,
  onOpenDetail,
  onListMetaChange,
}: IssueListPageProps) {
  const [listData, setListData] = React.useState<IssueListResponse | null>(null)
  const [listLoading, setListLoading] = React.useState(true)
  const [listError, setListError] = React.useState<string | null>(null)
  const [reloadToken, setReloadToken] = React.useState(0)

  React.useEffect(() => {
    let active = true
    setListLoading(true)
    setListError(null)

    fetchIssueList(query)
      .then((data) => {
        if (!active) {
          return
        }

        setListData(data)
      })
      .catch((error) => {
        if (!active) {
          return
        }

        setListError(error instanceof Error ? error.message : t("issue_list_load_failed"))
      })
      .finally(() => {
        if (active) {
          setListLoading(false)
        }
      })

    return () => {
      active = false
    }
  }, [query, reloadToken])

  const totalPages = React.useMemo(() => {
    if (!listData) {
      return 1
    }

    return Math.max(1, Math.ceil(listData.total / Math.max(1, listData.per_page)))
  }, [listData])

  const retryLoad = React.useCallback(() => {
    setReloadToken((prev) => prev + 1)
  }, [])

  React.useEffect(() => {
    onListMetaChange({
      total: listData?.total || 0,
      totalPages,
    })
  }, [listData?.total, onListMetaChange, totalPages])

  return (
    <div className="overflow-hidden rounded-lg border shadow-sm my-4">
      {listLoading ? (
        <div className="flex min-h-40 items-center justify-center">
          <Spinner className="size-8" />
        </div>
      ) : listError ? (
        <Empty className="min-h-40">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <AlertCircle className="size-5" />
            </EmptyMedia>
            <EmptyTitle>{t("issue_list_load_failed")}</EmptyTitle>
            <EmptyDescription>{listError}</EmptyDescription>
          </EmptyHeader>
          <EmptyContent>
            <Button onClick={retryLoad}>{t("retry")}</Button>
          </EmptyContent>
        </Empty>
      ) : !listData || listData.items.length === 0 ? (
        <Empty className="min-h-40">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <MessageSquareText className="size-5" />
            </EmptyMedia>
            <EmptyTitle>{t("issue_empty_title")}</EmptyTitle>
            <EmptyDescription>{t("issue_empty_description")}</EmptyDescription>
          </EmptyHeader>
        </Empty>
      ) : (
        <div className="divide-y">
          {listData.items.map((issue) => (
            <button
              key={issue.id}
              type="button"
              onClick={() => onOpenDetail(issue.id)}
              className="flex w-full items-start justify-between gap-4 p-5 text-left transition-colors hover:bg-muted/40"
            >
              <div className="min-w-0 flex-1 space-y-3">
                <div className="flex flex-wrap items-center gap-2">
                  <StatusBadge status={issue.status} />
                  <TypeBadge type={issue.type} />
                  <span className="text-base font-semibold text-foreground">
                    {issue.title}
                  </span>
                  <span className="line-clamp-1 text-sm text-muted-foreground">
                    {htmlToPlainText(issue.content) || t("no_description")}
                  </span>
                </div>

                {issue.post && (
                  <p className="line-clamp-1 text-sm text-muted-foreground">
                    {t("initiated_on")}
                    <a
                      href={issue.post.url}
                      target="_blank"
                      rel="noreferrer"
                      className="text-primary hover:text-foreground hover:underline"
                    >
                      {issue.post.title}
                    </a>
                  </p>
                )}

                <div className={cn("flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground")}>
                  <span>
                    #{issue.id}
                  </span>
                  <span className="flex items-center">
                    <Avatar className="size-5 border mr-2">
                      <AvatarImage src={issue.user?.avatar} alt={issue.user?.name} />
                      <AvatarFallback>{getUserInitials(issue.user?.name)}</AvatarFallback>
                    </Avatar>
                    {issue.user?.name}
                  </span>
                  <span>
                    {t("created_at")} {formatDate(issue.created_at, true)}
                  </span>
                </div>
              </div>

              <div className="flex shrink-0 items-center gap-2 text-sm text-muted-foreground">
                <MessageSquareText className="size-4" />
                {issue.comment_count}
              </div>
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

function IssueDetailPage({
  currentUser,
  issueId,
  refreshKey,
  onDetailChange,
}: IssueDetailPageProps) {
  const [detail, setDetail] = React.useState<IssueRecord | null>(null)
  const [detailLoading, setDetailLoading] = React.useState(true)
  const [detailError, setDetailError] = React.useState<string | null>(null)
  const [comments, setComments] = React.useState<IssueComment[]>([])
  const [commentsLoading, setCommentsLoading] = React.useState(true)
  const [commentsError, setCommentsError] = React.useState<string | null>(null)
  const [commentContent, setCommentContent] = React.useState("")
  const [commentSubmitting, setCommentSubmitting] = React.useState(false)

  const loadDetail = React.useCallback(async () => {
    const data = await fetchIssueDetail(issueId)
    setDetail(data.issue)
    return data.issue
  }, [issueId])

  const loadComments = React.useCallback(async () => {
    const data = await fetchIssueComments(issueId)
    setComments(data.items)
    return data.items
  }, [issueId])

  React.useEffect(() => {
    let active = true
    setDetailLoading(true)
    setDetailError(null)

    fetchIssueDetail(issueId)
      .then((data) => {
        if (!active) {
          return
        }

        setDetail(data.issue)
        onDetailChange(data.issue)
      })
      .catch((error) => {
        if (!active) {
          return
        }

        setDetail(null)
        setDetailError(error instanceof Error ? error.message : t("issue_detail_load_failed"))
        onDetailChange(null)
      })
      .finally(() => {
        if (active) {
          setDetailLoading(false)
        }
      })

    return () => {
      active = false
    }
  }, [issueId, onDetailChange, refreshKey])

  React.useEffect(() => {
    let active = true
    setCommentsLoading(true)
    setCommentsError(null)

    fetchIssueComments(issueId)
      .then((data) => {
        if (!active) {
          return
        }

        setComments(data.items)
      })
      .catch((error) => {
        if (!active) {
          return
        }

        setComments([])
        setCommentsError(error instanceof Error ? error.message : t("issue_reply_list_load_failed"))
      })
      .finally(() => {
        if (active) {
          setCommentsLoading(false)
        }
      })

    return () => {
      active = false
    }
  }, [issueId, refreshKey])

  const handleCommentSubmit = React.useCallback(async () => {
    if (!currentUser) {
      return
    }

    if (commentSubmitting) {
      return
    }

    if (!commentContent.trim()) {
      toast.error(t("issue_reply_content_required"))
      return
    }

    setCommentSubmitting(true)

    try {
      const data = await apiRequest<CreateCommentResponse>("issue/comment/create", {
        method: "POST",
        body: JSON.stringify({
          issue_id: issueId,
          content: commentContent.trim(),
          status: "publish",
        }),
      })

      toast.success(data.message || t("issue_reply_published"))
      setCommentContent("")
      await Promise.all([loadComments(), loadDetail()])
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("issue_reply_send_failed"))
    } finally {
      setCommentSubmitting(false)
    }
  }, [commentContent, commentSubmitting, currentUser, issueId, loadComments, loadDetail])

  if (detailLoading) {
    return (
      <div className="flex min-h-40 items-center justify-center">
        <Spinner className="size-8" />
      </div>
    )
  }

  if (detailError || !detail) {
    return (
      <Empty className="min-h-40">
        <EmptyHeader>
          <EmptyMedia variant="icon">
            <AlertCircle className="size-5" />
          </EmptyMedia>
          <EmptyTitle>404</EmptyTitle>
          <EmptyDescription>{detailError || t("issue_not_found")}</EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
          <p className="text-sm text-muted-foreground">{t("return_to_list_continue")}</p>
        </EmptyContent>
      </Empty>
    )
  }

  const canReply = detail.can_reply && Boolean(currentUser)
  const timelineItems = [
    {
      key: `issue-${detail.id}`,
      author: detail.user,
      createdAt: detail.created_at,
      content: detail.content || t("issue_body_empty"),
      label: "正文",
      isIssue: true,
    },
    ...comments.map((comment) => ({
      key: `comment-${comment.id}`,
      author: comment.user,
      createdAt: comment.created_at,
      content: comment.content || "",
      label: "回复",
      isIssue: false,
    })),
  ]

  return (
    <div className="overflow-hidden gap-y-4">

      <div className="flex flex-wrap items-center justify-between gap-3 my-4">
        <div className="min-w-0 flex-1 space-y-2">
          <div className="flex flex-wrap items-center gap-2 mb-4">
            <StatusBadge status={detail.status} />
            <TypeBadge type={detail.type} />
            <h2 className="text-2xl font-semibold tracking-tight">{detail.title}</h2>
          </div>

          <div className={cn("flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground")}>
            <span>
              #{detail.id}
            </span>
            <span className="flex items-center">
              <Avatar className="size-5 border mr-2">
                <AvatarImage src={detail.user?.avatar} alt={detail.user?.name} />
                <AvatarFallback>{getUserInitials(detail.user?.name)}</AvatarFallback>
              </Avatar>
              {detail.user?.name}
            </span>
            <span>
              {t("created_at")} {formatDate(detail.created_at, true)}
            </span>
            {detail.comment_count > 0 && (
              <span>
                {detail.comment_count} {t("issue_reply_count_suffix")}
              </span>
            )}
            {detail.post && (
              <span className="line-clamp-1 text-sm text-muted-foreground">
                {t("initiated_on")}
                <a
                  href={detail.post.url}
                  target="_blank"
                  rel="noreferrer"
                  className="text-primary hover:text-foreground hover:underline"
                >
                  {detail.post.title}
                </a>
              </span>
            )}
          </div>
        </div>
      </div>

      <div className="space-y-4 ">
        <Separator className="my-8" />
        {timelineItems.map((item, index) => (
          <div key={item.key} className="flex items-start gap-3">
            <div className="relative flex flex-col items-center self-stretch">
              <Avatar className="size-9 border">
                <AvatarImage src={item.author?.avatar} alt={item.author?.name} />
                <AvatarFallback>{getUserInitials(item.author?.name)}</AvatarFallback>
              </Avatar>
              {index < timelineItems.length - 1 && (
                <div className="mt-2 w-px flex-1 bg-border" />
              )}
            </div>
            <div className="min-w-0 flex-1 space-y-2">
              <div className="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                <span className="font-medium">{item.author?.name || t("anonymous_user")}</span>
                <span className="text-muted-foreground">
                  {item.isIssue ? t("published_at") : t("replied_at")} {formatDate(item.createdAt, true)}
                </span>
              </div>
              <div className="min-w-0 rounded-lg border border-border/60 bg-secondary/50 shadow-sm">
                <div className="prose prose-sm max-w-none px-4 py-4 dark:prose-invert">
                  <ReactMarkdown
                    remarkPlugins={[remarkGfm, remarkBreaks]}
                    rehypePlugins={[rehypeRaw, rehypeSanitize]}
                  >
                    {item.content}
                  </ReactMarkdown>
                </div>
              </div>
            </div>
          </div>
        ))}
        {commentsLoading ? (
          <div className="flex min-h-24 items-center justify-center">
            <Spinner className="size-6" />
          </div>
        ) : null}
        {commentsError ? (
          <p className="text-sm text-destructive">{commentsError}</p>
        ) : null}

        <p className="text-sm text-muted-foreground mt-8 my-4">
          {!commentsLoading && !commentsError && comments.length === 0 ? (
            <span>{t("issue_no_one_has_responded")}</span>
          ) : (
            <span>{t("last_active_at")} {formatDate(detail.updated_at, true)}</span>
          )}
        </p>
        <Separator className="my-8" />

        {canReply && (
          <>
            <h5 className="text-md font-bold flex items-center">
              <Pencil className="mr-2 size-4" />
              {t("reply_issue")}
            </h5>
            <div className="rounded-lg border shadow-md ">
              <div className="space-y-3 p-4">
                <Textarea
                  className="w-full min-h-24"
                  value={commentContent}
                  onChange={(event) => setCommentContent(event.target.value)}
                  rows={6}
                  placeholder={t("add_reply")}
                  disabled={commentSubmitting}
                />
                <div className="flex justify-end">
                  <Button onClick={handleCommentSubmit} disabled={commentSubmitting || !commentContent.trim()}>
                    {commentSubmitting ? <Spinner className="mr-2 size-4" /> : <Send className="mr-2 size-4" />}
                    {commentSubmitting ? t("sending") : t("reply")}
                  </Button>
                </div>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  )
}

function IssueCreateForm({
  allowedTypes,
  form,
  issueSubmitting,
  submitLabel,
  onChange,
  onSubmit,
}: ssueCreateFormProps) {
  return (
    <Card className="rounded-lg border shadow-md my-4">
      <CardHeader>
        <CardTitle>{t("issue_form_title")}</CardTitle>
        <CardDescription>{t("issue_form_description")}</CardDescription>
      </CardHeader>
      <CardContent className="space-y-5">
        <div className="grid gap-4 md:grid-cols-[80%_minmax(0,1fr)]">
          <div className="space-y-2">
            <label className="text-sm font-medium">{t("title")}</label>
            <Input
              value={form.title}
              onChange={(event) => onChange((prev) => ({ ...prev, title: event.target.value }))}
              placeholder={t("issue_title_brief_placeholder")}
              disabled={issueSubmitting}
            />
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium">{t("issue_form_type_label")}</label>
            <Select value={form.type} onValueChange={(value) => onChange((prev) => ({ ...prev, type: value }))}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder={t("issue_type_placeholder")} />
              </SelectTrigger>
              <SelectContent>
                {allowedTypes.map((type) => (
                  <SelectItem key={type} value={type}>
                    {issueTypeLabel(type as IssueSortTypeBy)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <div className="space-y-2">
          <label className="text-sm font-medium">{t("issue_body_label")}</label>
          <Textarea
            className="w-full min-h-24"
            value={form.content}
            onChange={(event) => onChange((prev) => ({ ...prev, content: event.target.value }))}
            rows={14}
            placeholder={t("issue_body_placeholder")}
            disabled={issueSubmitting}
          />
        </div>

        <div className="flex flex-wrap justify-end gap-2">
          <Button onClick={onSubmit} disabled={issueSubmitting}>
            {issueSubmitting ? <Spinner className="mr-2 size-4" /> : <Send className="mr-2 size-4" />}
            {submitLabel}
          </Button>
        </div>
      </CardContent>
    </Card>
  )
}

function IssueCreatePage({
  currentUser,
  allowedTypes,
  initialType,
  onOpenDetail,
}: IssueCreatePageProps) {
  const [form, setForm] = React.useState<IssueFormState>({
    title: "",
    content: "",
    type: initialType,
  })
  const [issueSubmitting, setIssueSubmitting] = React.useState(false)

  React.useEffect(() => {
    setForm({
      title: "",
      content: "",
      type: initialType,
    })
  }, [initialType])

  const handleIssueSubmit = React.useCallback(async () => {
    if (!currentUser) {
      return
    }

    if (!form.title.trim()) {
      toast.error(t("issue_title_required"))
      return
    }

    if (!form.content.trim()) {
      toast.error(t("issue_content_required"))
      return
    }

    setIssueSubmitting(true)

    try {
      const data = await apiRequest<CreateOrUpdateIssueResponse>("issue/create", {
        method: "POST",
        body: JSON.stringify({
          title: form.title.trim(),
          content: form.content.trim(),
          type: form.type,
          status: "open",
        }),
      })

      toast.success(data.message || t("issue_created"))
      onOpenDetail(data.issue.id, true)
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("issue_save_failed"))
    } finally {
      setIssueSubmitting(false)
    }
  }, [currentUser, form.content, form.title, form.type, onOpenDetail])

  return (
    <IssueCreateForm
      allowedTypes={allowedTypes}
      form={form}
      issueSubmitting={issueSubmitting}
      submitLabel={t("create_issue")}
      onChange={setForm}
      onSubmit={handleIssueSubmit}
    />
  )
}

function IssueEditPage({
  currentUser,
  allowedTypes,
  issueId,
  onOpenList,
  onOpenDetail,
}: IssueEditPageProps) {
  const [detail, setDetail] = React.useState<IssueRecord | null>(null)
  const [detailLoading, setDetailLoading] = React.useState(true)
  const [detailError, setDetailError] = React.useState<string | null>(null)
  const [form, setForm] = React.useState<IssueFormState>({
    title: "",
    content: "",
    type: allowedTypes[0] || "issue",
  })
  const [issueSubmitting, setIssueSubmitting] = React.useState(false)

  React.useEffect(() => {
    let active = true
    setDetailLoading(true)
    setDetailError(null)

    fetchIssueDetail(issueId)
      .then((data) => {
        if (!active) {
          return
        }

        setDetail(data.issue)
        setForm({
          title: data.issue.title,
          content: data.issue.content,
          type: data.issue.type,
        })
      })
      .catch((error) => {
        if (!active) {
          return
        }

        setDetail(null)
        setDetailError(error instanceof Error ? error.message : t("issue_detail_load_failed"))
      })
      .finally(() => {
        if (active) {
          setDetailLoading(false)
        }
      })

    return () => {
      active = false
    }
  }, [issueId])

  const handleIssueSubmit = React.useCallback(async () => {
    if (!currentUser) {
      return
    }

    if (!form.title.trim()) {
      toast.error(t("issue_title_required"))
      return
    }

    if (!form.content.trim()) {
      toast.error(t("issue_content_required"))
      return
    }

    setIssueSubmitting(true)

    try {
      const data = await apiRequest<CreateOrUpdateIssueResponse>("issue/update", {
        method: "POST",
        body: JSON.stringify({
          issue_id: issueId,
          title: form.title.trim(),
          content: form.content.trim(),
          type: form.type,
          status: detail?.status || "open",
        }),
      })

      toast.success(data.message || t("issue_updated"))
      onOpenDetail(data.issue.id, true)
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("issue_save_failed"))
    } finally {
      setIssueSubmitting(false)
    }
  }, [currentUser, detail?.status, form.content, form.title, form.type, issueId, onOpenDetail])

  if (detailLoading) {
    return (
      <div className="flex min-h-40 items-center justify-center">
        <Spinner className="size-8" />
      </div>
    )
  }

  if (detailError || !detail) {
    return (
      <Empty className="min-h-40">
        <EmptyHeader>
          <EmptyMedia variant="icon">
            <AlertCircle className="size-5" />
          </EmptyMedia>
          <EmptyTitle>{t("issue_not_found_or_inaccessible")}</EmptyTitle>
          <EmptyDescription>{detailError || t("issue_not_found_message")}</EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
          <Button variant="outline" onClick={onOpenList}>
            {t("return_to_list")}
          </Button>
        </EmptyContent>
      </Empty>
    )
  }

  if (!detail.can_edit) {
    return (
      <Empty className="min-h-40">
        <EmptyHeader>
          <EmptyMedia variant="icon">
            <AlertCircle className="size-5" />
          </EmptyMedia>
          <EmptyTitle>{t("no_edit_permission")}</EmptyTitle>
          <EmptyDescription>{t("cannot_edit_issue")}</EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
          <Button variant="outline" onClick={() => onOpenDetail(detail.id)}>
            {t("return")}
          </Button>
        </EmptyContent>
      </Empty>
    )
  }

  return (
    <IssueCreateForm
      allowedTypes={allowedTypes}
      form={form}
      issueSubmitting={issueSubmitting}
      submitLabel={t("save_changes")}
      onChange={setForm}
      onSubmit={handleIssueSubmit}
    />
  )
}

export default function Issues({
  currentUser = null,
  allowedTypes = ["issue", "discussion", "question", "feedback"],
  allowedStatuses = ["open", "closed", "accepted", "progress", "resolved", "pending"],
  pageUrl,
}: IssuesProps) {
  const isLoggedIn = Boolean(currentUser)
  const [listMeta, setListMeta] = React.useState<IssueListMeta>({ total: 0, totalPages: 1 })
  const [detailSummary, setDetailSummary] = React.useState<IssueRecord | null>(null)
  const [detailRefreshKey, setDetailRefreshKey] = React.useState(0)
  const [detailAction, setDetailAction] = React.useState<"toggle-status" | "delete" | null>(null)
  const [detailStatusFeedback, setDetailStatusFeedback] = React.useState<{
    type: "success" | "error"
    text: string
  } | null>(null)
  const parseRoute = React.useCallback((urlLike?: string) => {
    const url = new URL(urlLike || window.location.href)
    const issueId = parsePositiveInt(url.searchParams.get("issue"))
    const page = parsePositiveInt(url.searchParams.get("page")) || 1
    const rawType = url.searchParams.get("type") || "all"
    const rawStatus = url.searchParams.get("status") || "all"
    const rawSortBy = url.searchParams.get("orderby") || "updated_at"
    const rawSortOrder = (url.searchParams.get("order") || "DESC").toUpperCase()

    const type = rawType === "all" || allowedTypes.includes(rawType) ? rawType : "all"
    const status = rawStatus === "all" || allowedStatuses.includes(rawStatus) ? rawStatus : "all"
    const sortBy: IssueSortField = rawSortBy === "created_at" || rawSortBy === "comment_count" || rawSortBy === "updated_at" ? rawSortBy : "updated_at"
    const sortOrder: IssueSortOrder = rawSortOrder === "ASC" ? "ASC" : "DESC"

    let nextRoute: RouteState = {
      view: "list",
      issueId: null,
      page,
      type,
      status,
      sortBy,
      sortOrder,
    }

    if (url.searchParams.get("create") === "1") {
      nextRoute = { ...nextRoute, view: "create" }
    } else if (issueId && url.searchParams.get("edit") === "1") {
      nextRoute = { ...nextRoute, view: "edit", issueId }
    } else if (issueId) {
      nextRoute = { ...nextRoute, view: "detail", issueId }
    }

    if (!isLoggedIn) {
      if (nextRoute.view === "create") {
        nextRoute = { ...nextRoute, view: "list" }
      } else if (nextRoute.view === "edit") {
        nextRoute = nextRoute.issueId
          ? { ...nextRoute, view: "detail" }
          : { ...nextRoute, view: "list", issueId: null }
      }
    }

    return nextRoute
  }, [allowedStatuses, allowedTypes, isLoggedIn])

  const [route, setRoute] = React.useState<RouteState>(() => parseRoute())

  const pushRoute = React.useCallback((next: RouteState, replace = false) => {
    const normalizedRoute = !isLoggedIn && next.view === "create"
      ? { ...next, view: "list", issueId: null }
      : !isLoggedIn && next.view === "edit"
        ? (next.issueId ? { ...next, view: "detail" } : { ...next, view: "list", issueId: null })
        : next

    const base = new URL(pageUrl || window.location.href, window.location.origin)
    base.search = ""

    if (normalizedRoute.type !== "all") {
      base.searchParams.set("type", normalizedRoute.type)
    }
    if (normalizedRoute.status !== "all") {
      base.searchParams.set("status", normalizedRoute.status)
    }
    if (normalizedRoute.sortBy !== "updated_at") {
      base.searchParams.set("orderby", normalizedRoute.sortBy)
    }
    if (normalizedRoute.sortOrder !== "DESC") {
      base.searchParams.set("order", normalizedRoute.sortOrder)
    }
    if (normalizedRoute.page > 1) {
      base.searchParams.set("page", String(normalizedRoute.page))
    }
    if (normalizedRoute.view === "create") {
      base.searchParams.set("create", "1")
    }
    if ((normalizedRoute.view === "detail" || normalizedRoute.view === "edit") && normalizedRoute.issueId) {
      base.searchParams.set("issue", String(normalizedRoute.issueId))
    }
    if (normalizedRoute.view === "edit" && normalizedRoute.issueId) {
      base.searchParams.set("edit", "1")
    }

    const url = `${base.pathname}${base.search}`

    if (replace) {
      window.history.replaceState({}, "", url)
    } else {
      window.history.pushState({}, "", url)
    }

    setRoute(normalizedRoute as RouteState)
  }, [isLoggedIn, pageUrl])

  React.useEffect(() => {
    const handlePopState = () => {
      setRoute(parseRoute())
    }

    window.addEventListener("popstate", handlePopState)
    return () => {
      window.removeEventListener("popstate", handlePopState)
    }
  }, [parseRoute])

  React.useEffect(() => {
    if (!isLoggedIn && (route.view === "create" || route.view === "edit")) {
      pushRoute(route, true)
    }
  }, [isLoggedIn, pushRoute, route])

  const openCreate = React.useCallback(() => {
    if (!currentUser) {
      return
    }

    pushRoute({
      ...route,
      view: "create",
      issueId: null,
    })
  }, [currentUser, pushRoute, route])

  const openList = React.useCallback((replace = false) => {
    pushRoute({
      view: "list",
      issueId: null,
      page: route.page,
      type: route.type,
      status: route.status,
      sortBy: route.sortBy,
      sortOrder: route.sortOrder,
    }, replace)
  }, [pushRoute, route.page, route.sortBy, route.sortOrder, route.status, route.type])

  const openDetail = React.useCallback((issueId: number, replace = false) => {
    pushRoute({
      ...route,
      view: "detail",
      issueId,
    }, replace)
  }, [pushRoute, route])

  const openEdit = React.useCallback((issueId: number) => {
    if (!currentUser) {
      return
    }

    pushRoute({
      ...route,
      view: "edit",
      issueId,
    })
  }, [currentUser, pushRoute, route])

  const updateListFilter = React.useCallback((patch: IssueListFilterPatch) => {
    pushRoute({
      view: "list",
      issueId: null,
      page: patch.page ?? 1,
      type: patch.type ?? route.type,
      status: patch.status ?? route.status,
      sortBy: patch.sortBy ?? route.sortBy,
      sortOrder: patch.sortOrder ?? route.sortOrder,
    })
  }, [pushRoute, route.sortBy, route.sortOrder, route.status, route.type])

  React.useEffect(() => {
    if (route.view !== "list") {
      setListMeta({ total: 0, totalPages: 1 })
    }

    if (route.view !== "detail") {
      setDetailSummary(null)
      setDetailRefreshKey(0)
      setDetailAction(null)
      setDetailStatusFeedback(null)
    }
  }, [route.view])

  const sortFieldLabels: Record<IssueSortField, string> = {
    created_at: t("sort_created_at"),
    comment_count: t("sort_comment_count"),
    updated_at: t("sort_updated_at"),
  }

  const sortOrderLabels: Record<IssueSortOrder, string> = {
    DESC: t("sort_desc"),
    ASC: t("sort_asc"),
  }

  const createInitialType = route.type !== "all" ? route.type : (allowedTypes[0] || "issue")

  const handleChangeIssueStatus = React.useCallback(async (nextStatus: string) => {
    if (!detailSummary?.can_edit || detailAction || detailSummary.status === nextStatus) {
      return
    }

    setDetailAction("toggle-status")

    setDetailStatusFeedback(null)

    try {
      const data = await requestIssueStatusUpdate(detailSummary.id, nextStatus)
      setDetailSummary(data.issue)
      setDetailRefreshKey((value) => value + 1)
      setDetailStatusFeedback({
        type: "success",
        text: sprintf(t("status_updated_to_s"), issueStatusLabel(nextStatus as IssueSortStatusBy)),
      })
      toast.success(sprintf(t("issue_status_updated_to_s"), issueStatusLabel(nextStatus as IssueSortStatusBy)))
    } catch (error) {
      const message = error instanceof Error ? error.message : t("status_update_failed")
      setDetailStatusFeedback({
        type: "error",
        text: message,
      })
      toast.error(message)
    } finally {
      setDetailAction(null)
    }
  }, [detailAction, detailSummary])

  const handleDeleteIssue = React.useCallback(async () => {
    if (!detailSummary?.can_delete || detailAction) {
      return
    }

    if (!window.confirm(sprintf(t("confirm_delete_issue_s"), detailSummary.title))) {
      return
    }

    setDetailAction("delete")

    try {
      await requestIssueDelete(detailSummary.id)
      toast.success(t("issue_deleted"))
      openList(true)
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("issue_delete_failed"))
    } finally {
      setDetailAction(null)
    }
  }, [detailAction, detailSummary, openList])

  return (
    <div className=" w-full mx-auto py-4">
      {route.view === "list" && (
        <div className="flex flex-1 flex-wrap items-center justify-between gap-3">
          <div className="flex flex-wrap items-center">
            {currentUser ? (
              <Button onClick={openCreate}>
                <Plus className="mr-2 size-4" />
                {t("create_issue")}
              </Button>
            ) : (
              <div className="rounded-md border px-3 py-2 text-muted-foreground">
                <span className="text-sm">{t("login_first")}</span>
              </div>
            )}
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <span className="px-3 py-2 text-sm text-muted-foreground">
              {sprintf(t("issues_count_d"), listMeta.total)}
            </span>

            <Select
              value={route.status}
              onValueChange={(value) => updateListFilter({ status: value, page: 1 })}
            >
              <SelectTrigger className="w-[140px]">
                <SelectValue placeholder={t("filter_status")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">{t("all_statuses")}</SelectItem>
                {allowedStatuses.map((status) => (
                  <SelectItem key={status} value={status}>
                    {issueStatusLabel(status as IssueSortStatusBy)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <Select
              value={route.type}
              onValueChange={(value) => updateListFilter({ type: value, page: 1 })}
            >
              <SelectTrigger className="hidden w-[120px] md:flex">
                <SelectValue placeholder={t("filter_type")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">{t("all_categories")}</SelectItem>
                {allowedTypes.map((type) => (
                  <SelectItem key={type} value={type}>
                    {issueTypeLabel(type as IssueSortTypeBy)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <Select
              value={route.sortBy}
              onValueChange={(value) => updateListFilter({ sortBy: value as IssueSortField, page: 1 })}
            >
              <SelectTrigger className="hidden w-[140px] md:flex">
                <SelectValue placeholder={t("sort_field")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="updated_at">{sortFieldLabels.updated_at}</SelectItem>
                <SelectItem value="created_at">{sortFieldLabels.created_at}</SelectItem>
                <SelectItem value="comment_count">{sortFieldLabels.comment_count}</SelectItem>
              </SelectContent>
            </Select>

            <Select
              value={route.sortOrder}
              onValueChange={(value) => updateListFilter({ sortOrder: value as IssueSortOrder, page: 1 })}
            >
              <SelectTrigger className="hidden w-[120px] md:flex">
                <SelectValue placeholder={t("sort_order")} />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="DESC">{sortOrderLabels.DESC}</SelectItem>
                <SelectItem value="ASC">{sortOrderLabels.ASC}</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      )}

      {route.view !== "list" && (
        <div className="flex flex-wrap items-center justify-between gap-3">
          <div className="flex flex-wrap items-center gap-2">
            <Button variant="outline" onClick={() => openList()}>
              <ArrowLeft className="mr-2 size-4" />
              {t("issue_home")}
            </Button>

            {route.view === "detail" && detailSummary?.can_edit && (
              <Button variant="outline" onClick={() => openEdit(detailSummary.id)}>
                <Pencil className="mr-2 size-4" />
                {t("edit")}
              </Button>
            )}

            {route.view === "edit" && route.issueId && (
              <Button variant="outline" onClick={() => route.issueId && openDetail(route.issueId)}>
                <ArrowLeft className="mr-2 size-4" />
                {t("back_to_issue")}
              </Button>
            )}

            {route.view === "detail" && detailSummary?.can_edit && (
              <Select
                value={detailSummary.status}
                onValueChange={handleChangeIssueStatus}
                disabled={detailAction !== null}
              >
                <SelectTrigger className="w-[160px]">
                  <SelectValue placeholder={t("select_issue_status")} />
                </SelectTrigger>
                <SelectContent>
                  {allowedStatuses.map((status) => (
                    <SelectItem key={status} value={status}>
                      {issueStatusLabel(status as IssueSortStatusBy)}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            )}

            {route.view === "detail" && detailSummary?.can_delete && (
              <Button variant="destructive" onClick={handleDeleteIssue} disabled={detailAction !== null}>
                {detailAction === "delete" ? <Spinner className="mr-2 size-4" /> : <Trash2 className="mr-2 size-4" />}
                {t("delete_issue")}
              </Button>
            )}
          </div>

          <div className="min-h-9 text-sm">
            {route.view === "detail" && detailSummary?.can_edit && detailStatusFeedback ? (
              <span className={cn(
                "inline-flex min-h-9 items-center rounded-md px-3",
                detailStatusFeedback.type === "success"
                  ? "bg-emerald-50 text-emerald-700 "
                  : "bg-destructive/10 text-destructive",
              )}>
                {detailStatusFeedback.text}
              </span>
            ) : null}

            {route.view === "create" && (
              <span className={cn(
                "inline-flex min-h-9 items-center rounded-md px-3",
                "bg-sky-50 text-sky-700 ",
              )}>
                {t("create_issue")}
              </span>
            )}

            {route.view === "edit" && (
              <span className={cn(
                "inline-flex min-h-9 items-center rounded-md px-3",
                "bg-sky-50 text-sky-700 ",
              )}>
                {t("issue_edit_mode_hint")}
              </span>
            )}
          </div>
        </div>
      )}

      {route.view === "list" ? (
        <>
          <IssueListPage
            query={{
              page: route.page,
              type: route.type,
              status: route.status,
              sortBy: route.sortBy,
              sortOrder: route.sortOrder,
            }}
            onOpenDetail={openDetail}
            onListMetaChange={setListMeta}
          />
          <IssueListPagination
            currentPage={route.page}
            totalPages={listMeta.totalPages}
            onPageChange={(page) => updateListFilter({ page })}
          />
        </>
      ) : route.view === "detail" && route.issueId ? (
        <IssueDetailPage
          currentUser={currentUser}
          issueId={route.issueId}
          refreshKey={detailRefreshKey}
          onDetailChange={setDetailSummary}
        />
      ) : route.view === "edit" && route.issueId ? (
        <IssueEditPage
          currentUser={currentUser}
          allowedTypes={allowedTypes}
          issueId={route.issueId}
          onOpenList={() => openList()}
          onOpenDetail={openDetail}
        />
      ) : (
        <IssueCreatePage
          currentUser={currentUser}
          allowedTypes={allowedTypes}
          initialType={createInitialType}
          onOpenDetail={openDetail}
        />
      )}
    </div>
  )
}
