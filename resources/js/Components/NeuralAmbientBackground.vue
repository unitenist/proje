<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        fixed?: boolean;
        opacity?: number;
        showLabels?: boolean;
    }>(),
    { fixed: false, opacity: 0.85, showLabels: true },
);

const canvasRef = ref<HTMLCanvasElement | null>(null);

interface Particle {
    x: number;
    y: number;
    vx: number;
    vy: number;
    size: number;
    colorIndex: number;
    minX?: number;
}

interface LabelPoint {
    x: number;
    y: number;
    text: string;
    color: string;
}

const CONNECT_DIST = 150;
const MOUSE_RADIUS = 220;
const TERMS = ['ERP', 'CRM', 'MRP', 'AI AGENT', 'API', 'RPA', 'CLOUD', 'SQL'];

/** Reads the live theme accent variables so colors stay correct in both light and dark mode. */
function readThemeColors() {
    if (typeof window === 'undefined') return { violet: '#8052ff', amber: '#ffb829', verdant: '#15846e' };
    const styles = getComputedStyle(document.documentElement);
    const violet = styles.getPropertyValue('--color-accent').trim() || '#8052ff';
    const amber = styles.getPropertyValue('--color-accent-2').trim() || '#ffb829';
    const verdant = styles.getPropertyValue('--color-accent-3').trim() || '#15846e';
    return { violet, amber, verdant };
}

let themeColors = readThemeColors();
let palette = [themeColors.violet, themeColors.violet, themeColors.violet, themeColors.amber, themeColors.verdant];

function mulberry32(seed: number) {
    let state = seed;
    return () => {
        state |= 0;
        state = (state + 0x6d2b79f5) | 0;
        let t = Math.imul(state ^ (state >>> 15), 1 | state);
        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

const rand = mulberry32(21);

let particles: Particle[] = [];
let labeled: { particle: Particle; text: string }[] = [];
let width = 0;
let height = 0;
let rafId = 0;
let running = true;
let frame = 0;

const labelPoints = ref<LabelPoint[]>([]);
const mouse = { x: null as number | null, y: null as number | null };

function createParticles(count: number): Particle[] {
    const list: Particle[] = [];
    for (let i = 0; i < count; i++) {
        list.push({
            x: rand() * width,
            y: rand() * height,
            vx: (rand() - 0.5) * 0.6,
            vy: (rand() - 0.5) * 0.6,
            size: 1 + rand() * 1.8,
            colorIndex: Math.floor(rand() * palette.length),
        });
    }
    return list;
}

function draw(ctx: CanvasRenderingContext2D) {
    ctx.clearRect(0, 0, width, height);

    for (const p of particles) {
        const leftBound = p.minX ?? 0;
        if (p.x > width || p.x < leftBound) p.vx = -p.vx;
        if (p.y > height || p.y < 0) p.vy = -p.vy;
        p.x += p.vx;
        p.y += p.vy;

        const color = palette[p.colorIndex] ?? themeColors.violet;
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.shadowColor = color;
        ctx.shadowBlur = 5;
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
    }

    for (let a = 0; a < particles.length; a++) {
        for (let b = a + 1; b < particles.length; b++) {
            const dx = particles[a].x - particles[b].x;
            const dy = particles[a].y - particles[b].y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < CONNECT_DIST) {
                ctx.globalAlpha = (1 - dist / CONNECT_DIST) * 0.65;
                ctx.strokeStyle = themeColors.violet;
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(particles[a].x, particles[a].y);
                ctx.lineTo(particles[b].x, particles[b].y);
                ctx.stroke();
                ctx.globalAlpha = 1;
            }
        }

        if (mouse.x !== null && mouse.y !== null) {
            const dxMouse = particles[a].x - mouse.x;
            const dyMouse = particles[a].y - mouse.y;
            const distMouse = Math.sqrt(dxMouse * dxMouse + dyMouse * dyMouse);
            if (distMouse < MOUSE_RADIUS) {
                ctx.globalAlpha = (1 - distMouse / MOUSE_RADIUS) * 0.85;
                ctx.strokeStyle = themeColors.amber;
                ctx.lineWidth = 1.4;
                ctx.beginPath();
                ctx.moveTo(particles[a].x, particles[a].y);
                ctx.lineTo(mouse.x, mouse.y);
                ctx.stroke();
                ctx.globalAlpha = 1;
            }
        }
    }
}

function loop() {
    if (!running) return;
    const canvas = canvasRef.value;
    if (canvas) {
        const ctx = canvas.getContext('2d');
        if (ctx) draw(ctx);
    }
    frame++;
    if (props.showLabels && frame % 5 === 0) {
        labelPoints.value = labeled.map((l) => ({
            x: l.particle.x,
            y: l.particle.y,
            text: l.text,
            color: palette[l.particle.colorIndex] ?? themeColors.violet,
        }));
    }
    rafId = requestAnimationFrame(loop);
}

function resize() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    width = canvas.width = rect.width;
    height = canvas.height = rect.height;
    particles = createParticles(Math.min(110, Math.round((width * height) / 9000)));

    const rightBound = width * 0.58;
    const candidates = particles.filter((p) => p.x > rightBound);
    labeled = TERMS.map((text, i) => {
        const particle = candidates[Math.floor(rand() * candidates.length)] ?? particles[i];
        particle.minX = rightBound;
        if (particle.x < rightBound) particle.x = rightBound + rand() * (width - rightBound);
        return { particle, text };
    });
}

function onMouseMove(e: MouseEvent) {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    mouse.x = x >= 0 && x <= width ? x : null;
    mouse.y = y >= 0 && y <= height ? y : null;
}

function onMouseLeave() {
    mouse.x = null;
    mouse.y = null;
}

let themeObserver: MutationObserver | null = null;

onMounted(() => {
    resize();
    window.addEventListener('resize', resize);
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseout', onMouseLeave);
    rafId = requestAnimationFrame(loop);

    themeObserver = new MutationObserver(() => {
        themeColors = readThemeColors();
        palette = [themeColors.violet, themeColors.violet, themeColors.violet, themeColors.amber, themeColors.verdant];
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
});

onBeforeUnmount(() => {
    running = false;
    cancelAnimationFrame(rafId);
    window.removeEventListener('resize', resize);
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseout', onMouseLeave);
    themeObserver?.disconnect();
});
</script>

<template>
    <div class="ambient-wrap" :class="{ 'ambient-wrap-fixed': props.fixed }">
        <canvas ref="canvasRef" class="ambient-canvas" :style="{ opacity: props.opacity }" aria-hidden="true"></canvas>
        <div v-if="props.showLabels" class="ambient-labels" aria-hidden="true">
            <span
                v-for="(label, i) in labelPoints"
                :key="i"
                class="node-label"
                :style="{ transform: `translate(${label.x}px, ${label.y}px) translate(-50%, -100%)`, '--label-color': label.color }"
            >
                <i class="node-label-tick"></i>
                {{ label.text }}
            </span>
        </div>
    </div>
</template>
