import '../css/app.css';

import { createApp, type DefineComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { vReveal } from './directives/reveal';
import { vTilt } from './directives/tilt';
import { vMagnetic } from './directives/magnetic';

const appName = import.meta.env.VITE_APP_NAME || 'UNITENIST';

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .directive('reveal', vReveal)
            .directive('tilt', vTilt)
            .directive('magnetic', vMagnetic)
            .mount(el);
    },
    progress: {
        color: '#68e1c2',
    },
});
