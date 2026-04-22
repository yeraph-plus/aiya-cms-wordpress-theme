import { __ } from '@wordpress/i18n';

import * as React from "react"
import { MasonryGrid } from "@/components/ui/masonry-grid"
import {
  MessageSquare,
  Heart,
  Inbox
} from "lucide-react"
import { cn, getConfig } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardHeader,
  CardFooter,
} from "@/components/ui/card"
import {
  Empty,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
  EmptyDescription
} from "@/components/ui/empty"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { initViewer } from "@/lib/viewer-plugin"

export interface PostAuthor {
  name: string
  avatar: string
}

export interface Post {
  id: number
  url: string
  title: string
  attr_title: string
  content: string
  date: string
  date_iso: string
  likes: string
  comments: string
  author: PostAuthor
}

interface TweetGridProps {
  posts: Post[]
  loopTitle?: string
  className?: string
}

function TweetCard({ post }: { post: Post }) {
  const [likes, setLikes] = React.useState(parseInt(post.likes) || 0)
  const [hasLiked, setHasLiked] = React.useState(false)
  const [isLoading, setIsLoading] = React.useState(false)
  const gridRef = React.useRef<HTMLDivElement>(null)

  // 解析内容提取图片
  const { content: processedContent, images } = React.useMemo(() => {
    if (typeof window === 'undefined') return { content: post.content, images: [] }

    try {
      const parser = new DOMParser()
      const doc = parser.parseFromString(post.content, 'text/html')
      const imgs = Array.from(doc.querySelectorAll('img'))

      const imagesData = imgs.map(img => ({
        src: img.getAttribute('src') || '',
        alt: img.getAttribute('alt') || ''
      }))

      // 移除图片及其可能存在的空父级P标签
      imgs.forEach(img => {
        const parent = img.parentElement
        img.remove()
        if (parent && parent.tagName === 'P' && parent.innerHTML.trim() === '') {
          parent.remove()
        }
      })

      return { content: doc.body.innerHTML, images: imagesData }
    } catch (e) {
      console.error("Failed to parse tweet content", e)
      return { content: post.content, images: [] }
    }
  }, [post.content])

  // 初始化ViewerJS
  React.useEffect(() => {
    if (gridRef.current && images.length > 0) {
      const viewer = initViewer({ container: gridRef.current, force: true })
      return () => {
        viewer?.destroy()
      }
    }
  }, [images])

  // 计算Grid布局类名
  const gridClassName = React.useMemo(() => {
    const count = images.length
    if (count === 1) return "grid-cols-1"
    if (count === 2 || count === 4) return "grid-cols-2"
    return "grid-cols-3"
  }, [images.length])

  const handleLike = async () => {
    if (isLoading || hasLiked) return

    setIsLoading(true)
    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/post_like`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': apiNonce || '',
        },
        body: JSON.stringify({ post_id: post.id }),
      })

      if (response.ok) {
        const data = await response.json()
        console.log(data)
        if (data.status === 'done') {
          setLikes(data.count)
          setHasLiked(true)
        }
      }
    } catch (error) {
      console.error('Like failed:', error)
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <Card className="overflow-hidden hover:shadow-md transition-shadow border-border/50">
      <CardHeader className="flex flex-row items-start justify-between space-y-0 px-4">
        <div className="flex items-center gap-3">
          <Avatar className="w-10 h-10 border">
            <AvatarImage src={post.author.avatar} alt={post.author.name} />
            <AvatarFallback>{post.author.name[0]}</AvatarFallback>
          </Avatar>
          <div className="flex flex-col">
            <span className="font-semibold text-sm leading-none mb-1">{post.author.name}</span>
            <time className="text-xs text-muted-foreground leading-none" dateTime={post.date_iso}>
              {post.date}
            </time>
          </div>
        </div>
        <div className="flex items-center gap-1 text-muted-foreground">
          <Button variant="ghost" size="icon" className="h-8 w-8 hover:text-primary" asChild>
            <a href={`${post.url}#comments`}>
              <MessageSquare className="w-4 h-4" />
              <span className="sr-only">{__('查看评论', 'aiya-cms')}</span>
            </a>
          </Button>
          <span className="text-xs mr-2">{post.comments}</span>

          <Button
            variant="ghost"
            size="icon"
            className={cn("h-8 w-8 hover:text-red-500", hasLiked && "text-red-500")}
            onClick={handleLike}
            disabled={isLoading || hasLiked}
          >
            <Heart className={cn("w-4 h-4", hasLiked && "fill-current")} />
            <span className="sr-only">{__('点赞', 'aiya-cms')}</span>
          </Button>
          <span className="text-xs">{likes}</span>
        </div>
      </CardHeader>
      <CardContent className="p-4">
        <h3 className="text-lg font-bold mb-4 leading-tight">
          <a href={post.url} className="hover:text-primary transition-colors" title={post.attr_title}>
            {post.title}
          </a>
        </h3>
        <div
          className="prose prose-sm dark:prose-invert max-w-none break-words"
          dangerouslySetInnerHTML={{ __html: processedContent }}
        />
      </CardContent>
      {images.length > 0 && (
        <CardFooter className="p-4 pt-0 block">
          <div ref={gridRef} className={cn("grid gap-2 w-full", gridClassName)}>
            {images.map((img, i) => (
              <div key={i} className={cn(
                "relative overflow-hidden rounded-md bg-muted/30 group",
                images.length === 1 ? "aspect-auto max-h-[400px]" : "aspect-square"
              )}>
                <img
                  src={img.src}
                  alt={img.alt}
                  className={cn(
                    "w-full h-full object-cover transition-transform duration-300 cursor-zoom-in group-hover:scale-105",
                    images.length === 1 && "object-contain"
                  )}
                  loading="lazy"
                />
              </div>
            ))}
          </div>
        </CardFooter>
      )}
    </Card>
  )
}

export default function LoopTweet({ posts, loopTitle, className }: TweetGridProps) {
  if (!posts || posts.length === 0) {
    return (
      <div className={cn("my-4 space-y-6", className)}>
        <Empty className="mx-auto my-8 border-dashed ">
          <EmptyHeader>
            <EmptyMedia variant="icon"><Inbox className="h-8 w-8" /></EmptyMedia>
            <EmptyTitle>{__('暂无内容', 'aiya-cms')}</EmptyTitle>
            <EmptyDescription>
              {__('当前没有任何用户发表过推文', 'aiya-cms')}
            </EmptyDescription>
          </EmptyHeader>
        </Empty>
      </div>
    )
  }

  return (
    <div className={cn("w-full my-4 space-y-6", className)}>
      {loopTitle && <h2 className="text-xl font-bold mb-6 px-1">{loopTitle}</h2>}
      <MasonryGrid
        items={posts}
        columns={{
          default: 1,
          sm: 2,
          md: 3,
        }}
        gap={24}
        render={(post: Post) => <TweetCard key={post.id} post={post} />}
      />
    </div>
  )
}
