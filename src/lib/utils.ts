import * as React from "react"
import { clsx, type ClassValue } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
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
interface AppConfig {
  apiUrl: string;
  apiNonce: string;
  defaultColorTheme: string;
  [key: string]: any;
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
