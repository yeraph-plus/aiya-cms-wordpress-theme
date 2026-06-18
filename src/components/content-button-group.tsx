import { useState } from "react";

import { Heart, Bookmark, MessageSquarePlus } from "lucide-react";
import { toast } from "sonner";
import { cn, getConfig } from "@/lib/utils";
import { joinTranslations } from '@/lib/i18n';
import { Spinner } from "@/components/ui/spinner";
import { Button } from "@/components/ui/button";
import { LoginDialog } from "@/components/dialog-login";
import { IssueDialog } from "@/components/dialog-issue";

type ContentButtonGroupProps = {
  post_id: number;
  likes?: string | number;
  is_favorite?: boolean;
  current_user_id?: number;
  issue_title_content?: string[];
};

const { t } = joinTranslations();

function getButtonClass(active = false, tone: "favorite" | "like" | "issue" = "like") {
  const activeClass =
    tone === "favorite"
      ? "border-amber-500 bg-amber-500 text-white hover:bg-amber-600"
      : tone === "issue"
        ? "border-primary bg-primary text-primary-foreground hover:bg-primary/90"
        : "border-rose-600 bg-rose-600 text-white hover:bg-rose-700";

  return cn(
    "min-w-24",
    active ? activeClass : "border-border bg-background hover:bg-muted/80"
  );
}

export default function ContentButtonGroup({
  post_id,
  likes,
  is_favorite = false,
  current_user_id = 0,
  issue_title_content = [],
}: ContentButtonGroupProps) {
  const [likeCount, setLikeCount] = useState<string | number>(likes ?? "");
  const [isFavorited, setIsFavorited] = useState(is_favorite);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);
  const [isIssueDialogOpen, setIsIssueDialogOpen] = useState(false);
  const [showLogin, setShowLogin] = useState(false);

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

    if (!current_user_id) {
      setShowLogin(true);
      return;
    }

    setIsFavoriteLoading(true);
    const toastId = toast.loading(isFavorited ? t('cancel_favorite') : t('loading') + '...');

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
        toast.success(nextFavorited ? t('favorited') : t('cancel_favorite'), { id: toastId });
      } else {
        toast.error(t('login_first'), { id: toastId });
      }
    } catch (error) {
      console.error("Favorite toggle failed:", error);
      toast.error(t('operation_failed'), { id: toastId });
    } finally {
      setIsFavoriteLoading(false);
    }
  };

  const handleOpenIssueDialog = () => {
    if (!current_user_id) {
      setShowLogin(true);
      return;
    }

    setIsIssueDialogOpen(true);
  };

  const favoriteLabel = isFavoriteLoading ? t('loading') : isFavorited ? t('favorited') : t('favorite');
  const likeLabel = isLikeLoading ? t('loading') : hasLiked ? t('liked') : t('like');
  const issueLabel = t('start_feedback');
  const hasLikeCount =
    likeCount !== "" && likeCount !== null && likeCount !== undefined;
  const favoriteDisabled = isFavoriteLoading;
  const likeDisabled = isLikeLoading || hasLiked;
  const favoriteActive = isFavorited || isFavoriteLoading;
  const likeActive = hasLiked || isLikeLoading;

  return (
    <>
      <div className="flex flex-wrap items-center gap-3">
        <Button
          variant="outline"
          onClick={handleOpenIssueDialog}
          className={getButtonClass(isIssueDialogOpen, "issue")}
        >
          <MessageSquarePlus className="h-4 w-4 text-primary" />
          <span>{issueLabel}</span>
        </Button>

        <Button
          variant="outline"
          onClick={handleFavorite}
          disabled={favoriteDisabled}
          className={getButtonClass(favoriteActive, "favorite")}
        >
          {isFavoriteLoading ? (
            <Spinner className="h-4 w-4" />
          ) : (
            <Bookmark className={cn("h-4 w-4 text-amber-500", favoriteActive && "fill-current text-white")} />
          )}
          <span>{favoriteLabel}</span>
        </Button>

        <Button
          variant="outline"
          onClick={handleLike}
          disabled={likeDisabled}
          className={getButtonClass(likeActive, "like")}
        >
          {isLikeLoading ? (
            <Spinner className="h-4 w-4" />
          ) : (
            <Heart className={cn("h-4 w-4 text-rose-500", likeActive && "fill-current text-white")} />
          )}
          <span>{likeLabel}</span>
          {hasLikeCount ? <span>{String(likeCount)}</span> : null}
        </Button>
      </div>

      <IssueDialog
        open={isIssueDialogOpen}
        onOpenChange={setIsIssueDialogOpen}
        postId={post_id}
        issueTitleContent={issue_title_content}
      />

      <LoginDialog
        open={showLogin}
        onOpenChange={setShowLogin}
      />
    </>
  );
}
