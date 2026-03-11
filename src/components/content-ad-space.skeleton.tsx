import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";

interface ContentAdSpaceSkeletonProps {
  className?: string;
  col?: number;
}

export default function ContentAdSpaceSkeleton({ className, col = 2 }: ContentAdSpaceSkeletonProps) {
  const column = {
    1: '',
    2: 'md:grid-cols-2',
  };

  return (
    <div className={cn(`container mx-auto my-4 grid grid-cols-1 ${column[col as keyof typeof column]} gap-2`, className)}>
      {[1, 2].map((i) => (
        <div key={i} className="block relative aspect-[3/1] rounded-lg border border-border bg-muted/30 overflow-hidden">
          <Skeleton className="w-full h-full" />
        </div>
      ))}
    </div>
  );
}
