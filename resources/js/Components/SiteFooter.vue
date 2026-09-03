<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const year = new Date().getFullYear();

const links = [
    { href: '/hizmetler', label: 'Hizmetler' },
    { href: '/yaklasimimiz', label: 'Yaklaşım' },
    { href: '/ai-lab', label: 'AI Lab' },
    { href: '/hakkimizda', label: 'Hakkımızda' },
    { href: '/iletisim', label: 'İletişim' },
];
</script>

<template>
    <footer class="relative py-[60px] text-[13px] font-light text-[color:var(--color-text-muted)]">
        <div class="mx-auto flex w-[min(calc(100%-40px),1280px)] flex-wrap items-center justify-between gap-5">
            <span>© {{ year }} UNITENIST. AI · ERP · AUTOMATION.</span>
            <nav class="flex flex-wrap gap-6" aria-label="Alt menü">
                <Link v-for="link in links" :key="link.href" :href="link.href" class="transition-colors hover:text-[color:var(--color-text)]">
                    {{ link.label }}
                </Link>
            </nav>
            <span>İstanbul · Türkiye</span>
        </div>
    </footer>
</template>
