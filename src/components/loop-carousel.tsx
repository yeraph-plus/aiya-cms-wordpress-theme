import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel"
import { Card, CardContent } from "@/components/ui/card"
import { Images } from "lucide-react"
import { useIsMobile } from "@/lib/utils"
import {
  Empty,
  EmptyHeader,
  EmptyTitle,
  EmptyDescription,
  EmptyMedia,
} from "@/components/ui/empty"

export interface CarouselPost {
  url: string
  thumbnail: string
  title: string
  description?: string
  target?: string
}

interface LoopCarouselProps {
  posts: CarouselPost[]
  layout?: 'basic' | 'full' | 'mosaic' | 'cms'
}

export default function LoopCarousel({ posts, layout = 'full' }: LoopCarouselProps) {
  const isMobile = useIsMobile()

  if (!posts || posts.length === 0) {
    return (
      <Empty className="py-12 my-4">
        <EmptyMedia variant="icon">
          <Images />
        </EmptyMedia>
        <EmptyHeader>
          <EmptyTitle>暂无轮播</EmptyTitle>
          <EmptyDescription>
            轮播列表中没有内容
          </EmptyDescription>
        </EmptyHeader>
      </Empty>
    )
  }

  const activeLayout = isMobile ? 'full' : (layout === 'basic' ? 'full' : layout)

  if (activeLayout === 'mosaic') {
    return <MosaicLayout posts={posts} className="my-4" />
  }
  if (activeLayout === 'cms') {
    return <CmsLayout posts={posts} className="my-4" />
  }
  
  return <FullLayout posts={posts} className="my-4" />
}

function FullLayout({ posts, className }: { posts: CarouselPost[], className?: string }) {

  return (
    <Carousel
      className={`w-full relative group ${className || ''}`}
    >
      <CarouselContent>
        {posts.map((post, index) => (
          <CarouselItem key={index}>
            <div className="relative aspect-[21/9] w-full overflow-hidden rounded-lg">
              <a href={post.url} target={post.target} className="block w-full h-full">
                <img src={post.thumbnail} alt={post.title} className="w-full h-full object-cover" />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex flex-col justify-end p-6 md:p-10">
                  <h3 className="text-white text-xl md:text-3xl font-bold mb-2">{post.title}</h3>
                  {post.description && <p className="text-white/90 text-sm md:text-base line-clamp-2 max-w-3xl">{post.description}</p>}
                </div>
              </a>
            </div>
          </CarouselItem>
        ))}
      </CarouselContent>
      <CarouselPrevious className="left-4 opacity-0 group-hover:opacity-100 transition-opacity bg-background/50 hover:bg-background border-none text-white hover:text-foreground" />
      <CarouselNext className="right-4 opacity-0 group-hover:opacity-100 transition-opacity bg-background/50 hover:bg-background border-none text-white hover:text-foreground" />
    </Carousel>
  )
}

function MosaicLayout({ posts, className }: { posts: CarouselPost[], className?: string }) {
  return (
    <Carousel
      opts={{
        align: "start",
        // loop: true,
      }}
      className={`w-full relative group ${className || ''}`}
    >
      <CarouselContent className="-ml-4">
        {posts.map((post, index) => (
          <CarouselItem key={index} className="pl-4 md:basis-1/2 lg:basis-1/3">
            <div className="h-full">
              <Card className="overflow-hidden border-0 shadow-sm h-full group/card hover:shadow-md transition-all">
                <CardContent className="p-0 relative aspect-[16/10] overflow-hidden rounded-lg">
                  <a href={post.url} target={post.target} className="block w-full h-full">
                    <img src={post.thumbnail} alt={post.title} className="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-110" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-4">
                      <h3 className="text-white font-bold text-lg leading-tight mb-1">{post.title}</h3>
                      {post.description && <p className="text-white/70 text-xs line-clamp-1">{post.description}</p>}
                    </div>
                  </a>
                </CardContent>
              </Card>
            </div>
          </CarouselItem>
        ))}
      </CarouselContent>
      <CarouselPrevious className="left-2 opacity-0 group-hover:opacity-100 transition-opacity" />
      <CarouselNext className="right-2 opacity-0 group-hover:opacity-100 transition-opacity" />
    </Carousel>
  )
}

function CmsLayout({ posts, className }: { posts: CarouselPost[], className?: string }) {
  // Use first 5 posts for the layout if available, or repeat
  // Left: Carousel (posts)
  // Right: 3 static items (posts[0], posts[1], posts[2] or specific logic)
  
  // We'll use the same posts for the carousel, and the first 3 for the side list
  const sidePosts = posts.slice(0, 3);

  return (
    <div className={`grid grid-cols-1 lg:grid-cols-3 gap-4 h-full ${className || ''}`}>
      <div className="lg:col-span-2 h-full min-h-[300px] lg:min-h-[400px]">
        <FullLayout posts={posts} className="h-full [&_.aspect-\[21\/9\]]:aspect-auto [&_.aspect-\[21\/9\]]:h-full" />
      </div>
      <div className="lg:col-span-1 flex flex-col gap-4 h-full">
        {sidePosts.map((post, i) => (
          <a key={i} href={post.url} target={post.target} className="relative flex-1 overflow-hidden rounded-lg group block min-h-[120px]">
            <img src={post.thumbnail} alt={post.title} className="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <div className="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors" />
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end p-4">
              <h4 className="text-white text-sm font-bold line-clamp-2">{post.title}</h4>
            </div>
          </a>
        ))}
      </div>
    </div>
  )
}
