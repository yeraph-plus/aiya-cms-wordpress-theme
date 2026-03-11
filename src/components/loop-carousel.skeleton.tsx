import { Skeleton } from "@/components/ui/skeleton";

export default function LoopCarouselSkeleton() {
  return (
    <div className="w-full relative my-4">
      <div className="relative aspect-[21/9] w-full overflow-hidden rounded-lg">
        <Skeleton className="w-full h-full" />
        <div className="absolute inset-0 flex flex-col justify-end p-6 md:p-10 space-y-4">
          <Skeleton className="h-8 w-1/2 bg-white/20" />
          <Skeleton className="h-4 w-3/4 bg-white/20" />
        </div>
      </div>
    </div>
  );
}
