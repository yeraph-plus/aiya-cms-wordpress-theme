import { __ } from '@wordpress/i18n';

import { useState } from "react";
import { Heart, Bookmark } from "lucide-react";
import { toast } from "sonner";
import { cn, getConfig } from "@/lib/utils";
import { Spinner } from "@/components/ui/spinner";

type ContentButtonGroupProps = {
  post_id: number;
  likes?: string | number;
  is_favorite?: boolean;
};

function getButtonClass(active = false, tone: "favorite" | "like" = "like") {
  const activeClass =
    tone === "favorite"
      ? "border-amber-500 bg-amber-500 text-white hover:bg-amber-600"
      : "border-rose-600 bg-rose-600 text-white hover:bg-rose-700";

  return cn(
    "inline-flex items-center justify-center gap-2 rounded-md border px-3 py-2 text-sm transition-colors",
    active ? activeClass : "border-border bg-background hover:bg-muted/80"
  );
}

export default function ContentButtonGroup({
  post_id,
  likes,
  is_favorite = false,
}: ContentButtonGroupProps) {
  const [likeCount, setLikeCount] = useState<string | number>(likes ?? "");
  const [isFavorited, setIsFavorited] = useState(is_favorite);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);

  const handleLike = async () => {
    if (isLikeLoading || hasLiked) {
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
    if (isFavoriteLoading) {
      return;
    }

    setIsFavoriteLoading(true);
    const toastId = toast.loading(isFavorited ? __('正在取消收藏...', 'aiya-cms') : __('正在收藏...', 'aiya-cms'));

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
        toast.success(nextFavorited ? __('已收藏', 'aiya-cms') : __('已取消', 'aiya-cms'), { id: toastId });
      } else {
        toast.error(__('请先登录', 'aiya-cms'), { id: toastId });
      }
    } catch (error) {
      console.error("Favorite toggle failed:", error);
      toast.error(__('操作失败', 'aiya-cms'), { id: toastId });
    } finally {
      setIsFavoriteLoading(false);
    }
  };

  const favoriteLabel = isFavoriteLoading
    ? isFavorited
      ? __('取消中', 'aiya-cms')
      : __('收藏中', 'aiya-cms')
    : isFavorited
      ? __('已收藏', 'aiya-cms')
      : __('收藏', 'aiya-cms');
  const likeLabel = isLikeLoading ? __('处理中', 'aiya-cms') : hasLiked ? __('已赞', 'aiya-cms') : __('点赞', 'aiya-cms');
  const hasLikeCount =
    likeCount !== "" && likeCount !== null && likeCount !== undefined;
  const favoriteActive = isFavorited || isFavoriteLoading;
  const likeActive = hasLiked || isLikeLoading;

  return (
    <div className="flex items-center gap-3">
      <button
        type="button"
        onClick={handleFavorite}
        disabled={isFavoriteLoading}
        className={getButtonClass(favoriteActive, "favorite")}
      >
        {isFavoriteLoading ? (
          <Spinner className="h-4 w-4" />
        ) : (
          <Bookmark className={cn("h-4 w-4 text-amber-500", favoriteActive && "fill-current text-white")} />
        )}
        <span>{favoriteLabel}</span>
      </button>

      <button
        type="button"
        onClick={handleLike}
        disabled={isLikeLoading || hasLiked}
        className={getButtonClass(likeActive, "like")}
      >
        {isLikeLoading ? (
          <Spinner className="h-4 w-4" />
        ) : (
          <Heart className={cn("h-4 w-4 text-rose-500", likeActive && "fill-current text-white")} />
        )}
        <span>{likeLabel}</span>
        {hasLikeCount ? <span>{String(likeCount)}</span> : null}
      </button>
    </div>
  );
}
