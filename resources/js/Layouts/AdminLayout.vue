<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

function logout() {
    router.post('/admin/logout');
}
</script>

<template>
    <div class="min-h-screen bg-[color:var(--color-bg,#000)] text-[color:var(--color-text,#f5f5f5)]">
        <header class="sticky top-0 z-10 border-b border-white/10 bg-black/70 backdrop-blur-xl">
            <div class="mx-auto flex h-[70px] w-[min(calc(100%-40px),1100px)] items-center justify-between">
                <Link href="/admin" class="text-base font-normal tracking-[0.1em]">
                    UNI<span class="text-[color:var(--color-accent,#8052ff)]">TE</span>NIST <span class="text-white/40">/ admin</span>
                </Link>
                <div class="flex items-center gap-5 text-sm font-light">
                    <Link href="/" class="text-white/50 hover:text-white">Siteyi görüntüle</Link>
                    <button type="button" class="rounded-full border border-white/15 px-4 py-1.5 text-white/70 hover:text-white" @click="logout">
                        Çıkış yap
                    </button>
                </div>
            </div>
        </header>
        <main class="mx-auto w-[min(calc(100%-40px),1100px)] py-12">
            <slot />
        </main>
    </div>
</template>
