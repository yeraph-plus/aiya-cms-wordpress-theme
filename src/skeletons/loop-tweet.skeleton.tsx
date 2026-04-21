import { Skeleton } from "@/components/ui/skeleton";

export default function LoopTweetSkeleton() {
  return (
    <div className="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4 mx-auto">
       {[1, 2, 3, 4, 5, 6].map((i) => (
          <div key={i} className="break-inside-avoid mb-4 rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden p-4 space-y-4">
              <div className="flex items-center gap-3">
                  <Skeleton className="h-10 w-10 rounded-full" />
                  <div className="space-y-1">
                      <Skeleton className="h-4 w-24" />
                      <Skeleton className="h-3 w-16" />
                  </div>
              </div>
              <div className="space-y-2">
                  <Skeleton className="h-4 w-full" />
                  <Skeleton className="h-4 w-full" />
                  <Skeleton className="h-4 w-3/4" />
              </div>
              <Skeleton className="w-full aspect-square rounded-md" />
              <div className="flex justify-between pt-2">
                  <Skeleton className="h-8 w-16" />
                  <Skeleton className="h-8 w-16" />
                  <Skeleton className="h-8 w-16" />
              </div>
          </div>
       ))}
    </div>
  );
}
