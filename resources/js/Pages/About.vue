<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    content: {
        kicker: string;
        title: string;
        quote: string;
        cta_text: string;
        cta_href: string;
        paragraphs: Array<{ text: string }>;
    };
}>();
</script>

<template>
    <Head>
        <title>Hakkımızda | UNITENIST</title>
        <meta name="description" content="UNITENIST, ERP ve operasyon sistemlerini yapay zekâ ile yeniden yorumlayan bağımsız bir teknoloji markasıdır. Kurucu: Sermet Sandıkçı." />
        <link rel="canonical" href="https://www.unitenist.com/hakkimizda" />
        <meta property="og:title" content="Hakkımızda | UNITENIST" />
        <meta property="og:url" content="https://www.unitenist.com/hakkimizda" />
    </Head>

    <section class="py-[100px]">
        <div class="mx-auto grid w-[min(calc(100%-40px),1280px)] grid-cols-1 gap-10 lg:grid-cols-[1fr_1.4fr] lg:gap-[90px]">
            <div v-reveal>
                <div class="text-sm font-light uppercase tracking-wide text-accent">{{ content.kicker }}</div>
                <h1 class="my-4 text-[clamp(40px,6vw,78px)] font-normal leading-[1.02] tracking-[-0.04em]">{{ content.title }}</h1>
            </div>
            <div v-reveal class="space-y-6 text-lg font-light leading-relaxed text-[color:var(--color-text-muted)]">
                <p v-for="(paragraph, i) in content.paragraphs" :key="i">{{ paragraph.text }}</p>
                <p class="text-[24px] font-light leading-[1.4] text-[color:var(--color-text)]">{{ content.quote }}</p>
                <Link v-magnetic :href="content.cta_href" class="mt-4 inline-flex items-center justify-center rounded-3xl bg-accent px-6 py-3.5 font-normal text-white">
                    {{ content.cta_text }}
                </Link>
            </div>
        </div>
    </section>
</template>
