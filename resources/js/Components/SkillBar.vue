<script setup lang="ts">
withDefaults(
    defineProps<{
        name: string;
        percentage: number;
        delay?: number;
    }>(),
    { delay: 0 },
);
</script>

<template>
    <div v-reveal :style="{ transitionDelay: `${delay}ms` }">
        <div class="mb-2 flex items-baseline justify-between text-sm font-light">
            <span>{{ name }}</span>
            <span class="text-[color:var(--color-text-muted)]">{{ percentage }}%</span>
        </div>
        <div class="h-[3px] w-full overflow-hidden rounded-full bg-white/10">
            <div
                class="h-full rounded-full bg-[color:var(--color-accent)] transition-[width] duration-1000 ease-out"
                :style="{ width: `${percentage}%` }"
            ></div>
        </div>
    </div>
</template>
