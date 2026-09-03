<script setup lang="ts">
import { onBeforeUnmount, ref } from 'vue';
import NeuralScanLoader from '@/Components/NeuralScanLoader.vue';
import VideoBackground from '@/Components/VideoBackground.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

defineProps<{
    logo?: string | null;
    profile: {
        name: string;
        title: string;
        photo?: string | null;
        email: string;
        linkedin?: string;
    };
}>();

const menu = [
    { href: '#home', label: 'Ana Sayfa', icon: 'ri-home-3-line' },
    { href: '#about', label: 'Hakkımızda', icon: 'ri-user-3-line' },
    { href: '#resume', label: 'Yaklaşım', icon: 'ri-align-center' },
    { href: '#services', label: 'Hizmetler', icon: 'ri-briefcase-2-line' },
    { href: '#skills', label: 'Teknolojiler', icon: 'ri-shapes-line' },
    { href: '#portfolio', label: 'Projeler', icon: 'ri-macbook-line' },
    { href: '#comments', label: 'Referanslar', icon: 'ri-message-2-line' },
    { href: '#contact', label: 'İletişim', icon: 'ri-mail-send-line' },
];

const alreadyLoaded = typeof window !== 'undefined' && sessionStorage.getItem('unitenist-loaded') === '1';
const showLoader = ref(!alreadyLoaded);
const menuOpen = ref(false);

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

// Guard against the overflow lock leaking if the loader is interrupted mid-navigation.
onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});

function closeMenu() {
    menuOpen.value = false;
}
</script>

<template>
    <NeuralScanLoader v-if="showLoader" @done="onLoaderDone" />
    <VideoBackground src="/videos/code.mp4" :opacity="0.8" />

    <div class="relative min-h-screen text-[color:var(--color-text)]">
        <!-- Mobile top bar -->
        <header class="glass-panel sticky top-0 z-30 flex h-[64px] items-center justify-between px-5 lg:hidden">
            <a href="#home" class="flex items-center text-base font-normal tracking-[0.12em]">
                <img v-if="logo" :src="logo" alt="Logo" class="h-8 w-auto" />
                <template v-else>UNI<span class="text-[color:var(--color-accent)]">TE</span>NIST</template>
            </a>
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-full text-xl"
                aria-label="Menüyü aç"
                @click="menuOpen = true"
            >
                <i class="ri-menu-3-line" aria-hidden="true"></i>
            </button>
        </header>

        <div class="mx-auto grid w-[min(calc(100%-32px),1400px)] grid-cols-1 gap-8 py-8 lg:grid-cols-[230px_1fr_260px] lg:gap-10 lg:py-10">
            <!-- Desktop left menu -->
            <nav class="side-menu glass-panel hidden self-start lg:block" aria-label="Bölüm menüsü">
                <a href="#home" class="mb-4 flex items-center justify-center px-4 pb-4 border-b border-[color:color-mix(in_srgb,var(--color-text)_10%,transparent)]">
                    <img v-if="logo" :src="logo" alt="Logo" class="h-9 w-auto" />
                    <span v-else class="text-base font-normal tracking-[0.12em]">UNI<span class="text-[color:var(--color-accent)]">TE</span>NIST</span>
                </a>
                <a v-for="item in menu" :key="item.href" :href="item.href" class="side-menu-link">
                    <i :class="item.icon" aria-hidden="true"></i>
                    {{ item.label }}
                </a>
                <div class="mt-4 flex justify-center border-t border-[color:color-mix(in_srgb,var(--color-text)_10%,transparent)] pt-4">
                    <ThemeToggle />
                </div>
            </nav>

            <!-- Content -->
            <main class="content-card min-w-0">
                <slot />
            </main>

            <!-- Profile box -->
            <aside class="profile-box glass-panel lg:self-start">
                <img v-if="profile.photo" :src="profile.photo" alt="" class="profile-photo" />
                <div v-else class="profile-avatar">UN</div>
                <div>
                    <h5 class="text-lg font-normal">{{ profile.name }}</h5>
                    <p class="mt-1 text-sm font-light text-[color:var(--color-text-muted)]">{{ profile.title }}</p>
                </div>
                <div v-if="profile.linkedin" class="profile-socials">
                    <a :href="profile.linkedin" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="ri-linkedin-fill"></i></a>
                    <a href="https://www.instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                    <a href="https://x.com" target="_blank" rel="noopener" aria-label="X"><i class="ri-twitter-x-line"></i></a>
                </div>
                <a :href="`mailto:${profile.email}`" class="text-sm font-normal text-[color:var(--color-accent)]">{{ profile.email }}</a>
            </aside>
        </div>

        <!-- Mobile menu overlay -->
        <Transition name="fade">
            <div v-if="menuOpen" class="glass-panel fixed inset-0 z-40 flex flex-col p-6 lg:hidden">
                <div class="flex items-center justify-between">
                    <span class="text-base font-normal tracking-[0.12em]">Menu</span>
                    <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-xl" aria-label="Menüyü kapat" @click="closeMenu">
                        <i class="ri-close-line" aria-hidden="true"></i>
                    </button>
                </div>
                <nav class="mt-8 flex flex-col gap-1" aria-label="Bölüm menüsü">
                    <a v-for="item in menu" :key="item.href" :href="item.href" class="side-menu-link text-lg!" @click="closeMenu">
                        <i :class="item.icon" aria-hidden="true"></i>
                        {{ item.label }}
                    </a>
                </nav>
                <div class="mt-auto flex justify-center pt-6">
                    <ThemeToggle />
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
