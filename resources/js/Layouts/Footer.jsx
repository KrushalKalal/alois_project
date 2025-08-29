import React from "react";

export default function Footer() {
    return (
        <div className="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
            <p className="mb-0 footer-p">
                Alois © {new Date().getFullYear()}
            </p>
            <p>
                Designed & Developed By
                <a href="javascript:void(0);" className="text-primary mx-1">
                    Qubeta Technolab
                </a>
            </p>
        </div>
    );
}
