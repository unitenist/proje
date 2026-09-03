<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import gsap from 'gsap';

const props = withDefaults(
    defineProps<{
        greeting: string;
        questions: Array<{ text: string; yes_next?: number; no_next?: number }>;
    }>(),
    { questions: () => [] },
);

type Mode = 'intro' | 'wandering' | 'chat' | 'contact-form' | 'thanks';

const robotRef = ref<HTMLDivElement | null>(null);
const mode = ref<Mode>('intro');
const bubbleText = ref(props.greeting);
const showBubble = ref(true);
const questionIndex = ref(-1);
const currentIndex = ref(0);
const stepsTaken = ref(0);
const answers = ref<string[]>([]);
const sending = ref(false);
const leadForm = reactive({ name: '', email: '', phone: '', notes: '' });

const MAX_STEPS = 6;
const currentQuestion = computed(() => props.questions[currentIndex.value]?.text ?? '');

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
}

// Each answer picks the next question via yes_next/no_next — a simple branching
// conversation tree so the flow feels adaptive rather than a fixed static list.
function answer(reply: 'Evet' | 'Hayır') {
    if (mode.value !== 'chat') return;
    const question = props.questions[currentIndex.value];
    if (!question) {
        finishChat();
        return;
    }

    answers.value.push(`Soru: ${question.text} → Cevap: ${reply}`);
    stepsTaken.value += 1;

    const nextIndex = reply === 'Evet' ? question.yes_next : question.no_next;
    const hasNext = typeof nextIndex === 'number' && nextIndex >= 0 && nextIndex < props.questions.length;

    if (!hasNext || stepsTaken.value >= MAX_STEPS) {
        finishChat();
        return;
    }

    currentIndex.value = nextIndex as number;
}

function finishChat() {
    // If there was any interest along the path taken, ask for contact details right in the widget.
    const hasInterest = answers.value.some((a) => a.endsWith('Evet'));
    if (hasInterest) {
        leadForm.name = '';
        leadForm.email = '';
        leadForm.phone = '';
        leadForm.notes = '';
        mode.value = 'contact-form';
    } else {
        mode.value = 'thanks';
        setTimeout(closeChat, 2600);
    }
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
                <p class="robot-chat-question">{{ currentQuestion }}</p>
                <div class="robot-chat-actions">
                    <button type="button" class="robot-chat-btn robot-chat-btn-yes" @click="answer('Evet')">Evet</button>
                    <button type="button" class="robot-chat-btn" @click="answer('Hayır')">Hayır</button>
                </div>
                <span class="robot-chat-step">Soru {{ stepsTaken + 1 }}</span>
            </div>
            <form v-else-if="mode === 'contact-form'" class="robot-chat glass-panel" @submit.prevent="submitLead">
                <p class="robot-chat-question">Harika! Size ulaşabilmemiz için bilgilerinizi bırakır mısınız?</p>
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
    gap: 10px;
    border-radius: 18px;
    padding: 16px;
    text-align: center;
    pointer-events: auto;
}

.robot-chat-question {
    margin: 0;
    font-size: 14px;
    font-weight: 300;
}

.robot-chat-actions {
    display: flex;
    gap: 8px;
}

.robot-chat-btn {
    border-radius: 999px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 400;
    color: var(--color-text);
    border: 1px solid color-mix(in srgb, var(--color-text) 15%, transparent);
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

@media (max-width: 640px) {
    .robot-guide {
        display: none;
    }
}
</style>
