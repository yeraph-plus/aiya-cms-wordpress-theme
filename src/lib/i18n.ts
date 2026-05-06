type Primitive = string | number | boolean | null | undefined;
type TranslationMap = Record<string, string>;

declare global {
  interface Window {
    AIYACMS_I18N?: {
      locale?: string;
      translations?: TranslationMap;
    };
  }
}

// 简易翻译桥接器
function getI18nSource(): { locale: string; translations: TranslationMap } {
  const source = (globalThis as any).AIYACMS_I18N;

  if (!source || typeof source !== "object") {
    return { locale: "zh_CN", translations: {} };
  }

  const locale =
    typeof source.locale === "string" && source.locale !== ""
      ? source.locale
      : "zh_CN";

  const translations =
    source.translations && typeof source.translations === "object"
      ? source.translations
      : {};

  return { locale, translations };
}

// 复刻 @wordpress/i18n 的格式化字符串实现
export function sprintf(format: string, ...args: Primitive[]): string {
  if (typeof format !== "string") {
    return "";
  }

  let index = 0;

  return format.replace(/%(%|s|d|f)/g, (token, specifier: string) => {
    if (specifier === "%") {
      return "%";
    }

    const value = args[index++];
    if (value === null || value === undefined) {
      return "";
    }

    if (specifier === "d") {
      const n = Number(value);
      return Number.isFinite(n) ? String(Math.trunc(n)) : "0";
    }

    if (specifier === "f") {
      const n = Number(value);
      return Number.isFinite(n) ? String(n) : "0";
    }

    return String(value);
  });
}

// 应用语言包
export function joinTranslations() {
  const { locale, translations } = getI18nSource();

  function t(key: string, fallback?: string): string {
    const translated = translations[key];
    if (typeof translated === "string" && translated !== "") {
      return translated;
    }

    if (typeof fallback === "string") {
      return fallback;
    }

    return key;
  }

  function __(
    key: string,
    ...args: Primitive[]
  ): string {
    return sprintf(t(key), ...args);
  }

  return { locale, t, __, sprintf };
}

export function getTranslations() {
  return joinTranslations();
}