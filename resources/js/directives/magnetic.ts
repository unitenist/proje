import type { Directive } from 'vue';

interface MagneticHandlers {
    onMove: (e: MouseEvent) => void;
    onLeave: () => void;
}

const handlers = new WeakMap<HTMLElement, MagneticHandlers>();

/**
 * v-magnetic — pulls an element gently toward the cursor while hovered.
 */
export const vMagnetic: Directive<HTMLElement> = {
    mounted(el) {
        el.classList.add('magnetic');

        const onMove = (e: MouseEvent) => {
            const rect = el.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            el.style.transform = `translate(${(x * 0.25).toFixed(1)}px, ${(y * 0.25).toFixed(1)}px)`;
        };

        const onLeave = () => {
            el.style.transform = 'translate(0, 0)';
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
