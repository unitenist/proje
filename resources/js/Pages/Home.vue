<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import OnePageLayout from '@/Layouts/OnePageLayout.vue';
import RobotGuide from '@/Components/RobotGuide.vue';

interface HomeContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    subtitle: string;
    cta_primary_text: string;
    cta_primary_href: string;
    cta_secondary_text: string;
    cta_secondary_href: string;
}

interface AboutContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    quote: string;
    paragraphs: Array<{ text: string }>;
}

interface ApproachContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    subtitle: string;
    steps: Array<{ label: string; title: string; description: string }>;
}

interface ServicesContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    subtitle: string;
    items: Array<{ icon: string; title: string; description: string }>;
}

interface SkillsContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    items: Array<{ logo?: string | null; name: string; percentage: number }>;
}

interface LabContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    subtitle: string;
    projects: Array<{ tag: string; title: string; description: string }>;
}

interface TestimonialsContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    items: Array<{ quote: string; author: string; role?: string }>;
}

interface ContactContent {
    seo_title: string;
    seo_description: string;
    kicker: string;
    title: string;
    subtitle: string;
    email: string;
    phone?: string;
    address?: string;
    linkedin: string;
}

interface SiteSettings {
    logo: string | null;
    profile_photo: string | null;
    profile_name: string;
    profile_title: string;
}

interface AssistantContent {
    enabled: boolean;
    greeting: string;
    questions: Array<{
        topic?: string;
        text: string;
        hint?: string;
        options?: Array<{ label: string; value?: string; next?: number; interest?: boolean }>;
        yes_next?: number;
        no_next?: number;
    }>;
}

const props = defineProps<{
    site: SiteSettings;
    assistant: AssistantContent;
    content: HomeContent;
    about: AboutContent;
    approach: ApproachContent;
    services: ServicesContent;
    skills: SkillsContent;
    lab: LabContent;
    testimonials: TestimonialsContent;
    contact: ContactContent;
}>();

const openAccordion = ref(0);

function toggleAccordion(index: number) {
    openAccordion.value = openAccordion.value === index ? -1 : index;
}

const contactForm = useForm({ name: '', email: '', phone: '', website: '', message: '' });
const contactSent = computed(() => (usePage().props as any).flash?.success as string | undefined);

function submitContactForm() {
    contactForm.post('/iletisim', {
        preserveScroll: true,
        onSuccess: () => contactForm.reset(),
    });
}

const jsonLd = computed(() => {
    const sections = [
        { id: 'home', content: props.content },
        { id: 'about', content: props.about },
        { id: 'resume', content: props.approach },
        { id: 'services', content: props.services },
        { id: 'skills', content: props.skills },
        { id: 'portfolio', content: props.lab },
        { id: 'comments', content: props.testimonials },
        { id: 'contact', content: props.contact },
    ];

    return JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': [
            {
                '@type': 'Organization',
                '@id': 'https://www.unitenist.com/#organization',
                name: 'UNITENIST',
                url: 'https://www.unitenist.com/',
                founder: { '@type': 'Person', name: 'Sermet Sandıkçı' },
                description: 'AI, ERP entegrasyonu ve iş süreçleri otomasyonu çözümleri.',
            },
            {
                '@type': 'WebPage',
                '@id': 'https://www.unitenist.com/#webpage',
                url: 'https://www.unitenist.com/',
                name: props.content.seo_title,
                description: props.content.seo_description,
                isPartOf: { '@id': 'https://www.unitenist.com/#organization' },
                hasPart: sections.map((s) => ({
                    '@type': 'WebPageElement',
                    '@id': `https://www.unitenist.com/#${s.id}`,
                    name: s.content.seo_title,
                    description: s.content.seo_description,
                })),
            },
            {
                '@type': 'ItemList',
                name: 'Hizmetler',
                itemListElement: props.services.items.map((item, i) => ({
                    '@type': 'Service',
                    position: i + 1,
                    name: item.title,
                    description: item.description,
                    provider: { '@id': 'https://www.unitenist.com/#organization' },
                })),
            },
            {
                '@type': 'Review',
                itemReviewed: { '@id': 'https://www.unitenist.com/#organization' },
                reviewBody: props.testimonials.items[0]?.quote,
                author: { '@type': 'Person', name: props.testimonials.items[0]?.author },
            },
        ],
    });
});
</script>

<template>
    <Head>
        <title>{{ content.seo_title }}</title>
        <meta name="description" :content="content.seo_description" />
        <link rel="canonical" href="https://www.unitenist.com/" />
        <meta property="og:title" :content="content.seo_title" />
        <meta property="og:description" :content="content.seo_description" />
        <meta property="og:url" content="https://www.unitenist.com/" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="content.seo_title" />
        <meta name="twitter:description" :content="content.seo_description" />
        <component is="script" type="application/ld+json" v-html="jsonLd"></component>
    </Head>

    <OnePageLayout
        :logo="site.logo"
        :profile="{ name: site.profile_name, title: site.profile_title, photo: site.profile_photo, email: contact.email, linkedin: contact.linkedin }"
    >
        <!-- Home -->
        <section id="home" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ content.kicker }}</div>
            <h1 v-reveal class="my-5 text-[clamp(38px,6vw,70px)] font-normal leading-[1.05] tracking-[-0.03em]">{{ content.title }}</h1>
            <p v-reveal class="max-w-[620px] text-lg font-light leading-relaxed text-[color:var(--color-text-muted)]">{{ content.subtitle }}</p>
            <div v-reveal class="mt-9 flex flex-wrap gap-4">
                <a v-magnetic :href="content.cta_primary_href" class="inline-flex items-center justify-center rounded-full bg-[color:var(--color-accent)] px-6 py-3.5 font-normal text-white">
                    {{ content.cta_primary_text }}
                </a>
                <a v-magnetic :href="content.cta_secondary_href" class="inline-flex items-center justify-center px-2 py-3.5 font-normal text-[color:var(--color-accent-2)]">
                    {{ content.cta_secondary_text }}
                </a>
            </div>
        </section>

        <!-- About -->
        <section id="about" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ about.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ about.title }}</h2>
            <div class="mt-6 space-y-5 text-lg font-light leading-relaxed text-[color:var(--color-text-muted)]">
                <p v-for="(p, i) in about.paragraphs" :key="i" v-reveal>{{ p.text }}</p>
                <p v-reveal class="text-[22px] font-light leading-[1.5] text-[color:var(--color-text)]">{{ about.quote }}</p>
            </div>
        </section>

        <!-- Resume / Yaklaşım -->
        <section id="resume" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ approach.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ approach.title }}</h2>
            <p v-reveal class="max-w-[620px] text-lg font-light text-[color:var(--color-text-muted)]">{{ approach.subtitle }}</p>
            <div class="mt-10 space-y-0">
                <div
                    v-for="(step, i) in approach.steps"
                    :key="step.title"
                    v-reveal
                    class="flex flex-col gap-1 border-b border-[color:color-mix(in_srgb,var(--color-text)_10%,transparent)] py-6 sm:flex-row sm:items-baseline sm:gap-8"
                >
                    <span class="w-[110px] shrink-0 text-sm font-semibold tracking-[0.1em] text-[color:var(--color-accent-2)]">{{ String(i + 1).padStart(2, '0') }} / {{ step.label }}</span>
                    <div>
                        <h3 class="text-xl font-normal">{{ step.title }}</h3>
                        <p class="mt-1.5 font-light text-[color:var(--color-text-muted)]">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services (accordion) -->
        <section id="services" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ services.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ services.title }}</h2>
            <p v-reveal class="max-w-[620px] text-lg font-light text-[color:var(--color-text-muted)]">{{ services.subtitle }}</p>
            <div class="mt-8">
                <div
                    v-for="(item, i) in services.items"
                    :key="item.title"
                    v-reveal
                    class="accordion-item"
                    :class="{ 'is-open': openAccordion === i }"
                >
                    <button type="button" class="accordion-header w-full text-left" @click="toggleAccordion(i)">
                        <span>{{ item.title }}</span>
                        <span class="accordion-arrow"><i class="ri-arrow-right-down-line"></i></span>
                    </button>
                    <div class="accordion-content">
                        <p>{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Skills -->
        <section id="skills" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ skills.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ skills.title }}</h2>
            <div class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                <div v-for="(skill, i) in skills.items" :key="skill.name" v-reveal="'flip'" class="skill-card glass-panel" :style="{ transitionDelay: `${i * 60}ms` }">
                    <div class="skill-card-icon">
                        <img v-if="skill.logo" :src="skill.logo" :alt="skill.name" />
                        <span v-else class="skill-card-fallback">{{ skill.name.slice(0, 2).toUpperCase() }}</span>
                        <span class="skill-card-badge">{{ skill.percentage }}%</span>
                    </div>
                    <span class="skill-card-name">{{ skill.name }}</span>
                </div>
            </div>
        </section>

        <!-- Portfolio / Projects -->
        <section id="portfolio" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">Projeler</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ lab.title }}</h2>
            <p v-reveal class="max-w-[620px] text-lg font-light text-[color:var(--color-text-muted)]">{{ lab.subtitle }}</p>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <article v-for="(project, i) in lab.projects" :key="project.title" v-reveal="'flip'" class="project-card glass-panel" :style="{ transitionDelay: `${i * 70}ms` }">
                    <span class="project-card-tag">{{ project.tag }}</span>
                    <h3 class="text-xl font-normal">{{ project.title }}</h3>
                    <p class="font-light text-[color:var(--color-text-muted)]">{{ project.description }}</p>
                </article>
            </div>
        </section>

        <!-- Testimonials -->
        <section id="comments" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ testimonials.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ testimonials.title }}</h2>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <article v-for="(t, i) in testimonials.items" :key="t.author + i" v-reveal="'flip'" class="comment-card glass-panel" :style="{ transitionDelay: `${i * 70}ms` }">
                    <p class="m-0 text-[16px] font-light leading-relaxed">"{{ t.quote }}"</p>
                    <div class="text-sm font-normal">
                        {{ t.author }}
                        <span v-if="t.role" class="font-light text-[color:var(--color-text-muted)]"> · {{ t.role }}</span>
                    </div>
                </article>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="scroll-mt-24 py-10 lg:py-16">
            <div v-reveal class="eyebrow-badge">{{ contact.kicker }}</div>
            <h2 v-reveal class="my-5 text-[clamp(30px,4.5vw,48px)] font-normal leading-[1.1] tracking-[-0.02em]">{{ contact.title }}</h2>
            <p v-reveal class="max-w-[620px] text-lg font-light text-[color:var(--color-text-muted)]">{{ contact.subtitle }}</p>

            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="contact-card glass-panel">
                    <p class="mb-3 text-lg font-normal">İletişim Bilgileri</p>
                    <p class="m-0 font-light text-[color:var(--color-text-muted)]">{{ contact.email }}</p>
                    <p v-if="contact.phone" class="m-0 font-light text-[color:var(--color-text-muted)]">{{ contact.phone }}</p>
                </div>
                <div v-if="contact.address" class="contact-card glass-panel">
                    <p class="mb-3 text-lg font-normal">Adres</p>
                    <p class="m-0 font-light text-[color:var(--color-text-muted)]">{{ contact.address }}</p>
                </div>
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="submitContactForm">
                <div v-if="contactSent" class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-light text-emerald-300">
                    {{ contactSent }}
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Ad</label>
                        <input id="name" v-model="contactForm.name" type="text" class="form-input" placeholder="Adınız" required />
                        <p v-if="contactForm.errors.name" class="mt-1 text-sm font-light text-red-400">{{ contactForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="email" class="form-label">E-posta</label>
                        <input id="email" v-model="contactForm.email" type="email" class="form-input" placeholder="E-postanız" required />
                        <p v-if="contactForm.errors.email" class="mt-1 text-sm font-light text-red-400">{{ contactForm.errors.email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="form-label">Telefon (opsiyonel)</label>
                        <input id="phone" v-model="contactForm.phone" type="tel" class="form-input" placeholder="Telefon numaranız" />
                    </div>
                    <div>
                        <label for="website" class="form-label">Web sitesi (opsiyonel)</label>
                        <input id="website" v-model="contactForm.website" type="url" class="form-input" placeholder="Şirket web sitesi" />
                    </div>
                </div>
                <div>
                    <label for="message" class="form-label">Mesaj</label>
                    <textarea id="message" v-model="contactForm.message" class="form-textarea" rows="5" placeholder="Mesajınız" required></textarea>
                    <p v-if="contactForm.errors.message" class="mt-1 text-sm font-light text-red-400">{{ contactForm.errors.message }}</p>
                </div>
                <button type="submit" :disabled="contactForm.processing" class="inline-flex items-center justify-center rounded-full bg-[color:var(--color-accent)] px-7 py-3.5 font-normal text-white disabled:opacity-50">
                    {{ contactForm.processing ? 'Gönderiliyor…' : 'Gönder' }}
                </button>
            </form>
        </section>
    </OnePageLayout>

    <!-- Teleported to <body> so it always stays fixed to the viewport, never clipped/scrolled by the content column. -->
    <Teleport to="body">
        <RobotGuide v-if="assistant.enabled" :greeting="assistant.greeting" :questions="assistant.questions" />
    </Teleport>
</template>

