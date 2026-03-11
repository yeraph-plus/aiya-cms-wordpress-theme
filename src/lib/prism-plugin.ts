import Prism from "prismjs"

import "prismjs/plugins/line-numbers/prism-line-numbers"
import "prismjs/plugins/normalize-whitespace/prism-normalize-whitespace"
import "prismjs/plugins/toolbar/prism-toolbar"
import "prismjs/plugins/show-language/prism-show-language"
import "prismjs/components/prism-markup"
import "prismjs/components/prism-markup-templating"
import "prismjs/components/prism-javascript"
import "prismjs/components/prism-typescript"
import "prismjs/components/prism-css"
import "prismjs/components/prism-php"
import "prismjs/components/prism-ruby"
import "prismjs/components/prism-python"
import "prismjs/components/prism-java"
import "prismjs/components/prism-c"
import "prismjs/components/prism-cpp"
import "prismjs/components/prism-csharp"
import "prismjs/components/prism-yaml"
import "prismjs/components/prism-json"
import "prismjs/components/prism-sql"


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

export function initPrism(options: InitPrismOptions = {}) {
  const container =
    options.container ??
    (options.selector
      ? document.querySelector(options.selector)
      : document.getElementById("article-content"))

  if (!container) return

  const codeEls = Array.from(container.querySelectorAll("pre code, code"))
  if (codeEls.length === 0) return

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
