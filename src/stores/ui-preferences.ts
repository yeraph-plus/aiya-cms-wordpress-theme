import { create } from 'zustand';
import { createJSONStorage, persist } from 'zustand/middleware';

export type LoopGridLayout = 'grid' | 'list';
export type CookieConsentDecision = 'accepted' | 'declined' | null;

interface UiPreferencesState {
    loopGridLayout: LoopGridLayout;
    cookieConsentDecision: CookieConsentDecision;
    setLoopGridLayout: (layout: LoopGridLayout) => void;
    setCookieConsentDecision: (decision: Exclude<CookieConsentDecision, null>) => void;
}

export const usePreferencesStore = create<UiPreferencesState>()(
    persist(
        (set) => ({
            loopGridLayout: 'grid',
            cookieConsentDecision: null,
            setLoopGridLayout: (layout) => set({ loopGridLayout: layout }),
            setCookieConsentDecision: (decision) => set({ cookieConsentDecision: decision }),
        }),
        {
            name: 'ui-preferences',
            storage: createJSONStorage(() => localStorage),
            partialize: (state) => ({
                loopGridLayout: state.loopGridLayout,
                cookieConsentDecision: state.cookieConsentDecision,
            }),
        }
    )
);
