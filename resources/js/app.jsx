// resources/js/app.jsx
import "./bootstrap";
import "../css/app.css";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { router } from "@inertiajs/react";

const appName = import.meta.env.VITE_APP_NAME || "Alois";

router.on("exception", (event) => {
    if (event.detail.status === 419) {
        console.warn("Session expired, redirecting to login");
        window.location.href = route("login");
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx")
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <>
                <App {...props} />
                <ToastContainer position="top-right" autoClose={3000} />
            </>
        );
    },
    progress: { color: "#F26522" },
});
