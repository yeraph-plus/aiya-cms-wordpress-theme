import {
  Empty,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
  EmptyDescription,
  EmptyContent,
} from "@/components/ui/empty"
import { Button } from "@/components/ui/button"
import { Terminal, Bot, ArrowLeft } from "lucide-react"

import { joinTranslations } from '@/lib/i18n';

const { t } = joinTranslations();

type NotFoundProps = {
  slug?: string
  title?: string
  description?: string
}

export default function NotFound({
  slug,
  title = "404 NOT FOUND",
  description = "",
}: NotFoundProps) {
  return (
    <div className="w-full h-[60vh] flex items-center justify-center">
      <Empty className="border-none">
        <EmptyHeader>
          <EmptyMedia variant="icon" className="mb-4">
            {slug === '404' ? (
              <Terminal className="h-8 w-8 text-muted-foreground" />
            ) : (
              <Bot className="h-8 w-8 text-muted-foreground" />
            )}
          </EmptyMedia>
          <EmptyTitle className="text-2xl font-bold">
            {title}
          </EmptyTitle>
          <EmptyDescription className="text-md mt-2">
            {description}
          </EmptyDescription>
        </EmptyHeader>
        <EmptyContent className="mt-8">
          <Button variant="outline" asChild>
            <a href="/" className="flex items-center gap-2">
              <ArrowLeft className="h-4 w-4" />
              {t('return_to_home')}
            </a>
          </Button>
        </EmptyContent>
      </Empty>
    </div>
  )
}
