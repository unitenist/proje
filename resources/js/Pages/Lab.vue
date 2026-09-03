<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TerminalDemo from '@/Components/TerminalDemo.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    content: {
        kicker: string;
        title: string;
        subtitle: string;
        quote: string;
        cta_text: string;
        cta_href: string;
        projects: Array<{ tag: string; title: string; description: string }>;
    };
}>();
</script>

<template>
    <Head>
        <title>AI Lab | UNITENIST</title>
        <meta name="description" content="UNITENIST AI Lab: gerçek işletme problemlerini AI agent, veri erişimi, otomasyon ve güvenli entegrasyon mimarileriyle çözüyoruz." />
        <link rel="canonical" href="https://www.unitenist.com/ai-lab" />
        <meta property="og:title" content="AI Lab | UNITENIST" />
        <meta property="og:url" content="https://www.unitenist.com/ai-lab" />
    </Head>

    <section class="py-[100px]">
        <div class="mx-auto w-[min(calc(100%-40px),1280px)]">
            <div v-reveal class="mb-[70px] max-w-[820px]">
                <div class="text-sm font-light uppercase tracking-wide text-accent">{{ content.kicker }}</div>
                <h1 class="my-4 text-[clamp(40px,6vw,78px)] font-normal leading-[1.02] tracking-[-0.04em]">{{ content.title }}</h1>
                <p class="text-lg font-light text-[color:var(--color-text-muted)]">{{ content.subtitle }}</p>
            </div>
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                <div v-reveal>
                    <TerminalDemo />
                </div>
                <div v-reveal="'flip'">
                    <p class="m-0 text-[26px] font-light leading-[1.4] text-[color:var(--color-text)]">
                        "{{ content.quote }}"
                    </p>
                    <Link v-magnetic href="/iletisim" class="mt-8 inline-flex items-center justify-center rounded-3xl bg-accent px-6 py-3.5 font-normal text-white">
                        {{ content.cta_text }}
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <section class="py-[80px]">
        <div class="mx-auto w-[min(calc(100%-40px),1280px)]">
            <div v-reveal class="mb-[50px] max-w-[730px]">
                <div class="text-sm font-light uppercase tracking-wide text-accent">Projeler</div>
                <h2 class="my-4 text-[clamp(30px,4vw,48px)] font-normal leading-[1.05] tracking-[-0.03em]">Lab'ta üzerinde çalıştığımız senaryolar.</h2>
            </div>
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="(project, i) in content.projects" :key="project.title" v-reveal="'flip'" class="rounded-[20px] border border-white/10 p-7" :style="{ transitionDelay: `${i * 80}ms` }">
                    <div class="mb-4 text-sm font-semibold tracking-[0.025em] text-[color:var(--color-accent-2)]">{{ project.tag }}</div>
                    <h3 class="mb-2.5 text-[22px] font-normal leading-[1.15] tracking-[-0.02em]">{{ project.title }}</h3>
                    <p class="m-0 text-[15px] font-light leading-relaxed text-[color:var(--color-text-muted)]">{{ project.description }}</p>
                </article>
            </div>
        </div>
    </section>
</template>
