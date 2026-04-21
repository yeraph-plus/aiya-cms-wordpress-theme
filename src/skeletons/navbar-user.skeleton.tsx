import { Skeleton } from "@/components/ui/skeleton";

export default function NavUserSkeleton() {
  return (
    <div className="flex items-center gap-2">
        <Skeleton className="h-8 w-16 rounded-md" /> {/* Login button or Avatar */}
    </div>
  );
}
