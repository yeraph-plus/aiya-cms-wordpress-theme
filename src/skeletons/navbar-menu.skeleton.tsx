import { Skeleton } from "@/components/ui/skeleton";
import { NavigationMenu, NavigationMenuList, NavigationMenuItem } from "@/components/ui/navigation-menu";

export default function NavMenuSkeleton() {
  return (
    <NavigationMenu>
      <NavigationMenuList className="gap-2">
        {[1, 2, 3, 4].map((i) => (
          <NavigationMenuItem key={i}>
            <Skeleton className="h-9 w-16 rounded-md" />
          </NavigationMenuItem>
        ))}
      </NavigationMenuList>
    </NavigationMenu>
  );
}
