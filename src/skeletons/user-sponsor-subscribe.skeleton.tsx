import { Skeleton } from "@/components/ui/skeleton"
import { Card, CardHeader, CardTitle, CardFooter } from "@/components/ui/card"

export default function UserSponsorSubscribeSkeleton() {
  return (
    <>
      <div className="flex items-center gap-2 pl-2 my-4">
        <Skeleton className="w-6 h-6 rounded-full" />
        <Skeleton className="h-7 w-32" />
      </div>

      <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
        {Array.from({ length: 5 }).map((_, i) => (
          <Card
            key={i}
            className="flex flex-col h-full border-t-4 border-l-4"
          >
            <CardHeader>
              <CardTitle className="w-full flex flex-col items-start gap-1">
                <Skeleton className="h-6 w-24" />
              </CardTitle>
              <div className="space-y-2 mt-2">
                <Skeleton className="h-8 w-20" />
                <Skeleton className="h-4 w-full" />
              </div>
            </CardHeader>
            <CardFooter className="mt-auto">
              <Skeleton className="h-10 w-full" />
            </CardFooter>
          </Card>
        ))}
      </div>
    </>
  )
}
