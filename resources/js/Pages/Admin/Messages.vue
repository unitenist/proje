<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

interface Message {
    id: number;
    name: string;
    email: string;
    website: string | null;
    phone: string | null;
    message: string;
    read_at: string | null;
    created_at: string;
}

defineProps<{
    messages: Message[];
}>();

function markRead(id: number) {
    router.post(`/admin/messages/${id}/read`, {}, { preserveScroll: true });
}

function destroy(id: number) {
    if (!confirm('Bu mesajı silmek istediğinize emin misiniz?')) return;
    router.delete(`/admin/messages/${id}`, { preserveScroll: true });
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString('tr-TR', { dateStyle: 'medium', timeStyle: 'short' });
}
</script>

<template>
    <Head><title>Gelen Mesajlar | Admin</title></Head>

    <Link href="/admin" class="mb-6 inline-block text-sm font-light text-white/50 hover:text-white">← Panele dön</Link>
    <h1 class="mb-8 text-3xl font-normal">Gelen Mesajlar</h1>

    <p v-if="!messages.length" class="text-sm font-light text-white/40">Henüz mesaj yok.</p>

    <div class="space-y-4">
        <div
            v-for="m in messages"
            :key="m.id"
            class="rounded-2xl border p-6"
            :class="m.read_at ? 'border-white/10 bg-white/[0.03]' : 'border-[#8052ff]/40 bg-[#8052ff]/10'"
            @click="!m.read_at && markRead(m.id)"
        >
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="font-normal">{{ m.name }}</span>
                    <span class="ml-2 text-sm font-light text-white/40">{{ m.email }}</span>
                    <span v-if="m.website" class="ml-2 text-sm font-light text-white/40">· {{ m.website }}</span>
                    <span v-if="m.phone" class="ml-2 text-sm font-light text-white/40">· {{ m.phone }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-light text-white/30">{{ formatDate(m.created_at) }}</span>
                    <button type="button" class="text-sm font-light text-red-400/70 hover:text-red-400" @click.stop="destroy(m.id)">Sil</button>
                </div>
            </div>
            <p class="m-0 whitespace-pre-line font-light text-white/80">{{ m.message }}</p>
        </div>
    </div>
</template>
