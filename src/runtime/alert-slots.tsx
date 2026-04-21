import { createElement } from "react"
import { renderToStaticMarkup } from "react-dom/server"
import {
  AlertTriangle,
  CheckCircle2,
  Info,
  XCircle,
  type LucideIcon,
} from "lucide-react"

import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"

const SLOT_SELECTOR = "[data-alert-slot]"
const PROCESSED_ATTR = "data-alert-hydrated"

type AlertLevel = "default" | "warning" | "info" | "success" | "error"

type AlertMeta = {
  Icon: LucideIcon
  variant?: "default" | "destructive"
  className: string
  descriptionClassName?: string
}

const alertMetaMap: Record<AlertLevel, AlertMeta> = {
  default: {
    Icon: AlertTriangle,
    variant: "default",
    className:
      "border-border bg-card text-card-foreground [&>svg]:text-muted-foreground",
  },
  warning: {
    Icon: AlertTriangle,
    variant: "default",
    className:
      "border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-100 [&>svg]:text-amber-500",
    descriptionClassName: "text-amber-900/80 dark:text-amber-100/80",
  },
  info: {
    Icon: Info,
    variant: "default",
    className:
      "border-sky-200 bg-sky-50 text-sky-950 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-sky-100 [&>svg]:text-sky-500",
    descriptionClassName: "text-sky-900/80 dark:text-sky-100/80",
  },
  success: {
    Icon: CheckCircle2,
    variant: "default",
    className:
      "border-emerald-200 bg-emerald-50 text-emerald-950 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-100 [&>svg]:text-emerald-500",
    descriptionClassName: "text-emerald-900/80 dark:text-emerald-100/80",
  },
  error: {
    Icon: XCircle,
    variant: "destructive",
    className:
      "border-rose-200 bg-rose-50 dark:border-rose-900/60 dark:bg-rose-950/30 [&>svg]:text-rose-500",
    descriptionClassName: "text-rose-900/80 dark:text-rose-100/80",
  },
}

function getAlertLevel(slot: HTMLElement): AlertLevel {
  const rawLevel = slot.dataset.alertLevel?.trim().toLowerCase()

  if (!rawLevel) {
    return "default"
  }

  if (rawLevel === "warning" || rawLevel === "info" || rawLevel === "success" || rawLevel === "error") {
    return rawLevel
  }

  return "default"
}

function getAlertMarkup(level: AlertLevel, title: string, description: string): string {
  const meta = alertMetaMap[level]

  return renderToStaticMarkup(
    createElement(
      Alert,
      { variant: meta.variant, className: meta.className },
      createElement(meta.Icon, {
        className: "h-4 w-4",
        "aria-hidden": true,
        focusable: false,
      }),
      createElement(AlertTitle, null, title),
      createElement(
        AlertDescription,
        { className: meta.descriptionClassName },
        createElement("p", null, description)
      )
    )
  )
}

function hydrateAlertSlot(slot: HTMLElement): void {
  if (slot.getAttribute(PROCESSED_ATTR) === "true") {
    return
  }

  const title = slot.dataset.alertTitle?.trim() ?? ""
  const description = slot.dataset.alertDescription?.trim() ?? ""

  if (!title && !description) {
    return
  }

  slot.setAttribute(PROCESSED_ATTR, "true")
  slot.className = ""
  slot.innerHTML = getAlertMarkup(getAlertLevel(slot), title, description)
}

function collectAlertSlots(node: ParentNode): HTMLElement[] {
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

export function hydrateAlertSlots(root: ParentNode = document): void {
  collectAlertSlots(root).forEach(hydrateAlertSlot)
}

let alertSlotObserver: MutationObserver | null = null

export function bootAlertSlots(root: Document | HTMLElement = document): void {
  const observeTarget = root instanceof Document ? root.body : root

  hydrateAlertSlots(root)

  if (!observeTarget || alertSlotObserver) {
    return
  }

  alertSlotObserver = new MutationObserver((records) => {
    records.forEach((record) => {
      record.addedNodes.forEach((node) => {
        if (node instanceof HTMLElement) {
          hydrateAlertSlots(node)
        }
      })
    })
  })

  alertSlotObserver.observe(observeTarget, {
    childList: true,
    subtree: true,
  })
}
