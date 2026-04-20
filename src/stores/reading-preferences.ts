import { create } from 'zustand';
import { createJSONStorage, persist } from 'zustand/middleware';

interface ReadingPreferencesState {
    dismissedNoticeKeys: string[];
    dismissNotice: (key: string) => void;
    restoreNotice: (key: string) => void;
    isNoticeDismissed: (key: string) => boolean;
}

export const useReadingPreferencesStore = create<ReadingPreferencesState>()(
    persist(
        (set, get) => ({
            dismissedNoticeKeys: [],
            dismissNotice: (key) =>
                set((state) => {
                    if (!key || state.dismissedNoticeKeys.includes(key)) {
                        return state;
                    }

                    return {
                        dismissedNoticeKeys: [...state.dismissedNoticeKeys, key],
                    };
                }),
            restoreNotice: (key) =>
                set((state) => ({
                    dismissedNoticeKeys: state.dismissedNoticeKeys.filter((item) => item !== key),
                })),
            isNoticeDismissed: (key) => get().dismissedNoticeKeys.includes(key),
        }),
        {
            name: 'aiya-reading-preferences',
            storage: createJSONStorage(() => localStorage),
            partialize: (state) => ({
                dismissedNoticeKeys: state.dismissedNoticeKeys,
            }),
        }
    )
);
