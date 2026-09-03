<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import gsap from 'gsap';

const props = withDefaults(
    defineProps<{
        greeting: string;
        questions: Array<{
            topic?: string;
            text: string;
            hint?: string;
            options?: Array<{ label: string; value?: string; next?: number; interest?: boolean }>;
            yes_next?: number;
            no_next?: number;
        }>;
    }>(),
    { questions: () => [] },
);

type Mode = 'intro' | 'wandering' | 'chat' | 'assessment' | 'contact-form' | 'thanks';

const robotRef = ref<HTMLDivElement | null>(null);
const mode = ref<Mode>('intro');
const bubbleText = ref(props.greeting);
const showBubble = ref(true);
const questionIndex = ref(-1);
const currentIndex = ref(0);
const stepsTaken = ref(0);
const answers = ref<string[]>([]);
const selectedAnswers = ref<string[]>([]);
const hasInterest = ref(false);
const sending = ref(false);
const leadForm = reactive({ name: '', email: '', phone: '', notes: '' });

const MAX_STEPS = 6;
const currentQuestion = computed(() => props.questions[currentIndex.value]?.text ?? '');
const currentHint = computed(() => props.questions[currentIndex.value]?.hint ?? '');
const currentOptions = computed(() => {
    const question = props.questions[currentIndex.value];
    return question?.options ?? [
        { label: 'Evet', value: 'Evet', next: question?.yes_next, interest: true },
        { label: 'Hayır', value: 'Hayır', next: question?.no_next },
    ];
});
const assessment = computed(() => {
    const responses = selectedAnswers.value.join(' ').toLocaleLowerCase('tr-TR');
    const opportunities: string[] = [];

    if (/(erp|stok|sipariş|tahsilat|finans|satış)/.test(responses)) {
        opportunities.push('Kritik operasyon verisini tek görünümde izleme ve sapmaları erken yakalama');
    }
    if (/(manuel|excel|farklı uygulama|dağınık|parçalı)/.test(responses)) {
        opportunities.push('Tekrarlayan aktarımları ve kontrol işlerini onaylı otomasyonla azaltma');
    }
    if (/(rapor|karar|uyarı)/.test(responses)) {
        opportunities.push('Yönetimin doğal dille anlık sorgu, özet ve aksiyon önerisi alması');
    }
    if (/(yetki|onay|kvkk|izlenebilir|güven)/.test(responses)) {
        opportunities.push('Rol bazlı erişim, işlem kayıtları ve insan onayını AI tasarımına gömme');
    }

    return {
        opportunities: opportunities.slice(0, 3).length ? opportunities.slice(0, 3) : [
            'Mevcut süreçleri ölçülebilir bir AI pilotuna dönüştürme',
            'Doğru veri, yetki ve onay çerçevesiyle güvenli otomasyon kurma',
        ],
        nextStep: responses.includes('araştır')
            ? 'Şu an karar vermek zorunda değilsiniz. En düşük riskli sonraki adım, tek bir süreci seçip mevcut yöntem ile AI destekli yöntemin etkisini karşılaştırmaktır.'
            : 'Bu fırsatları tek seferde büyük bir dönüşüm yerine, başarı ölçütleri net bir pilotla doğrulamak en güvenli başlangıç olur.',
    };
});

let wanderTimer: ReturnType<typeof setInterval> | null = null;
let bubbleTimer: ReturnType<typeof setInterval> | null = null;
let introTimer: ReturnType<typeof setTimeout> | null = null;

// The widest child is the .robot-chat panel (240px) — clamp so the whole
// widget (avatar + bubble/chat, centered on `right`) always stays on-screen.
const PANEL_WIDTH = 240;
const HALF_PANEL = PANEL_WIDTH / 2;

function clampRight(centerX: number): number {
    if (typeof window === 'undefined') return HALF_PANEL;
    const rawRight = window.innerWidth - centerX - HALF_PANEL;
    const minRight = 16;
    const maxRight = window.innerWidth - PANEL_WIDTH - 16;
    return Math.min(Math.max(rawRight, minRight), maxRight);
}

// Horizontal center of the profile box column, so the docked robot lines up with it.
function dockCenterX(): number {
    if (typeof document === 'undefined' || typeof window === 'undefined') return 0;
    const profile = document.querySelector('.profile-box');
    if (profile) {
        const rect = profile.getBoundingClientRect();
        return rect.left + rect.width / 2;
    }
    return window.innerWidth - 130;
}

// Anchored by `bottom` (not `top`) so the panel/bubble always grows upward from
// a fixed floor instead of risking overflow below the viewport.
const DOCK_BOTTOM = 28;
const INTRO_BOTTOM = 220;

// Small, occasional horizontal drift near the docked spot — not a full-screen wander.
function wander() {
    if (mode.value !== 'wandering') return;
    const el = robotRef.value;
    if (!el || typeof window === 'undefined') return;

    const centerX = dockCenterX() + (Math.random() * 40 - 20);
    gsap.to(el, { right: clampRight(centerX), bottom: DOCK_BOTTOM, duration: 2.4, ease: 'power2.inOut' });
}

function nextIdleQuestion() {
    if (mode.value !== 'wandering' || !props.questions.length) return;
    questionIndex.value = (questionIndex.value + 1) % props.questions.length;
    bubbleText.value = props.questions[questionIndex.value].text;
    showBubble.value = true;
}

function dockToCorner() {
    const el = robotRef.value;
    if (!el || typeof window === 'undefined') return;
    gsap.to(el, { right: clampRight(dockCenterX()), bottom: DOCK_BOTTOM, duration: 0.9, ease: 'power2.inOut' });
}

function startWandering() {
    mode.value = 'wandering';
    showBubble.value = false;
    dockToCorner();
    wanderTimer = setInterval(wander, 9000);
    bubbleTimer = setInterval(nextIdleQuestion, 8000);
}

function startChat() {
    if (mode.value !== 'wandering' && mode.value !== 'intro') return;
    if (introTimer) clearTimeout(introTimer);
    mode.value = 'chat';
    showBubble.value = false;
    if (wanderTimer) clearInterval(wanderTimer);
    if (bubbleTimer) clearInterval(bubbleTimer);
    dockToCorner();
    currentIndex.value = 0;
    stepsTaken.value = 0;
    answers.value = [];
    selectedAnswers.value = [];
    hasInterest.value = false;
}

function answer(option: { label: string; value?: string; next?: number; interest?: boolean }) {
    if (mode.value !== 'chat') return;
    const question = props.questions[currentIndex.value];
    if (!question) {
        finishChat();
        return;
    }

    answers.value.push(`${question.topic ? `[${question.topic}] ` : ''}Soru: ${question.text}\nYanıt: ${option.value ?? option.label}`);
    selectedAnswers.value.push(option.value ?? option.label);
    stepsTaken.value += 1;
    hasInterest.value ||= option.interest === true;

    const nextIndex = option.next;
    const hasNext = typeof nextIndex === 'number' && nextIndex >= 0 && nextIndex < props.questions.length;

    if (!hasNext || stepsTaken.value >= MAX_STEPS) {
        finishChat();
        return;
    }

    currentIndex.value = nextIndex as number;
}

function finishChat() {
    if (hasInterest.value) {
        leadForm.name = '';
        leadForm.email = '';
        leadForm.phone = '';
        leadForm.notes = '';
        mode.value = 'contact-form';
    } else {
        mode.value = 'assessment';
    }
}

function requestSolutionSummary() {
    leadForm.name = '';
    leadForm.email = '';
    leadForm.phone = '';
    leadForm.notes = '';
    mode.value = 'contact-form';
}

async function submitLead() {
    sending.value = true;
    const token = decodeURIComponent(document.cookie.split('; ').find((c) => c.startsWith('XSRF-TOKEN='))?.split('=')[1] ?? '');
    try {
        await fetch('/robot-lead', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': token },
            body: JSON.stringify({
                answers: answers.value,
                name: leadForm.name,
                email: leadForm.email,
                phone: leadForm.phone,
                notes: leadForm.notes,
            }),
        });
    } finally {
        sending.value = false;
        mode.value = 'thanks';
        setTimeout(closeChat, 3200);
    }
}

function closeChat() {
    startWandering();
}

onMounted(() => {
    if (typeof window === 'undefined') return;
    const el = robotRef.value;
    if (el) {
        // Appear front-and-center first so it's clearly noticed, then settle near the profile column.
        gsap.set(el, { right: clampRight(window.innerWidth / 2), bottom: INTRO_BOTTOM });
    }
    mode.value = 'intro';
    bubbleText.value = props.greeting;
    showBubble.value = true;
    introTimer = setTimeout(startWandering, 4000);
});

onBeforeUnmount(() => {
    if (wanderTimer) clearInterval(wanderTimer);
    if (bubbleTimer) clearInterval(bubbleTimer);
    if (introTimer) clearTimeout(introTimer);
});
</script>

<template>
    <div ref="robotRef" class="robot-guide" role="complementary" aria-label="AI asistan">
        <Transition name="bubble-fade">
            <div v-if="(mode === 'wandering' || mode === 'intro') && showBubble" class="robot-bubble glass-panel" @click="startChat">
                {{ bubbleText }}
            </div>
        </Transition>

        <Transition name="bubble-fade">
            <div v-if="mode === 'chat'" class="robot-chat glass-panel">
                <div class="robot-chat-status"><span></span> AI keşif motoru analiz ediyor</div>
                <span v-if="props.questions[currentIndex]?.topic" class="robot-chat-topic">{{ props.questions[currentIndex]?.topic }}</span>
                <p class="robot-chat-question">{{ currentQuestion }}</p>
                <p v-if="currentHint" class="robot-chat-hint">{{ currentHint }}</p>
                <div class="robot-chat-actions">
                    <button v-for="option in currentOptions" :key="option.label" type="button" class="robot-chat-btn" @click="answer(option)">
                        {{ option.label }}
                    </button>
                </div>
                <span class="robot-chat-step">Soru {{ stepsTaken + 1 }}</span>
            </div>
            <div v-else-if="mode === 'assessment'" class="robot-chat robot-assessment glass-panel">
                <div class="robot-chat-status"><span></span> Analiz tamamlandı</div>
                <span class="robot-chat-topic">Sizin için görünen fırsatlar</span>
                <p class="robot-chat-question">Şu an görüşme planlamasanız bile bu alanlarda değer kaybı oluşabilir:</p>
                <ul class="robot-assessment-list">
                    <li v-for="opportunity in assessment.opportunities" :key="opportunity">{{ opportunity }}</li>
                </ul>
                <p class="robot-chat-hint">{{ assessment.nextStep }}</p>
                <div class="robot-chat-actions">
                    <button type="button" class="robot-chat-btn robot-chat-btn-yes" @click="requestSolutionSummary">Bana çözüm özeti hazırlayın</button>
                    <button type="button" class="robot-chat-btn" @click="closeChat">Şimdilik keşfe devam et</button>
                </div>
            </div>
            <form v-else-if="mode === 'contact-form'" class="robot-chat glass-panel" @submit.prevent="submitLead">
                <div class="robot-chat-status"><span></span> Çözüm özeti hazırlanıyor</div>
                <p class="robot-chat-question">Yanıtlarınıza göre kısa bir çözüm özeti hazırlayabilmemiz için bilgilerinizi bırakır mısınız?</p>
                <input v-model="leadForm.name" type="text" placeholder="Adınız" required class="robot-form-input" />
                <input v-model="leadForm.email" type="email" placeholder="E-posta" required class="robot-form-input" />
                <input v-model="leadForm.phone" type="tel" placeholder="Telefon (opsiyonel)" class="robot-form-input" />
                <textarea v-model="leadForm.notes" placeholder="Ek talepleriniz (opsiyonel)" rows="2" class="robot-form-input robot-form-textarea"></textarea>
                <button type="submit" class="robot-chat-btn robot-chat-btn-yes robot-chat-btn-full" :disabled="sending">
                    {{ sending ? 'Gönderiliyor…' : 'Gönder' }}
                </button>
            </form>
            <div v-else-if="mode === 'thanks'" class="robot-chat glass-panel">
                <p class="robot-chat-question">{{ sending ? 'Kaydediliyor…' : 'Teşekkürler! Ekibimiz en kısa sürede size dönüş yapacak.' }}</p>
            </div>
        </Transition>

        <button
            type="button"
            class="robot-avatar"
            :class="{ 'robot-avatar-intro': mode === 'intro' }"
            aria-label="Asistanla konuş"
            @click="startChat"
        >
            <span v-if="mode === 'intro'" class="robot-wave" aria-hidden="true">👋</span>
            <i v-else class="ri-robot-2-line" aria-hidden="true"></i>
        </button>
    </div>
</template>

<style scoped>
.robot-guide {
    position: fixed;
    z-index: 25;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    pointer-events: none;
}

.robot-avatar {
    display: flex;
    height: 56px;
    width: 56px;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 26px;
    color: #fff;
    background: linear-gradient(135deg, var(--color-accent), var(--color-accent-3));
    box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-accent) 55%, transparent);
    animation: robot-float 3.2s ease-in-out infinite, robot-pulse 2.6s ease-out infinite;
    pointer-events: auto;
    cursor: pointer;
}

.robot-avatar-intro {
    height: 72px;
    width: 72px;
    font-size: 34px;
    animation: robot-appear 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), robot-pulse 2.6s ease-out infinite;
}

.robot-wave {
    display: inline-block;
    transform-origin: 70% 70%;
    animation: robot-wave 0.6s ease-in-out infinite;
}

.robot-bubble {
    max-width: 220px;
    border-radius: 16px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 300;
    text-align: center;
    cursor: pointer;
    pointer-events: auto;
}

.robot-chat {
    display: flex;
    width: 240px;
    flex-direction: column;
    align-items: center;
    gap: 11px;
    border-radius: 18px;
    padding: 16px;
    text-align: center;
    pointer-events: auto;
}

.robot-chat-question {
    margin: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.45;
}

.robot-chat-status {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--color-text-muted);
    font-size: 9px;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.robot-chat-status span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--color-accent-3);
    box-shadow: 0 0 8px var(--color-accent-3);
    animation: robot-status-pulse 1.4s ease-in-out infinite;
}

.robot-chat-topic {
    color: var(--color-accent-2);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.robot-chat-hint {
    margin: -3px 0 0;
    color: var(--color-text-muted);
    font-size: 11px;
    font-weight: 300;
    line-height: 1.45;
}

.robot-assessment {
    gap: 10px;
}

.robot-assessment-list {
    display: flex;
    width: 100%;
    flex-direction: column;
    gap: 7px;
    margin: 0;
    padding: 0;
    text-align: left;
    list-style: none;
}

.robot-assessment-list li {
    position: relative;
    padding-left: 15px;
    color: var(--color-text);
    font-size: 11px;
    font-weight: 300;
    line-height: 1.4;
}

.robot-assessment-list li::before {
    position: absolute;
    top: 0.45em;
    left: 0;
    width: 6px;
    height: 6px;
    border: 1px solid var(--color-accent-2);
    border-radius: 50%;
    content: '';
}

.robot-chat-actions {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 8px;
}

.robot-chat-btn {
    border-radius: 999px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 400;
    color: var(--color-text);
    border: 1px solid color-mix(in srgb, var(--color-text) 15%, transparent);
    text-align: left;
    transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}

.robot-chat-btn:hover {
    color: var(--color-accent);
    border-color: color-mix(in srgb, var(--color-accent) 45%, transparent);
    background: color-mix(in srgb, var(--color-accent) 10%, transparent);
}

.robot-chat-btn-yes {
    color: #fff;
    background: var(--color-accent);
    border-color: transparent;
}

.robot-chat-btn-full {
    width: 100%;
}

.robot-form-input {
    width: 100%;
    border-radius: 10px;
    border: 1px solid color-mix(in srgb, var(--color-text) 15%, transparent);
    background: color-mix(in srgb, var(--color-text) 4%, transparent);
    padding: 8px 12px;
    font-size: 13px;
    color: var(--color-text);
    outline: none;
}

.robot-form-input:focus {
    border-color: var(--color-accent);
}

.robot-form-textarea {
    resize: none;
    font-family: inherit;
}

.robot-chat-step {
    font-size: 11px;
    font-weight: 300;
    letter-spacing: 0.05em;
    color: var(--color-text-muted);
}

.bubble-fade-enter-active,
.bubble-fade-leave-active {
    transition: opacity 0.35s ease, transform 0.35s ease;
}

.bubble-fade-enter-from,
.bubble-fade-leave-to {
    opacity: 0;
    transform: translateY(6px);
}

@keyframes robot-float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-6px);
    }
}

@keyframes robot-appear {
    0% {
        transform: scale(0.2);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes robot-wave {
    0%,
    100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-18deg);
    }
    75% {
        transform: rotate(18deg);
    }
}

@keyframes robot-pulse {
    0% {
        box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-accent) 45%, transparent);
    }
    70% {
        box-shadow: 0 0 0 14px color-mix(in srgb, var(--color-accent) 0%, transparent);
    }
    100% {
        box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-accent) 0%, transparent);
    }
}

@keyframes robot-status-pulse {
    50% {
        opacity: 0.35;
        transform: scale(0.7);
    }
}

@media (max-width: 640px) {
    .robot-guide {
        display: none;
    }
}
</style>
