import { createElement } from "react";
import { renderToStaticMarkup } from "react-dom/server";
import {
  AlertTriangle,
  ArrowLeft,
  ArrowRight,
  CalendarDays,
  CalendarClock,
  CheckCircle,
  Copy,
  ChevronRight,
  CircleDashed,
  Clock3,
  LayoutGrid,
  LayoutList,
  Eye,
  EyeOff,
  FileEdit,
  Folder,
  Heart,
  Hourglass,
  Inbox,
  ContactRound,
  Layers2,
  Link,
  Lock,
  MessageSquare,
  Navigation,
  Pin,
  Sparkles,
  Tag,
  Search,
  House,
  LockKeyhole,
  Trash2,
  type LucideIcon,
} from "lucide-react";

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
  copy: Copy,
  "layout-grid": LayoutGrid,
  "layout-list": LayoutList,
  "circle-dashed": CircleDashed,
  folder: Folder,
  tag: Tag,
  alert: AlertTriangle,
  search: Search,
  house: House,
  "lock-keyhole": LockKeyhole,
  "contact-round": ContactRound,
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

const SLOT_SELECTOR = "span.icon-slot[data-icon]";
const DEFAULT_ICON_CLASS = "h-4 w-4";
const PROCESSED_ATTR = "data-icon-hydrated";
const ICON_SIZE_CLASS_MAP: Record<string, string> = {
  "3": "h-3 w-3",
  "3.5": "h-3.5 w-3.5",
  "4": "h-4 w-4",
  "4.5": "h-4.5 w-4.5",
  "5": "h-5 w-5",
  "6": "h-6 w-6",
  "7": "h-7 w-7",
  "8": "h-8 w-8",
};

let iconSlotObserver: MutationObserver | null = null;

function getBlankIconMarkup(iconClassName: string): string {
  return renderToStaticMarkup(
    createElement("span", {
      className: `inline-block ${iconClassName}`,
      "aria-hidden": true,
    })
  );
}

function getSlotIconMarkup(iconName: string, iconClassName: string): string {
  const Icon = iconMap[iconName];

  if (!Icon) {
    console.warn(`[Runtime] IconSlots.tsx Unknown icon "${iconName}", fallback to blank placeholder.`);
    return getBlankIconMarkup(iconClassName);
  }

  return renderToStaticMarkup(
    createElement(Icon, {
      className: iconClassName,
      "aria-hidden": true,
      focusable: false,
    })
  );
}

function resolveIconClassName(slot: HTMLElement): string {
  const size = slot.dataset.iconSize?.trim();
  if (size) {
    const mappedClass = ICON_SIZE_CLASS_MAP[size];
    if (mappedClass) {
      return mappedClass;
    }

    console.warn(`[Runtime] IconSlots.tsx Unknown icon size "${size}", fallback to default size.`);
  }

  return DEFAULT_ICON_CLASS;
}

function hydrateIconSlot(slot: HTMLElement): void {
  if (slot.getAttribute(PROCESSED_ATTR) === "true") {
    return;
  }

  const iconName = slot.dataset.icon;

  if (!iconName) {
    return;
  }

  const iconMarkup = getSlotIconMarkup(iconName, resolveIconClassName(slot));

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
