import * as React from "react"
import { __ } from "@wordpress/i18n"
import { toast } from "sonner"
import {
  Trash2,
  Pencil,
  ImagePlus,
  Hash
} from "lucide-react"

import { getConfig } from "@/lib/utils"

import { Card, CardContent, CardFooter } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Spinner } from "@/components/ui/spinner"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"

export interface TweetCardTag {
  id?: number
  name: string
  slug?: string
  count?: number
}

export interface TweetCardPost {
  id?: string
  title: string
  content: string
  status?: string
}

export interface TweetEditorProps {
  mode?: "create" | "edit"
  post?: TweetCardPost
  tags?: TweetCardTag[]
  redirectUrl?: string
}

type SubmitStatus = "publish" | "draft" | "pending" | "trash"

function getResponseMessage(data: any, fallback: string) {
  return data?.message || data?.detail || data?.data?.detail || fallback
}

function htmlToText(html: string) {
  if (!html) return ""
  if (typeof window === "undefined") return html

  const doc = new DOMParser().parseFromString(html, "text/html")

  doc.querySelectorAll("br").forEach((br) => br.replaceWith("\n"))
  doc.querySelectorAll("p").forEach((p) => {
    p.append("\n\n")
  })

  return (doc.body.textContent || "").trim()
}

export default function TweetEditor({
  mode = "create",
  post,
  tags = [],
  redirectUrl,
}: TweetEditorProps) {
  const { apiUrl, apiNonce } = getConfig()
  const [title, setTitle] = React.useState(post?.title || "")
  const [content, setContent] = React.useState(() => htmlToText(post?.content || ""))
  const [isDraft, setIsDraft] = React.useState(post?.status === "draft")
  const [isSubmitting, setIsSubmitting] = React.useState(false)
  const [isDeleting, setIsDeleting] = React.useState(false)

  const handleSubmit = async (nextStatus: SubmitStatus) => {
    if (!apiUrl) {
      toast.error(__("接口配置缺失", "aiya-cms"))
      return
    }

    if (!content.trim()) {
      toast.error(__("帖子内容不能为空", "aiya-cms"))
      return
    }

    const body: Record<string, unknown> = {
      title,
      content,
      status: nextStatus,
    }

    if (mode === "edit" && post?.id) {
      body.post_id = post.id
    }

    setIsSubmitting(true)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/tweet/${ mode === "edit" ? "update" : "create"}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify(body),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(getResponseMessage(data, mode === "edit" ? __('帖子更新失败', 'aiya-cms') : __('帖子发布失败', 'aiya-cms')))
      }

      toast.success(getResponseMessage(data, mode === "edit" ? __('帖子已更新', 'aiya-cms') : __('帖子已发布', 'aiya-cms')))

      const postData = data?.data?.post_data || data?.post_data
      const nextUrl = redirectUrl || postData?.url || postData?.link

      if (nextUrl && mode === "edit") {
        window.location.href = nextUrl
      } else {
        window.location.reload()
      }
    } catch (error) {
      toast.error(error instanceof Error ? error.message : __('保存失败', 'aiya-cms'))
    } finally {
      setIsSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!post?.id || !apiUrl || isDeleting) {
      return
    }

    if (!window.confirm(__("确定要删除这条推文吗？", "aiya-cms"))) {
      return
    }

    const { apiNonce } = getConfig()
    setIsDeleting(true)

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/tweet/delete`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({ post_id: post.id, status: "trash" }),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(getResponseMessage(data, __('删除帖子失败', 'aiya-cms')))
      }

      toast.success(getResponseMessage(data, __('帖子已删除', 'aiya-cms')))
      window.location.href = redirectUrl || getConfig().homeUrl || "/"
    } catch (error) {
      toast.error(error instanceof Error ? error.message : __('删除帖子失败', 'aiya-cms'))
    } finally {
      setIsDeleting(false)
    }
  }

  return (
    <Card className="py-2">
      <CardContent className="grid gap-2 px-4">
        <Input
          placeholder={__("标题", "aiya-cms")}
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          disabled={isSubmitting}
          className="prose prose-sm text-base font-medium border-0 px-0 focus-visible:ring-0 shadow-none"
        />
        <Textarea
          placeholder={__("有什么新鲜事？", "aiya-cms")}
          value={content}
          onChange={(e) => setContent(e.target.value)}
          disabled={isSubmitting}
          rows={4}
          className="resize-none border-0 px-0 focus-visible:ring-0 shadow-none text-base prose prose-sm"
        />
      </CardContent>

      <CardFooter className="flex items-center justify-between border-t [.border-t]:pt-2 px-4">
        <div className="flex items-center gap-2">
          <Button
            variant="ghost"
            size="sm"
            type="button"
            disabled={true}
            title={__("插入图片", "aiya-cms")}>
            <ImagePlus className="w-3.5 h-3.5"
            />
            {__("上传图片", "aiya-cms")}
          </Button>
          {tags.length > 0 && (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button
                  variant="ghost"
                  size="sm"
                  type="button"
                  disabled={isSubmitting}
                  title={__("插入标签", "aiya-cms")}>
                  <Hash className="w-3.5 h-3.5"
                  />
                  {__("标签", "aiya-cms")}
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="start" className="w-48 max-h-[300px] overflow-y-auto">
                {tags.map((tag) => (
                  <DropdownMenuItem
                    key={tag.id || tag.name}
                    onClick={() => {
                      setContent((prev) => {
                        const separator = prev && !prev.endsWith(" ") && !prev.endsWith("\n") ? " " : ""
                        return prev + separator + `#${tag.name}# `
                      })
                    }}
                    className="cursor-pointer"
                  >
                    {tag.name}
                    {tag.count !== undefined && <span className="ml-auto text-muted-foreground text-xs">{tag.count}</span>}
                  </DropdownMenuItem>
                ))}
              </DropdownMenuContent>
            </DropdownMenu>
          )}
        </div>

        <div className="flex items-center gap-2">
          <div className="flex items-center space-x-2 mr-6">
            <Checkbox
              id="draft-mode"
              checked={isDraft}
              onCheckedChange={(c) => setIsDraft(!!c)}
              disabled={isSubmitting}
            />
            <Label htmlFor="draft-mode" className="text-sm font-normal cursor-pointer">
              {__("保存为草稿", "aiya-cms")}
            </Label>
          </div>
          {mode === "edit" && post?.id && (
            <Button
              variant="destructive"
              size="sm"
              onClick={handleDelete}
              disabled={isDeleting || isSubmitting}
            >
              {isDeleting ? <Spinner className="w-3.5 h-3.5" /> : <Trash2 className="w-3.5 h-3.5" />}
              {__("删除", "aiya-cms")}
            </Button>
          )}
          <Button
            size="sm"
            onClick={() => handleSubmit(isDraft ? "draft" : "publish")}
            disabled={isSubmitting}
          >
            {isSubmitting ? <Spinner className="w-3.5 h-3.5 mr-2" /> : <Pencil className="w-3.5 h-3.5 mr-2" />}
            {mode === "edit" ? __("更新", "aiya-cms") : __("发布", "aiya-cms")}
          </Button>
        </div>
      </CardFooter>
    </Card>
  )
}
