import { ThemeProvider } from "next-themes";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Toaster } from "@/components/ui/sonner";
import {
    Component,
    StrictMode,
    type ErrorInfo,
    type ReactNode,
} from "react";

import { getConfig } from "@/lib/utils";

type ProvidersProps = {
    children: ReactNode;
    tooltipDelayDuration?: number;
    boundaryId?: string;
    withToaster?: boolean;
};

class IslandErrorBoundary extends Component<{ children: ReactNode; id: string }, { hasError: boolean }> {
    constructor(props: { children: ReactNode; id: string }) {
        super(props);
        this.state = { hasError: false };
    }

    static getDerivedStateFromError() {
        return { hasError: true };
    }

    componentDidCatch(error: Error, errorInfo: ErrorInfo) {
        console.error(`Island ${this.props.id} crashed:`, error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return <div data-island-error={`组件 ${this.props.id} 渲染失败`} />;
        }

        return this.props.children;
    }
}

export default function Providers({
    children,
    tooltipDelayDuration = 0,
    boundaryId = 'unknown',
    withToaster = false,
}: ProvidersProps) {
    const theme = getConfig().defaultColorTheme; // 从HTML读取设置

    return (
        <StrictMode>
            <ThemeProvider attribute="class" defaultTheme={theme || 'system'} enableSystem>
                <TooltipProvider delayDuration={tooltipDelayDuration}>
                    <IslandErrorBoundary id={boundaryId}>
                        {children}
                    </IslandErrorBoundary>
                </TooltipProvider>
                {withToaster ? <Toaster /> : null}
            </ThemeProvider>
        </StrictMode>
    );
}

export function GlobalToasterProviders() {
    const theme = getConfig().defaultColorTheme; // 从HTML读取设置

    return (
        <StrictMode>
            <ThemeProvider attribute="class" defaultTheme={theme || 'system'} enableSystem>
                <Toaster />
            </ThemeProvider>
        </StrictMode>
    );
}
