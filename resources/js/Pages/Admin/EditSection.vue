<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    section: string;
    label: string;
    content: Record<string, any>;
}>();

const form = reactive<Record<string, any>>(JSON.parse(JSON.stringify(props.content)));
const processing = reactive({ value: false });
const errors = reactive<Record<string, string>>({});
const uploading = reactive<Record<string, boolean>>({});

const flash = computed(() => (usePage().props as any).flash?.success as string | undefined);

// Scalar (text/textarea/image) fields vs repeatable list fields, in defined key order.
const scalarKeys = computed(() => Object.keys(form).filter((k) => !Array.isArray(form[k]) && (form[k] === null || typeof form[k] !== 'object')));
const listKeys = computed(() => Object.keys(form).filter((k) => Array.isArray(form[k])));

const longFieldHint = /description|subtitle|quote|paragraph|text|title$/i;

function isTextarea(key: string): boolean {
    return longFieldHint.test(key) && String(form[key] ?? '').length > 40 || key.includes('description') || key.includes('subtitle') || key.includes('quote');
}

function isImageField(key: string): boolean {
    return key === 'logo' || key === 'profile_photo' || key.endsWith('_photo') || key.endsWith('_image');
}

function labelFor(key: string): string {
    return key.replace(/_/g, ' ');
}

async function uploadTo(target: Record<string, any>, field: string, event: Event, uploadKey: string) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;

    uploading[uploadKey] = true;
    const body = new FormData();
    body.append('image', file);
    const token = decodeURIComponent(document.cookie.split('; ').find((c) => c.startsWith('XSRF-TOKEN='))?.split('=')[1] ?? '');

    try {
        const res = await fetch('/admin/upload', {
            method: 'POST',
            headers: { 'X-XSRF-TOKEN': token },
            body,
        });
        const data = await res.json();
        if (res.ok) {
            target[field] = data.url;
        } else {
            errors[uploadKey] = data.message ?? 'Yükleme başarısız oldu.';
        }
    } finally {
        uploading[uploadKey] = false;
    }
}

function uploadImage(key: string, event: Event) {
    return uploadTo(form, key, event, key);
}

function uploadItemImage(listKey: string, index: number, field: string, event: Event) {
    return uploadTo(form[listKey][index], field, event, `${listKey}-${index}-${field}`);
}

function blankItem(listKey: string): Record<string, any> {
    const sample = form[listKey][0] ?? {};
    const blank: Record<string, any> = {};
    for (const k of Object.keys(sample)) {
        blank[k] = isImageField(k) ? null : typeof sample[k] === 'number' ? 0 : '';
    }
    return blank;
}

function addItem(listKey: string) {
    form[listKey].push(blankItem(listKey));
}

function removeItem(listKey: string, index: number) {
    form[listKey].splice(index, 1);
}

function moveItem(listKey: string, index: number, dir: -1 | 1) {
    const arr = form[listKey];
    const target = index + dir;
    if (target < 0 || target >= arr.length) return;
    [arr[index], arr[target]] = [arr[target], arr[index]];
}

function submit() {
    processing.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);

    router.put(`/admin/${props.section}`, { content: form }, {
        preserveScroll: true,
        onError: (e) => Object.assign(errors, e),
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Head><title>{{ label }} | Admin</title></Head>

    <Link href="/admin" class="mb-6 inline-block text-sm font-light text-white/50 hover:text-white">← Panele dön</Link>
    <h1 class="mb-8 text-3xl font-normal">{{ label }}</h1>

    <div v-if="flash" class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-light text-emerald-300">
        {{ flash }}
    </div>

    <form class="space-y-10" @submit.prevent="submit">
        <section v-if="scalarKeys.length" class="space-y-5">
            <div v-for="key in scalarKeys" :key="key">
                <label class="mb-2 block text-sm font-light uppercase tracking-wide text-white/50" :for="key">{{ labelFor(key) }}</label>

                <div v-if="isImageField(key)" class="flex items-center gap-4">
                    <img v-if="form[key]" :src="form[key]" alt="" class="h-16 w-16 rounded-xl border border-white/15 object-cover" />
                    <div v-else class="flex h-16 w-16 items-center justify-center rounded-xl border border-dashed border-white/20 text-xs text-white/30">Yok</div>
                    <label class="cursor-pointer rounded-full border border-white/15 px-4 py-2 text-sm font-light text-white/70 hover:text-white">
                        {{ uploading[key] ? 'Yükleniyor…' : 'Görsel seç' }}
                        <input :id="key" type="file" accept="image/*" class="hidden" @change="uploadImage(key, $event)" />
                    </label>
                    <button v-if="form[key]" type="button" class="text-sm font-light text-white/40 hover:text-red-400" @click="form[key] = null">
                        Kaldır
                    </button>
                </div>
                <textarea
                    v-else-if="isTextarea(key)"
                    :id="key"
                    v-model="form[key]"
                    rows="3"
                    class="w-full rounded-2xl border border-white/15 bg-white/5 px-4 py-3 font-light text-[#f5f5f5] outline-none focus:border-[#8052ff]"
                ></textarea>
                <input
                    v-else
                    :id="key"
                    v-model="form[key]"
                    type="text"
                    class="w-full rounded-2xl border border-white/15 bg-white/5 px-4 py-3 font-light text-[#f5f5f5] outline-none focus:border-[#8052ff]"
                />
                <p v-if="errors[key]" class="mt-1 text-sm font-light text-red-400">{{ errors[key] }}</p>
            </div>
        </section>

        <section v-for="listKey in listKeys" :key="listKey" class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-normal capitalize">{{ labelFor(listKey) }}</h2>
                <button type="button" class="rounded-full border border-white/15 px-4 py-1.5 text-sm font-light text-white/70 hover:text-white" @click="addItem(listKey)">
                    + Ekle
                </button>
            </div>

            <div v-for="(item, index) in (form[listKey] as any[])" :key="index" class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-sm font-light text-white/40">#{{ index + 1 }}</span>
                    <div class="flex gap-2">
                        <button type="button" class="text-white/40 hover:text-white" title="Yukarı taşı" @click="moveItem(listKey, index, -1)">↑</button>
                        <button type="button" class="text-white/40 hover:text-white" title="Aşağı taşı" @click="moveItem(listKey, index, 1)">↓</button>
                        <button type="button" class="text-red-400/70 hover:text-red-400" title="Sil" @click="removeItem(listKey, index)">✕</button>
                    </div>
                </div>
                <div class="space-y-3">
                    <div v-for="field in Object.keys(item)" :key="field">
                        <label class="mb-1 block text-xs font-light uppercase tracking-wide text-white/40">{{ labelFor(field) }}</label>

                        <div v-if="isImageField(field)" class="flex items-center gap-3">
                            <img v-if="item[field]" :src="item[field]" alt="" class="h-12 w-12 rounded-lg border border-white/15 object-cover" />
                            <div v-else class="flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-white/20 text-[10px] text-white/30">Yok</div>
                            <label class="cursor-pointer rounded-full border border-white/15 px-3 py-1.5 text-xs font-light text-white/70 hover:text-white">
                                {{ uploading[`${listKey}-${index}-${field}`] ? 'Yükleniyor…' : 'Görsel seç' }}
                                <input type="file" accept="image/*" class="hidden" @change="uploadItemImage(listKey, index, field, $event)" />
                            </label>
                            <button v-if="item[field]" type="button" class="text-xs font-light text-white/40 hover:text-red-400" @click="item[field] = null">
                                Kaldır
                            </button>
                        </div>
                        <textarea
                            v-else-if="field === 'description' || field === 'quote'"
                            v-model="item[field]"
                            rows="2"
                            class="w-full rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-sm font-light text-[#f5f5f5] outline-none focus:border-[#8052ff]"
                        ></textarea>
                        <input
                            v-else
                            v-model="item[field]"
                            :type="typeof item[field] === 'number' ? 'number' : 'text'"
                            class="w-full rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-sm font-light text-[#f5f5f5] outline-none focus:border-[#8052ff]"
                        />
                    </div>
                </div>
            </div>

            <p v-if="!form[listKey].length" class="text-sm font-light text-white/40">Henüz öğe yok.</p>
        </section>

        <button
            type="submit"
            :disabled="processing.value"
            class="rounded-2xl bg-[#8052ff] px-8 py-3 font-normal text-white disabled:opacity-50"
        >
            Kaydet
        </button>
    </form>
</template>
