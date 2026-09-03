<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';

const { theme, setTheme } = useTheme();

const options = [
    { value: 'light' as const, label: 'Açık tema' },
    { value: 'dark' as const, label: 'Koyu tema' },
    { value: 'system' as const, label: 'Sistem teması' },
];
</script>

<template>
    <div class="theme-toggle" role="group" aria-label="Tema seçimi">
        <button
            v-for="opt in options"
            :key="opt.value"
            type="button"
            class="theme-toggle-btn"
            :class="{ 'theme-toggle-btn-active': theme === opt.value }"
            :aria-pressed="theme === opt.value"
            :aria-label="opt.label"
            :title="opt.label"
            @click="setTheme(opt.value)"
        >
            <svg v-if="opt.value === 'light'" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="4" />
                <path d="M12 2v2.4M12 19.6V22M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M2 12h2.4M19.6 12H22M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" />
            </svg>
            <svg v-else-if="opt.value === 'dark'" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.8A9 9 0 1111.2 3 7 7 0 0021 12.8z" />
            </svg>
            <svg v-else viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4.5" width="18" height="12" rx="2" />
                <path d="M8 20.5h8M12 16.5v4" />
            </svg>
        </button>
    </div>
</template>
