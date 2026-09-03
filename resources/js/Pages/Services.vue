<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    content: {
        kicker: string;
        title: string;
        subtitle: string;
        items: Array<{ icon: string; title: string; description: string }>;
    };
}>();
</script>

<template>
    <Head>
        <title>Hizmetler | UNITENIST</title>
        <meta name="description" content="AI ERP entegrasyonu, AI agent geliştirme, sistem entegrasyonu ve iş süreçleri otomasyonu hizmetlerimizi keşfedin." />
        <link rel="canonical" href="https://www.unitenist.com/hizmetler" />
        <meta property="og:title" content="Hizmetler | UNITENIST" />
        <meta property="og:description" content="AI ERP entegrasyonu, AI agent geliştirme, sistem entegrasyonu ve iş süreçleri otomasyonu hizmetlerimiz." />
        <meta property="og:url" content="https://www.unitenist.com/hizmetler" />
    </Head>

    <section class="py-[100px]">
        <div class="mx-auto w-[min(calc(100%-40px),1280px)]">
            <div v-reveal class="mb-[70px] max-w-[820px]">
                <div class="text-sm font-light uppercase tracking-[0.025em] text-[color:var(--color-accent)]">{{ content.kicker }}</div>
                <h1 class="my-4 text-[clamp(40px,6vw,78px)] font-normal leading-[1.02] tracking-[-0.04em]">{{ content.title }}</h1>
                <p class="text-lg font-light text-[color:var(--color-text-muted)]">{{ content.subtitle }}</p>
            </div>
            <div class="grid grid-cols-1 gap-x-10 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
                <ServiceCard v-for="(service, i) in content.items" :key="service.title" v-bind="service" :delay="i * 70" />
            </div>
        </div>
    </section>
</template>
