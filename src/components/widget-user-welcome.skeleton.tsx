import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent } from "@/components/ui/card"

export default function WidgetUserWelcomeSkeleton() {
  return (
    <Card className="border-0 shadow-none bg-transparent">
      <CardContent className="px-0 pb-0 space-y-4">
        <div className="space-y-2">
          <div className="flex items-center justify-start gap-3">
            <Skeleton className="w-10 h-10 rounded-full" />
            <Skeleton className="h-6 w-24" />
          </div>
          <Skeleton className="h-4 w-full" />
        </div>

        <div className="grid grid-cols-2 gap-3">
          <Skeleton className="h-10 w-full" />
          <Skeleton className="h-10 w-full" />
        </div>
      </CardContent>
    </Card>
  )
}
