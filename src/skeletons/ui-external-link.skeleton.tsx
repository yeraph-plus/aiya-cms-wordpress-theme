import { Skeleton } from "@/components/ui/skeleton";

export default function UiExternalLinkSkeleton() {
  return (
    <div className="w-full h-[60vh] flex items-center justify-center">
      <div className="flex flex-col items-center gap-4 text-center">
        <Skeleton className="h-16 w-16 rounded-full" />
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-4 w-64" />
        <Skeleton className="h-10 w-32 mt-4" />
      </div>
    </div>
  );
}
