import { cn } from "@/lib/utils";
import { Megaphone } from "lucide-react";

interface AdItem {
  title: string;
  url: string;
  view: string;
}

interface ContentAdSpaceProps {
  ads: AdItem[];
  className?: string;
}

export default function ContentAdSpace({ ads, className }: ContentAdSpaceProps) {
  if (!ads || !Array.isArray(ads) || ads.length === 0) return null;
  const isSingleAd = ads.length === 1;
  const gridColsClass = ads.length > 1 ? "md:grid-cols-2" : "";

  return (
    <div className={cn(`container mx-auto my-4 grid grid-cols-1 ${gridColsClass} gap-2`, className)}>
      {ads.map((ad, index) => (
        <a
          key={index}
          href={ad.url}
          target="_blank"
          rel="nofollow noopener noreferrer"
          className={cn(
            "block relative group overflow-hidden rounded-lg border border-border bg-muted/30 transition-all hover:border-primary/50 hover:shadow-sm no-underline",
            isSingleAd && "w-full md:max-w-2xl md:mx-auto"
          )}
          title={`外部链接 “${ad.title}”`}
        >
          <div className="w-full h-auto overflow-hidden">
            <img
              src={ad.view}
              alt={ad.title || 'Advertisement'}
              className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            />
          </div>
          {ad.title && (
            <span className="absolute top-2 left-2 px-1.5 py-0.5 text-[12px] uppercase font-bold tracking-wider bg-black/50 text-white rounded backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity">
              <Megaphone className="w-3 h-3 inline-block mr-1" />
              推广 | {ad.title}
            </span>
          )}
        </a>
      ))}
    </div>
  );
}
