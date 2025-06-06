import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// Ziggy Vue plugin - manual implementation for Vercel compatibility
const ZiggyVue = {
    install(app, config) {
        const route = (name, params = {}, absolute = true, customZiggy = config) => {
            if (!customZiggy) {
                throw new Error('Ziggy configuration is required');
            }
            
            if (!name) {
                return {
                    current: () => null,
                    has: () => false
                };
            }
            
            const routeData = customZiggy.routes[name];
            if (!routeData) {
                throw new Error(`Route ${name} not found`);
            }
            
            let url = customZiggy.url + '/' + routeData.uri;
            
            // Simple parameter replacement
            if (params && typeof params === 'object') {
                Object.keys(params).forEach(key => {
                    url = url.replace(`{${key}}`, params[key]);
                });
            }
            
            return url;
        };
        
        app.config.globalProperties.route = route;
        app.provide('route', route);
    }
};
import { Ziggy } from './ziggy.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
