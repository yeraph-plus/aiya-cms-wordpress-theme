import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Cookie, BotMessageSquare } from "lucide-react";
import { cn } from "@/lib/utils";
import { joinTranslations } from '@/lib/i18n';

import { usePreferencesStore } from "@/stores/ui-preferences";

const { t } = joinTranslations();

type FooterConsentText = {
  title?: string;
  description?: string;
  moreUrl?: string;
  moreText?: string;
  declineText?: string;
  acceptText?: string;
};

export default function FooterConsent({ slug, text }: { slug: string; text?: FooterConsentText }) {
  const [isVisible, setIsVisible] = useState(false);
  const consentDecision = usePreferencesStore((state) => state.consentDecisionBySlug[slug]);
  const setConsentDecision = usePreferencesStore((state) => state.setConsentDecision);

  useEffect(() => {
    if (consentDecision === undefined) {
      // Small delay for better UX
      const timer = setTimeout(() => setIsVisible(true), 1000);
      return () => clearTimeout(timer);
    }

    setIsVisible(false);
  }, [consentDecision]);

  const handleAccept = () => {
    setConsentDecision(slug, "accepted");
    setIsVisible(false);
  };

  const handleDecline = () => {
    setConsentDecision(slug, "declined");
    setIsVisible(false);
  };

  if (!isVisible) return null;

  return (
    <div className={cn(
      "fixed bottom-4 left-4 z-50 w-full max-w-[400px] transition-all duration-500 ease-in-out transform",
      isVisible ? "translate-y-0 opacity-100" : "translate-y-full opacity-0 pointer-events-none"
    )}>
      <Card className="shadow-lg border-primary/20 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <CardHeader className="flex flex-row items-center gap-2">
          {slug === 'cookie' ? (
            <Cookie className="h-5 w-5 text-primary" />
          ) : (
            <BotMessageSquare className="h-5 w-5 text-primary" />
          )}
          <CardTitle className="text-lg">{text?.title || t('tip')}</CardTitle>
        </CardHeader>
        <CardContent className="pb-2">
          <CardDescription>
            {text?.description && (
              <span
                dangerouslySetInnerHTML={{ __html: text?.description || "" }}
              />
            )}
            {text?.moreUrl && (
              <a href={text?.moreUrl} className="underline hover:text-primary ml-1" target="_blank" rel="noopener noreferrer">
                {text?.moreText || t('learn_more')}
              </a>
            )}
          </CardDescription>
        </CardContent>
        <CardFooter className="justify-end gap-2">
          {text?.declineText && (
            <Button variant="outline" size="sm" onClick={handleDecline}>
              {text?.declineText || t('cancel')}
            </Button>
          )}
          <Button size="sm" onClick={handleAccept}>
            {text?.acceptText || t('no_more_remind')}
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
}
