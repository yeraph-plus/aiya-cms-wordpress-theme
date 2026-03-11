import { Skeleton } from "@/components/ui/skeleton";

export default function PlyrPlayerSkeleton() {
    return (
        <div className="space-y-4">
            {/* Video Player Placeholder */}
            <div className="w-full aspect-video rounded-lg overflow-hidden shadow-lg bg-muted relative">
                <Skeleton className="h-full w-full" />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-16 h-16 rounded-full bg-muted-foreground/20" />
                </div>
            </div>
        </div>
    );
}
