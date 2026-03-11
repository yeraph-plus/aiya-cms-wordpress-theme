import { Skeleton } from "@/components/ui/skeleton";
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbSeparator } from "@/components/ui/breadcrumb";

export default function NavBreadcrumbSkeleton() {
  return (
    <Breadcrumb className="my-4">
      <BreadcrumbList>
        <BreadcrumbItem>
             <Skeleton className="h-4 w-4" />
        </BreadcrumbItem>
        <BreadcrumbSeparator />
        <BreadcrumbItem>
             <Skeleton className="h-4 w-16" />
        </BreadcrumbItem>
        <BreadcrumbSeparator />
        <BreadcrumbItem>
             <Skeleton className="h-4 w-24" />
        </BreadcrumbItem>
      </BreadcrumbList>
    </Breadcrumb>
  );
}
