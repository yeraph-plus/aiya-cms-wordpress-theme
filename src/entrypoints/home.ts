import { runPageEntry } from '../app/startup';
import { bootPostGridLayout } from '../runtime/post-layout-slots';

runPageEntry('home', () => {
    bootPostGridLayout(document);
});
