import { useState, type MouseEvent } from "react";
import { Heart, MessageSquare, ThumbsUp } from "lucide-react";
import { toast } from "sonner";
import { cn, getConfig } from "@/lib/utils";

type ContentButtonGroupProps = {
  post_id: number;
  comments?: string;
  likes?: string | number;
  is_favorite?: boolean;
  disallow_toggle?: boolean;
  variant?: "overlay" | "default";
};

function getButtonClass(
  variant: ContentButtonGroupProps["variant"],
  active = false
) {
  if (variant === "overlay") {
    return cn(
      "inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm border-none backdrop-blur-sm transition-colors",
      active
        ? "bg-white/90 text-primary hover:bg-white"
        : "bg-white/20 hover:bg-white/30 text-white"
    );
  }

  return cn(
    "inline-flex items-center justify-center gap-2 rounded-md border px-3 py-2 text-sm transition-colors",
    active
      ? "border-primary bg-primary text-primary-foreground hover:bg-primary/90"
      : "bg-background hover:bg-muted/80"
  );
}

export default function ContentButtonGroup({
  post_id,
  comments,
  likes,
  is_favorite = false,
  disallow_toggle = false,
  variant = "default",
}: ContentButtonGroupProps) {
  const [likeCount, setLikeCount] = useState<string | number>(likes ?? "");
  const [isFavorited, setIsFavorited] = useState(is_favorite);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);

  const handleLike = async () => {
    if (isLikeLoading || hasLiked || disallow_toggle) {
      return;
    }

    setIsLikeLoading(true);

    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/post_like`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({ post_id: Number(post_id) }),
      });

      const data = await response.json();

      if (data.success && data.data.status === "done") {
        setLikeCount(data.data.count);
        setHasLiked(true);
      }
    } catch (error) {
      console.error("Like failed:", error);
    } finally {
      setIsLikeLoading(false);
    }
  };

  const handleFavorite = async () => {
    if (isFavoriteLoading || disallow_toggle) {
      return;
    }

    setIsFavoriteLoading(true);
    const toastId = toast.loading(isFavorited ? "正在取消收藏..." : "正在收藏...");

    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/post_favorite`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({ post_id: Number(post_id) }),
      });

      const data = await response.json();

      if (data.success && data.data.status === "done") {
        const nextFavorited = data.data.action === "added";
        setIsFavorited(nextFavorited);
        toast.success(nextFavorited ? "已收藏" : "已取消", { id: toastId });
      } else {
        toast.error("请先登录", { id: toastId });
      }
    } catch (error) {
      console.error("Favorite toggle failed:", error);
      toast.error("操作失败", { id: toastId });
    } finally {
      setIsFavoriteLoading(false);
    }
  };

  const scrollToComments = (event: MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
    const commentsSection = document.getElementById("comments");

    if (commentsSection) {
      commentsSection.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  };

  const favoriteLabel = isFavoriteLoading
    ? isFavorited
      ? "取消中"
      : "收藏中"
    : isFavorited
      ? "已收藏"
      : "收藏";
  const likeLabel = isLikeLoading ? "处理中" : hasLiked ? "已赞" : "点赞";
  const hasLikeCount =
    likeCount !== "" && likeCount !== null && likeCount !== undefined;

  return (
    <div className="flex items-center gap-3">
      {!disallow_toggle ? (
        <button
          type="button"
          onClick={handleFavorite}
          disabled={isFavoriteLoading}
          className={getButtonClass(variant, isFavorited)}
        >
          <Heart className="h-4 w-4" />
          <span>{favoriteLabel}</span>
        </button>
      ) : null}

      {!disallow_toggle ? (
        <button
          type="button"
          onClick={handleLike}
          disabled={isLikeLoading || hasLiked}
          className={getButtonClass(variant, false)}
        >
          <ThumbsUp className="h-4 w-4" />
          <span>{likeLabel}</span>
          {hasLikeCount ? <span>{String(likeCount)}</span> : null}
        </button>
      ) : null}

      {comments ? (
        <button
          type="button"
          onClick={scrollToComments}
          className={getButtonClass(variant, false)}
        >
          <MessageSquare className="h-4 w-4" />
          <span>评论</span>
          <span>{comments}</span>
        </button>
      ) : null}
    </div>
  );
}
