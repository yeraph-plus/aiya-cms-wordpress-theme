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
import { Cookie } from "lucide-react";
import { cn } from "@/lib/utils";
import { useUiPreferencesStore } from "@/stores/ui-preferences";

export default function CookieConsent({ policyUrl }: { policyUrl?: string }) {
  const [isVisible, setIsVisible] = useState(false);
  const cookieConsentDecision = useUiPreferencesStore((state) => state.cookieConsentDecision);
  const setCookieConsentDecision = useUiPreferencesStore((state) => state.setCookieConsentDecision);

  useEffect(() => {
    if (cookieConsentDecision === null) {
      // Small delay for better UX
      const timer = setTimeout(() => setIsVisible(true), 1000);
      return () => clearTimeout(timer);
    }

    setIsVisible(false);
  }, [cookieConsentDecision]);

  const handleAccept = () => {
    setCookieConsentDecision("accepted");
    setIsVisible(false);
  };

  const handleDecline = () => {
    setCookieConsentDecision("declined");
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
          <Cookie className="h-5 w-5 text-primary" />
          <CardTitle className="text-lg">Cookie 使用提示</CardTitle>
        </CardHeader>
        <CardContent className="pb-2">
          <CardDescription>
            我们使用 Cookie 来提升您的浏览体验，分析网站流量并提供个性化内容。继续使用本网站即表示您同意我们使用 Cookie。
            {policyUrl && (
              <a href={policyUrl} className="underline hover:text-primary ml-1" target="_blank" rel="noopener noreferrer">
                了解更多
              </a>
            )}
          </CardDescription>
        </CardContent>
        <CardFooter className="justify-end gap-2">
           <Button variant="outline" size="sm" onClick={handleDecline}>
            拒绝
          </Button>
          <Button size="sm" onClick={handleAccept}>
            我同意
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
}
