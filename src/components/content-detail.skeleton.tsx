import { Skeleton } from "@/components/ui/skeleton";

export function ContentDetailSkeleton() {
  return (
    <div className="relative mb-8 animate-in fade-in duration-500">
      {/* Thumbnail */}
      <div className="relative w-full h-60 md:h-72 lg:h-96 rounded-lg overflow-hidden mb-6">
        <Skeleton className="w-full h-full" />
      </div>

      {/* Header */}
      <div className="mb-6 space-y-4">
        <Skeleton className="h-10 w-3/4" />
        <div className="flex items-center gap-4">
          <Skeleton className="h-5 w-24" />
          <Skeleton className="h-5 w-24" />
          <Skeleton className="h-5 w-24" />
        </div>
      </div>

      {/* Content Body */}
      <div className="space-y-4 mb-8">
        <Skeleton className="h-6 w-full" />
        <Skeleton className="h-6 w-full" />
        <Skeleton className="h-6 w-11/12" />
        <Skeleton className="h-6 w-full" />
        <Skeleton className="h-6 w-4/5" />
      </div>

      {/* Tags */}
      <div className="flex flex-wrap gap-2 mb-8">
        <Skeleton className="h-8 w-16 rounded-full" />
        <Skeleton className="h-8 w-20 rounded-full" />
        <Skeleton className="h-8 w-14 rounded-full" />
      </div>
    </div>
  );
}

export default ContentDetailSkeleton;
