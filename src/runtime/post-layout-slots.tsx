import {
  type LoopGridLayout,
  usePreferencesStore,
} from "@/stores/ui-preferences";

const MAIN_GRID_ID = "main-post-loop";
const ROOT_SELECTOR = `[data-post-grid-root]#${MAIN_GRID_ID}`;
const CONTROL_SELECTOR = `[data-post-grid-controls][data-target-id="${MAIN_GRID_ID}"]`;
const ACTIVE_BUTTON_CLASS = "bg-secondary text-foreground";
const INACTIVE_BUTTON_CLASS = "text-muted-foreground hover:bg-accent";

function setButtonState(
  button: HTMLButtonElement,
  active: boolean
): void {
  button.setAttribute("aria-pressed", active ? "true" : "false");
  button.dataset.active = active ? "true" : "false";
  button.classList.toggle("bg-secondary", active);
  button.classList.toggle("text-foreground", active);
  button.classList.toggle("text-muted-foreground", !active);
  button.classList.toggle("hover:bg-accent", !active);

  if (active) {
    button.classList.remove(...INACTIVE_BUTTON_CLASS.split(" "));
    button.classList.add(...ACTIVE_BUTTON_CLASS.split(" "));
    return;
  }

  button.classList.remove(...ACTIVE_BUTTON_CLASS.split(" "));
  button.classList.add(...INACTIVE_BUTTON_CLASS.split(" "));
}

function applyLayoutToGrid(root: HTMLElement, layout: LoopGridLayout): void {
  root.dataset.layout = layout;

  root.classList.remove(
    "grid-cols-2",
    "md:grid-cols-3",
    "lg:grid-cols-4",
    "xl:grid-cols-4",
    "2xl:grid-cols-5",
    "grid-cols-1",
    "gap-4"
  );

  if (layout === "grid") {
    root.classList.add(
      "grid-cols-2",
      "md:grid-cols-3",
      "lg:grid-cols-4",
      "xl:grid-cols-4",
      "2xl:grid-cols-5"
    );
  } else {
    root.classList.add("grid-cols-1", "gap-4");
  }

  root.querySelectorAll<HTMLElement>("[data-post-grid-card]").forEach((card) => {
    card.classList.remove(
      "flex-col",
      "flex-row",
      "h-48",
      "border-0",
      "ring-1",
      "ring-border",
      "border"
    );

    if (layout === "grid") {
      card.classList.add("flex-col", "border-0", "ring-1", "ring-border");
    } else {
      card.classList.add("flex-row", "h-48", "border");
    }
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-media]").forEach((media) => {
    media.classList.remove(
      "w-full",
      "h-40",
      "md:h-44",
      "xl:h-48",
      "rounded-t-lg",
      "rounded-t-xl",
      "rounded-none",
      "w-1/3",
      "min-w-[200px]",
      "max-w-[300px]",
      "h-full",
      "shrink-0"
    );

    if (layout === "grid") {
      media.classList.add("w-full", "h-40", "md:h-44", "xl:h-48", "rounded-t-lg");
    } else {
      media.classList.add("w-1/3", "min-w-[200px]", "max-w-[300px]", "h-full", "shrink-0", "rounded-none");
    }
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-body]").forEach((body) => {
    body.classList.toggle("min-w-0", layout === "list");
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-categories]").forEach((categories) => {
    categories.classList.toggle("mb-2", layout === "grid");
    categories.classList.toggle("mb-4", layout === "list");
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-title]").forEach((title) => {
    title.classList.remove("text-md", "md:text-lg", "text-lg");

    if (layout === "grid") {
      title.classList.add("text-md", "md:text-lg");
    } else {
      title.classList.add("text-lg");
    }
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-preview]").forEach((preview) => {
    preview.classList.toggle("flex-1", layout === "list");
  });

  root.querySelectorAll<HTMLElement>("[data-post-grid-footer]").forEach((footer) => {
    footer.classList.remove("pt-4", "pt-2");
    footer.classList.add(layout === "grid" ? "pt-4" : "pt-2");
  });
}

function updateControlState(layout: LoopGridLayout, root: ParentNode): void {
  const controls = root.querySelector<HTMLElement>(CONTROL_SELECTOR);

  if (!controls) {
    return;
  }

  controls
    .querySelectorAll<HTMLButtonElement>("[data-post-grid-toggle]")
    .forEach((button) => {
      setButtonState(button, button.dataset.postGridToggle === layout);
    });
}

function applyLayout(layout: LoopGridLayout, root: ParentNode): void {
  const mainGrid = root.querySelector<HTMLElement>(ROOT_SELECTOR);

  if (!mainGrid) {
    return;
  }

  applyLayoutToGrid(mainGrid, layout);

  updateControlState(layout, root);
}

export function bootPostGridLayout(root: ParentNode = document): void {
  const mainGrid = root.querySelector<HTMLElement>(ROOT_SELECTOR);

  if (!mainGrid) {
    return;
  }

  const controls = root.querySelector<HTMLElement>(CONTROL_SELECTOR);
  const currentLayout = usePreferencesStore.getState().loopGridLayout;
  applyLayoutToGrid(mainGrid, currentLayout);
  updateControlState(currentLayout, root);

  if (!controls) {
    return;
  }

  controls
    .querySelectorAll<HTMLButtonElement>("[data-post-grid-toggle]")
    .forEach((button) => {
      button.addEventListener("click", () => {
        const nextLayout = button.dataset.postGridToggle as LoopGridLayout;

        if (nextLayout !== "grid" && nextLayout !== "list") {
          return;
        }

        usePreferencesStore.getState().setLoopGridLayout(nextLayout);
        applyLayout(nextLayout, root);
      });
    });
}
