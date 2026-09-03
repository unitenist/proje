import type { Directive } from 'vue';

const observers = new WeakMap<Element, IntersectionObserver>();

/**
 * v-reveal / v-reveal="'flip'" — fades & slides an element in once it enters the viewport.
 */
export const vReveal: Directive<HTMLElement, string | undefined> = {
    mounted(el, binding) {
        el.classList.add('reveal-init');
        if (binding.value) {
            el.classList.add(`reveal-${binding.value}`);
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        el.classList.add('reveal-visible');
                        observer.unobserve(el);
                    }
                });
            },
            { threshold: 0.15, rootMargin: '0px 0px -8% 0px' },
        );

        observer.observe(el);
        observers.set(el, observer);
    },
    unmounted(el) {
        observers.get(el)?.disconnect();
        observers.delete(el);
    },
};
