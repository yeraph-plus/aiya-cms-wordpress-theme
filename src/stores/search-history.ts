import { create } from 'zustand';
import { createJSONStorage, persist } from 'zustand/middleware';

const SEARCH_HISTORY_LIMIT = 8;

interface SearchHistoryState {
    recentSearches: string[];
    addRecentSearch: (term: string) => void;
    clearRecentSearches: () => void;
}

export const useSearchHistoryStore = create<SearchHistoryState>()(
    persist(
        (set) => ({
            recentSearches: [],
            addRecentSearch: (term) =>
                set((state) => {
                    const normalized = term.trim();

                    if (!normalized) {
                        return state;
                    }

                    const next = [
                        normalized,
                        ...state.recentSearches.filter(
                            (item) => item.toLowerCase() !== normalized.toLowerCase()
                        ),
                    ].slice(0, SEARCH_HISTORY_LIMIT);

                    return { recentSearches: next };
                }),
            clearRecentSearches: () => set({ recentSearches: [] }),
        }),
        {
            name: 'aiya-search-history',
            storage: createJSONStorage(() => localStorage),
            partialize: (state) => ({ recentSearches: state.recentSearches }),
        }
    )
);
