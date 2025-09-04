// resources/js/app.js
import "./bootstrap";
import "../css/app.css";
import "@fortawesome/fontawesome-free/css/all.css";
import "sweetalert2/dist/sweetalert2.min.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import Swal from "sweetalert2/dist/sweetalert2.js";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// Import ApexCharts
import VueApexCharts from 'vue3-apexcharts';
import ApexCharts from 'apexcharts';

// Import ApexCharts CSS
import 'apexcharts/dist/apexcharts.css';

// Make SweetAlert2 available globally
window.Swal = Swal;

// Create a simple toast function using SweetAlert2
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
    /* didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    } */
});

// Make toast available globally
window.Toast = Toast;

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Register ApexCharts component
        app.component('apexchart', VueApexCharts);
        
        // Make ApexCharts available globally
        app.config.globalProperties.$apexcharts = ApexCharts;

        // Set the appName globally
        app.config.globalProperties.$appName = appName;

        // Make SweetAlert2 available in all components
        app.config.globalProperties.$swal = Swal;
        app.config.globalProperties.$toast = Toast;

        // Mount the app
        app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
