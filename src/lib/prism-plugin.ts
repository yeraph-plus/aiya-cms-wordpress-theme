type PrismCore = typeof import("prismjs")

let prismLoader: Promise<PrismCore> | null = null

function loadPrism() {
  if (prismLoader) {
    return prismLoader
  }

  prismLoader = (async () => {
    const prismModule = await import("prismjs")
    const Prism = (prismModule as { default?: PrismCore }).default ?? prismModule

    // Prism language/components expect a global Prism at execution time.
    ;(globalThis as typeof globalThis & { Prism?: PrismCore }).Prism = Prism

    await import("prismjs/plugins/line-numbers/prism-line-numbers")
    await import("prismjs/plugins/normalize-whitespace/prism-normalize-whitespace")
    await import("prismjs/plugins/toolbar/prism-toolbar")
    await import("prismjs/plugins/show-language/prism-show-language")
    await import("prismjs/components/prism-markup")
    await import("prismjs/components/prism-markup-templating")
    await import("prismjs/components/prism-javascript")
    await import("prismjs/components/prism-typescript")
    await import("prismjs/components/prism-css")
    await import("prismjs/components/prism-php")
    await import("prismjs/components/prism-ruby")
    await import("prismjs/components/prism-python")
    await import("prismjs/components/prism-java")
    await import("prismjs/components/prism-c")
    await import("prismjs/components/prism-cpp")
    await import("prismjs/components/prism-csharp")
    await import("prismjs/components/prism-yaml")
    await import("prismjs/components/prism-json")
    await import("prismjs/components/prism-sql")

    return Prism
  })().catch((error) => {
    prismLoader = null
    throw error
  })

  return prismLoader
}


export type InitPrismOptions = {
  container?: Element | null
  selector?: string
  force?: boolean
}

function normalizeLanguage(lang: string) {
  const v = lang.toLowerCase().trim()
  if (v === "js") return "javascript"
  if (v === "ts") return "typescript"
  if (v === "py") return "python"
  if (v === "sh") return "bash"
  return v
}

function getLanguageFromCodeEl(codeEl: Element) {
  const className = (codeEl as HTMLElement).className || ""
  const m = className.match(/language-([a-z0-9_-]+)/i)
  if (m && m[1]) return normalizeLanguage(m[1])
  const dataLang = (codeEl as HTMLElement).getAttribute("data-language")
  if (dataLang) return normalizeLanguage(dataLang)
  return null
}

export async function initPrism(options: InitPrismOptions = {}) {
  const container =
    options.container ??
    (options.selector
      ? document.querySelector(options.selector)
      : document.getElementById("article-content"))

  if (!container) return

  const codeEls = Array.from(container.querySelectorAll("pre code, code"))
  if (codeEls.length === 0) return
  const Prism = await loadPrism()

  for (const codeEl of codeEls) {
    if (!options.force && (codeEl as HTMLElement).dataset.prismHighlighted === "done") continue
    const lang = getLanguageFromCodeEl(codeEl)
    if (!lang) continue

    const pre = codeEl.closest("pre")
    if (pre) {
      pre.classList.add("line-numbers")
    }

    ;(codeEl as HTMLElement).dataset.prismHighlighted = "done"
    Prism.highlightElement(codeEl as HTMLElement)
  }
}
