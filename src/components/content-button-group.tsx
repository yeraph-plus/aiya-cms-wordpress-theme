import { useState } from "react";

import { Heart, Bookmark, MessageSquarePlus } from "lucide-react";
import { toast } from "sonner";
import { cn, getConfig } from "@/lib/utils";
import { joinTranslations } from '@/lib/i18n';
import { Spinner } from "@/components/ui/spinner";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

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
  const normalizedIssueTitles = issue_title_content
    .map((title) => title.trim())
    .filter(Boolean);
  const defaultIssueTitleValue = normalizedIssueTitles[0] || "__custom__";
  const [likeCount, setLikeCount] = useState<string | number>(likes ?? "");
  const [isFavorited, setIsFavorited] = useState(is_favorite);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);
  const [isIssueDialogOpen, setIsIssueDialogOpen] = useState(false);
  const [selectedIssueTitle, setSelectedIssueTitle] = useState(defaultIssueTitleValue);
  const [issueTitle, setIssueTitle] = useState("");
  const [issueContent, setIssueContent] = useState("");
  const [isIssueSubmitting, setIsIssueSubmitting] = useState(false);

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

  const resetIssueForm = () => {
    setSelectedIssueTitle(defaultIssueTitleValue);
    setIssueTitle("");
    setIssueContent("");
  };

  const handleOpenIssueDialog = () => {
    if (!current_user_id) {
      return;
    }

    setIsIssueDialogOpen(true);
  };

  const handleIssueSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const finalIssueTitle = selectedIssueTitle === "__custom__"
      ? issueTitle.trim()
      : selectedIssueTitle.trim();

    if (!finalIssueTitle) {
      toast.error(t('issue_title_required'));
      return;
    }

    setIsIssueSubmitting(true);

    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/issue/create`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": apiNonce || "",
        },
        body: JSON.stringify({
          post_id: Number(post_id),
          type: "feedback",
          status: "open",
          title: finalIssueTitle,
          content: issueContent.trim(),
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data?.message || data?.detail || data?.data?.detail || t('submit_issue_failed'));
      }

      toast.success(data?.data?.message || t('issue_created'));
      setIsIssueDialogOpen(false);
      resetIssueForm();

      const issuePermalink = data?.data?.issue?.permalink;
      if (typeof issuePermalink === "string" && issuePermalink) {
        window.location.href = issuePermalink;
      }
    } catch (error) {
      console.error("Create issue failed:", error);
      toast.error(error instanceof Error ? error.message : t('submit_issue_failed'));
    } finally {
      setIsIssueSubmitting(false);
    }
  };

  const favoriteLabel = isFavoriteLoading ? t('loading') : isFavorited ? t('favorited') : t('favorite');
  const likeLabel = isLikeLoading ? t('loading') : hasLiked ? t('liked') : t('like');
  const issueLabel = isIssueSubmitting ? t('loading') : t('start_feedback');
  const hasLikeCount =
    likeCount !== "" && likeCount !== null && likeCount !== undefined;
  const issueDisabled = isIssueSubmitting || current_user_id === 0;
  const favoriteDisabled = isFavoriteLoading || current_user_id === 0;
  const likeDisabled = isLikeLoading || hasLiked;
  const favoriteActive = isFavorited || isFavoriteLoading;
  const likeActive = hasLiked || isLikeLoading;

  return (
    <>
      <div className="flex flex-wrap items-center gap-3">
        <Button
          variant="outline"
          onClick={handleOpenIssueDialog}
          disabled={issueDisabled}
          className={getButtonClass(isIssueDialogOpen, "issue")}
        >
          {isIssueSubmitting ? (
            <Spinner className="h-4 w-4" />
          ) : (
            <MessageSquarePlus className="h-4 w-4 text-primary" />
          )}
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

      <Dialog
        open={isIssueDialogOpen}
        onOpenChange={(open) => {
          setIsIssueDialogOpen(open);
          if (!open) {
            resetIssueForm();
          }
        }}
      >
        <DialogContent className="sm:max-w-[560px]">
          <DialogHeader>
            <DialogTitle>{t('create_feedback')}</DialogTitle>
            <DialogDescription>{t('create_feedback_description')}</DialogDescription>
          </DialogHeader>

          <form onSubmit={handleIssueSubmit} className="grid gap-4">
            <div className="grid gap-2">
              <label className="text-sm font-medium">{t('issue_title')}</label>
              <Select
                value={selectedIssueTitle}
                onValueChange={(value) => {
                  setSelectedIssueTitle(value);
                  if (value !== "__custom__") {
                    setIssueTitle("");
                  }
                }}
                disabled={isIssueSubmitting}
              >
                <SelectTrigger className="w-full">
                  <SelectValue placeholder={t('issue_title_placeholder')} />
                </SelectTrigger>
                <SelectContent>
                  {normalizedIssueTitles.map((title) => (
                    <SelectItem key={title} value={title}>
                      {title}
                    </SelectItem>
                  ))}
                  <SelectItem value="__custom__">{t('manual_input') + "..."}</SelectItem>
                </SelectContent>
              </Select>
              {selectedIssueTitle === "__custom__" && (
                <Input
                  value={issueTitle}
                  onChange={(event) => setIssueTitle(event.target.value)}
                  placeholder={t('issue_title_placeholder')}
                  disabled={isIssueSubmitting}
                  required
                />
              )}
            </div>

            <div className="grid gap-2">
              <label className="text-sm font-medium">{t('issue_content')}</label>
              <Textarea
                value={issueContent}
                onChange={(event) => setIssueContent(event.target.value)}
                placeholder={t('issue_content_placeholder')}
                rows={8}
                disabled={isIssueSubmitting}
              />
            </div>

            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setIsIssueDialogOpen(false)} disabled={isIssueSubmitting}>
                取消
              </Button>
              <Button type="submit" disabled={isIssueSubmitting}>
                {isIssueSubmitting && <Spinner className="mr-2 h-4 w-4" />}
                {t('create_issue')}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </>
  );
}
