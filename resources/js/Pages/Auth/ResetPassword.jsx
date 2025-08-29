import { useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import GuestLayout from "@/Layouts/GuestLayout";
import { toast } from "react-toastify";

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token: token,
        email: email,
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        if (errors.email || errors.password || errors.password_confirmation) {
            toast.error(
                errors.email ||
                    errors.password ||
                    errors.password_confirmation ||
                    "Failed to reset password"
            );
        }
    }, [errors]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("password.store"), {
            onSuccess: () => {
                toast.success("Password reset successfully");
                setData({
                    token: token,
                    email: email,
                    password: "",
                    password_confirmation: "",
                });
            },
            onError: (errors) => {
                toast.error(
                    errors.email ||
                        errors.password ||
                        errors.password_confirmation ||
                        "Failed to reset password"
                );
            },
        });
    };

    return (
        <GuestLayout title="Reset Password">
            <style>
                {`
                    .container-fluid {
                        padding: 0px;
                    }
                    .loginFrom {
                        margin-top: 119px;
                    }
                    @media (max-width:767px) {
                        .row.responsive-row {
                            padding: 44px 50px;
                        }
                    }
                `}
            </style>
            <Head title="Reset Password" />
            <div className="row p-0">
                <div className="col-lg-6 col-md-6 col-sm-12 p-0">
                    <div className="login-background position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100">
                        <div className="bg-overlay-img">
                            <img
                                src="assets/img/bg/bg-01.png"
                                className="bg-1"
                                alt="Img"
                            />
                            <img
                                src="assets/img/bg/bg-02.png"
                                className="bg-2"
                                alt="Img"
                            />
                        </div>

                        <div className="authentication-card w-100">
                            <div className="authen-overlay-item border w-100 text-center p-4 d-flex flex-column justify-content-between h-100">
                                {/* Top: Title */}
                                <div className="mb-5">
                                    <h1 className="text-white display-6 fw-bold mb-0">
                                        ALOIS Hire Report
                                    </h1>
                                </div>

                                {/* Middle: Chart full width, more height */}
                                <div className="flex-grow-1 d-flex align-items-center justify-content-center mb-4">
                                    <img
                                        src="assets/img/bg/alois_chart.png"
                                        alt="Chart"
                                        className="img-fluid"
                                        style={{
                                            maxHeight: "400px", // taller chart
                                            width: "150%", // keep it centered with margins
                                            objectFit: "contain",
                                            marginTop: "15px",
                                        }}
                                    />
                                </div>

                                {/* Bottom: Illustration + Subtitle side by side */}
                                <div className="d-flex align-items-center justify-content-center gap-4 ">
                                    <img
                                        src="assets/img/bg/authentication-bg-01.png"
                                        alt="Auth Illustration"
                                        className="img-fluid"
                                        style={{
                                            maxHeight: "186px",
                                            objectFit: "contain",
                                        }}
                                    />
                                    <p className="text-white fs-20 fw-semibold mb-0 text-start">
                                        Efficiently manage your workforce,{" "}
                                        <br />
                                        streamline operations effortlessly.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-6 col-md-6 col-sm-12">
                    <div className="row responsive-row">
                        <div className="col-md-7 mx-auto">
                            <form onSubmit={handleSubmit} className="loginFrom">
                                <div className="d-flex flex-column justify-content-between pb-0">
                                    <div className="mx-auto text-center mb-2">
                                        <img
                                            src="/assets/img/logo_alois.png"
                                            className="img-fluid"
                                            alt="ALOIS Logo"
                                        />
                                    </div>
                                    <div>
                                        <div className="text-center mb-3">
                                            <h2 className="mb-2">
                                                Reset Password
                                            </h2>
                                            <p className="mb-0">
                                                Enter your new password
                                            </p>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label">
                                                Email Address
                                            </label>
                                            <div className="input-group">
                                                <input
                                                    type="email"
                                                    className={`form-control ${
                                                        errors.email
                                                            ? "is-invalid"
                                                            : ""
                                                    }`}
                                                    value={data.email}
                                                    onChange={(e) =>
                                                        setData(
                                                            "email",
                                                            e.target.value
                                                        )
                                                    }
                                                    required
                                                    placeholder="Enter your email"
                                                />
                                                {errors.email && (
                                                    <div className="invalid-feedback">
                                                        {errors.email}
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label">
                                                New Password
                                            </label>
                                            <div className="input-group">
                                                <input
                                                    type="password"
                                                    className={`form-control ${
                                                        errors.password
                                                            ? "is-invalid"
                                                            : ""
                                                    }`}
                                                    value={data.password}
                                                    onChange={(e) =>
                                                        setData(
                                                            "password",
                                                            e.target.value
                                                        )
                                                    }
                                                    required
                                                    placeholder="Enter new password"
                                                />
                                                {errors.password && (
                                                    <div className="invalid-feedback">
                                                        {errors.password}
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label">
                                                Confirm Password
                                            </label>
                                            <div className="input-group">
                                                <input
                                                    type="password"
                                                    className={`form-control ${
                                                        errors.password_confirmation
                                                            ? "is-invalid"
                                                            : ""
                                                    }`}
                                                    value={
                                                        data.password_confirmation
                                                    }
                                                    onChange={(e) =>
                                                        setData(
                                                            "password_confirmation",
                                                            e.target.value
                                                        )
                                                    }
                                                    required
                                                    placeholder="Confirm new password"
                                                />
                                                {errors.password_confirmation && (
                                                    <div className="invalid-feedback">
                                                        {
                                                            errors.password_confirmation
                                                        }
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <input
                                            type="hidden"
                                            name="token"
                                            value={data.token}
                                        />

                                        <div className="mb-3">
                                            <button
                                                type="submit"
                                                className="btn btn-primary w-100"
                                                disabled={processing}
                                            >
                                                Reset Password
                                            </button>
                                        </div>

                                        <div className="text-center">
                                            <Link
                                                href={route("login")}
                                                className="link-primary"
                                            >
                                                Back to Login
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </GuestLayout>
    );
}
