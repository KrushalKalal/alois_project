import { useEffect } from "react";
import { Head, useForm, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import { toast } from "react-toastify";

export default function EmployeeProfile({ user, employee, status }) {
    const { data, setData, post, processing, errors } = useForm({
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
    });

    useEffect(() => {
        if (status) {
            toast.success(status);
        }
        if (errors.error) {
            toast.error(errors.error);
        }
    }, [status, errors]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("employee.profile.password"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Password updated successfully");
                setData({
                    current_password: "",
                    new_password: "",
                    new_password_confirmation: "",
                });
            },
            onError: (errors) => {
                toast.error(errors.error || "Failed to update password");
            },
        });
    };

    const isChecker =
        employee && ["checker", "po_checker"].includes(employee.role);

    return (
        <MainLayout title="Employee Profile">
            <style>
                {`
                    .container-fluid {
                        padding: 0px;
                    }
                    .loginFrom {
                        margin-top: 50px;
                    }
                    @media (max-width:767px) {
                        .row.responsive-row {
                            padding: 44px 50px;
                        }
                    }
                `}
            </style>
            <Head title="Employee Profile" />
            <div className="container-fluid">
                <div className="row p-0">
                    <div className="col-lg-7 col-md-7 col-sm-7 mx-auto">
                        <div className="row responsive-row">
                            <div className="col-md-7 mx-auto">
                                <div className="loginFrom">
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
                                                    Employee Profile
                                                </h2>
                                                <p className="mb-0">
                                                    View and update your profile
                                                </p>
                                                {status && (
                                                    <div className="alert alert-success">
                                                        {status}
                                                    </div>
                                                )}
                                            </div>

                                            {employee && (
                                                <div className="row">
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Employee ID
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.emp_id
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Name
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.name
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Email Address
                                                        </label>
                                                        <input
                                                            type="email"
                                                            className="form-control"
                                                            value={
                                                                employee.email ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Phone
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.phone ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Company
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.company_id ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Role
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.role ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    {!isChecker && (
                                                        <div className="col-6 mb-3">
                                                            <label className="form-label">
                                                                Checker
                                                            </label>
                                                            <input
                                                                type="text"
                                                                className="form-control"
                                                                value={
                                                                    employee.checker ||
                                                                    "N/A"
                                                                }
                                                                readOnly
                                                            />
                                                        </div>
                                                    )}
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Designation
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.designation ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Status
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            value={
                                                                employee.status ||
                                                                "N/A"
                                                            }
                                                            readOnly
                                                        />
                                                    </div>
                                                </div>
                                            )}

                                            <form onSubmit={handleSubmit}>
                                                <div className="row">
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Current Password
                                                        </label>
                                                        <div className="input-group">
                                                            <input
                                                                type="password"
                                                                className={`form-control ${
                                                                    errors.error
                                                                        ? "is-invalid"
                                                                        : ""
                                                                }`}
                                                                value={
                                                                    data.current_password
                                                                }
                                                                onChange={(e) =>
                                                                    setData(
                                                                        "current_password",
                                                                        e.target
                                                                            .value
                                                                    )
                                                                }
                                                                required
                                                                placeholder="Enter current password"
                                                            />
                                                            {errors.error && (
                                                                <div className="invalid-feedback">
                                                                    {
                                                                        errors.error
                                                                    }
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            New Password
                                                        </label>
                                                        <div className="input-group">
                                                            <input
                                                                type="password"
                                                                className={`form-control ${
                                                                    errors.new_password
                                                                        ? "is-invalid"
                                                                        : ""
                                                                }`}
                                                                value={
                                                                    data.new_password
                                                                }
                                                                onChange={(e) =>
                                                                    setData(
                                                                        "new_password",
                                                                        e.target
                                                                            .value
                                                                    )
                                                                }
                                                                required
                                                                placeholder="Enter new password"
                                                            />
                                                            {errors.new_password && (
                                                                <div className="invalid-feedback">
                                                                    {
                                                                        errors.new_password
                                                                    }
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                    <div className="col-6 mb-3">
                                                        <label className="form-label">
                                                            Confirm New Password
                                                        </label>
                                                        <div className="input-group">
                                                            <input
                                                                type="password"
                                                                className={`form-control ${
                                                                    errors.new_password_confirmation
                                                                        ? "is-invalid"
                                                                        : ""
                                                                }`}
                                                                value={
                                                                    data.new_password_confirmation
                                                                }
                                                                onChange={(e) =>
                                                                    setData(
                                                                        "new_password_confirmation",
                                                                        e.target
                                                                            .value
                                                                    )
                                                                }
                                                                required
                                                                placeholder="Confirm new password"
                                                            />
                                                            {errors.new_password_confirmation && (
                                                                <div className="invalid-feedback">
                                                                    {
                                                                        errors.new_password_confirmation
                                                                    }
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="mb-3">
                                                    <button
                                                        type="submit"
                                                        className="btn btn-primary w-100"
                                                        disabled={processing}
                                                    >
                                                        Update Password
                                                    </button>
                                                </div>
                                            </form>

                                            <div className="text-center">
                                                <Link
                                                    href={route(
                                                        "employee.dashboard"
                                                    )}
                                                    className="link-primary"
                                                >
                                                    Back to Dashboard
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
