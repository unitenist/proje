<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });

function submit() {
    form.post('/admin/login', {
        preserveScroll: true,
        onSuccess: () => form.reset('password'),
    });
}
</script>

<template>
    <Head><title>Admin Girişi | UNITENIST</title></Head>

    <div class="flex min-h-screen items-center justify-center bg-black px-6 text-[#f5f5f5]">
        <form class="w-full max-w-[380px]" @submit.prevent="submit">
            <div class="mb-8 text-center text-lg font-normal tracking-[0.12em]">
                UNI<span class="text-[#8052ff]">TE</span>NIST <span class="text-white/40">/ admin</span>
            </div>
            <label for="password" class="mb-2 block text-sm font-light uppercase tracking-wide text-white/50">Şifre</label>
            <input
                id="password"
                v-model="form.password"
                type="password"
                autofocus
                class="w-full rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-[#f5f5f5] outline-none focus:border-[#8052ff]"
                placeholder="••••••••"
            />
            <p v-if="form.errors.password" class="mt-2 text-sm font-light text-red-400">{{ form.errors.password }}</p>
            <button
                type="submit"
                :disabled="form.processing"
                class="mt-6 w-full rounded-2xl bg-[#8052ff] px-6 py-3 font-normal text-white disabled:opacity-50"
            >
                Giriş yap
            </button>
        </form>
    </div>
</template>
