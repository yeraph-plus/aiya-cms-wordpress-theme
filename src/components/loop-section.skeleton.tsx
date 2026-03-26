import { Skeleton } from "@/components/ui/skeleton";

export default function LoopSectionSkeleton() {
  return (
    <div className="mt-6 mb-4 space-y-6">
      <div className="flex items-center justify-start gap-2">
        <Skeleton className="h-6 w-6" />
        <Skeleton className="h-7 w-32" />
      </div>

      <div className="grid gap-2 md:gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
        {[1, 2, 3, 4].map((i) => (
           <div key={i} className="flex flex-col rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden h-full">
             <div className="aspect-video w-full overflow-hidden bg-muted relative">
                 <Skeleton className="w-full h-full" />
             </div>
             <div className="p-4 space-y-2">
                 <Skeleton className="h-5 w-full" />
                 <Skeleton className="h-4 w-2/3" />
             </div>
           </div>
        ))}
      </div>
    </div>
  );
}
