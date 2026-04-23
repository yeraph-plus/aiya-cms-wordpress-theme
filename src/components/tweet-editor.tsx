import * as React from "react"
import { __ } from "@wordpress/i18n"
import { toast } from "sonner"

import { getConfig } from "@/lib/utils"
import type { TweetCardPost } from "@/components/tweet-card"

interface TweetEditorProps {
  mode?: "create" | "edit"
  post?: TweetCardPost & { status?: string }
  redirectUrl?: string
  className?: string
}

type SubmitStatus = "publish" | "draft" | "pending"

type UploadedImage = {
  path: string
  url: string
}

function getResponseMessage(data: any, fallback: string) {
  return data?.message || data?.detail || data?.data?.detail || fallback
}

function buildTweetImageUrl(path: string) {
  if (/^https?:\/\//i.test(path)) {
    return path
  }

  if (typeof window === "undefined") {
    return `/wp-content/upload-tweet/${path.replace(/^\/+/, "")}`
  }

  return `${window.location.origin}/wp-content/upload-tweet/${path.replace(/^\/+/, "")}`
}

function getInitialGalleryImages(post?: TweetEditorProps["post"]): UploadedImage[] {
  if (!post?.gallery_images || !Array.isArray(post.gallery_images)) {
    return []
  }

  return post.gallery_images
    .filter((item) => typeof item === "string" && item !== "")
    .map((item) => ({
      path: item,
      url: buildTweetImageUrl(item),
    }))
}

export default function TweetEditor({
  mode = "create",
  post,
  redirectUrl,
  className = "",
}: TweetEditorProps) {
  const [title, setTitle] = React.useState(post?.title || "")
  const [content, setContent] = React.useState(post?.content || "")
  const [status, setStatus] = React.useState<SubmitStatus>((post?.status as SubmitStatus) || "pending")
  const [galleryImages, setGalleryImages] = React.useState<UploadedImage[]>(() => getInitialGalleryImages(post))
  const [isSubmitting, setIsSubmitting] = React.useState(false)
  const [isDeleting, setIsDeleting] = React.useState(false)
  const [isUploading, setIsUploading] = React.useState(false)

  const endpointBase = React.useMemo(() => {
    const { apiUrl } = getConfig()
    return apiUrl ? `${apiUrl}/aiya/v1` : ""
  }, [])

  const handleUpload = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0]

    if (!file) {
      return
    }

    if (!endpointBase) {
      toast.error(__("接口配置缺失", "aiya-cms"))
      return
    }

    const { apiNonce } = getConfig()
    const formData = new FormData()
    formData.append("file", file)

    setIsUploading(true)

    try {
      const response = await fetch(`${endpointBase}/tweet/upload_image`, {
        method: "POST",
        headers: {
          "X-WP-Nonce": apiNonce || "",
        },
        body: formData,
      })

      const data = await response.json()
      const payload = data?.data || data

      if (!response.ok) {
        throw new Error(getResponseMessage(data, __('图片上传失败', 'aiya-cms')))
      }

      const path = payload?.path
      const url = payload?.url

      if (!path || !url) {
        throw new Error(__("上传接口返回的数据不完整", "aiya-cms"))
      }

      setGalleryImages((prev) => {
        const next = prev.filter((item) => item.path !== path)
        next.push({ path, url })
        return next
      })
      toast.success(getResponseMessage(data, __('图片上传成功', 'aiya-cms')))
    } catch (error) {
      toast.error(error instanceof Error ? error.message : __('图片上传失败', 'aiya-cms'))
    } finally {
      setIsUploading(false)
      event.target.value = ""
    }
  }

  const handleSubmit = async (nextStatus: SubmitStatus) => {
    if (!endpointBase) {
      toast.error(__("接口配置缺失", "aiya-cms"))
      return
    }

    if (!content.trim()) {
      toast.error(__("帖子内容不能为空", "aiya-cms"))
      return
    }

    const { apiNonce } = getConfig()
    const body: Record<string, unknown> = {
      title,
      content,
      status: nextStatus,
      gallery_images: galleryImages.map((item) => item.path),
    }

    if (mode === "edit" && post?.id) {
      body.post_id = post.id
    }

    setIsSubmitting(true)

    try {
      const response = await fetch(`${endpointBase}/tweet/${mode === "edit" ? "update" : "create"}`, {
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
      if (postData) {
        setTitle(postData.title || "")
        setContent(postData.content_raw || postData.content || "")
        setStatus((postData.status as SubmitStatus) || nextStatus)
        setGalleryImages(getInitialGalleryImages(postData))
      }

      const nextUrl = redirectUrl || postData?.url || postData?.link
      if (nextUrl && mode === "create") {
        window.location.href = nextUrl
      }
    } catch (error) {
      toast.error(error instanceof Error ? error.message : __('保存失败', 'aiya-cms'))
    } finally {
      setIsSubmitting(false)
    }
  }

  const handleDelete = async () => {
    if (!post?.id || !endpointBase || isDeleting) {
      return
    }

    if (!window.confirm(__("确定要删除这条推文吗？", "aiya-cms"))) {
      return
    }

    const { apiNonce } = getConfig()
    setIsDeleting(true)

    try {
      const response = await fetch(`${endpointBase}/tweet/delete`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({ post_id: post.id }),
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
    <section className={className} style={{ display: "grid", gap: "16px" }}>
      <div style={{ display: "grid", gap: "8px" }}>
        <label htmlFor="tweet-title">{__("标题", "aiya-cms")}</label>
        <input
          id="tweet-title"
          type="text"
          value={title}
          onChange={(event) => setTitle(event.target.value)}
          disabled={isSubmitting}
          style={{ padding: "10px 12px", border: "1px solid #d4d4d8", borderRadius: "8px" }}
        />
      </div>

      <div style={{ display: "grid", gap: "8px" }}>
        <label htmlFor="tweet-content">{__("内容", "aiya-cms")}</label>
        <textarea
          id="tweet-content"
          value={content}
          onChange={(event) => setContent(event.target.value)}
          disabled={isSubmitting}
          rows={8}
          style={{ padding: "10px 12px", border: "1px solid #d4d4d8", borderRadius: "8px", resize: "vertical" }}
        />
      </div>

      <div style={{ display: "grid", gap: "8px" }}>
        <label htmlFor="tweet-status">{__("状态", "aiya-cms")}</label>
        <select
          id="tweet-status"
          value={status}
          onChange={(event) => setStatus(event.target.value as SubmitStatus)}
          disabled={isSubmitting}
          style={{ padding: "10px 12px", border: "1px solid #d4d4d8", borderRadius: "8px" }}
        >
          <option value="pending">{__("待审核", "aiya-cms")}</option>
          <option value="draft">{__("草稿", "aiya-cms")}</option>
          <option value="publish">{__("发布", "aiya-cms")}</option>
        </select>
      </div>

      <div style={{ display: "grid", gap: "8px" }}>
        <label htmlFor="tweet-upload">{__("上传图片", "aiya-cms")}</label>
        <input id="tweet-upload" type="file" accept="image/*" onChange={handleUpload} disabled={isUploading || isSubmitting} />
        {isUploading ? <p>{__("图片上传中...", "aiya-cms")}</p> : null}
      </div>

      {galleryImages.length > 0 ? (
        <div style={{ display: "grid", gap: "8px" }}>
          <strong>{__("已上传图片", "aiya-cms")}</strong>
          <div style={{ display: "grid", gap: "8px" }}>
            {galleryImages.map((image) => (
              <div
                key={image.path}
                style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: "8px", border: "1px solid #d4d4d8", borderRadius: "8px", padding: "8px 12px" }}
              >
                <a href={image.url} target="_blank" rel="noreferrer" style={{ wordBreak: "break-all" }}>
                  {image.url}
                </a>
                <button
                  type="button"
                  onClick={() => setGalleryImages((prev) => prev.filter((item) => item.path !== image.path))}
                  disabled={isSubmitting}
                >
                  {__("移除", "aiya-cms")}
                </button>
              </div>
            ))}
          </div>
        </div>
      ) : null}

      <div style={{ display: "flex", flexWrap: "wrap", gap: "12px" }}>
        <button type="button" onClick={() => handleSubmit(status)} disabled={isSubmitting || isUploading}>
          {isSubmitting ? __("提交中...", "aiya-cms") : mode === "edit" ? __("更新推文", "aiya-cms") : __("发布推文", "aiya-cms")}
        </button>
        <button type="button" onClick={() => handleSubmit("draft")} disabled={isSubmitting || isUploading}>
          {__("保存草稿", "aiya-cms")}
        </button>
        {mode === "edit" && post?.id ? (
          <button type="button" onClick={handleDelete} disabled={isDeleting || isSubmitting}>
            {isDeleting ? __("删除中...", "aiya-cms") : __("删除推文", "aiya-cms")}
          </button>
        ) : null}
      </div>
    </section>
  )
}
