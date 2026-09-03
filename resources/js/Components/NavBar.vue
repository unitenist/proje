<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const links = [
    { href: '/', label: 'Ana Sayfa', icon: 'ri-home-3-line' },
    { href: '/hakkimizda', label: 'Hakkımızda', icon: 'ri-user-3-line' },
    { href: '/yaklasimimiz', label: 'Yaklaşım', icon: 'ri-align-center' },
    { href: '/hizmetler', label: 'Hizmetler', icon: 'ri-briefcase-2-line' },
    { href: '/ai-lab', label: 'AI Lab', icon: 'ri-shapes-line' },
    { href: '/iletisim', label: 'İletişim', icon: 'ri-mail-send-line' },
];

const currentUrl = computed(() => usePage().url);

function isActive(href: string): boolean {
    return href === '/' ? currentUrl.value === '/' : currentUrl.value.startsWith(href);
}

const mobileOpen = ref(false);
</script>

<template>
    <!-- Desktop: fixed vertical icon sidebar -->
    <aside class="side-nav glass-panel hidden md:flex" aria-label="Ana menü">
        <Link href="/" class="side-nav-link" aria-label="UNITENIST ana sayfa">
            <span class="text-base font-normal tracking-[0.08em]">U<span class="text-[color:var(--color-accent)]">·</span>N</span>
        </Link>
        <nav class="flex flex-col gap-2">
            <Link
                v-for="link in links"
                :key="link.href"
                :href="link.href"
                class="side-nav-link"
                :class="{ 'is-active': isActive(link.href) }"
            >
                <i :class="link.icon" aria-hidden="true"></i>
                <span class="side-nav-tooltip">{{ link.label }}</span>
            </Link>
        </nav>
        <div class="flex flex-col items-center gap-4">
            <ThemeToggle />
            <Link v-magnetic href="/iletisim" class="side-nav-link side-nav-cta">
                <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                <span class="side-nav-tooltip">Projenizi konuşalım</span>
            </Link>
        </div>
    </aside>

    <!-- Mobile: top bar + slide-in overlay -->
    <header class="glass-panel sticky top-0 z-20 md:hidden">
        <div class="mx-auto flex h-[70px] w-[min(calc(100%-32px),1280px)] items-center justify-between">
            <Link href="/" class="text-base font-normal tracking-[0.12em]" aria-label="UNITENIST ana sayfa">
                UNI<span class="text-[color:var(--color-accent)]">TE</span>NIST
            </Link>
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-full text-xl"
                aria-label="Menüyü aç"
                @click="mobileOpen = true"
            >
                <i class="ri-menu-3-line" aria-hidden="true"></i>
            </button>
        </div>
    </header>

    <Transition name="fade">
        <div v-if="mobileOpen" class="glass-panel fixed inset-0 z-30 flex flex-col p-8 md:hidden">
            <div class="flex items-center justify-between">
                <span class="text-base font-normal tracking-[0.12em]">
                    UNI<span class="text-[color:var(--color-accent)]">TE</span>NIST
                </span>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-xl" aria-label="Menüyü kapat" @click="mobileOpen = false">
                    <i class="ri-close-line" aria-hidden="true"></i>
                </button>
            </div>
            <nav class="mt-12 flex flex-col gap-1 text-lg font-light" aria-label="Ana menü">
                <Link
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    class="flex items-center gap-4 rounded-2xl px-4 py-3.5"
                    :class="isActive(link.href) ? 'text-[color:var(--color-accent)]' : 'text-[color:var(--color-text)]'"
                    @click="mobileOpen = false"
                >
                    <i :class="link.icon" class="text-xl" aria-hidden="true"></i>
                    {{ link.label }}
                </Link>
            </nav>
            <div class="mt-auto flex items-center justify-between">
                <ThemeToggle />
                <Link
                    v-magnetic
                    href="/iletisim"
                    class="inline-flex items-center justify-center rounded-3xl bg-[color:var(--color-accent)] px-6 py-3 text-sm font-normal text-white"
                    @click="mobileOpen = false"
                >
                    Projenizi konuşalım
                </Link>
            </div>
        </div>
    </Transition>
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
