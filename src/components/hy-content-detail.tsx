import { useState, type MouseEvent } from "react";
import { toast } from "sonner";
import { cn, getConfig } from "@/lib/utils";

type HyContentDetailProps = {
  postId: number;
  comments?: string;
  likes?: string | number;
  isFavorite?: boolean;
  disallowToggle?: boolean;
  variant?: "overlay" | "default";
};

function getButtonClass(variant: HyContentDetailProps["variant"], active = false) {
  if (variant === "overlay") {
    return cn(
      "inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm border-none backdrop-blur-sm transition-colors",
      active ? "bg-white/90 text-primary hover:bg-white" : "bg-white/20 hover:bg-white/30 text-white"
    );
  }

  return cn(
    "inline-flex items-center justify-center gap-2 rounded-md border px-3 py-2 text-sm transition-colors",
    active
      ? "border-primary bg-primary text-primary-foreground hover:bg-primary/90"
      : "bg-background hover:bg-muted/80"
  );
}

export default function HyContentDetail({
  postId,
  comments,
  likes,
  isFavorite = false,
  disallowToggle = false,
  variant = "default",
}: HyContentDetailProps) {
  const [likeCount, setLikeCount] = useState<string | number>(likes ?? "");
  const [isFavorited, setIsFavorited] = useState(isFavorite);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);

  const handleLike = async () => {
    if (isLikeLoading || hasLiked || disallowToggle) {
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
        body: JSON.stringify({ post_id: Number(postId) }),
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
    if (isFavoriteLoading || disallowToggle) {
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
        body: JSON.stringify({ post_id: Number(postId) }),
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
    ? (isFavorited ? "取消中" : "收藏中")
    : (isFavorited ? "已收藏" : "收藏");
  const likeLabel = isLikeLoading ? "处理中" : (hasLiked ? "已赞" : "点赞");
  const hasLikeCount = likeCount !== "" && likeCount !== null && likeCount !== undefined;

  return (
    <div className="flex items-center gap-3">
      {!disallowToggle ? (
        <button
          type="button"
          onClick={handleFavorite}
          disabled={isFavoriteLoading}
          className={getButtonClass(variant, isFavorited)}
        >
          <span>{favoriteLabel}</span>
        </button>
      ) : null}

      {!disallowToggle ? (
        <button
          type="button"
          onClick={handleLike}
          disabled={isLikeLoading || hasLiked}
          className={getButtonClass(variant, false)}
        >
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
          <span>评论</span>
          <span>{comments}</span>
        </button>
      ) : null}
    </div>
  );
}
