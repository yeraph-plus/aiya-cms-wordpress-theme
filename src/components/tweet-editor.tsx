import * as React from "react"

import { toast } from "sonner"
import {
  Trash2,
  Pencil,
  ImagePlus,
  Hash
} from "lucide-react"

import { getConfig } from "@/lib/utils"
import { joinTranslations } from '@/lib/i18n';

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

const { t } = joinTranslations();

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
      toast.error(t("api_config_missing"))
      return
    }

    if (!content.trim()) {
      toast.error(t("tweet_content_required"))
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
      const response = await fetch(`${apiUrl}/aiya/v1/tweet/${mode === "edit" ? "update" : "create"}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify(body),
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(getResponseMessage(data, mode === "edit" ? t("tweet_update_failed") : t("tweet_publish_failed")))
      }

      toast.success(getResponseMessage(data, mode === "edit" ? t("tweet_updated") : t("tweet_published")))

      const postData = data?.data?.post_data || data?.post_data
      const nextUrl = redirectUrl || postData?.url || postData?.link

      if (nextUrl && mode === "edit") {
        window.location.href = nextUrl
      } else {
        window.location.reload()
      }
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("save_failed"))
    } finally {
      setIsSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!post?.id || !apiUrl || isDeleting) {
      return
    }

    if (!window.confirm(t("confirm_delete_tweet"))) {
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
        throw new Error(getResponseMessage(data, t("delete_tweet_failed")))
      }

      toast.success(getResponseMessage(data, t("tweet_deleted")))
      window.location.href = redirectUrl || getConfig().homeUrl || "/"
    } catch (error) {
      toast.error(error instanceof Error ? error.message : t("delete_tweet_failed"))
    } finally {
      setIsDeleting(false)
    }
  }

  return (
    <Card className="py-2">
      <CardContent className="grid gap-2 px-4">
        <Input
          placeholder={t("title")}
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          disabled={isSubmitting}
          className="prose prose-sm text-base font-medium border-0 px-0 focus-visible:ring-0 shadow-none"
        />
        <Textarea
          placeholder={t("what_is_happening")}
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
            title={t("insert_image")}>
            <ImagePlus className="w-3.5 h-3.5"
            />
            {t("upload_image")}
          </Button>
          {tags.length > 0 && (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button
                  variant="ghost"
                  size="sm"
                  type="button"
                  disabled={isSubmitting}
                  title={t("insert_tag")}>
                  <Hash className="w-3.5 h-3.5"
                  />
                  {t("tag")}
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
              {t("save_as_draft")}
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
              {t("delete")}
            </Button>
          )}
          <Button
            size="sm"
            onClick={() => handleSubmit(isDraft ? "draft" : "publish")}
            disabled={isSubmitting}
          >
            {isSubmitting ? <Spinner className="w-3.5 h-3.5 mr-2" /> : <Pencil className="w-3.5 h-3.5 mr-2" />}
            {mode === "edit" ? t("update") : t("publish")}
          </Button>
        </div>
      </CardFooter>
    </Card>
  )
}
