import type { ComponentType } from "react";
import { Skeleton } from "@/components/ui/skeleton";

type IslandSkeletonModule = { default: ComponentType<any> };

function DefaultSkeleton() {
    return (
        <div className="space-y-2 p-4">
            <Skeleton className="h-4 w-[250px]" />
        </div>
    );
}

const skeletonModules = import.meta.glob<IslandSkeletonModule>(
    "../skeletons/**/*.skeleton.{tsx,ts,jsx,js}",
    { eager: true }
);

const islandSkeletons = Object.fromEntries(
    Object.entries(skeletonModules).map(([path, mod]) => {
        const filename = path.split("/").pop() ?? "";
        const id = filename.replace(/\.skeleton\.[^.]+$/, "");
        return [id, mod.default];
    })
) as Record<string, ComponentType<any>>;

export function getIslandSkeleton(name: string): ComponentType<any> {
    return islandSkeletons[name] || DefaultSkeleton;
}
