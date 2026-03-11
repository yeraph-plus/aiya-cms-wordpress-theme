import { useEffect, useCallback, useRef } from 'react';

interface FetchResult {
    html: string;
    title: string;
    containers: {
        [key: string]: string | undefined;
    };
}

export function usePJAXFetch() {
    const requestId = useRef(0);
    const abortController = useRef<AbortController | null>(null);

    const fetchPage = useCallback(async (url: string): Promise<FetchResult> => {
        requestId.current++;
        abortController.current?.abort();
        const ac = new AbortController();
        abortController.current = ac;

        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            signal: ac.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const html = await res.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        
        const containers: FetchResult['containers'] = {};
        const main = doc.querySelector('[data-pjax-container="main"]');
        if (main) containers['main'] = main.innerHTML;
        
        const sidebar = doc.querySelector('[data-pjax-container="sidebar"]');
        if (sidebar) containers['sidebar'] = sidebar.innerHTML;

        if (!containers['main']) throw new Error('New page missing [data-pjax-container="main"]');

        return {
            html,
            title: doc.title,
            containers,
        };
    }, []);

    // 取消当前请求
    const abort = useCallback(() => {
        abortController.current?.abort();
    }, []);

    return { fetchPage, abort };
}

export function useLinkInterception(navigate: (url: string) => void) {
    useEffect(() => {
        const shouldIgnoreClick = (e: MouseEvent, link: HTMLAnchorElement, url: URL) => {
            if (e.defaultPrevented || e.button !== 0) return true;
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return true;
            if (link.target && link.target !== '_self') return true;
            if (link.hasAttribute('download') || link.hasAttribute('data-no-pjax')) return true;
            
            // Check if it's a hash link on the same page
            if (url.origin === location.origin && 
                url.pathname === location.pathname && 
                url.search === location.search && 
                url.hash) {
                return true;
            }

            if (url.origin !== location.origin) return true;
            if (url.protocol !== 'http:' && url.protocol !== 'https:') return true;

            return (url.pathname === location.pathname && url.search === location.search);
        };

        const onClick = (e: MouseEvent) => {
            const target = e.target as Element;
            const link = target.tagName === 'A' ? target as HTMLAnchorElement : target.closest<HTMLAnchorElement>('a');
            
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href) return;
            
            // Handle hash links specifically
            if (href.startsWith('#')) {
                // If it's just a hash, let the browser handle it (scrolls to anchor)
                // We return here to prevent PJAX from handling it
                return;
            }

            const url = new URL(link.href, location.href);
            if (shouldIgnoreClick(e, link, url)) return;

            e.preventDefault();
            history.pushState({}, '', url.href);
            navigate(url.href);
        };

        const onPop = () => navigate(location.href);

        const onSubmit = (e: SubmitEvent) => {
            const form = e.target as HTMLFormElement;
            if (e.defaultPrevented || form.method.toLowerCase() !== 'get') return;
            if (form.target && form.target !== '_self') return;
            
            // 构造 URL
            const url = new URL(form.action, location.href);
            // 将表单数据转为 URLSearchParams
            const formData = new FormData(form);
            const params = new URLSearchParams(formData as any);
            
            // 合并 URL 中的现有参数和表单参数
            // 这里简单处理：表单参数覆盖 URL 参数
            url.search = params.toString();

            if (url.origin !== location.origin) return;

            e.preventDefault();
            history.pushState({}, '', url.href);
            navigate(url.href);
        };

        document.addEventListener('click', onClick);
        document.addEventListener('submit', onSubmit);
        window.addEventListener('popstate', onPop);
        return () => {
            document.removeEventListener('click', onClick);
            document.removeEventListener('submit', onSubmit);
            window.removeEventListener('popstate', onPop);
        };
    }, [navigate]);
}