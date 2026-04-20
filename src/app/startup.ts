import { hydrateAllIslands } from '../runtime/islands';
import { bootBadgeSlots } from '../runtime/badge-slots';
import { bootIconSlots } from '../runtime/icon-slots';
import '../styles/tailwind.css';

export type CmsPageEntry = 'common' | 'home' | 'archive' | 'single' | 'user';

let hasBootstrapped = false;

function onDocumentReady(callback: () => void) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}

function bootstrapApplication() {
    if (hasBootstrapped) {
        return;
    }

    hasBootstrapped = true;
    document.documentElement.dataset.cmsAppMode = 'mpa';

    if (!document.documentElement.dataset.cmsPageEntry) {
        document.documentElement.dataset.cmsPageEntry = 'common';
    }

    hydrateAllIslands(document);
    bootBadgeSlots(document);
    bootIconSlots(document);

    window.dispatchEvent(new Event('app:ready'));

    console.log(
        '\n\n %c AIYA-CMS %c https://www.yeraph.com',
        'color:#f1ab0e;background:#222;padding:5px;',
        'background:#eee;padding:5px;'
    );
}

function ensureApplicationBootstrapped() {
    onDocumentReady(bootstrapApplication);
}

export function runPageEntry(entry: CmsPageEntry, onReady?: () => void) {
    document.documentElement.dataset.cmsPageEntry = entry;
    ensureApplicationBootstrapped();

    if (onReady) {
        onDocumentReady(onReady);
    }
}
