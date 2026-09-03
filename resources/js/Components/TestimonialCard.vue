<script setup lang="ts">
withDefaults(
    defineProps<{
        quote: string;
        author: string;
        role?: string;
        delay?: number;
    }>(),
    { delay: 0, role: '' },
);
</script>

<template>
    <article v-reveal="'flip'" class="rounded-[20px] border border-white/10 p-7" :style="{ transitionDelay: `${delay}ms` }">
        <p class="m-0 text-[17px] font-light leading-relaxed text-[color:var(--color-text)]">“{{ quote }}”</p>
        <div class="mt-5 text-sm font-normal">
            {{ author }}
            <span v-if="role" class="font-light text-[color:var(--color-text-muted)]"> · {{ role }}</span>
        </div>
    </article>
</template>
