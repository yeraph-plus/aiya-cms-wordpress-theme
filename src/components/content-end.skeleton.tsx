import { Skeleton } from "@/components/ui/skeleton";

export default function ContentEndSkeleton() {
  return (
    <footer className="my-8 space-y-8">
      {/* Divider */}
      <div className="flex items-center gap-4">
        <div className="h-px bg-border flex-1"></div>
        <Skeleton className="h-4 w-16" />
        <div className="h-px bg-border flex-1"></div>
      </div>

      {/* Statement */}
      <div className="bg-muted/30 rounded-lg p-6">
        <div className="flex flex-col items-center gap-2">
            <Skeleton className="h-4 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
        </div>
      </div>

      {/* Navigation */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {[1, 2].map((i) => (
          <div key={i} className="flex flex-col p-6 rounded-lg border border-border bg-card h-32 justify-between">
            <Skeleton className="h-4 w-16 mb-2" />
            <Skeleton className="h-6 w-full" />
          </div>
        ))}
      </div>
    </footer>
  );
}
