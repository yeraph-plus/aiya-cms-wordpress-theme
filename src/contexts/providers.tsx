import { ThemeProvider } from "next-themes";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Toaster } from "@/components/ui/sonner";
import { StrictMode, type ReactNode } from "react";

import { getConfig } from "@/lib/utils";

export default function Providers({ children, tooltipDelayDuration = 0 }: { children: ReactNode; tooltipDelayDuration?: number }) {
    const theme = getConfig().defaultColorTheme; // 从HTML读取设置

    return (
        <StrictMode>
            <ThemeProvider attribute="class" defaultTheme={theme || 'system'} enableSystem>
                <TooltipProvider delayDuration={tooltipDelayDuration}>
                    {children}
                </TooltipProvider>
                <Toaster />
            </ThemeProvider>
        </StrictMode>
    );
}