<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    content: {
        kicker: string;
        title: string;
        subtitle: string;
        email: string;
        phone?: string;
        address?: string;
        linkedin: string;
    };
}>();
</script>

<template>
    <Head>
        <title>İletişim | UNITENIST</title>
        <meta name="description" content="ERP'nizde AI için nereden başlamanız gerektiğini birlikte bulalım. UNITENIST ile iletişime geçin." />
        <link rel="canonical" href="https://www.unitenist.com/iletisim" />
        <meta property="og:title" content="İletişim | UNITENIST" />
        <meta property="og:url" content="https://www.unitenist.com/iletisim" />
    </Head>

    <section class="py-[100px]">
        <div class="mx-auto w-[min(calc(100%-40px),1280px)]">
            <div v-reveal class="max-w-[820px]">
                <div class="text-sm font-light uppercase tracking-wide text-accent">{{ content.kicker }}</div>
                <h1 class="my-4 text-[clamp(40px,6vw,78px)] font-normal leading-[1.02] tracking-[-0.04em]">{{ content.title }}</h1>
                <p class="max-w-[680px] text-lg font-light text-[color:var(--color-text-muted)]">
                    {{ content.subtitle }}
                </p>
                <div class="mt-9 flex flex-wrap gap-4">
                    <a v-magnetic :href="`mailto:${content.email}`" class="inline-flex items-center justify-center rounded-3xl bg-accent px-6 py-3.5 font-normal text-white">
                        {{ content.email }} →
                    </a>
                    <a v-magnetic :href="content.linkedin" rel="noopener" class="inline-flex items-center justify-center px-2 py-3.5 font-normal text-accent-2">
                        LinkedIn
                    </a>
                </div>
            </div>

            <div v-if="content.phone || content.address" v-reveal class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div v-if="content.phone" class="rounded-[20px] border border-white/10 p-7">
                    <p class="mb-2 text-sm font-light uppercase tracking-wide text-[color:var(--color-text-muted)]">Telefon</p>
                    <p class="m-0 text-lg font-normal">{{ content.phone }}</p>
                </div>
                <div v-if="content.address" class="rounded-[20px] border border-white/10 p-7">
                    <p class="mb-2 text-sm font-light uppercase tracking-wide text-[color:var(--color-text-muted)]">Adres</p>
                    <p class="m-0 text-lg font-normal">{{ content.address }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
