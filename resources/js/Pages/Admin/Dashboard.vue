<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    sections: Record<string, string>;
    unreadMessages: number;
}>();
</script>

<template>
    <Head><title>Admin Panel | UNITENIST</title></Head>

    <h1 class="mb-2 text-3xl font-normal">İçerik Yönetimi</h1>
    <p class="mb-10 font-light text-white/50">Sitedeki bölümlerin metin ve listelerini buradan düzenleyebilirsiniz.</p>

    <Link
        href="/admin/messages"
        class="mb-8 flex items-center justify-between rounded-2xl border border-[#8052ff]/40 bg-[#8052ff]/10 p-6 transition-colors hover:border-[#8052ff]/70"
    >
        <div>
            <div class="text-lg font-normal">Gelen Mesajlar (İletişim Formu)</div>
            <div class="mt-1 text-sm font-light text-white/40">Görüntüle →</div>
        </div>
        <span v-if="unreadMessages > 0" class="rounded-full bg-[#8052ff] px-3 py-1 text-sm font-normal text-white">
            {{ unreadMessages }} yeni
        </span>
    </Link>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <Link
            v-for="(label, key) in sections"
            :key="key"
            :href="`/admin/${key}`"
            class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 transition-colors hover:border-[#8052ff]/60 hover:bg-white/[0.06]"
        >
            <div class="text-lg font-normal">{{ label }}</div>
            <div class="mt-1 text-sm font-light text-white/40">Düzenle →</div>
        </Link>
    </div>
</template>
