import { useState, useEffect } from "react";
import { ArrowUp } from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Kbd, KbdGroup } from "@/components/ui/kbd"
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";

export default function BackToTop() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const toggleVisibility = () => {
      if (window.scrollY > 300) {
        setIsVisible(true);
      } else {
        setIsVisible(false);
      }
    };

    const handleKeyDown = (event: KeyboardEvent) => {
      // Check for Ctrl+ArrowUp
      if (event.ctrlKey && event.key === "ArrowUp") {
        event.preventDefault();
        scrollToTop();
      }
    };

    window.addEventListener("scroll", toggleVisibility);
    window.addEventListener("keydown", handleKeyDown);

    return () => {
      window.removeEventListener("scroll", toggleVisibility);
      window.removeEventListener("keydown", handleKeyDown);
    };
  }, []);

  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };

  return (
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger asChild>
          <Button
            variant="secondary"
            size="icon"
            className={cn(
              "fixed bottom-8 right-8 z-50 rounded-full shadow-lg transition-all duration-300 hover:shadow-xl w-12 h-12",
              isVisible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-4 pointer-events-none"
            )}
            onClick={scrollToTop}
            aria-label="Back to top"
          >
            <ArrowUp className="h-6 w-6" />
          </Button>
        </TooltipTrigger>
        <TooltipContent side="left">
          <p>返回顶部 <KbdGroup><Kbd>Ctrl</Kbd><span>+</span><Kbd>↑</Kbd></KbdGroup></p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  );
}
