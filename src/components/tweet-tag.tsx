import * as React from "react"
import { __ } from "@wordpress/i18n"
import { Hash, LayoutGrid, ChevronDown, ChevronUp } from "lucide-react"

import { cn } from "@/lib/utils"

export interface TweetTagItem {
  id: number
  name: string
  slug: string
  count?: number
}

interface TweetTagProps {
  tags?: TweetTagItem[]
  selected?: string[]
  archiveUrl?: string
}

const MAX_TAGS = 15

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
  archiveUrl,
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

  const [isExpanded, setIsExpanded] = React.useState(false)

  const allHref = React.useMemo(() => {
    const origin = typeof window !== "undefined" ? window.location.origin : "/tweet/"
    const url = new URL(fallbackArchiveUrl, origin)
    url.searchParams.delete("t_tag")
    return `${url.pathname}${url.search}`
  }, [fallbackArchiveUrl])

  const isAllActive = normalizedSelected.length === 0
  const displayTags = isExpanded ? tags : tags.slice(0, MAX_TAGS)

  return (
    <nav className="flex flex-col gap-2 my-4">
      <a
        href={allHref}
        aria-pressed={isAllActive}
        className={cn(
          "flex items-center justify-between px-3 py-2 text-md font-medium rounded-md transition-colors",
          isAllActive
            ? "bg-primary text-primary-foreground hover:bg-primary/90"
            : "text-muted-foreground hover:bg-muted hover:text-foreground"
        )}
      >
        <div className="flex items-center gap-2">
          <LayoutGrid className="w-4 h-4" />
          <span>{__("全部", "aiya-cms")}</span>
        </div>
      </a>

      {displayTags.map((tag) => {
        const isActive = normalizedSelected.includes(tag.slug)
        const href = buildTagUrl(fallbackArchiveUrl, normalizedSelected, tag.slug)

        return (
          <a
            key={tag.id}
            href={href}
            aria-pressed={isActive}
            className={cn(
              "flex items-center justify-between px-3 py-2 text-md font-medium rounded-md transition-colors",
              isActive
                ? "bg-primary text-primary-foreground hover:bg-primary/90"
                : "text-muted-foreground hover:bg-muted hover:text-foreground"
            )}
          >
            <div className="flex items-center gap-2">
              <Hash className="w-4 h-4" />
              <span>{tag.name}</span>
            </div>
            {typeof tag.count === "number" ? (
              <span
                className={cn(
                  "text-xs px-2 py-0.5 rounded-full",
                  isActive ? "bg-primary-foreground/20 text-primary-foreground" : "bg-muted-foreground/20 text-muted-foreground"
                )}
              >
                {tag.count}
              </span>
            ) : null}
          </a>
        )
      })}

      {tags.length > MAX_TAGS && (
        <button
          onClick={() => setIsExpanded(!isExpanded)}
          className="flex items-center justify-center gap-1 px-3 py-2 mt-1 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground rounded-md transition-colors"
        >
          {isExpanded ? (
            <>
              <ChevronUp className="w-4 h-4" />
              <span>{__("收起", "aiya-cms")}</span>
            </>
          ) : (
            <>
              <ChevronDown className="w-4 h-4" />
              <span>{__("展开更多", "aiya-cms")}</span>
            </>
          )}
        </button>
      )}
    </nav>
  )
}
