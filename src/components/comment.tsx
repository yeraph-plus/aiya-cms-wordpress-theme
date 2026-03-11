import React, { useState, useEffect, useMemo } from 'react';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination"
import { MessageSquare, Reply, Send } from 'lucide-react';
import { toast } from 'sonner';
import { cn, getConfig } from '@/lib/utils';

interface Comment {
    id: number;
    post: number;
    parent: number;
    author: number;
    author_name: string;
    author_url: string;
    date: string;
    content: { rendered: string };
    author_avatar_urls?: { [key: string]: string };
    _links?: any;
    status?: string;
}

interface UserInfo {
    name: string;
    email: string;
    avatar: string;
    id?: number;
}

interface CommentSettings {
    comment_registration: boolean;
    require_name_email: boolean;
    thread_comments: boolean;
    thread_comments_depth: number;
    pageComments: boolean;
    commentsPerPage: number;
    defaultCommentsPage: string;
    commentOrder: string;
}

interface CommentSectionProps {
    postId: number;
    commentsOpen: boolean;
    commentsCount: number;
    currentUser: UserInfo | null;
    settings: CommentSettings;
}

const CommentForm = ({
    onSubmit,
    isSubmitting,
    authorName, setAuthorName,
    authorEmail, setAuthorEmail,
    content, setContent,
    currentUser,
    requireNameEmail,
    onCancel
}: any) => {
    return (
        <form onSubmit={onSubmit} className="space-y-4 animate-in fade-in slide-in-from-top-2">
            {!currentUser && (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Input
                        placeholder="Name"
                        value={authorName}
                        onChange={(e) => setAuthorName(e.target.value)}
                        required={requireNameEmail}
                    />
                    <Input
                        type="email"
                        placeholder="Email"
                        value={authorEmail}
                        onChange={(e) => setAuthorEmail(e.target.value)}
                        required={requireNameEmail}
                    />
                </div>
            )}
            <div className="relative">
                <Textarea
                    placeholder="Write a comment..."
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    required
                    className="min-h-[100px] resize-y"
                />
            </div>
            <div className="flex justify-end gap-2">
                {onCancel && (
                    <Button type="button" variant="ghost" onClick={onCancel}>
                        Cancel
                    </Button>
                )}
                <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? <Spinner className="mr-2" /> : <Send className="h-4 w-4 mr-2" />}
                    Submit
                </Button>
            </div>
        </form>
    );
};

const CommentItem = ({
    comment,
    depth = 0,
    settings,
    commentsOpen,
    replyTo,
    onReply,
    onCancelReply,
    formProps
}: any) => {
    const isReplying = replyTo?.id === comment.id;

    return (
        <div className={cn("flex gap-4 group", depth > 0 && "mt-6")}>
            <Avatar className="h-10 w-10 border shrink-0">
                <AvatarImage src={comment.author_avatar_urls?.['96']} />
                <AvatarFallback>{comment.author_name?.charAt(0) || 'A'}</AvatarFallback>
            </Avatar>

            <div className="flex-1 min-w-0 space-y-2">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <span className="font-semibold text-sm truncate">{comment.author_name}</span>
                        <span className="text-xs text-muted-foreground shrink-0">
                            {new Date(comment.date).toLocaleDateString()}
                        </span>
                    </div>
                    {commentsOpen && depth < (parseInt(settings.thread_comments_depth as any) || 5) && (
                        <Button
                            variant="ghost"
                            size="sm"
                            className={cn(
                                "h-6 px-2 text-xs transition-opacity",
                                isReplying ? "opacity-100" : "opacity-0 group-hover:opacity-100"
                            )}
                            onClick={() => onReply(comment)}
                        >
                            <Reply className="h-3 w-3 mr-1" /> Reply
                        </Button>
                    )}
                </div>

                <div
                    className="text-sm prose dark:prose-invert max-w-none text-muted-foreground/90 break-words"
                    dangerouslySetInnerHTML={{ __html: comment.content.rendered }}
                />

                {isReplying && (
                    <div className="mt-4 bg-muted/30 p-4 rounded-lg border">
                        <div className="flex items-center justify-between mb-4">
                            <span className="text-xs font-medium text-muted-foreground">Replying to {comment.author_name}</span>
                        </div>
                        <CommentForm
                            {...formProps}
                            onCancel={onCancelReply}
                        />
                    </div>
                )}

                {comment.children && comment.children.length > 0 && (
                    <div className="mt-4 border-l-2 border-muted pl-4">
                        {comment.children.map((child: any) => (
                            <CommentItem
                                key={child.id}
                                comment={child}
                                depth={depth + 1}
                                settings={settings}
                                commentsOpen={commentsOpen}
                                replyTo={replyTo}
                                onReply={onReply}
                                onCancelReply={onCancelReply}
                                formProps={formProps}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
};

export default function CommentSection({
    postId,
    commentsOpen,
    commentsCount,
    currentUser,
    settings
}: CommentSectionProps) {
    const [comments, setComments] = useState<Comment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [replyTo, setReplyTo] = useState<Comment | null>(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    // Form state
    const [authorName, setAuthorName] = useState(currentUser?.name || '');
    const [authorEmail, setAuthorEmail] = useState(currentUser?.email || '');
    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Fetch comments
    useEffect(() => {
        const fetchComments = async () => {
            setIsLoading(true);
            try {
                const { apiUrl, apiNonce } = getConfig()
                let url = `${apiUrl}/wp/v2/comments?post=${postId}`;

                // Pagination settings
                if (settings.pageComments) {
                    url += `&page=${currentPage}`;
                    url += `&per_page=${settings.commentsPerPage || 20}`;
                } else {
                    url += `&per_page=100`;
                }

                // Order settings
                url += `&order=${settings.commentOrder || 'asc'}`;

                const res = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': apiNonce || '',
                    }
                });
                if (!res.ok) throw new Error('Failed to fetch comments');

                // Get total pages from header
                const totalPagesHeader = res.headers.get('X-WP-TotalPages');
                if (totalPagesHeader) {
                    setTotalPages(parseInt(totalPagesHeader));
                }

                const data = await res.json();
                setComments(data);
            } catch (error) {
                console.error('Error fetching comments:', error);
                toast.error('Failed to load comments');
            } finally {
                setIsLoading(false);
            }
        };

        if (postId) {
            fetchComments();
        }
    }, [postId, currentPage, settings]);

    // Build comment tree
    const commentTree = useMemo(() => {
        const map = new Map<number, Comment & { children: any[] }>();
        const roots: (Comment & { children: any[] })[] = [];

        // Initialize map
        comments.forEach(comment => {
            map.set(comment.id, { ...comment, children: [] });
        });

        // Build hierarchy
        comments.forEach(comment => {
            const node = map.get(comment.id)!;
            if (comment.parent && map.has(comment.parent)) {
                map.get(comment.parent)!.children.push(node);
            } else {
                roots.push(node);
            }
        });

        // Sort by date
        return roots.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
    }, [comments]);

    const handleSubmit = async (e?: React.FormEvent) => {
        if (e) e.preventDefault();

        if (!content.trim()) {
            toast.error('Comment content cannot be empty');
            return;
        }

        if (!currentUser && settings.require_name_email && (!authorName || !authorEmail)) {
            toast.error('Name and Email are required');
            return;
        }

        setIsSubmitting(true);

        try {
            const { apiUrl, apiNonce } = getConfig()
            const body: any = {
                post: postId,
                content: content,
                parent: replyTo ? replyTo.id : 0,
            };

            if (!currentUser) {
                body.author_name = authorName;
                body.author_email = authorEmail;
            }

            const res = await fetch(`${apiUrl}/wp/v2/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': apiNonce || '',
                },
                body: JSON.stringify(body)
            });

            if (!res.ok) {
                const errorData = await res.json();
                throw new Error(errorData.message || 'Failed to submit comment');
            }

            const newComment = await res.json();

            // Check if comment is pending
            if (newComment.status === 'hold') {
                toast.success('Comment submitted and waiting for moderation.');
            } else {
                toast.success('Comment submitted successfully');
            }

            // Add to list (might look weird if paginated, but immediate feedback is good)
            setComments(prev => [...prev, newComment]);
            setContent('');
            setReplyTo(null);

        } catch (error: any) {
            console.error('Error submitting comment:', error);
            toast.error(error.message || 'Failed to submit comment');
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleReply = (comment: Comment) => {
        setReplyTo(comment);
        setContent(''); // Clear content when switching reply target
    };

    const handleCancelReply = () => {
        setReplyTo(null);
        setContent('');
    };

    const formProps = {
        onSubmit: handleSubmit,
        isSubmitting,
        authorName, setAuthorName,
        authorEmail, setAuthorEmail,
        content, setContent,
        currentUser,
        requireNameEmail: settings.require_name_email
    };

    const renderPagination = () => {
        if (totalPages <= 1) return null;

        return (
            <Pagination className="mt-8">
                <PaginationContent>
                    <PaginationItem>
                        <PaginationPrevious
                            href="#"
                            onClick={(e) => { e.preventDefault(); if (currentPage > 1) setCurrentPage(p => p - 1); }}
                            aria-disabled={currentPage === 1}
                            className={currentPage === 1 ? "pointer-events-none opacity-50" : "cursor-pointer"}
                        />
                    </PaginationItem>

                    {Array.from({ length: totalPages }, (_, i) => i + 1).map(page => {
                        // Show first, last, and pages around current
                        if (
                            page === 1 ||
                            page === totalPages ||
                            (page >= currentPage - 1 && page <= currentPage + 1)
                        ) {
                            return (
                                <PaginationItem key={page}>
                                    <PaginationLink
                                        href="#"
                                        isActive={page === currentPage}
                                        onClick={(e) => { e.preventDefault(); setCurrentPage(page); }}
                                    >
                                        {page}
                                    </PaginationLink>
                                </PaginationItem>
                            );
                        }

                        // Show ellipsis
                        if (
                            (page === currentPage - 2 && page > 2) ||
                            (page === currentPage + 2 && page < totalPages - 1)
                        ) {
                            return (
                                <PaginationItem key={page}>
                                    <PaginationEllipsis />
                                </PaginationItem>
                            )
                        }

                        return null;
                    })}

                    <PaginationItem>
                        <PaginationNext
                            href="#"
                            onClick={(e) => { e.preventDefault(); if (currentPage < totalPages) setCurrentPage(p => p + 1); }}
                            aria-disabled={currentPage === totalPages}
                            className={currentPage === totalPages ? "pointer-events-none opacity-50" : "cursor-pointer"}
                        />
                    </PaginationItem>
                </PaginationContent>
            </Pagination>
        );
    };

    // If registration required and not logged in
    if (settings.comment_registration && !currentUser) {
        return (
            <Card className="w-full border-none shadow-none bg-transparent">
                <CardHeader className="px-0">
                    <CardTitle className="flex items-center gap-2 text-xl">
                        <MessageSquare className="h-5 w-5" />
                        Comments ({commentsCount})
                    </CardTitle>
                </CardHeader>
                <CardContent className="flex flex-col items-center justify-center py-8 space-y-4 bg-muted/20 rounded-lg border border-dashed">
                    <p className="text-muted-foreground">You must be logged in to post a comment.</p>
                    <Button onClick={() => (window as any).LoginAction?.showLogin()}>
                        Login
                    </Button>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card className="w-full border-none shadow-none bg-transparent">
            <CardHeader className="px-0">
                <CardTitle className="flex items-center gap-2 text-xl">
                    <MessageSquare className="h-5 w-5" />
                    Comments ({comments.length > 0 ? comments.length : commentsCount})
                </CardTitle>
            </CardHeader>
            <CardContent className="space-y-8 px-0">
                {isLoading ? (
                    <div className="flex justify-center py-8">
                        <Spinner className="h-8 w-8 animate-spin text-muted-foreground" />
                    </div>
                ) : (
                    <>
                        <div className="space-y-8">
                            {commentTree.map(comment => (
                                <CommentItem
                                    key={comment.id}
                                    comment={comment}
                                    settings={settings}
                                    commentsOpen={commentsOpen}
                                    replyTo={replyTo}
                                    onReply={handleReply}
                                    onCancelReply={handleCancelReply}
                                    formProps={formProps}
                                />
                            ))}
                            {comments.length === 0 && (
                                <p className="text-center text-muted-foreground py-8">
                                    No comments yet. Be the first to share your thoughts!
                                </p>
                            )}
                        </div>

                        {settings.pageComments && renderPagination()}

                        {commentsOpen && !replyTo && (
                            <div className="mt-8 pt-8 border-t">
                                <h3 className="font-semibold mb-4 text-lg">Leave a comment</h3>
                                <CommentForm {...formProps} />
                            </div>
                        )}

                        {!commentsOpen && (
                            <div className="mt-8 pt-8 border-t text-center text-muted-foreground">
                                Comments are closed.
                            </div>
                        )}
                    </>
                )}
            </CardContent>
        </Card>
    );
}
