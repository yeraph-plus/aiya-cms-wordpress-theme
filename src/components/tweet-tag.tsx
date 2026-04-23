import * as React from "react"
import { __ } from "@wordpress/i18n"

export interface TweetTagItem {
  id: number
  name: string
  slug: string
  count?: number
}

interface TweetTagProps {
  tags?: TweetTagItem[]
  selected?: string[]
  title?: string
  archiveUrl?: string
  className?: string
}

function buildTagUrl(archiveUrl: string, selected: string[], tagSlug: string) {
  const nextSelected = selected.includes(tagSlug)
    ? selected.filter((item) => item !== tagSlug)
    : [...selected, tagSlug]

  const url = new URL(archiveUrl, window.location.origin)

  if (nextSelected.length > 0) {
    url.searchParams.set("t_tag", nextSelected.join(","))
  } else {
    url.searchParams.delete("t_tag")
  }

  return `${url.pathname}${url.search}`
}

export default function TweetTag({
  tags = [],
  selected = [],
  title,
  archiveUrl,
  className = "",
}: TweetTagProps) {
  const normalizedSelected = React.useMemo(() => {
    return Array.from(new Set(selected.map((item) => item.trim()).filter(Boolean)))
  }, [selected])

  const fallbackArchiveUrl = React.useMemo(() => {
    if (archiveUrl) {
      return archiveUrl
    }

    if (typeof window === "undefined") {
      return "/tweet/"
    }

    return window.location.pathname
  }, [archiveUrl])

  if (!tags.length) {
    return null
  }

  return (
    <section className={className}>
      {title ? <h2>{title}</h2> : <h2>{__("推文标签", "aiya-cms")}</h2>}
      <div style={{ display: "flex", flexWrap: "wrap", gap: "8px", marginTop: "12px" }}>
        {tags.map((tag) => {
          const isActive = normalizedSelected.includes(tag.slug)
          const href = buildTagUrl(fallbackArchiveUrl, normalizedSelected, tag.slug)

          return (
            <a
              key={tag.id}
              href={href}
              aria-pressed={isActive}
              style={{
                display: "inline-flex",
                alignItems: "center",
                gap: "6px",
                padding: "6px 10px",
                borderRadius: "999px",
                border: "1px solid currentColor",
                textDecoration: "none",
                backgroundColor: isActive ? "CanvasText" : "Canvas",
                color: isActive ? "Canvas" : "CanvasText",
              }}
            >
              <span>#{tag.name}</span>
              {typeof tag.count === "number" ? <small>({tag.count})</small> : null}
            </a>
          )
        })}
      </div>
    </section>
  )
}
