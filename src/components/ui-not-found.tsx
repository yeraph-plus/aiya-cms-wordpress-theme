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
import { Terminal, ArrowLeft } from "lucide-react"

export default function NotFound() {
  return (
    <div className="w-full h-[60vh] flex items-center justify-center">
      <Empty className="border-none">
        <EmptyHeader>
          <EmptyMedia variant="icon" className="mb-4">
            <Terminal className="h-8 w-8 text-muted-foreground" />
          </EmptyMedia>
          <EmptyTitle className="text-2xl font-bold">
            {__('页面未找到', 'aiya-cms')}
          </EmptyTitle>
          <EmptyDescription className="text-md mt-2">
            {__('抱歉，您访问的页面不存在或已被移除', 'aiya-cms')}
          </EmptyDescription>
        </EmptyHeader>
        <EmptyContent className="mt-8">
          <Button variant="outline" asChild>
            <a href="/" className="flex items-center gap-2">
              <ArrowLeft className="h-4 w-4" />
              {__('返回首页', 'aiya-cms')}
            </a>
          </Button>
        </EmptyContent>
      </Empty>
    </div>
  )
}
