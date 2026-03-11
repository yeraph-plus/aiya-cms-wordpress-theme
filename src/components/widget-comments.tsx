import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { MessageSquare, ExternalLink } from "lucide-react"

export interface CommentItem {
  id: number
  author: string
  avatar: string
  content: string
  date: string
  url: string
  post_title: string
}

interface WidgetCommentsProps {
  comments: CommentItem[]
  widgetTitle?: string
  className?: string
}

export default function WidgetComments({ comments, widgetTitle, className }: WidgetCommentsProps) {
  if (!comments || comments.length === 0) return null

  return (
    <Card className={`border-0 shadow-none bg-transparent ${className || ''}`}>
      {widgetTitle && (
        <CardHeader className="px-0 pt-0 pb-4">
          <CardTitle className="text-lg font-bold flex items-center gap-2">
            <MessageSquare className="w-5 h-5 text-primary" />
            {widgetTitle}
          </CardTitle>
        </CardHeader>
      )}
      <CardContent className="px-0 pb-0 grid gap-4">
        {comments.map((comment) => (
          <div key={comment.id} className="group">
            <div className="flex gap-3 items-start">
              <Avatar className="w-10 h-10 border shrink-0 shadow-sm transition-all hover:shadow-md">
                <AvatarImage src={comment.avatar} alt={comment.author} />
                <AvatarFallback>{comment.author.slice(0, 2).toUpperCase()}</AvatarFallback>
              </Avatar>

              <div className="flex-1 min-w-0 space-y-1">
                <div className="flex items-center justify-between gap-2 mb-1">
                  <span className="text-sm font-bold truncate max-w-[160px]">
                    {comment.author}
                  </span>
                  <span className="text-xs text-muted-foreground whitespace-nowrap shrink-0">
                    {comment.date}
                  </span>
                </div>

                <div className="relative p-3 bg-muted/50 rounded-lg rounded-tl-none border shadow-sm transition-all hover:shadow-md group-hover:bg-muted">
                  <div
                    className="text-sm text-muted-foreground line-clamp-4 break-all relative"
                    dangerouslySetInnerHTML={{ __html: comment.content }}
                  />
                </div>

                <a
                  href={comment.url}
                  className="inline-flex items-center gap-1 text-xs text-primary/80 hover:text-primary transition-colors mt-1 group/link"
                >
                  <ExternalLink className="w-3 h-3" />
                  <span className="truncate max-w-[200px]">
                    {comment.post_title}
                  </span>
                </a>
              </div>
            </div>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}
