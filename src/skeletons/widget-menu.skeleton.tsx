import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export default function WidgetMenuSkeleton() {
  return (
    <Card className="border-0 shadow-none bg-transparent">
      <CardHeader className="px-0 pb-0">
        <CardTitle className="text-lg font-bold flex items-center gap-2">
          <Skeleton className="w-5 h-5 rounded-full" />
          <Skeleton className="h-6 w-32" />
        </CardTitle>
      </CardHeader>
      <CardContent className="p-2 grid gap-1 mt-4">
        {Array.from({ length: 5 }).map((_, i) => (
          <Skeleton key={i} className="h-9 w-full rounded-md" />
        ))}
      </CardContent>
    </Card>
  )
}
