import { Skeleton } from "@/components/ui/skeleton";
import { Card, CardContent } from "@/components/ui/card";

export default function LoopAuthorSkeleton() {
  return (
    <Card className="mb-8 border-none shadow-none bg-secondary/50">
      <CardContent className="flex flex-col sm:flex-row items-center sm:items-start gap-6 p-6">
        <Skeleton className="w-24 h-24 rounded-full border-4 border-background shrink-0" />
        
        <div className="flex-1 space-y-4 w-full flex flex-col items-center sm:items-start">
          <div className="flex flex-col sm:flex-row items-center gap-3 w-full justify-center sm:justify-start">
            <Skeleton className="h-8 w-32" />
            <Skeleton className="h-5 w-16 rounded-full" />
          </div>
          
          <div className="space-y-2 w-full max-w-2xl">
            <Skeleton className="h-4 w-full" />
            <Skeleton className="h-4 w-3/4" />
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
