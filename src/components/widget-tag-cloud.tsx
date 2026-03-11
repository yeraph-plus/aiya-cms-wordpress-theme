import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Tags } from "lucide-react"

export interface TagItem {
    id: number
    name: string
    url: string
    count: number
}

interface WidgetTagCloudProps {
    tags: TagItem[]
    widgetTitle?: string
    className?: string
}

export default function WidgetTagCloud({ tags, widgetTitle, className }: WidgetTagCloudProps) {
    if (!tags || tags.length === 0) return null

    // Function to generate random variants for badges
    // We use the tag id to ensure consistency across renders for the same tag
    const getColorClass = (id: number) => {
        const colors = [
            "bg-red-500 hover:bg-red-600",
            "bg-orange-500 hover:bg-orange-600",
            "bg-amber-500 hover:bg-amber-600",
            "bg-yellow-500 hover:bg-yellow-600",
            "bg-lime-500 hover:bg-lime-600",
            "bg-green-500 hover:bg-green-600",
            "bg-emerald-500 hover:bg-emerald-600",
            "bg-teal-500 hover:bg-teal-600",
            "bg-cyan-500 hover:bg-cyan-600",
            "bg-sky-500 hover:bg-sky-600",
            "bg-blue-500 hover:bg-blue-600",
            "bg-indigo-500 hover:bg-indigo-600",
            "bg-violet-500 hover:bg-violet-600",
            "bg-purple-500 hover:bg-purple-600",
            "bg-fuchsia-500 hover:bg-fuchsia-600",
            "bg-pink-500 hover:bg-pink-600",
            "bg-rose-500 hover:bg-rose-600",
        ]
        return colors[id % colors.length]
    }

    return (
        <Card className={`border-0 shadow-none bg-transparent ${className || ''}`}>
            {widgetTitle && (
                <CardHeader className="px-0 pt-0 pb-4">
                    <CardTitle className="text-lg font-bold flex items-center gap-2">
                        <Tags className="w-5 h-5 text-primary" />
                        {widgetTitle}
                    </CardTitle>
                </CardHeader>
            )}
            <CardContent className="px-0 pb-0 flex flex-wrap gap-2">
                {tags.map((tag) => (
                    <a
                        key={tag.id}
                        href={tag.url}
                        title={`浏览和 ${tag.name} 有关的文章 (${tag.count})`}
                        className="no-underline"
                    >
                        <Badge
                            className={`px-3 py-1 text-sm font-medium hover:scale-105 transition-transform cursor-pointer text-white border-0 shadow-sm transition-all hover:shadow-md ${getColorClass(tag.id)}`}
                        >
                            # {tag.name}
                        </Badge>
                    </a>
                ))}
            </CardContent>
        </Card>
    )
}
