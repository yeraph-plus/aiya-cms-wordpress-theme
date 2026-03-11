import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent } from "@/components/ui/card"

export default function WidgetSearchSkeleton() {
  return (
    <Card className="border-0 shadow-none bg-transparent">
      <CardContent className="p-0">
        <Skeleton className="h-10 w-full rounded-lg" />
      </CardContent>
    </Card>
  )
}
