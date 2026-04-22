import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Eye, MessageSquare, Heart, Clock, Shuffle, Flame, Rss, Inbox } from "lucide-react"
import { Empty, EmptyHeader, EmptyTitle, EmptyMedia } from "@/components/ui/empty"

interface WidgetPostItem {
  id: number
  url: string
  title: string
  attr_title: string
  thumbnail: string
  date: string
  date_ago: string
  views: string
  comments: string
  likes: string
}

export type WidgetPostType = 'default' | 'newest' | 'random' | 'comments' | 'likes' | 'views';

interface WidgetPostProps {
  posts: WidgetPostItem[]
  widgetTitle?: string
  className?: string
  postType?: WidgetPostType
}

export default function WidgetPost({ posts, widgetTitle, className, postType = 'default' }: WidgetPostProps) {
  const hasPosts = posts && posts.length > 0;

  const renderTitleIcon = () => {
    switch (postType) {
      case 'newest':
        return <Rss className="w-5 h-5 text-primary" />
      case 'random':
        return <Shuffle className="w-5 h-5 text-primary" />
      case 'comments':
        return <MessageSquare className="w-5 h-5 text-primary" />
      case 'likes':
        return <Heart className="w-5 h-5 text-red-500" />
      case 'views':
        return <Flame className="w-5 h-5 text-orange-500" />
      default:
        return <span className="w-1 h-5 bg-primary rounded-full" />
    }
  }

  const renderMeta = (post: WidgetPostItem) => {
    switch (postType) {
      case 'comments':
        return (
          <>
            <span className="flex items-center gap-1">
              <MessageSquare className="w-3 h-3" />
              {post.comments}
              <span>•</span>
              {post.date_ago}
            </span>
          </>
        )
      case 'likes':
        return (
          <>
            <span className="flex items-center gap-1">
              <Heart className="w-3 h-3" />
              {post.likes}
            </span>
          </>
        )
      case 'views':
        return (
          <>
            <span className="flex items-center gap-1 text-orange-500 font-medium">
              <Eye className="w-3 h-3" />
              {post.views}
            </span>
          </>
        )
      default:
        return (
          <>
            <span className="flex items-center gap-1 text-muted-foreground">
              <Eye className="w-3 h-3" />
              {post.views}
              <Clock className="w-3 h-3" />
              {post.date}
            </span>
          </>
        )
    }
  }

  return (
    <Card className={`border-0 shadow-none bg-transparent ${className || ''}`}>
      {widgetTitle && (
        <CardHeader className="px-0 pb-0">
          <CardTitle className="text-lg font-bold flex items-center gap-2">
            {renderTitleIcon()}
            {widgetTitle}
          </CardTitle>
        </CardHeader>
      )}
      <CardContent className="px-0 pb-0 grid gap-4">
        {!hasPosts ? (
          <Empty className="p-4 border border-dashed rounded-lg">
            <EmptyMedia variant="icon" className="size-10 bg-muted/50 mb-2">
              <Inbox className="size-5 text-muted-foreground" />
            </EmptyMedia>
            <EmptyHeader>
              <EmptyTitle className="text-sm text-muted-foreground font-normal">暂无相关内容</EmptyTitle>
            </EmptyHeader>
          </Empty>
        ) : (
          posts.map((post) => (
          <div key={post.id} className="group">
            <a
              href={post.url}
              title={post.attr_title}
              className="flex gap-3 items-start group-hover:opacity-80 transition-opacity"
            >
              <div className="relative w-24 h-16 flex-shrink-0 overflow-hidden rounded-lg bg-muted shadow-sm transition-all hover:shadow-md ">
                {post.thumbnail ? (
                  <img
                    src={post.thumbnail}
                    alt={post.attr_title}
                    className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    loading="lazy"
                  />
                ) : (
                  <div className="w-full h-full flex items-center justify-center text-muted-foreground text-xs">
                    No Image
                  </div>
                )}
              </div>
              <div className="flex-1 min-w-0 flex flex-col justify-between gap-2 mt-2">
                <h3 className="text-base font-bold leading-tight line-clamp-1 overflow-hidden break-all group-hover:text-primary transition-colors">
                  {post.title}
                </h3>
                <div className="flex items-center gap-3 text-xs text-muted-foreground overflow-hidden">
                  {renderMeta(post)}
                </div>
              </div>
            </a>
          </div>
        ))
      )}
      </CardContent>
    </Card>
  )
}
