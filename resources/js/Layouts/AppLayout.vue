<script setup lang="ts">
import { ref } from 'vue';
import NeuralScanLoader from '@/Components/NeuralScanLoader.vue';
import NeuralAmbientBackground from '@/Components/NeuralAmbientBackground.vue';
import VideoBackground from '@/Components/VideoBackground.vue';
import NavBar from '@/Components/NavBar.vue';
import SiteFooter from '@/Components/SiteFooter.vue';

const alreadyLoaded = typeof window !== 'undefined' && sessionStorage.getItem('unitenist-loaded') === '1';
const showLoader = ref(!alreadyLoaded);

if (showLoader.value && typeof document !== 'undefined') {
    document.body.style.overflow = 'hidden';
}

function onLoaderDone() {
    showLoader.value = false;
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
        sessionStorage.setItem('unitenist-loaded', '1');
    }
}
</script>

<template>
    <NeuralScanLoader v-if="showLoader" @done="onLoaderDone" />
    <VideoBackground src="/videos/code.mp4" :opacity="0.55" />
    <NeuralAmbientBackground fixed :opacity="0.4" :show-labels="false" class="pointer-events-none" />
    <div class="relative min-h-screen bg-bg text-[color:var(--color-text)] md:pl-[96px]">
        <NavBar />
        <main>
            <slot />
        </main>
        <SiteFooter />
    </div>
</template>
