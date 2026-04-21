import { runPageEntry } from '../app/startup';
import { initPrism } from '../lib/prism-plugin';
import { initViewer } from '../lib/viewer-plugin';
import { bootAlertSlots } from '../runtime/alert-slots';
import { bootClipboardSlots } from '../runtime/clipboard-slots';

let viewerInstance: ReturnType<typeof initViewer> = null;

runPageEntry('single', () => {
    const container = document.querySelector('#article-content');

    if (!container) {
        return;
    }

    if (viewerInstance) {
        viewerInstance.destroy();
        viewerInstance = null;
    }

    viewerInstance = initViewer({ container, force: true });
    initPrism({ container, force: true });
    bootAlertSlots(document);
    bootClipboardSlots(document);
});
