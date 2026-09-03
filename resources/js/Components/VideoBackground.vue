<script setup lang="ts">
withDefaults(
    defineProps<{
        src: string;
        opacity?: number;
    }>(),
    { opacity: 0.55 },
);
</script>

<template>
    <div class="video-bg" aria-hidden="true">
        <video class="video-bg-el" :style="{ opacity }" autoplay muted loop playsinline preload="auto">
            <source :src="src" type="video/mp4" />
        </video>
        <div class="video-bg-scrim"></div>
    </div>
</template>
