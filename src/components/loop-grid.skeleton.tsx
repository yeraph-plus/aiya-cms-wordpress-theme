import { Skeleton } from "@/components/ui/skeleton";

export default function LoopGridSkeleton() {
  return (
    <div className="my-4 space-y-6">
      <Skeleton className="h-2 w-full my-6" /> {/* Separator */}

      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <Skeleton className="w-6 h-6 rounded-md" />
          <Skeleton className="h-7 w-24" />
        </div>
        <div className="hidden md:flex gap-2">
          <Skeleton className="h-8 w-8 rounded-md" />
          <Skeleton className="h-8 w-8 rounded-md" />
        </div>
      </div>

      <div className="grid gap-2 md:gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
        {[1, 2, 3, 4, 5, 6, 7, 8].map((i) => (
          <div key={i} className="flex flex-col rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden h-full">
            <div className="aspect-video w-full overflow-hidden bg-muted relative">
              <Skeleton className="w-full h-full" />
              <Skeleton className="absolute top-2 left-2 h-5 w-16" /> {/* Badge */}
            </div>
            <div className="flex flex-col space-y-3 p-4">
              <div className="flex items-center gap-2">
                <Skeleton className="h-3 w-16" />
                <Skeleton className="h-3 w-16" />
              </div>
              <Skeleton className="h-5 w-full" />
              <Skeleton className="h-5 w-2/3" />
              <div className="mt-2 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Skeleton className="h-6 w-6 rounded-full" />
                  <Skeleton className="h-3 w-12" />
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
