
import { useState } from 'react';
import {
  Calendar,
  Eye,
  MessageSquare,
  Heart,
  Hash,
  AlertTriangle,
  Clock,
  Star,
  Info,
  CheckCircle,
  XCircle,
  HelpCircle,
} from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Toggle } from "@/components/ui/toggle";
import { Spinner } from "@/components/ui/spinner";
import { toast } from "sonner"
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { cn, getConfig } from "@/lib/utils";

interface Category {
  id: number
  name: string;
  url: string;
}

interface Tag {
  id: number
  name: string;
  url: string;
}

interface Author {
  name: string;
  url: string;
  avatar: string;
}

interface ContentDetailProps {
  className?: string;
  postId: number;
  title: string;
  author: Author;
  date: string;
  dateIso: string;
  status?: 'sticky' | 'newest' | 'password' | 'private' | 'pending' | 'future' | 'draft' | 'auto-draft' | 'inherit' | 'trash' | 'publish';
  modifiedAgo?: string;
  views?: string;
  comments?: string;
  likes?: string;
  disallowToggle?: boolean;
  isFavorite?: boolean;
  thumbnail?: string;
  categories?: Category[];
  tags?: Tag[];
  isOutdated?: boolean;
  outdatedText?: string;
  alertTips?: Array<{
    alert?: 'default' | 'info' | 'success' | 'warning' | 'error';
    name: string;
    description: string;
  }>;
}

export default function ContentDetail({
  postId,
  title,
  author,
  date,
  dateIso,
  status,
  modifiedAgo,
  views,
  comments,
  likes,
  thumbnail,
  categories,
  tags,
  isOutdated,
  outdatedText,
  alertTips,
  isFavorite,
  disallowToggle = false,
  className
}: ContentDetailProps) {
  const hasThumbnail = !!thumbnail;
  const [likeCount, setLikeCount] = useState(likes ?? 0);
  const [isFavorited, setIsFavorited] = useState(isFavorite || false);
  const [isLikeLoading, setIsLikeLoading] = useState(false);
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false);
  const [hasLiked, setHasLiked] = useState(false);

  const handleLike = async () => {
    if (isLikeLoading) return;
    setIsLikeLoading(true);

    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/post_like`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': apiNonce || ''
        },
        body: JSON.stringify({ post_id: Number(postId) })
      });

      const data = await response.json();

      if (data.success && data.data.status === 'done') {
        setLikeCount(data.data.count);
        setHasLiked(true);
      }
    } catch (error) {
      console.error('Like failed:', error);
    } finally {
      setIsLikeLoading(false);
    }
  };

  const handleFavorite = async () => {
    if (isFavoriteLoading) return;
    setIsFavoriteLoading(true);
    const toastId = toast.loading(isFavorited ? "正在取消收藏..." : "正在收藏...");

    try {
      const { apiUrl, apiNonce } = getConfig();
      const response = await fetch(`${apiUrl}/aiya/v1/post_favorite`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': apiNonce || ''
        },
        body: JSON.stringify({ post_id: Number(postId) })
      });

      const data = await response.json();

      if (data.success && data.data.status === 'done') {
        const nextFavorited = data.data.action === 'added';
        setIsFavorited(nextFavorited);
        toast.success(nextFavorited ? "已收藏" : "已取消", { id: toastId });
      } else {
        toast.error("请先登录", { id: toastId });
      }
    } catch (error) {
      console.error('Favorite toggle failed:', error);
      toast.error("操作失败", { id: toastId });
    } finally {
      setIsFavoriteLoading(false);
    }
  };

  // Status badge variant mapping
  const getStatusVariant = (status: any) => {
    // Handle object case safely
    if (typeof status === 'object' && status !== null) {
      if (status.sticky) return "default";
      // Try to find a known status key or fallback
      return "outline";
    }

    switch (status) {
      case 'sticky':
        return "default" // primary
      case 'newest':
        return "secondary" // secondary
      case 'password':
      case 'private':
        return "outline" // neutral
      case 'pending':
      case 'future':
      case 'draft':
      case 'auto-draft':
        return "secondary" // info
      case 'inherit':
      case 'trash':
        return "destructive" // error
      default:
        return "outline" // accent
    }
  };

  const getStatusLabel = (status: any) => {
    // Handle object case safely
    if (typeof status === 'object' && status !== null) {
      if (status.sticky) return '置顶';
      return ''; // Return empty string instead of object to prevent React error
    }

    switch (status) {
      case 'publish': return '已发布';
      case 'draft': return '草稿';
      case 'pending': return '待审核';
      case 'private': return '私密';
      case 'password': return '密码保护';
      case 'future': return '定时发布';
      case 'trash': return '回收站';
      case 'auto-draft': return '自动草稿';
      case 'inherit': return '继承';
      case 'sticky': return '置顶';
      case 'newest': return '最新';
      default: return status;
    }
  }

  const scrollToComments = (e: React.MouseEvent) => {
    e.preventDefault();
    const commentsSection = document.getElementById('comments');
    if (commentsSection) {
      commentsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // Common Header Content Render Function
  const renderHeaderContent = (isOverlay: boolean) => {
    const metaColor = isOverlay ? "text-white/90" : "text-muted-foreground";
    const buttonVariant = isOverlay ? "secondary" : "outline";
    const buttonClass = isOverlay
      ? "gap-2 bg-white/20 hover:bg-white/30 text-white border-none backdrop-blur-sm"
      : "gap-2";
    const activeFavClass = isOverlay
      ? "bg-white/90 text-primary hover:bg-white"
      : ""; // Default variant handles active state for non-overlay

    return (
      <div className="flex flex-col gap-4">

        <h1 className={cn(
          "text-xl md:text-3xl lg:text-4xl font-bold leading-tight flex flex-wrap items-center gap-3",
          isOverlay ? "text-white drop-shadow-lg" : "text-base-content"
        )}>
          {title}
          {status && status !== 'publish' && (
            <Badge variant={getStatusVariant(status)} className="border-none align-middle">
              {getStatusLabel(status)}
            </Badge>
          )}
        </h1>

        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className={cn("flex flex-wrap items-center gap-4 text-sm", metaColor)}>
            {/* Author */}
            <a href={author.url} className="flex items-center gap-2 hover:opacity-80 transition-opacity" title={author.name}>
              <Avatar className={cn("h-8 w-8", isOverlay ? "border-2 border-white/20" : "border border-base-300")}>
                <AvatarImage src={author.avatar} alt={author.name} />
                <AvatarFallback>{author.name.charAt(0)}</AvatarFallback>
              </Avatar>
              <span className={cn("font-medium", isOverlay ? "" : "text-base-content")}>{author.name}</span>
            </a>
            {/* Date */}
            <div className="flex items-center gap-1.5" title={dateIso}>
              <Calendar className="h-4 w-4" />
              <time dateTime={dateIso}>{date}</time>
              {modifiedAgo && (
                <>
                  <Clock className="h-4 w-4" />
                  <span className="opacity-80">Updated {modifiedAgo}</span>
                </>
              )}
            </div>
            {/* Views */}
            {views && (
              <div className="flex items-center gap-1.5">
                <Eye className="h-4 w-4" />
                <span>{views}</span>
              </div>
            )}
          </div>

          <div className="flex items-center gap-3">
            <Toggle
              variant="outline"
              size="sm"
              pressed={isFavorited}
              onPressedChange={handleFavorite}
              disabled={isFavoriteLoading || disallowToggle}
              className={cn(
                buttonClass,
                isFavorited && activeFavClass,
                !isOverlay && "data-[state=on]:bg-primary data-[state=on]:text-primary-foreground hover:data-[state=on]:bg-primary/90 hover:data-[state=on]:text-primary-foreground"
              )}
            >
              {isFavoriteLoading ? (
                <Spinner className="h-4 w-4" />
              ) : (
                <Star className={cn("h-4 w-4", isFavorited && "fill-current")} />
              )}
              {isFavoriteLoading ? (isFavorited ? "取消中" : "收藏中") : (isFavorited ? "已收藏" : "收藏")}
            </Toggle>
            <Toggle
              variant="outline"
              size="sm"
              pressed={hasLiked}
              onPressedChange={handleLike}
              disabled={isLikeLoading || hasLiked || disallowToggle}
              className={buttonClass}
            >
              {isLikeLoading ? (
                <Spinner className="h-4 w-4" />
              ) : (
                <Heart className={cn("h-4 w-4", hasLiked && "fill-current text-red-500")} />
              )}
              <span>{hasLiked ? "已赞" : "点赞"} {likeCount}</span>
            </Toggle>
            {comments && (
              <Button
                variant={buttonVariant}
                size="sm"
                className={buttonClass}
                onClick={scrollToComments}
              >
                <MessageSquare className="h-4 w-4" />
                <span>{comments}</span>
              </Button>
            )}
          </div>
        </div>
      </div>
    );
  };

  // Get alert style based on type
  const getAlertStyle = (type?: string) => {
    switch (type) {
      case 'error':
        return "border-red-200 bg-red-50 text-red-900 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-200";
      case 'warning':
        return "border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200";
      case 'success':
        return "border-green-200 bg-green-50 text-green-900 dark:border-green-900/50 dark:bg-green-950/30 dark:text-green-200";
      case 'info':
        return "border-blue-200 bg-blue-50 text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-200";
      case 'default':
      default:
        return "border-slate-200 bg-slate-50 text-slate-900 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200";
    }
  };

  const getAlertIcon = (type?: string) => {
    switch (type) {
      case 'error':
        return <XCircle className="h-4 w-4 !text-red-600 dark:!text-red-400" />;
      case 'warning':
        return <AlertTriangle className="h-4 w-4 !text-amber-600 dark:!text-amber-400" />;
      case 'success':
        return <CheckCircle className="h-4 w-4 !text-green-600 dark:!text-green-400" />;
      case 'info':
        return <Info className="h-4 w-4 !text-blue-600 dark:!text-blue-400" />;
      case 'default':
      default:
        return <HelpCircle className="h-4 w-4 !text-slate-600 dark:!text-slate-400" />;
    }
  };

  const hasCategories = categories && categories.length > 0;
  const hasTags = tags && tags.length > 0;

  return (
    <div className={cn("relative mb-8", className)}>
      {hasThumbnail && (
        <div className="relative w-full h-60 md:h-72 lg:h-96 rounded-lg overflow-hidden mb-6">
          <img
            src={thumbnail}
            alt={title}
            className="absolute inset-0 w-full h-full object-cover object-center transition-transform hover:scale-105 duration-500"
            loading="lazy"
          />
          {/* Mask */}
          <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20" />

          {/* Title and Meta in Thumbnail */}
          <div className="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-10">
            {renderHeaderContent(true)}
          </div>
        </div>
      )}

      {!hasThumbnail && (
        <div className="mb-6 pb-6 border-b border-base-200">
          {renderHeaderContent(false)}
        </div>
      )}

      {/* Categories & Tags */}
      {(hasCategories || hasTags) && (
        <div className="flex flex-wrap items-center gap-2 mb-6 text-sm">
          {hasCategories && (
            <>
              <span className="text-muted-foreground">分类：</span>
              {categories.map((cat, index) => (
                <a key={`cat-${index}`} href={cat.url} className="bg-primary/90 text-primary-foreground backdrop-blur-sm border-none hover:bg-primary/80 text-sm px-2 py-1 rounded-md">
                  {cat.name}
                </a>
              ))}
            </>
          )}

          {hasTags && tags.map((tag, index) => (
            <a key={`tag-${index}`} href={tag.url} className="flex items-center gap-1 text-muted-foreground hover:text-primary transition-colors no-underline">
              <Hash className="h-3 w-3 opacity-70" />
              {tag.name}
            </a>
          ))}
        </div>
      )}

      {/* Outdated Alert */}
      {isOutdated && (
        <div className="flex flex-col gap-4 mt-6">
          <Alert className="border-slate-200 bg-slate-50 text-slate-900 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200">
            <Info className="h-4 w-4 !text-slate-600 dark:!text-slate-400" />
            <AlertTitle>注意</AlertTitle>
            <AlertDescription>这篇文章发布于 {outdatedText} ，部分信息可能已过时，请留意。</AlertDescription>
          </Alert>
        </div>
      )}

      {/* Custom Alert Tips */}
      {alertTips && alertTips.length > 0 && (
        <div className="flex flex-col gap-4 mt-6">
          {alertTips.map((tip, index) => (
            <Alert key={index} className={getAlertStyle(tip.alert)}>
              {getAlertIcon(tip.alert)}
              <AlertTitle>{tip.name}</AlertTitle>
              <AlertDescription>{tip.description}</AlertDescription>
            </Alert>
          ))}
        </div>
      )}
    </div>
  );
}
