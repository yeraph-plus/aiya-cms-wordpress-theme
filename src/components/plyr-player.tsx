import React, { useEffect, useRef, useState } from 'react';
import { Plyr, type APITypes } from 'plyr-react';
import 'plyr-react/plyr.css';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';

declare global {
    interface Window {
        Hls: any;
    }
}

export interface VideoSource {
    type: 'video' | 'audio';
    title?: string;
    sources: {
        src: string;
        provider?: string;
        type?: string;
    }[];
    poster?: string;
}

interface VideoPlayerProps {
    playlist: VideoSource[];
    options?: Plyr.Options;
    className?: string;
    storageKey?: string;
}

const VideoPlayer: React.FC<VideoPlayerProps> = ({ playlist = [], options, className, storageKey = 'aya-player-history' }) => {
    const [currentIndex, setCurrentIndex] = useState(0);
    const ref = useRef<APITypes>(null);
    const [hasMounted, setHasMounted] = useState(false);

    // Load history
    useEffect(() => {
        setHasMounted(true);
        if (!playlist || playlist.length === 0) return;

        try {
            const history = JSON.parse(localStorage.getItem(storageKey) || '{}');
            const pageKey = window.location.pathname;
            const savedIndex = history[pageKey];
            if (typeof savedIndex === 'number' && savedIndex >= 0 && savedIndex < playlist.length) {
                setCurrentIndex(savedIndex);
            }
        } catch (e) {
            console.error('Failed to load video history', e);
        }
    }, [playlist, storageKey]);

    // Save history
    useEffect(() => {
        if (!hasMounted) return;
        try {
            const history = JSON.parse(localStorage.getItem(storageKey) || '{}');
            const pageKey = window.location.pathname;
            history[pageKey] = currentIndex;
            localStorage.setItem(storageKey, JSON.stringify(history));
        } catch (e) {
            console.error('Failed to save video history', e);
        }
    }, [currentIndex, hasMounted, storageKey]);

    const currentSource = playlist[currentIndex];

    // HLS Integration
    useEffect(() => {
        if (!currentSource || !ref.current) return;
        const player = ref.current.plyr;
        if (!player) return;

        const isHls = currentSource.sources.some(s => s.src.includes('.m3u8') || s.type === 'application/x-mpegURL');
        
        if (isHls && window.Hls && window.Hls.isSupported()) {
             const hls = new window.Hls();
             hls.loadSource(currentSource.sources[0].src);
             hls.attachMedia((player as any).media);
             return () => hls.destroy();
        }
    }, [currentSource]);

    if (!currentSource) return null;

    return (
        <div className={cn("space-y-4", className)}>
            <div className="w-full aspect-video rounded-lg overflow-hidden shadow-lg bg-black">
                <Plyr
                    ref={ref}
                    source={currentSource as any}
                    options={{ ...options, autoplay: false }}
                />
            </div>
            
            {playlist.length > 1 && (
                <Card>
                    <CardHeader className="py-4">
                        <CardTitle>选集 ({currentIndex + 1}/{playlist.length})</CardTitle>
                    </CardHeader>
                    <CardContent className="pb-4">
                        <ScrollArea className="h-full max-h-[200px]">
                            <div className="flex flex-wrap gap-2">
                                {playlist.map((item, index) => (
                                    <Button
                                        key={index}
                                        variant={index === currentIndex ? "default" : "outline"}
                                        size="sm"
                                        onClick={() => setCurrentIndex(index)}
                                        className="min-w-[3rem]"
                                    >
                                        {item.title || `第 ${index + 1} 集`}
                                    </Button>
                                ))}
                            </div>
                        </ScrollArea>
                    </CardContent>
                </Card>
            )}
        </div>
    );
};

export default VideoPlayer;
