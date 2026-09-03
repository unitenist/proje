import { onBeforeUnmount, onMounted, ref } from 'vue';

export type ThemePreference = 'light' | 'dark' | 'system';

const STORAGE_KEY = 'unitenist-theme';

function readStored(): ThemePreference {
    if (typeof window === 'undefined') return 'system';
    const stored = window.localStorage.getItem(STORAGE_KEY);
    return stored === 'light' || stored === 'dark' ? stored : 'system';
}

function resolve(pref: ThemePreference): 'light' | 'dark' {
    if (pref === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return pref;
}

function apply(pref: ThemePreference) {
    document.documentElement.setAttribute('data-theme', resolve(pref));
}

const theme = ref<ThemePreference>(readStored());

/** Shared light/dark/system theme state, synced to <html data-theme> and localStorage. */
export function useTheme() {
    let media: MediaQueryList | null = null;

    function onSystemChange() {
        if (theme.value === 'system') apply('system');
    }

    onMounted(() => {
        apply(theme.value);
        media = window.matchMedia('(prefers-color-scheme: dark)');
        media.addEventListener('change', onSystemChange);
    });

    onBeforeUnmount(() => {
        media?.removeEventListener('change', onSystemChange);
    });

    function setTheme(pref: ThemePreference) {
        theme.value = pref;
        window.localStorage.setItem(STORAGE_KEY, pref);
        apply(pref);
    }

    return { theme, setTheme };
}
