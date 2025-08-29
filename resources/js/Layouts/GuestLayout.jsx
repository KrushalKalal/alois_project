// resources/js/Layouts/GuestLayout.jsx
import { useEffect, useState } from "react";
import { Head } from "@inertiajs/react";

export default function GuestLayout({ children, title }) {
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const scripts = [
            "/assets/js/jquery-3.7.1.min.js",
            "/assets/js/bootstrap.bundle.min.js",
            "/assets/js/script.js",
            "/assets/js/moment.js",
            "/assets/js/theme-script.js",
            "/assets/js/theme-colorpicker.js",
            "/assets/js/jquery.slimscroll.min.js",
            "/assets/js/feather.min.js",
            "/assets/js/bootstrap-datetimepicker.min.js",
            "/assets/js/todo.js",
        ];

        const styles = [
            "/assets/css/bootstrap.min.css",
            "/assets/css/style.css",
            "/assets/css/bootstrap-datetimepicker.min.css",
        ];

        let loadedAssets = 0;
        const totalAssets = scripts.length + styles.length;

        const checkAllLoaded = () => {
            loadedAssets += 1;
            if (loadedAssets === totalAssets) {
                setIsLoading(false);
            }
        };

        scripts.forEach((src) => {
            const script = document.createElement("script");
            script.src = src;
            script.async = false;
            script.onload = checkAllLoaded;
            script.onerror = () => {
                // console.error(`Failed to load script: ${src}`);
                checkAllLoaded();
            };
            document.body.appendChild(script);
        });

        styles.forEach((href) => {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = href;
            link.onload = checkAllLoaded;
            link.onerror = () => {
                // console.error(`Failed to load stylesheet: ${href}`);
                checkAllLoaded();
            };
            document.head.appendChild(link);
        });

        return () => {
            scripts.forEach((src) => {
                const script = document.querySelector(`script[src="${src}"]`);
                if (script) document.body.removeChild(script);
            });
            styles.forEach((href) => {
                const link = document.querySelector(`link[href="${href}"]`);
                if (link) document.head.removeChild(link);
            });
        };
    }, []);

    if (isLoading) {
        return (
            <div className="loader-wrapper">
                <div className="loader-content">
                    <h1 className="loader-title">ALOIS</h1>
                    <div className="loader-text">Loading ...</div>
                </div>
                <style jsx>{`
                    .loader-wrapper {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(255, 255, 255, 0.9);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 9999;
                    }
                    .loader-content {
                        text-align: center;
                    }
                    .loader-title {
                        font-size: 3rem;
                        font-weight: bold;
                        color: #f26522;
                        margin-bottom: 0.5rem;
                    }
                    .loader-text {
                        font-size: 1.5rem;
                        color: #f26522;
                    }
                `}</style>
            </div>
        );
    }

    return (
        <div className="main-wrapper">
            <Head title={title || "Alois"} />
            <div className="container-fluid">
                <div className="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                    {children}
                </div>
            </div>
        </div>
    );
}
