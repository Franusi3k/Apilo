import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { ZiggyVue } from 'ziggy-js';

createInertiaApp({
  resolve: name => import(`./Pages/${name}.vue`),
  setup({ el, app, props, plugin }) {
    createApp({ render: () => h(app, props) })
      .use(plugin)
      .use(ZiggyVue)
      .mount(el)
  },
});