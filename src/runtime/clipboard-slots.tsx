import * as React from "react"
import ClipboardJS from "clipboard"
import { createRoot, type Root } from "react-dom/client"

import {
    Check,
    Copy
} from "lucide-react";

import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip"

const SLOT_SELECTOR = "[data-clipboard-slot]"
const PROCESSED_ATTR = "data-clipboard-hydrated"

const clipboardRootMap = new WeakMap<HTMLElement, Root>()
let clipboardObserver: MutationObserver | null = null

function ClipboardInline({ slot }: { slot: HTMLElement }) {
    const buttonRef = React.useRef<HTMLButtonElement | null>(null)
    const defaultLabel = "点击复制"
    const [text] = React.useState(() => slot.textContent?.trim() || "")

    const [tooltipLabel, setTooltipLabel] = React.useState(defaultLabel)
    const [copyState, setCopyState] = React.useState<"idle" | "success" | "error">("idle")

    React.useEffect(() => {
        const button = buttonRef.current

        if (!button || !text) {
            return
        }

        const clipboard = new ClipboardJS(button, {
            text: () => text,
        })

        clipboard.on("success", (event) => {
            event.clearSelection()
            setCopyState("success")
            setTooltipLabel("已复制")
        })

        clipboard.on("error", () => {
            setCopyState("error")
            setTooltipLabel("复制失败")
        })

        return () => {
            clipboard.destroy()
        }
    }, [defaultLabel, text])

    React.useEffect(() => {
        setCopyState("idle")
        setTooltipLabel(defaultLabel)
    }, [defaultLabel, text])

    if (!text) {
        return null
    }

    return (
        <div className="inline-flex items-center align-baseline">
            <TooltipProvider delayDuration={0}>
                <Tooltip>
                    <TooltipTrigger asChild>
                        <button
                            ref={buttonRef}
                            type="button"
                            aria-label={defaultLabel}
                            className="inline-flex items-center gap-1.5 rounded-md border border-border bg-muted/40 px-2 py-1 text-[0.9em] text-foreground transition-colors hover:bg-muted/70"
                        >
                            <span>{text}</span>
                            {copyState === "success" ? (
                                <Check className="h-3.5 w-3.5 text-emerald-600" />
                            ) : (
                                <Copy className="h-3.5 w-3.5 text-muted-foreground" />
                            )}
                            <span className="sr-only">{defaultLabel}</span>
                        </button>
                    </TooltipTrigger>
                    <TooltipContent side="top" sideOffset={6}>
                        {tooltipLabel}
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>
    )
}

function collectClipboardSlots(node: ParentNode): HTMLElement[] {
    const slots: HTMLElement[] = []

    if (node instanceof HTMLElement && node.matches(SLOT_SELECTOR)) {
        slots.push(node)
    }

    if ("querySelectorAll" in node) {
        node.querySelectorAll<HTMLElement>(SLOT_SELECTOR).forEach((slot) => {
            slots.push(slot)
        })
    }

    return slots
}

function hydrateClipboardSlot(slot: HTMLElement): void {
    if (slot.getAttribute(PROCESSED_ATTR) === "true" || clipboardRootMap.has(slot)) {
        return
    }

    slot.setAttribute(PROCESSED_ATTR, "true")

    const root = createRoot(slot)
    clipboardRootMap.set(slot, root)
    root.render(<ClipboardInline slot={slot} />)
}

export function hydrateClipboardSlots(root: ParentNode = document): void {
    collectClipboardSlots(root).forEach(hydrateClipboardSlot)
}

export function bootClipboardSlots(root: Document | HTMLElement = document): void {
    const observeTarget = root instanceof Document ? root.body : root

    hydrateClipboardSlots(root)

    if (!observeTarget || clipboardObserver) {
        return
    }

    clipboardObserver = new MutationObserver((records) => {
        records.forEach((record) => {
            record.addedNodes.forEach((node) => {
                if (node instanceof HTMLElement) {
                    hydrateClipboardSlots(node)
                }
            })
        })
    })

    clipboardObserver.observe(observeTarget, {
        childList: true,
        subtree: true,
    })
}
