import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";

interface ContentAdSpaceSkeletonProps {
  className?: string;
}

export default function ContentAdSpaceSkeleton({ className }: ContentAdSpaceSkeletonProps) {
  return (
    <div className={cn("container mx-auto my-4", className)}>
      <div className="block relative aspect-[3/1] w-full rounded-lg border border-border bg-muted/30 overflow-hidden">
        <Skeleton className="w-full h-full" />
      </div>
    </div>
  );
}
