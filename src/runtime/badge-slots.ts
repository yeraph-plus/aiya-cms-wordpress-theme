import { createElement } from "react";
import { renderToStaticMarkup } from "react-dom/server";
import {
  CalendarClock,
  CheckCircle,
  CircleDashed,
  EyeOff,
  FileEdit,
  Hourglass,
  Lock,
  Pin,
  Sparkles,
  Trash2,
  type LucideIcon,
} from "lucide-react";

const SLOT_SELECTOR =
  "span.badge-slot[data-badge-variant], span.badge-slot[data-badge-alias]";
const PROCESSED_ATTR = "data-badge-hydrated";
const BASE_BADGE_CLASS =
  "inline-flex items-center justify-center rounded-full border border-transparent px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 gap-1 transition-[color,box-shadow] overflow-hidden";

const badgeVariantMap: Record<string, string> = {
  default: "bg-primary text-primary-foreground",
  secondary: "bg-secondary text-secondary-foreground",
  destructive:
    "bg-destructive text-white focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40 dark:bg-destructive/60",
  outline: "border-border text-foreground",
  ghost: "",
  link: "text-primary underline-offset-4",
};

const badgeAliasMap: Record<
  string,
  {
    variant: keyof typeof badgeVariantMap;
    icon: string;
  }
> = {
  sticky: {
    variant: "default",
    icon: "pin",
  },
  newest: {
    variant: "secondary",
    icon: "sparkles",
  },
  password: {
    variant: "secondary",
    icon: "lock",
  },
  private: {
    variant: "destructive",
    icon: "eye-off",
  },
  pending: {
    variant: "secondary",
    icon: "hourglass",
  },
  future: {
    variant: "outline",
    icon: "calendar-clock",
  },
  draft: {
    variant: "outline",
    icon: "file-edit",
  },
  "auto-draft": {
    variant: "outline",
    icon: "circle-dashed",
  },
  trash: {
    variant: "destructive",
    icon: "trash-2",
  },
  default: {
    variant: "outline",
    icon: "check-circle",
  },
};

const badgeIconMap: Record<string, LucideIcon> = {
  pin: Pin,
  sparkles: Sparkles,
  lock: Lock,
  "eye-off": EyeOff,
  hourglass: Hourglass,
  "calendar-clock": CalendarClock,
  "file-edit": FileEdit,
  "circle-dashed": CircleDashed,
  "trash-2": Trash2,
  "check-circle": CheckCircle,
};

let badgeSlotObserver: MutationObserver | null = null;

function getBadgeIconMarkup(iconName: string, iconClassName: string): string {
  const Icon = badgeIconMap[iconName];

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

function ensureBadgeAliasIcon(
  slot: HTMLElement,
  iconName: string,
  iconClassName = "h-4 w-4"
): void {
  if (slot.querySelector(":scope > .badge-slot-icon")) {
    return;
  }

  const iconMarkup = getBadgeIconMarkup(iconName, iconClassName);

  if (iconMarkup === "") {
    console.warn(`[Badge Slots] Unknown badge icon "${iconName}"`, slot);
    return;
  }

  const iconSlot = document.createElement("span");
  iconSlot.className = "badge-slot-icon shrink-0";
  iconSlot.setAttribute("aria-hidden", "true");
  iconSlot.innerHTML = iconMarkup;
  slot.prepend(iconSlot);
}

function hydrateBadgeSlot(slot: HTMLElement): void {
  if (slot.getAttribute(PROCESSED_ATTR) === "true") {
    return;
  }

  const alias = slot.dataset.badgeAlias || "";
  const aliasConfig = alias ? badgeAliasMap[alias] || badgeAliasMap.default : null;
  const variant = slot.dataset.badgeVariant || aliasConfig?.variant || "default";
  const variantClass = badgeVariantMap[variant];

  if (variantClass === undefined) {
    console.warn(`[Badge Slots] Unknown badge variant "${variant}"`, slot);
    return;
  }

  slot.setAttribute(PROCESSED_ATTR, "true");
  slot.dataset.slot = "badge";
  slot.classList.add(...BASE_BADGE_CLASS.split(" "));

  if (variantClass !== "") {
    slot.classList.add(...variantClass.split(" "));
  }

  if (aliasConfig) {
    ensureBadgeAliasIcon(slot, aliasConfig.icon);
  }
}

function collectBadgeSlots(node: ParentNode): HTMLElement[] {
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

export function hydrateBadgeSlots(root: ParentNode = document): void {
  collectBadgeSlots(root).forEach(hydrateBadgeSlot);
}

export function bootBadgeSlots(root: Document | HTMLElement = document): void {
  const observeTarget = root instanceof Document ? root.body : root;

  hydrateBadgeSlots(root);

  if (!observeTarget || badgeSlotObserver) {
    return;
  }

  badgeSlotObserver = new MutationObserver((records) => {
    records.forEach((record) => {
      record.addedNodes.forEach((node) => {
        if (node instanceof HTMLElement) {
          hydrateBadgeSlots(node);
        }
      });
    });
  });

  badgeSlotObserver.observe(observeTarget, {
    childList: true,
    subtree: true,
  });
}
