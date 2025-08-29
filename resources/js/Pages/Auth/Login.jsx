import { useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import GuestLayout from "@/Layouts/GuestLayout";
import axios from "axios";

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    // useEffect(() => {
    //     // Log axios request headers for debugging
    //     const requestInterceptor = axios.interceptors.request.use((config) => {
    //         console.log("Axios Request Headers:", config.headers);
    //         return config;
    //     });

    //     // Log axios response errors
    //     const responseInterceptor = axios.interceptors.response.use(
    //         (response) => response,
    //         (error) => {
    //             console.error(
    //                 "Axios Error:",
    //                 error.response?.data || error.message
    //             );
    //             return Promise.reject(error);
    //         }
    //     );

    //     return () => {
    //         reset("password");
    //         axios.interceptors.request.eject(requestInterceptor);
    //         axios.interceptors.response.eject(responseInterceptor);
    //     };
    // }, [reset]);

    const handleSubmit = (e) => {
        e.preventDefault();
        // console.log("Form Data:", data);
        // console.log(
        //     "CSRF Token:",
        //     document.querySelector('meta[name="csrf-token"]')?.content
        // );

        post(route("login"), data, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                // console.log("Login Success", page.props);
                toast.success(page.props.success || "Login successful");
                // Inertia handles redirect()->intended automatically
                // Fallback: redirect based on user role if needed
                const userRole = page.props.auth?.user?.role;
                if (!page.props.redirect_url && userRole) {
                    const redirectUrl =
                        userRole === "admin"
                            ? route("admin.dashboard")
                            : route("employee.dashboard");
                    router.visit(redirectUrl, { preserveState: false });
                }
            },
            onError: (errors) => {
                // console.log("Inertia Post Errors:", errors);
                toast.error(errors.email || "Failed to login");
            },
        });
    };

    return (
        <GuestLayout title="Log in">
            <style>
                {`
      .container-fluid {
    padding: 0px;
}
    .loginFrom{
      margin-top:119px;
    }
@media (max-width:767px){
.row.responsive-row {
    padding: 44px 50px;
}
}

    `}
            </style>
            <Head title="Log in" />
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
                        <div className="col-md-7 mx-auto ">
                            <form onSubmit={handleSubmit} className="loginFrom">
                                <div className=" d-flex flex-column justify-content-between pb-0">
                                    <div className="mx-auto text-center mb-2">
                                        <img
                                            src="/assets/img/logo_alois.png"
                                            className="img-fluid"
                                            alt="ALOIS Logo"
                                        />
                                    </div>
                                    <div>
                                        <div className="text-center mb-3">
                                            <h2 className="mb-2">Sign In</h2>
                                            <p className="mb-0">
                                                Please enter your details to
                                                sign in
                                            </p>
                                            {status && (
                                                <div className="alert alert-success">
                                                    {status}
                                                </div>
                                            )}
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
                                                    autoFocus
                                                />
                                                {/* <span className="input-group-text">
                                                    <i className="ti ti-mail" />
                                                </span> */}
                                                {errors.email && (
                                                    <div className="invalid-feedback">
                                                        {errors.email}
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label">
                                                Password
                                            </label>
                                            <div className="pass-group">
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
                                                />
                                                <span className="ti toggle-password ti-eye-off" />
                                                {errors.password && (
                                                    <div className="invalid-feedback">
                                                        {errors.password}
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="d-flex align-items-center justify-content-between mb-3">
                                            <div className="form-check">
                                                <input
                                                    type="checkbox"
                                                    className="form-check-input"
                                                    checked={data.remember}
                                                    onChange={(e) =>
                                                        setData(
                                                            "remember",
                                                            e.target.checked
                                                        )
                                                    }
                                                />
                                                <label className="form-check-label">
                                                    Remember me
                                                </label>
                                            </div>
                                            <Link
                                                href={route("password.request")}
                                                className="link-danger"
                                            >
                                                Forgot Password?
                                            </Link>
                                        </div>

                                        <div className="mb-3">
                                            <button
                                                type="submit"
                                                className="btn btn-primary w-100"
                                                disabled={processing}
                                            >
                                                Sign In
                                            </button>
                                        </div>

                                        {/*
                                           <div className="text-center">
                                            <h6 className="fw-normal text-dark mb-0">
                                                Don’t have an account?{" "}
                                                <Link
                                                    href={route("register")}
                                                    className="hover-a"
                                                >
                                                    Create Account
                                                </Link>
                                            </h6>
                                        </div>
                                         <div className="login-or">
                                            <span className="span-or">Or</span>
                                        </div>
                                        <div className="mt-2 d-flex justify-content-center">
                                            <div className="text-center me-2 flex-fill">
                                                <a
                                                    href="#"
                                                    className="btn btn-info br-10 p-2 d-flex justify-content-center align-items-center gap-2"
                                                >
                                                    <i className="fab fa-facebook-f fa-lg text-white"></i>
                                                    <span className="text-white fw-bold">
                                                        Facebook
                                                    </span>
                                                </a>
                                            </div>
                                            <div className="text-center me-2 flex-fill">
                                                <a
                                                    href="#"
                                                    className="btn btn-outline-light border br-10 p-2 d-flex justify-content-center align-items-center gap-2"
                                                >
                                                    <i className="fab fa-google fa-lg text-danger"></i>
                                                    <span className="text-dark fw-bold">
                                                        Google
                                                    </span>
                                                </a>
                                            </div>
                                            <div className="text-center flex-fill">
                                                <a
                                                    href="#"
                                                    className="btn btn-dark br-10 p-2 d-flex justify-content-center align-items-center gap-2"
                                                >
                                                    <i className="fab fa-apple fa-lg text-white"></i>
                                                    <span className="text-white fw-bold">
                                                        Apple
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                         <div className="mt-5 pb-4 text-center">
                                        <p className="mb-0 text-gray-9">
                                            Copyright © 2025 -{" "}
                                            <span style={{ color: "#F26522" }}>
                                                ALOIS
                                            </span>
                                        </p>
                                    </div>
                                        */}
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
