import { Skeleton } from "@/components/ui/skeleton";

export default function LoopPaginationSkeleton() {
  return (
    <div className="my-4 flex flex-col items-center gap-4">
      <div className="flex items-center gap-1">
        <Skeleton className="h-9 w-20" /> {/* Prev */}
        {[1, 2, 3, 4, 5].map((i) => (
            <Skeleton key={i} className="h-9 w-9" />
        ))}
        <Skeleton className="h-9 w-20" /> {/* Next */}
      </div>
      <Skeleton className="h-4 w-32" />
    </div>
  );
}
