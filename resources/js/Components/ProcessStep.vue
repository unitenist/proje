<script setup lang="ts">
withDefaults(
    defineProps<{
        number: string;
        label: string;
        title: string;
        description: string;
        delay?: number;
    }>(),
    { delay: 0 },
);
</script>

<template>
    <div v-reveal class="pt-[22px]" :style="{ transitionDelay: `${delay}ms` }">
        <div class="text-sm font-semibold tracking-[0.025em] text-[color:var(--color-accent-2)]">{{ number }} / {{ label }}</div>
        <h3 class="mb-[7px] mt-2.5 text-[24px] font-normal tracking-[-0.02em]">{{ title }}</h3>
        <p class="m-0 font-light text-[color:var(--color-text-muted)]">{{ description }}</p>
    </div>
</template>
