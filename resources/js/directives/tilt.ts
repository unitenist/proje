import type { Directive } from 'vue';

interface TiltHandlers {
    onMove: (e: MouseEvent) => void;
    onLeave: () => void;
}

const handlers = new WeakMap<HTMLElement, TiltHandlers>();

/**
 * v-tilt — subtle 3D tilt + glow-follow effect on hover, driven by CSS custom properties.
 */
export const vTilt: Directive<HTMLElement> = {
    mounted(el) {
        el.classList.add('tilt-card');

        const onMove = (e: MouseEvent) => {
            const rect = el.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            el.style.setProperty('--tilt-x', `${(-y * 8).toFixed(2)}deg`);
            el.style.setProperty('--tilt-y', `${(x * 8).toFixed(2)}deg`);
            el.style.setProperty('--glow-x', `${(x + 0.5) * 100}%`);
            el.style.setProperty('--glow-y', `${(y + 0.5) * 100}%`);
        };

        const onLeave = () => {
            el.style.setProperty('--tilt-x', '0deg');
            el.style.setProperty('--tilt-y', '0deg');
        };

        el.addEventListener('mousemove', onMove);
        el.addEventListener('mouseleave', onLeave);
        handlers.set(el, { onMove, onLeave });
    },
    unmounted(el) {
        const h = handlers.get(el);
        if (h) {
            el.removeEventListener('mousemove', h.onMove);
            el.removeEventListener('mouseleave', h.onLeave);
            handlers.delete(el);
        }
    },
};
