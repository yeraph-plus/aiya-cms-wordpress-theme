import { Empty, EmptyDescription, EmptyHeader, EmptyTitle } from "@/components/ui/empty";
import { cn } from "@/lib/utils";

interface LoopGridEmptyProps {
  title?: string;
  description?: string;
  className?: string;
}

export default function LoopGridEmpty({
  title = "暂无内容",
  description = "当前列表没有任何文章可显示",
  className,
}: LoopGridEmptyProps) {
  return (
    <div className={cn("my-4 space-y-6", className)}>
      <Empty className="mx-auto my-8 border rounded-lg">
        <EmptyHeader>
          <EmptyTitle>{title}</EmptyTitle>
          <EmptyDescription className="mt-2">{description}</EmptyDescription>
        </EmptyHeader>
      </Empty>
    </div>
  );
}
