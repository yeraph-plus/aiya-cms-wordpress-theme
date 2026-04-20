import { createElement } from "react";
import { renderToStaticMarkup } from "react-dom/server";
import {
  AlertTriangle,
  ArrowLeft,
  ArrowRight,
  CalendarDays,
  CalendarClock,
  CheckCircle,
  ChevronRight,
  CircleDashed,
  Clock3,
  Eye,
  EyeOff,
  FileEdit,
  Folder,
  Heart,
  Hourglass,
  Inbox,
  Layers2,
  Link,
  Lock,
  MessageSquare,
  Navigation,
  Pin,
  Sparkles,
  Tag,
  Trash2,
  type LucideIcon,
} from "lucide-react";

const SLOT_SELECTOR = "span.icon-slot[data-icon]";
const DEFAULT_ICON_CLASS = "h-4 w-4";
const PROCESSED_ATTR = "data-icon-hydrated";

const iconMap: Record<string, LucideIcon> = {
  navigation: Navigation,
  "chevron-right": ChevronRight,
  clock: Clock3,
  comments: MessageSquare,
  heart: Heart,
  views: Eye,
  calendar: CalendarDays,
  "calendar-clock": CalendarClock,
  "check-circle": CheckCircle,
  "circle-dashed": CircleDashed,
  folder: Folder,
  tag: Tag,
  alert: AlertTriangle,
  "arrow-left": ArrowLeft,
  "arrow-right": ArrowRight,
  "eye-off": EyeOff,
  "file-edit": FileEdit,
  hourglass: Hourglass,
  inbox: Inbox,
  "layers-2": Layers2,
  link: Link,
  lock: Lock,
  pin: Pin,
  sparkles: Sparkles,
  "trash-2": Trash2,
};

let iconSlotObserver: MutationObserver | null = null;

function getSlotIconMarkup(iconName: string, iconClassName: string): string {
  const Icon = iconMap[iconName];

  if (!Icon) {
    return "";
  }

  return renderToStaticMarkup(
    createElement(Icon, {
      className: iconClassName,
      "aria-hidden": true,
      focusable: false,
    })
  );
}

function hydrateIconSlot(slot: HTMLElement): void {
  if (slot.getAttribute(PROCESSED_ATTR) === "true") {
    return;
  }

  const iconName = slot.dataset.icon;

  if (!iconName) {
    return;
  }

  const iconMarkup = getSlotIconMarkup(iconName, slot.dataset.iconClass || DEFAULT_ICON_CLASS);

  if (iconMarkup === "") {
    console.warn(`[Icon Slots] Unknown icon "${iconName}"`, slot);
    return;
  }

  slot.setAttribute(PROCESSED_ATTR, "true");
  slot.setAttribute("aria-hidden", "true");
  slot.innerHTML = iconMarkup;
}

function collectIconSlots(node: ParentNode): HTMLElement[] {
  const slots: HTMLElement[] = [];

  if (node instanceof HTMLElement && node.matches(SLOT_SELECTOR)) {
    slots.push(node);
  }

  if ("querySelectorAll" in node) {
    node.querySelectorAll<HTMLElement>(SLOT_SELECTOR).forEach((slot) => {
      slots.push(slot);
    });
  }

  return slots;
}

export function hydrateIconSlots(root: ParentNode = document): void {
  collectIconSlots(root).forEach(hydrateIconSlot);
}

export function bootIconSlots(root: Document | HTMLElement = document): void {
  const observeTarget = root instanceof Document ? root.body : root;

  hydrateIconSlots(root);

  if (!observeTarget || iconSlotObserver) {
    return;
  }

  iconSlotObserver = new MutationObserver((records) => {
    records.forEach((record) => {
      record.addedNodes.forEach((node) => {
        if (node instanceof HTMLElement) {
          hydrateIconSlots(node);
        }
      });
    });
  });

  iconSlotObserver.observe(observeTarget, {
    childList: true,
    subtree: true,
  });
}
