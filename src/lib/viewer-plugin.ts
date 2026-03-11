import Viewer from "viewerjs"

export type InitViewerOptions = {
  container?: Element | null
  selector?: string
  force?: boolean
}

export function initViewer(options: InitViewerOptions = {}) {
  const container =
    options.container ??
    (options.selector
      ? document.querySelector(options.selector)
      : document.getElementById("article-content"))

  if (!container) return null

  const key = "viewerInitialized"

  if (!options.force && (container as HTMLElement).dataset[key] === "done") return null

  const images = container.querySelectorAll("img")

  if (images.length === 0) return null; (container as HTMLElement).dataset[key] = "done"

  const viewer = new Viewer(container as HTMLElement, {
    inline: false,
    toolbar: false,
    keyboard: false,
    url: 'src',
    viewed() {
      viewer.zoomTo(0.8);
    },
    ready() {
      //完成时添加一条Log
      console.log('ViewerJS wrapper is activated.');
    },
    show() {
      //显示时检查图片加载状态
      const currentImage = (viewer as any).image;

      if (currentImage && !currentImage.complete) {
        currentImage.onload = () => {
          viewer.zoomTo(0.8);
        };
      }
    }
  })

  return viewer
}
