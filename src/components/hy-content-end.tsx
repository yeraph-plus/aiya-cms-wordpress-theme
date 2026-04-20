import type { ComponentProps } from "react";
import ContentEnd from "./content-end";
import { cn } from "@/lib/utils";

type HyContentEndProps = ComponentProps<typeof ContentEnd>;

export default function HyContentEnd(props: HyContentEndProps) {
  const { endDivider, prevPost, nextPost, statementText, className } = props;

  return (
    <footer className={cn("my-8 space-y-8", className)}>
      {endDivider && (
        <div className="flex items-center gap-4 text-muted-foreground/50">
          <div className="h-px bg-border flex-1" />
          <span className="text-xs font-semibold uppercase tracking-widest">The End</span>
          <div className="h-px bg-border flex-1" />
        </div>
      )}

      {statementText && (
        <div className="bg-muted/30 rounded-lg p-6 text-center">
          <p className="text-sm text-muted-foreground leading-relaxed mx-auto">{statementText}</p>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {prevPost ? (
          <a
            href={prevPost.url}
            className="group relative flex flex-col p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50"
          >
            <div className="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
              <span>上一篇</span>
            </div>
            <h3 className="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2">
              {prevPost.title}
            </h3>
          </a>
        ) : (
          <div className="hidden md:block" />
        )}

        {nextPost ? (
          <a
            href={nextPost.url}
            className="group relative flex flex-col items-end text-right p-6 rounded-lg border border-border bg-card hover:bg-muted/50 transition-all hover:border-primary/50"
          >
            <div className="flex items-center gap-2 text-xs font-medium text-muted-foreground mb-2">
              <span>下一篇</span>
            </div>
            <h3 className="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-2">
              {nextPost.title}
            </h3>
          </a>
        ) : (
          <div className="hidden md:block" />
        )}
      </div>
    </footer>
  );
}
