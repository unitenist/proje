<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import gsap from 'gsap';

const emit = defineEmits<{ done: [] }>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const wrapRef = ref<HTMLDivElement | null>(null);
const percent = ref(0);

interface Node {
    x: number;
    y: number;
    hub: boolean;
    color: string;
    revealAt: number;
    revealedAt: number | null;
}

interface Edge {
    a: number;
    b: number;
    revealedAt: number | null;
}

interface Pulse {
    edge: number;
    t: number;
    speed: number;
    reverse: boolean;
}

const PALETTE = { violet: '#8052ff', amber: '#ffb829', verdant: '#15846e', white: '#ffffff' };

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

const rand = mulberry32(11);

/** Rejection-samples an organic, single-lobe node cluster (normalized 0..1 coordinates). */
function buildNodes(count: number): Node[] {
    const nodes: Node[] = [];
    let attempts = 0;
    while (nodes.length < count && attempts < count * 10) {
        attempts++;
        const theta = rand() * Math.PI * 2;
        const bump = 1 + 0.12 * Math.sin(5 * theta) + 0.06 * Math.sin(9 * theta + 1.3);
        const radiusFrac = Math.sqrt(rand());
        const rx = 0.3 * bump * radiusFrac;
        const ry = 0.24 * bump * radiusFrac;
        const x = 0.5 + Math.cos(theta) * rx;
        const y = 0.52 + Math.sin(theta) * ry;
        const roll = rand();
        nodes.push({
            x,
            y,
            hub: roll > 0.92,
            color: roll > 0.92 ? PALETTE.white : roll > 0.72 ? PALETTE.amber : roll > 0.62 ? PALETTE.verdant : PALETTE.violet,
            revealAt: x + (rand() - 0.5) * 0.06,
            revealedAt: null,
        });
    }
    return nodes;
}

function buildEdges(nodes: Node[]): Edge[] {
    const edges: Edge[] = [];
    const seen = new Set<string>();
    for (let i = 0; i < nodes.length; i++) {
        const distances: { j: number; d: number }[] = [];
        for (let j = 0; j < nodes.length; j++) {
            if (i === j) continue;
            const dx = nodes[i].x - nodes[j].x;
            const dy = nodes[i].y - nodes[j].y;
            distances.push({ j, d: Math.sqrt(dx * dx + dy * dy) });
        }
        distances.sort((a, b) => a.d - b.d);
        for (const { j, d } of distances.slice(0, 3)) {
            if (d > 0.11) continue;
            const key = i < j ? `${i}-${j}` : `${j}-${i}`;
            if (seen.has(key)) continue;
            seen.add(key);
            edges.push({ a: i, b: j, revealedAt: null });
        }
    }
    return edges;
}

const nodes = buildNodes(85);
const edges = buildEdges(nodes);
const pulses: Pulse[] = [];

const timeline = { scan: 0 };
let rafId = 0;
let startTime = 0;
let running = true;

function ensurePulses(revealedEdgeCount: number) {
    const target = Math.min(10, Math.floor(revealedEdgeCount / 4));
    while (pulses.length < target) {
        const candidates = edges.map((e, i) => i).filter((i) => edges[i].revealedAt !== null);
        if (candidates.length === 0) break;
        const edge = candidates[Math.floor(rand() * candidates.length)];
        pulses.push({ edge, t: rand(), speed: 0.35 + rand() * 0.4, reverse: rand() > 0.5 });
    }
}

function draw(ctx: CanvasRenderingContext2D, w: number, h: number, elapsed: number) {
    ctx.clearRect(0, 0, w, h);

    const cx = w * 0.5;
    const cy = h * 0.52;
    const ringR = Math.min(w, h) * 0.44;

    // outer rotating dashed HUD ring
    ctx.save();
    ctx.translate(cx, cy);
    ctx.rotate(elapsed * 0.00025);
    ctx.strokeStyle = 'rgba(128,82,255,0.35)';
    ctx.lineWidth = 1;
    ctx.setLineDash([2, 10]);
    ctx.beginPath();
    ctx.arc(0, 0, ringR, 0, Math.PI * 2);
    ctx.stroke();
    ctx.restore();

    // inner counter-rotating tick ring
    ctx.save();
    ctx.translate(cx, cy);
    ctx.rotate(-elapsed * 0.00018);
    ctx.strokeStyle = 'rgba(255,184,41,0.28)';
    ctx.lineWidth = 1.4;
    for (let i = 0; i < 36; i++) {
        const a = (i / 36) * Math.PI * 2;
        const r1 = ringR * 0.9;
        const r2 = ringR * (i % 3 === 0 ? 0.82 : 0.87);
        ctx.beginPath();
        ctx.moveTo(Math.cos(a) * r1, Math.sin(a) * r1);
        ctx.lineTo(Math.cos(a) * r2, Math.sin(a) * r2);
        ctx.stroke();
    }
    ctx.restore();

    const scan = timeline.scan;
    let revealedEdgeCount = 0;

    // edges
    for (const edge of edges) {
        const n1 = nodes[edge.a];
        const n2 = nodes[edge.b];
        const threshold = Math.max(n1.revealAt, n2.revealAt);
        if (scan >= threshold) {
            if (edge.revealedAt === null) edge.revealedAt = elapsed;
            revealedEdgeCount++;
            const age = elapsed - edge.revealedAt;
            const flash = Math.max(0, 1 - age / 260);
            ctx.strokeStyle = `rgba(128,82,255,${(0.14 + flash * 0.5).toFixed(3)})`;
            ctx.lineWidth = flash > 0.5 ? 1.2 : 0.6;
            ctx.beginPath();
            ctx.moveTo(n1.x * w, n1.y * h);
            ctx.lineTo(n2.x * w, n2.y * h);
            ctx.stroke();
        }
    }

    // nodes
    for (const node of nodes) {
        if (scan < node.revealAt) continue;
        if (node.revealedAt === null) node.revealedAt = elapsed;
        const age = elapsed - node.revealedAt;
        const flash = Math.max(0, 1 - age / 320);
        const baseR = node.hub ? 3.2 : 1.6;
        const r = baseR + flash * 2.2;
        ctx.beginPath();
        ctx.fillStyle = node.color;
        ctx.shadowColor = node.color;
        ctx.shadowBlur = node.hub ? 12 : 5 + flash * 6;
        ctx.globalAlpha = 0.55 + flash * 0.45;
        ctx.arc(node.x * w, node.y * h, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.globalAlpha = 1;
        ctx.shadowBlur = 0;
    }

    // traveling data pulses along revealed edges
    ensurePulses(revealedEdgeCount);
    for (const pulse of pulses) {
        const edge = edges[pulse.edge];
        if (!edge || edge.revealedAt === null) continue;
        pulse.t += pulse.speed * 0.012;
        if (pulse.t > 1) {
            pulse.t = 0;
            pulse.reverse = rand() > 0.5;
        }
        const n1 = nodes[pulse.reverse ? edge.b : edge.a];
        const n2 = nodes[pulse.reverse ? edge.a : edge.b];
        const px = (n1.x + (n2.x - n1.x) * pulse.t) * w;
        const py = (n1.y + (n2.y - n1.y) * pulse.t) * h;
        ctx.beginPath();
        ctx.fillStyle = PALETTE.amber;
        ctx.shadowColor = PALETTE.amber;
        ctx.shadowBlur = 8;
        ctx.arc(px, py, 1.8, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
    }

    // scanning sweep line
    if (scan < 1) {
        const sx = scan * w;
        const grad = ctx.createLinearGradient(sx - 26, 0, sx + 26, 0);
        grad.addColorStop(0, 'rgba(128,82,255,0)');
        grad.addColorStop(0.5, 'rgba(128,82,255,0.5)');
        grad.addColorStop(1, 'rgba(128,82,255,0)');
        ctx.fillStyle = grad;
        ctx.fillRect(sx - 26, 0, 52, h);
        ctx.strokeStyle = 'rgba(200,180,255,0.9)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(sx, 0);
        ctx.lineTo(sx, h);
        ctx.stroke();
    }
}

function loop(ts: number) {
    if (!running) return;
    if (!startTime) startTime = ts;
    const elapsed = ts - startTime;
    const canvas = canvasRef.value;
    if (canvas) {
        const ctx = canvas.getContext('2d');
        if (ctx) draw(ctx, canvas.width, canvas.height, elapsed);
    }
    rafId = requestAnimationFrame(loop);
}

onMounted(() => {
    const canvas = canvasRef.value;
    if (canvas) {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        const ctx = canvas.getContext('2d');
        if (ctx) ctx.scale(dpr, dpr);
        canvas.width = rect.width;
        canvas.height = rect.height;
    }

    rafId = requestAnimationFrame(loop);

    const tl = gsap.timeline({
        onComplete: () => {
            running = false;
            cancelAnimationFrame(rafId);
            emit('done');
        },
    });

    tl.to(timeline, {
        scan: 1,
        duration: 1.6,
        ease: 'power2.inOut',
        onUpdate: () => {
            percent.value = Math.round(timeline.scan * 100);
        },
    })
        .to('.scan-label', { opacity: 1, y: 0, duration: 0.35 }, '-=1.1')
        .to({}, { duration: 0.55 })
        .to(wrapRef.value, { opacity: 0, scale: 1.05, duration: 0.6, ease: 'power2.in' });
});

onBeforeUnmount(() => {
    running = false;
    cancelAnimationFrame(rafId);
});
</script>

<template>
    <div ref="wrapRef" class="scan-loader" aria-hidden="true">
        <canvas ref="canvasRef" class="scan-canvas"></canvas>
        <div class="scan-label">
            <p class="scan-title">UNITENIST<span>.</span></p>
            <p class="scan-sub">NÖRAL ARAYÜZ SENKRONİZE EDİLİYOR — {{ percent }}%</p>
        </div>
    </div>
</template>
