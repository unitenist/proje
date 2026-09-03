<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProcessStep from '@/Components/ProcessStep.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    content: {
        kicker: string;
        title: string;
        subtitle: string;
        cta_text: string;
        cta_href: string;
        steps: Array<{ label: string; title: string; description: string }>;
    };
}>();
</script>

<template>
    <Head>
        <title>Yaklaşımımız | UNITENIST</title>
        <meta name="description" content="UNITENIST'in AI dönüşüm yaklaşımı: analiz, tasarım, entegrasyon ve ölçüm adımlarıyla ERP'nizi yapay zekâ ile buluşturuyoruz." />
        <link rel="canonical" href="https://www.unitenist.com/yaklasimimiz" />
        <meta property="og:title" content="Yaklaşımımız | UNITENIST" />
        <meta property="og:url" content="https://www.unitenist.com/yaklasimimiz" />
    </Head>

    <section class="py-[100px]">
        <div class="mx-auto w-[min(calc(100%-40px),1280px)]">
            <div v-reveal class="mb-[70px] max-w-[820px]">
                <div class="text-sm font-light uppercase tracking-wide text-accent">{{ content.kicker }}</div>
                <h1 class="my-4 text-[clamp(40px,6vw,78px)] font-normal leading-[1.02] tracking-[-0.04em]">{{ content.title }}</h1>
                <p class="text-lg font-light text-[color:var(--color-text-muted)]">{{ content.subtitle }}</p>
            </div>
            <div class="grid grid-cols-1 gap-x-10 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
                <ProcessStep
                    v-for="(step, i) in content.steps"
                    :key="step.title"
                    :number="String(i + 1).padStart(2, '0')"
                    v-bind="step"
                    :delay="i * 80"
                />
            </div>
            <div v-reveal class="mt-16">
                <Link v-magnetic href="/iletisim" class="inline-flex items-center justify-center rounded-3xl bg-accent px-6 py-3.5 font-normal text-white">
                    {{ content.cta_text }}
                </Link>
            </div>
        </div>
    </section>
</template>
