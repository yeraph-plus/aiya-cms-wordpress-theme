import * as React from "react"
import { decodeEntities } from "@wordpress/html-entities"
import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function sanitizeBackendText(value?: unknown) {
  const raw = String(value ?? "")

  if (!raw) {
    return ""
  }

  const textContent =
    typeof window === "undefined"
      ? raw.replace(/<[^>]*>/g, " ")
      : new DOMParser().parseFromString(raw, "text/html").body.textContent || ""

  return decodeEntities(textContent).replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim()
}

const MOBILE_BREAKPOINT = 768

export function useIsMobile() {
  const [isMobile, setIsMobile] = React.useState<boolean | undefined>(undefined)

  React.useEffect(() => {
    const mql = window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT - 1}px)`)
    const onChange = () => {
      setIsMobile(window.innerWidth < MOBILE_BREAKPOINT)
    }
    mql.addEventListener("change", onChange)
    setIsMobile(window.innerWidth < MOBILE_BREAKPOINT)
    return () => mql.removeEventListener("change", onChange)
  }, [])

  return !!isMobile
}

export interface AppConfig {
  apiUrl: string;
  apiNonce: string;
  homeUrl: string;
  defaultColorTheme: string;
  defaultLoopList: boolean;
  [key: string]: any;
}

declare global {
  interface Window {
    AIYACMS_CONFIG?: AppConfig;
  }
}

// 从HTML获取配置
export function getConfig(): Partial<AppConfig> {
  const v = (globalThis as any).AIYACMS_CONFIG;
  if (v && typeof v === 'object') {
    const config = { ...v };

    return config;
  }
  return {};
}
