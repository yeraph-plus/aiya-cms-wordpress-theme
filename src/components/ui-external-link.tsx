import { __ } from '@wordpress/i18n';

import {
  Empty,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
  EmptyDescription,
  EmptyContent,
} from "@/components/ui/empty"
import { Button } from "@/components/ui/button"
import { Link, Unlink, Home, ArrowRight } from "lucide-react"

interface UiExternalLinkProps {
  url: string
  checked: boolean
}

export default function UiExternalLink({ url, checked }: UiExternalLinkProps) {
  if (!checked) {
    return (
      <div className="w-full h-[60vh] flex items-center justify-center">
        <Empty className="border-none">
          <EmptyHeader>
            <EmptyMedia variant="icon" className="mb-4">
              <Unlink className="h-8 w-8 text-destructive" />
            </EmptyMedia>
            <EmptyTitle className="text-2xl font-bold text-destructive">{__('错误请求', 'aiya-cms')}</EmptyTitle>
            <EmptyDescription className="text-md mt-2">{__('当前错误请求', 'aiya-cms')}</EmptyDescription>
            <EmptyDescription className="text-md mt-2">
              {__('当前页面是通过外部来源打开的，与本站点无关，如需访问请请手动复制链接。', 'aiya-cms')}
            </EmptyDescription>
            <p className="text-sm text-muted-foreground bg-muted p-2 rounded max-w-md break-all select-all">
              {url}
            </p>
          </EmptyHeader>
          <EmptyContent className="mt-8 flex flex-col items-center gap-4">
            <Button variant="outline" asChild>
              <a href="/" className="flex items-center gap-2">
                <Home className="h-4 w-4" />
                {__('返回首页', 'aiya-cms')}
              </a>
            </Button>
          </EmptyContent>
        </Empty>
      </div>
    )
  }

  return (
    <div className="w-full h-[60vh] flex items-center justify-center">
      <Empty className="border-none">
        <EmptyHeader>
          <EmptyMedia variant="icon" className="mb-4">
            <Link className="h-8 w-8 text-muted-foreground" />
          </EmptyMedia>
          <EmptyTitle className="text-2xl font-bold">{__('您即将离开', 'aiya-cms')}</EmptyTitle>
          <EmptyDescription className="text-md mt-2">
            {__('您即将离开本站，此链接将带您前往外部网站。', 'aiya-cms')}
          </EmptyDescription>
          <p className="text-sm text-muted-foreground bg-muted p-2 rounded max-w-md break-all">
            {url}
          </p>
        </EmptyHeader>
        <EmptyContent className="mt-8 flex flex-col items-center gap-4">
          <Button asChild>
            <a href={url} target="_blank" rel="noopener noreferrer" className="flex items-center gap-2">
              {__('前往', 'aiya-cms')}
              <ArrowRight className="h-4 w-4" />
            </a>
          </Button>
        </EmptyContent>
      </Empty>
    </div>
  )
}
