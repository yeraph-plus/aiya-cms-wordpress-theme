import { __ } from "@wordpress/i18n"

export interface TweetCardAuthor {
  name: string
  avatar?: string
}

export interface TweetCardTag {
  id?: number
  name: string
  slug?: string
  count?: number
}

export interface TweetCardPost {
  id: number
  url?: string
  title?: string
  attr_title?: string
  content?: string
  date?: string
  date_iso?: string
  comments?: string
  likes?: string
  author?: TweetCardAuthor
  tags?: TweetCardTag[]
  gallery_images?: string[]
}

interface TweetCardProps {
  post: TweetCardPost
  archiveUrl?: string
  className?: string
}

function getImageUrl(path: string) {
  if (/^https?:\/\//i.test(path)) {
    return path
  }

  if (typeof window === "undefined") {
    return path
  }

  return `${window.location.origin}/wp-content/upload-tweet/${path.replace(/^\/+/, "")}`
}

function buildTagHref(archiveUrl: string | undefined, slug?: string) {
  if (!slug) {
    return undefined
  }

  const base = archiveUrl || (typeof window !== "undefined" ? window.location.pathname : "/tweet/")
  const url = new URL(base, typeof window !== "undefined" ? window.location.origin : "https://example.com")
  url.searchParams.set("t_tag", slug)
  return `${url.pathname}${url.search}`
}

export default function TweetCard({ post, archiveUrl, className = "" }: TweetCardProps) {
  return (
    <article
      className={className}
      style={{
        border: "1px solid #d4d4d8",
        borderRadius: "12px",
        padding: "16px",
        display: "grid",
        gap: "12px",
        backgroundColor: "#fff",
      }}
    >
      <header style={{ display: "flex", justifyContent: "space-between", gap: "12px", alignItems: "flex-start" }}>
        <div style={{ display: "flex", gap: "12px", alignItems: "center" }}>
          {post.author?.avatar ? (
            <img
              src={post.author.avatar}
              alt={post.author.name || "avatar"}
              width={40}
              height={40}
              style={{ width: "40px", height: "40px", borderRadius: "999px", objectFit: "cover" }}
            />
          ) : null}
          <div>
            <div style={{ fontWeight: 600 }}>{post.author?.name || __("匿名用户", "aiya-cms")}</div>
            {post.date ? <time dateTime={post.date_iso}>{post.date}</time> : null}
          </div>
        </div>
        <div style={{ display: "flex", gap: "12px", fontSize: "14px" }}>
          <span>{__("评论", "aiya-cms")}: {post.comments || "0"}</span>
          <span>{__("点赞", "aiya-cms")}: {post.likes || "0"}</span>
        </div>
      </header>

      {post.title ? (
        <h3 style={{ margin: 0, fontSize: "18px" }}>
          {post.url ? (
            <a href={post.url} title={post.attr_title || post.title} style={{ color: "inherit" }}>
              {post.title}
            </a>
          ) : (
            post.title
          )}
        </h3>
      ) : null}

      {post.content ? (
        <div dangerouslySetInnerHTML={{ __html: post.content }} style={{ lineHeight: 1.7, wordBreak: "break-word" }} />
      ) : null}

      {post.gallery_images && post.gallery_images.length > 0 ? (
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(120px, 1fr))", gap: "8px" }}>
          {post.gallery_images.map((image) => (
            <img
              key={image}
              src={getImageUrl(image)}
              alt="tweet"
              loading="lazy"
              style={{ width: "100%", borderRadius: "8px", display: "block" }}
            />
          ))}
        </div>
      ) : null}

      {post.tags && post.tags.length > 0 ? (
        <footer style={{ display: "flex", flexWrap: "wrap", gap: "8px" }}>
          {post.tags.map((tag) => {
            const href = buildTagHref(archiveUrl, tag.slug)

            return href ? (
              <a
                key={`${tag.slug || tag.name}`}
                href={href}
                style={{
                  textDecoration: "none",
                  border: "1px solid #d4d4d8",
                  borderRadius: "999px",
                  padding: "4px 10px",
                }}
              >
                #{tag.name}
              </a>
            ) : (
              <span
                key={`${tag.slug || tag.name}`}
                style={{
                  border: "1px solid #d4d4d8",
                  borderRadius: "999px",
                  padding: "4px 10px",
                }}
              >
                #{tag.name}
              </span>
            )
          })}
        </footer>
      ) : null}
    </article>
  )
}
