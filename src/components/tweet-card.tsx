import * as React from "react"
import { __ } from "@wordpress/i18n"
import {
  MessageCircle,
  Heart,
  MoreHorizontal
} from "lucide-react"

import { getConfig } from "@/lib/utils"

import { Card, CardHeader, CardContent, CardFooter } from "@/components/ui/card"
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"

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
  is_author?: boolean
  gallery_images?: string[]
}

interface TweetCardProps {
  post: TweetCardPost
  archiveUrl?: string
  className?: string
}

function buildTagHref(archiveUrl: string | undefined, slug?: string) {
  if (!slug) {
    return undefined
  }
  const { homeUrl } = getConfig()

  const base = archiveUrl || (typeof window !== "undefined" ? window.location.pathname : "/tweet/")
  const url = new URL(base, typeof window !== "undefined" ? window.location.origin : homeUrl)
  url.searchParams.set("t_tag", slug)
  return `${url.pathname}${url.search}`
}

export default function TweetCard({ post, archiveUrl }: TweetCardProps) {
  const { apiUrl, apiNonce } = getConfig();
  const [likeCount, setLikeCount] = React.useState<string | number>(post.likes ?? "");
  const [isLikeLoading, setIsLikeLoading] = React.useState(false);
  const [hasLiked, setHasLiked] = React.useState(false);

  const handleLike = async () => {
    if (isLikeLoading || hasLiked) {
      return;
    }

    setIsLikeLoading(true);

    try {
      const response = await fetch(`${apiUrl}/aiya/v1/post_like`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({ post_id: Number(post.id) }),
      });

      const data = await response.json();

      if (data.success && data.data.status === "done") {
        setLikeCount(data.data.count);
        setHasLiked(true);
      }
    } catch (error) {
      console.error("Like failed:", error);
    } finally {
      setIsLikeLoading(false);
    }
  };

  const renderContent = () => {
    if (!post.content) return null

    return (
      <div
        dangerouslySetInnerHTML={{ __html: post.content }}
        className="text-base leading-relaxed break-words prose prose-sm max-w-none"
      />
    )
  }

  // 渲染图片网格
  const renderImageGrid = () => {
    const images = post.gallery_images
    if (!images || images.length === 0) return null

    const len = images.length

    // 单图
    if (len === 1) {
      return (
        <div className="mt-3 rounded-xl overflow-hidden border bg-muted">
          <img
            src={images[0]}
            alt="tweet image"
            loading="lazy"
            className="w-full h-auto max-h-[600px] object-cover object-center"
          />
        </div>
      )
    }

    // 2图
    if (len === 2) {
      return (
        <div className="mt-3 grid grid-cols-2 gap-2 rounded-xl overflow-hidden">
          {images.map((image, i) => (
            <div key={i} className="aspect-square bg-muted border">
              <img src={image} alt={`tweet image ${i + 1}`} className="w-full h-full object-cover" loading="lazy" />
            </div>
          ))}
        </div>
      )
    }

    // 3图
    if (len === 3) {
      return (
        <div className="mt-3 grid grid-cols-2 gap-2 rounded-xl overflow-hidden">
          <div className="aspect-auto h-full bg-muted border">
            <img src={images[0]} alt="tweet image 1" className="w-full h-full object-cover" loading="lazy" />
          </div>
          <div className="grid grid-rows-2 gap-2">
            <div className="aspect-square bg-muted border">
              <img src={images[1]} alt="tweet image 2" className="w-full h-full object-cover" loading="lazy" />
            </div>
            <div className="aspect-square bg-muted border">
              <img src={images[2]} alt="tweet image 3" className="w-full h-full object-cover" loading="lazy" />
            </div>
          </div>
        </div>
      )
    }

    // 4图
    if (len === 4) {
      return (
        <div className="mt-3 grid grid-cols-2 gap-2 rounded-xl overflow-hidden">
          {images.map((image, i) => (
            <div key={i} className="aspect-square bg-muted border">
              <img src={image} alt={`tweet image ${i + 1}`} className="w-full h-full object-cover" loading="lazy" />
            </div>
          ))}
        </div>
      )
    }

    // 5图及以上 (9宫格模式，最多显示9张)
    const displayImages = images.slice(0, 9)
    return (
      <div className="mt-3 grid grid-cols-3 gap-2 rounded-xl overflow-hidden">
        {displayImages.map((image, i) => (
          <div key={i} className="aspect-square bg-muted border relative">
            <img src={image} alt={`tweet image ${i + 1}`} className="w-full h-full object-cover" loading="lazy" />
            {i === 8 && images.length > 9 && (
              <div className="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-medium text-xl">
                +{images.length - 9}
              </div>
            )}
          </div>
        ))}
      </div>
    )
  }

  return (
    <Card className="py-2 my-4 gap-0">
      <CardHeader className="p-4 pb-2 flex flex-row items-start justify-between space-y-0">
        <div className="flex items-center gap-3">
          <Avatar className="h-10 w-10 border">
            {post.author?.avatar ? (
              <AvatarImage src={post.author.avatar} alt={post.author.name || "avatar"} />
            ) : null}
            <AvatarFallback>{(post.author?.name || "").trim().charAt(0).toUpperCase()}</AvatarFallback>
          </Avatar>
          <div className="flex flex-col">
            <span className="font-semibold text-base leading-none">
              {post.author?.name}
            </span>
            {post.date && (
              <time dateTime={post.date_iso} className="text-sm text-muted-foreground mt-1">
                {post.date}
              </time>
            )}
          </div>
        </div>
        {post.is_author && (
          <Button
            variant="outline"
            size="xs"
            className="text-muted-foreground flex rounded-none hover:bg-muted/50"
            onClick={() => {
              if (post.url) {
                window.location.href = post.url + "?update=true"
              }
            }}
          >
            <MoreHorizontal className="h-3 w-3 mr-2" />
            <span>{__("编辑", "aiya-cms")}</span>
          </Button>
        )}
      </CardHeader>

      <CardContent className="p-4 pt-2">
        {post.title && (
          <h3 className="font-bold text-lg mb-2">
            {post.url ? (
              <a href={post.url} title={post.attr_title || post.title} className="hover:underline">
                {post.title}
              </a>
            ) : (
              post.title
            )}
          </h3>
        )}

        {renderContent()}

        {renderImageGrid()}

        {post.tags && post.tags.length > 0 && (
          <div className="flex flex-wrap gap-2 mt-4">
            {post.tags.map((tag) => {
              const href = buildTagHref(archiveUrl, tag.slug)

              return href ? (
                <a key={`${tag.slug || tag.name}`} href={href}>
                  <Badge variant="secondary" className="hover:bg-secondary/80 font-normal rounded-full">
                    #{tag.name}
                  </Badge>
                </a>
              ) : (
                <Badge key={`${tag.slug || tag.name}`} variant="secondary" className="font-normal rounded-full">
                  #{tag.name}
                </Badge>
              )
            })}
          </div>
        )}
      </CardContent>
      <CardFooter className="p-4 py-0 flex items-center justify-start mt-2">
        <Button
          variant="ghost"
          size="sm"
          className="text-muted-foreground flex rounded-none hover:bg-muted/50"
          onClick={handleLike}
          disabled={isLikeLoading || hasLiked}
        >
          <Heart className={`w-4 h-4 mr-2 ${hasLiked ? "fill-current text-red-500" : ""}`} />
          <span>{likeCount || "0"}</span>
        </Button>
        <Button
          variant="ghost"
          size="sm"
          className="text-muted-foreground flex rounded-none hover:bg-muted/50"
          onClick={() => {
            if (post.url) {
              window.location.href = post.url + "#comments"
            }
          }}
        >
          <MessageCircle className="w-4 h-4 mr-2" />
          <span>{post.comments || "0"}</span>
        </Button>
      </CardFooter>
    </Card>
  )
}
