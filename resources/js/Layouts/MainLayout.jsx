import { useEffect, useState } from "react";
import { Head } from "@inertiajs/react";
import Footer from "./Footer";
import Header from "./Header";
import Sidebar from "./Sidebar";

export default function MainLayout({ children, title, auth }) {
    const [isLoading, setIsLoading] = useState(true);

    const toggleSidebar = () => {
        // Used by Header.jsx for mobile menu toggle
    };

    useEffect(() => {
        const scripts = [
            "/assets/js/jquery-3.7.1.min.js",
            "/assets/js/bootstrap.bundle.min.js",
            "/assets/js/script.js",
            "/assets/js/moment.js",
            "/assets/js/theme-script.js",
            "/assets/js/theme-colorpicker.js",
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
                // Disable theme scripts for sidebar and logo
                if (window.jQuery) {
                    window.jQuery("#sidebar").off("mouseenter mouseleave");
                    window.jQuery(".sidebar-overlay").remove();
                    window.jQuery("#mobile_btn").off("click");
                    window.jQuery(".sidebar-logo").remove();
                }
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

    const Loader = () => (
        <div className="loader-wrapper">
            <div className="loader-content">
                
                <h1 className="loader-title">  <img
                        src="/assets/img/logo_alois.png"
                        alt="Logo" className="img-fluid"
                       
                    /> </h1>
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

    if (isLoading) {
        return <Loader />;
    }

    return (
        <div className="main-wrapper">
            <Head title={title || "Alois"} />
            <Header auth={auth} toggleSidebar={toggleSidebar} />
            <Sidebar auth={auth} />
            <div className="page-wrapper">
                <div className="content">{children}</div>
                 <Footer />
            </div>

           
        </div>
        
    );
}
