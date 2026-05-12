import { create } from 'zustand';
import { createJSONStorage, persist } from 'zustand/middleware';

export type LoopGridLayout = 'grid' | 'list';
export type ConsentDecision = 'accepted' | 'declined';
export type ConsentMap = Partial<Record<string, ConsentDecision>>;

interface UiPreferencesState {
    loopGridLayout: LoopGridLayout;
    consentDecisionBySlug: ConsentMap;
    setLoopGridLayout: (layout: LoopGridLayout) => void;
    setConsentDecision: (slug: string, decision: ConsentDecision) => void;
}

export const usePreferencesStore = create<UiPreferencesState>()(
    persist(
        (set) => ({
            loopGridLayout: 'grid',
            consentDecisionBySlug: {},
            setLoopGridLayout: (layout) => set({ loopGridLayout: layout }),
            setConsentDecision: (slug, decision) =>
                set((state) => ({
                    consentDecisionBySlug: {
                        ...state.consentDecisionBySlug,
                        [slug]: decision,
                    },
                })),
        }),
        {
            name: 'ui-preferences',
            storage: createJSONStorage(() => localStorage),
            partialize: (state) => ({
                loopGridLayout: state.loopGridLayout,
                consentDecisionBySlug: state.consentDecisionBySlug,
            }),
        }
    )
);
