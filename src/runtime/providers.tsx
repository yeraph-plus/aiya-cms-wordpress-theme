import { ThemeProvider } from "next-themes";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Toaster } from "@/components/ui/sonner";
import {
    Component,
    StrictMode,
    useEffect,
    useRef,
    useState,
    type ErrorInfo,
    type ReactNode,
} from "react";

import { getConfig } from "@/lib/utils";

type ProvidersProps = {
    children: ReactNode;
    tooltipDelayDuration?: number;
    boundaryId?: string;
};

const toasterSubscribers = new Set<() => void>();
let toasterOwner: symbol | null = null;

function notifyToasterSubscribers() {
    toasterSubscribers.forEach((listener) => {
        listener();
    });
}

function useSingletonToasterOwner() {
    const ownerRef = useRef<symbol>(Symbol('providers-toaster-owner'));
    const [isOwner, setIsOwner] = useState(false);

    useEffect(() => {
        const syncOwner = () => {
            if (toasterOwner === null) {
                toasterOwner = ownerRef.current;
            }

            setIsOwner(toasterOwner === ownerRef.current);
        };

        toasterSubscribers.add(syncOwner);
        syncOwner();

        return () => {
            toasterSubscribers.delete(syncOwner);

            if (toasterOwner === ownerRef.current) {
                toasterOwner = null;
                notifyToasterSubscribers();
            }
        };
    }, []);

    return isOwner;
}

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

export default function Providers({ children, tooltipDelayDuration = 0, boundaryId = 'unknown' }: ProvidersProps) {
    const theme = getConfig().defaultColorTheme; // 从HTML读取设置
    const isToasterOwner = useSingletonToasterOwner();

    return (
        <StrictMode>
            <ThemeProvider attribute="class" defaultTheme={theme || 'system'} enableSystem>
                <TooltipProvider delayDuration={tooltipDelayDuration}>
                    <IslandErrorBoundary id={boundaryId}>
                        {children}
                    </IslandErrorBoundary>
                </TooltipProvider>
                {isToasterOwner ? <Toaster /> : null}
            </ThemeProvider>
        </StrictMode>
    );
}
