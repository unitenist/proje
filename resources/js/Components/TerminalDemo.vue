<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface TerminalLine {
    text: string;
    tone: 'blue' | 'green';
}

const lines: TerminalLine[] = [
    { text: 'USER → "Tahsilat riski yüksek müşterileri bul."', tone: 'blue' },
    { text: 'AI AGENT → get_customer_balances()', tone: 'green' },
    { text: 'AI AGENT → analyze_payment_risk()', tone: 'green' },
    { text: 'AI AGENT → prepare_collection_plan()', tone: 'green' },
    { text: 'RESULT → 7 müşteri / 1.84M TL açık bakiye', tone: 'blue' },
];

const rendered = ref<string[]>(lines.map(() => ''));
const started = ref(false);
const root = ref<HTMLElement | null>(null);

function wait(ms: number) {
    return new Promise<void>((resolve) => setTimeout(resolve, ms));
}

async function typeLine(index: number) {
    const full = lines[index].text;
    for (let i = 0; i <= full.length; i++) {
        rendered.value[index] = full.slice(0, i);
        await wait(12);
    }
}

async function play() {
    if (started.value) return;
    started.value = true;
    for (let i = 0; i < lines.length; i++) {
        await typeLine(i);
        await wait(i === lines.length - 2 ? 320 : 140);
    }
}

onMounted(() => {
    if (!root.value) return;
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    play();
                    observer.disconnect();
                }
            });
        },
        { threshold: 0.4 },
    );
    observer.observe(root.value);
});
</script>

<template>
    <div ref="root" class="rounded-[24px] bg-[color:var(--color-terminal-bg)] p-[30px] font-mono text-[15px] text-[color:var(--color-text-muted)]" aria-label="AI ERP mimarisi örneği">
        <p v-for="(line, i) in lines" :key="i" class="terminal-line" :class="line.tone === 'green' ? 'text-[color:var(--color-accent-3)]' : 'text-[color:var(--color-accent-2)]'">
            {{ rendered[i] }}<span v-if="rendered[i].length > 0 && rendered[i].length < line.text.length" class="caret">▍</span>
            <br v-if="i === 0" />
        </p>
    </div>
</template>
