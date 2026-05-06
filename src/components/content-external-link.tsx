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
import { joinTranslations } from '@/lib/i18n';

const { t } = joinTranslations();

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
            <EmptyTitle className="text-2xl font-bold text-destructive">{t('error_request')}</EmptyTitle>
            <EmptyDescription className="text-md mt-2">{t('current_error_request')}</EmptyDescription>
            <EmptyDescription className="text-md mt-2">
              {t('external_source_warning_manual_copy')}
            </EmptyDescription>
            <p className="text-sm text-muted-foreground bg-muted p-2 rounded max-w-md break-all select-all">
              {url}
            </p>
          </EmptyHeader>
          <EmptyContent className="mt-8 flex flex-col items-center gap-4">
            <Button variant="outline" asChild>
              <a href="/" className="flex items-center gap-2">
                <Home className="h-4 w-4" />
                {t('return_to_home')}
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
          <EmptyTitle className="text-2xl font-bold">{t('you_are_leaving')}</EmptyTitle>
          <EmptyDescription className="text-md mt-2">
            {t('you_are_leaving_description')}
          </EmptyDescription>
          <p className="text-sm text-muted-foreground bg-muted p-2 rounded max-w-md break-all">
            {url}
          </p>
        </EmptyHeader>
        <EmptyContent className="mt-8 flex flex-col items-center gap-4">
          <Button asChild>
            <a href={url} target="_blank" rel="noopener noreferrer" className="flex items-center gap-2">
              {t('go_to')}
              <ArrowRight className="h-4 w-4" />
            </a>
          </Button>
        </EmptyContent>
      </Empty>
    </div>
  )
}
