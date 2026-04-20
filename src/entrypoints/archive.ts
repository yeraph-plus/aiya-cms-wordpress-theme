import { runPageEntry } from '../app/startup';
import { bootPostGridLayout } from '../runtime/post-grid-layout';

runPageEntry('archive', () => {
    bootPostGridLayout(document);
});
