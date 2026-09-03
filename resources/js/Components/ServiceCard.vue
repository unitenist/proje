<script setup lang="ts">
withDefaults(
    defineProps<{
        icon: string;
        title: string;
        description: string;
        delay?: number;
    }>(),
    { delay: 0 },
);
</script>

<template>
    <article v-tilt v-reveal="'flip'" class="p-1" :style="{ transitionDelay: `${delay}ms` }">
        <div class="mb-7 text-sm font-semibold tracking-[0.025em] text-[color:var(--color-accent)]">{{ icon }}</div>
        <h3 class="mb-2.5 text-[27px] font-normal leading-[1.15] tracking-[-0.02em]">{{ title }}</h3>
        <p class="m-0 text-[15px] font-light leading-relaxed text-[color:var(--color-text-muted)]">{{ description }}</p>
    </article>
</template>
