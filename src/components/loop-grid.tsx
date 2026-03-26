"use client"

import * as React from "react"
import {
  LayoutGrid,
  LayoutList,
  MessageSquare,
  Heart,
  Pin,
  Sparkles,
  Lock,
  EyeOff,
  Hourglass,
  CalendarClock,
  FileEdit,
  CircleDashed,
  Trash2,
  CheckCircle,
  Link,
  Inbox,
  Home,
  Archive,
  LibraryBig,
  Search
} from "lucide-react"
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from "@/components/ui/tooltip"
import {
  Card,
  CardFooter,
  CardTitle,
} from "@/components/ui/card"
import {
  Empty,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
  EmptyDescription
} from "@/components/ui/empty"
import { Separator } from "@/components/ui/separator"

export interface PostAuthor {
  name: string
  avatar: string
}

export interface Category {
  id: number
  name: string
  url: string
}

export interface Post {
  id: number
  url: string
  title: string
  attr_title: string
  thumbnail: string
  preview: string
  date: string
  date_iso: string
  views: string
  comments: string
  likes: string
  status: 'sticky' | 'newest' | 'password' | 'private' | 'pending' | 'future' | 'draft' | 'auto-draft' | 'inherit' | 'trash' | 'publish';
  cat_list: Category[]
  author: PostAuthor
}

interface LoopGridProps {
  posts: Post[]
  loopTitle?: string
  className?: string
  showSeparator?: boolean
  pageType?: 'index' | 'archive' | 'author' | 'search' | ''
}

type LayoutType = 'grid' | 'list'

export default function LoopGrid({ posts, loopTitle = "文章列表", className, showSeparator = true, pageType = '' }: LoopGridProps) {
  const [layout, setLayout] = React.useState<LayoutType>('grid')

  React.useEffect(() => {
    // Restore preference from local storage if needed
    const saved = localStorage.getItem("loop-grid-layout")
    if (saved === 'grid' || saved === 'list') {
      setLayout(saved)
    }
  }, [])

  const handleLayoutChange = (newLayout: LayoutType) => {
    setLayout(newLayout)
    localStorage.setItem("loop-grid-layout", newLayout)
  }

  const renderTitleIcon = () => {
    switch (pageType) {
      case 'index':
        return <Home className="w-6 h-6 text-primary" />
      case 'archive':
        return <Archive className="w-6 h-6 text-primary" />
      case 'author':
        return <LibraryBig className="w-6 h-6 text-primary" />
      case 'search':
        return <Search className="w-6 h-6 text-primary" />
      default:
        return <span className="w-2 h-5 bg-primary rounded-full" />
    }
  }

  // Handle empty state
  if (!posts || posts.length === 0) {
    return (
      <div className={cn("my-4 space-y-6", className)}>
        <Empty className="mx-auto my-8 border-dashed ">
          <EmptyHeader>
            <EmptyMedia variant="icon"><Inbox className="h-8 w-8" /></EmptyMedia>
            <EmptyTitle>暂无内容</EmptyTitle>
            <EmptyDescription>
              当前列表没有任何文章可显示
            </EmptyDescription>
          </EmptyHeader>
        </Empty>
      </div>
    )
  }

  const gridClass = cn(
    "grid gap-2 md:gap-4 transition-all duration-300 ease-in-out",
    layout === 'grid'
      ? "grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5"
      : "grid-cols-1"
  )

  return (
    <div className={cn("my-4 space-y-6", className)}>
      {showSeparator && <Separator className="h-2 my-6 shadow-sm transition-all hover:shadow-md" />}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          {renderTitleIcon()}
          <h2 className="text-xl font-bold tracking-tight">{loopTitle}</h2>
        </div>
        <div className="hidden md:flex items-center gap-2 bg-muted/50 p-1 rounded-lg border">
          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                variant={layout === 'grid' ? "secondary" : "ghost"}
                size="sm"
                className="h-8 w-8 px-0 font-medium"
                onClick={() => handleLayoutChange('grid')}
              >
                <LayoutGrid className="h-4 w-4" />
                <span className="sr-only">网格视图</span>
              </Button>
            </TooltipTrigger>
            <TooltipContent>网格视图</TooltipContent>
          </Tooltip>

          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                variant={layout === 'list' ? "secondary" : "ghost"}
                size="sm"
                className="h-8 w-8 px-0 font-medium"
                onClick={() => handleLayoutChange('list')}
              >
                <LayoutList className="h-4 w-4" />
                <span className="sr-only">列表视图</span>
              </Button>
            </TooltipTrigger>
            <TooltipContent>列表视图</TooltipContent>
          </Tooltip>
        </div>
      </div>

      <div className={gridClass}>
        {posts.map((post) => (
          <PostCard key={post.id} post={post} layout={layout} />
        ))}
      </div>
    </div>
  )
}

function StatusBadges({ status }: { status: Post['status'] }) {
  if (!status) return null

  return (
    <div className="absolute top-2 left-2 flex flex-col gap-1 z-10 pointer-events-none">
      {Object.entries(status).map(([key, label]) => {
        let variant: "default" | "secondary" | "destructive" | "outline" = "outline"
        let Icon = Link

        switch (key) {
          case 'sticky':
            variant = "default"
            Icon = Pin
            break
          case 'newest':
            variant = "secondary"
            Icon = Sparkles
            break
          case 'password':
            variant = "secondary"
            Icon = Lock
            break
          case 'private':
            variant = "destructive"
            Icon = EyeOff
            break
          case 'pending':
            variant = "secondary"
            Icon = Hourglass
            break
          case 'future':
            variant = "outline"
            Icon = CalendarClock
            break
          case 'draft':
            variant = "outline"
            Icon = FileEdit
            break
          case 'auto-draft':
            variant = "outline"
            Icon = CircleDashed
            break
          case 'trash':
            variant = "destructive"
            Icon = Trash2
            break
          default:
            variant = "outline"
            Icon = CheckCircle
        }

        const outlineStyle = variant === 'outline'
          ? "bg-background/80 hover:bg-background/90 text-foreground border-transparent"
          : "opacity-90";

        return (
          <Badge
            key={key}
            variant={variant}
            className={cn(
              "shadow-sm backdrop-blur-sm gap-1 pl-1.5 text-xs",
              outlineStyle
            )}
          >
            <Icon className="h-5 w-5" />
            <span>{label}</span>
          </Badge>
        )
      })}
    </div>
  )
}

function PostCard({ post, layout = 'grid' }: { post: Post, layout?: LayoutType }) {
  if (layout === 'list') {
    return (
      <Card className="group relative flex flex-row shadow-sm transition-all hover:shadow-md overflow-hidden h-48 py-0 gap-0">
        <div className="relative w-1/3 min-w-[200px] max-w-[300px] h-full bg-muted overflow-hidden shrink-0">
          <StatusBadges status={post.status} />
          {post.thumbnail ? (
            <img
              src={post.thumbnail}
              alt={post.attr_title}
              className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
              loading="lazy"
            />
          ) : (
            <div className="flex h-full w-full items-center justify-center text-muted-foreground">
              No Image
            </div>
          )}
        </div>

        <div className="flex flex-1 flex-col min-w-0 p-4">
          <div className="flex items-center overflow-hidden whitespace-nowrap gap-2 text-xs text-muted-foreground mb-2">
            <span>{post.date}</span>
            <span>•</span>
            <span>{post.views} 阅读</span>
          </div>

          <CardTitle className="line-clamp-2 text-lg mb-2">
            <a href={post.url} className="hover:underline focus:outline-none" title={post.attr_title}>
              <span className="absolute inset-0" aria-hidden="true" />
              {post.title}
            </a>
          </CardTitle>

          {post.cat_list && post.cat_list.length > 0 && (
            <div className="hidden md:flex gap-1 mb-4 overflow-hidden whitespace-nowrap">
              {post.cat_list.map((cat) => (
                <a key={cat.id} href={cat.url} className="no-underline flex-shrink-0">
                  <Badge variant="secondary" className="hover:bg-secondary/80 text-xs px-1.5 py-0 h-5">
                    {cat.name}
                  </Badge>
                </a>
              ))}
            </div>
          )}

          <div
            className="hidden md:line-clamp-2 text-sm text-muted-foreground flex-1"
            dangerouslySetInnerHTML={{ __html: post.preview }}
          />

          <CardFooter className="mt-auto flex items-center justify-between p-0 pt-2">
            <div className="flex items-center gap-2 ">
              <img
                src={post.author.avatar}
                alt={post.author.name}
                className="h-6 w-6 rounded-full"
              />
              <span className="hidden md:block text-xs font-medium">{post.author.name}</span>
            </div>

            <div className="flex gap-2 text-xs text-muted-foreground">
              <span className="flex items-center gap-1">
                <MessageSquare className="h-3 w-3" />
                {post.comments}
              </span>
              <span className="flex items-center gap-1">
                <Heart className="h-3 w-3" />
                {post.likes}
              </span>
            </div>
          </CardFooter>
        </div>
      </Card>
    )
  }

  return (
    <Card className="group relative flex flex-col shadow-sm transition-all hover:shadow-md overflow-hidden py-0 gap-0 border-0 ring-1 ring-border">
      <div className="relative aspect-video w-full bg-muted overflow-hidden rounded-t-xl">
        <StatusBadges status={post.status} />
        {post.thumbnail ? (
          <img
            src={post.thumbnail}
            alt={post.attr_title}
            className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            loading="lazy"
          />
        ) : (
          <div className="flex h-full w-full items-center justify-center text-muted-foreground">
            No Image
          </div>
        )}
      </div>

      <div className="flex flex-1 flex-col p-4">
        <div className="flex items-center overflow-hidden whitespace-nowrap gap-2 text-xs text-muted-foreground mb-2">
          <span>{post.date}</span>
          <span>•</span>
          <span>{post.views} 阅读</span>
        </div>

        {post.cat_list && post.cat_list.length > 0 && (
          <div className="flex gap-1 mb-2 overflow-hidden whitespace-nowrap hidden md:block">
            {post.cat_list.map((cat) => (
              <a key={cat.id} href={cat.url} className="no-underline flex-shrink-0">
                <Badge variant="secondary" className="hover:bg-secondary/80 text-xs px-1.5 py-0 h-5">
                  {cat.name}
                </Badge>
              </a>
            ))}
          </div>
        )}

        <CardTitle className="line-clamp-2 text-md md:text-lg mb-2">
          <a href={post.url} className="hover:underline focus:outline-none" title={post.attr_title}>
            <span className="absolute inset-0" aria-hidden="true" />
            {post.title}
          </a>
        </CardTitle>

        <div
          className="hidden md:line-clamp-2 text-sm text-muted-foreground "
          dangerouslySetInnerHTML={{ __html: post.preview }}
        />

        <CardFooter className="mt-auto flex items-center justify-between p-0 pt-4">
          <div className="flex items-center gap-2">
            <img
              src={post.author.avatar}
              alt={post.author.name}
              className="h-6 w-6 rounded-full"
            />
            <span className="hidden md:block text-xs font-medium">{post.author.name}</span>
          </div>

          <div className="flex gap-2 text-xs text-muted-foreground">
            <span className="flex items-center gap-1">
              <MessageSquare className="h-3 w-3" />
              {post.comments}
            </span>
            <span className="flex items-center gap-1">
              <Heart className="h-3 w-3" />
              {post.likes}
            </span>
          </div>
        </CardFooter>
      </div>
    </Card>
  )
}
