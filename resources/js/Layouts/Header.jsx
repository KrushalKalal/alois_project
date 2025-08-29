import { useState, useEffect, useRef } from "react";
import { Link, usePage } from "@inertiajs/react";

export default function Header({ auth, toggleSidebar }) {
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const user = auth || { name: "Guest", email: "guest@example.com" };
    const dropdownRef = useRef();
    const menuRef = useRef();

    const role = auth?.role || "employee";
    const employeeRole = auth?.employee?.role || null;

    const adminMenus = [
        {
            icon: "fa fa-home",
            label: "Dashboard",
            href: route("admin.dashboard"),
        },
        {
            icon: "fa fa-building",
            label: "Company Master",
            href: route("company-masters.index"),
        },
        {
            icon: "fa fa-store",
            label: "Branch Master",
            href: route("branch-masters.index"),
        },
        {
            icon: "fa fa-users",
            label: "Employee Master",
            href: route("employees.index"),
        },
        {
            icon: "fa fa-user-tie",
            label: "Client Master",
            href: route("clients.index"),
        },
        {
            icon: "fa fa-clipboard-check",
            label: "Status Master",
            href: route("status-masters.index"),
        },
        {
            icon: "fa fa-briefcase",
            label: "Business Unit Master",
            href: route("business-unit-masters.index"),
        },
        {
            icon: "fa fa-search",
            label: "Temporary Job Seekers",
            href: route("job-seekers.temporary.index"),
        },
        {
            icon: "fa fa-search",
            label: "Permanent Job Seekers",
            href: route("job-seekers.permanent.index"),
        },
    ];

    const employeeMenus = (() => {
        if (employeeRole === "po_maker" || employeeRole === "po_checker") {
            return [
                {
                    icon: "fa fa-home",
                    label: "Dashboard",
                    href: route("employee.dashboard"),
                },
                {
                    icon: "fa fa-search",
                    label: "Temporary Job Seekers",
                    href: route("job-seekers.temporary.index"),
                },
            ];
        }
        if (
            employeeRole === "finance_maker" ||
            employeeRole === "finance_checker"
        ) {
            return [
                {
                    icon: "fa fa-home",
                    label: "Dashboard",
                    href: route("employee.dashboard"),
                },
                {
                    icon: "fa fa-search",
                    label: "Permanent Job Seekers",
                    href: route("job-seekers.permanent.index"),
                },
            ];
        }
        return [
            {
                icon: "fa fa-home",
                label: "Dashboard",
                href: route("employee.dashboard"),
            },
            {
                icon: "fa fa-search",
                label: "Temporary Job Seekers",
                href: route("job-seekers.temporary.index"),
            },
            {
                icon: "fa fa-search",
                label: "Permanent Job Seekers",
                href: route("job-seekers.permanent.index"),
            },
        ];
    })();

    const menus = role === "admin" ? adminMenus : employeeMenus;

    useEffect(() => {
        // console.log("Header auth:", auth);
        function handleClickOutside(event) {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target)
            ) {
                setIsDropdownOpen(false);
            }
            if (
                menuRef.current &&
                !menuRef.current.contains(event.target) &&
                !event.target.closest(".custom-mobile-btn")
            ) {
                setIsMenuOpen(false);
            }
        }
        document.addEventListener("mousedown", handleClickOutside);
        return () =>
            document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    const toggleDropdown = () => {
        setIsDropdownOpen(!isDropdownOpen);
    };

    const toggleMenu = () => {
        setIsMenuOpen(!isMenuOpen);
        toggleSidebar();
    };

    return (
        <div className="header">
            <div className="main-header d-flex align-items-center">
                <a
                    id="custom_mobile_btn"
                    className="custom-mobile-btn me-3 d-md-none"
                    href="javascript:void(0);"
                    onClick={toggleMenu}
                >
                    <i className="fa fa-bars"></i>
                </a>
                <div className="ms-auto d-flex align-items-center">
                    <div
                        className="dropdown profile-dropdown position-relative"
                        ref={dropdownRef}
                    >
                        <button
                            className="btn border-0 bg-transparent d-flex align-items-center"
                            onClick={toggleDropdown}
                        >
                            <span className="avatar avatar-sm online">
                                <img
                                    src="/assets/img/profiles/avatar-12.jpg"
                                    alt="User Profile"
                                    className="img-fluid rounded-circle"
                                />
                            </span>
                        </button>
                        {isDropdownOpen && (
                            <div
                                className="dropdown-menu show shadow-none position-absolute"
                                style={{ right: 0 }}
                            >
                                <div className="card mb-0">
                                    <div className="card-header">
                                        <div className="d-flex align-items-center">
                                            <span className="avatar avatar-lg me-2 avatar-rounded">
                                                <img
                                                    src="/assets/img/profiles/avatar-12.jpg"
                                                    alt="User Profile"
                                                />
                                            </span>
                                            <div>
                                                <h5 className="mb-0">
                                                    {user.name}
                                                </h5>
                                                <p className="fs-12 fw-medium mb-0">
                                                    <a
                                                        href={`mailto:${user.email}`}
                                                    >
                                                        {user.email}
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="card-body header-login">
                                        <Link
                                            className="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                            href={
                                                auth?.role === "admin"
                                                    ? route("admin.profile")
                                                    : route("employee.profile")
                                            }
                                        >
                                            <i className="fa fa-user-circle me-1"></i>{" "}
                                            My Profile
                                        </Link>
                                    </div>
                                    <div className="card-footer header-login">
                                        <Link
                                            className="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                            href={route("logout")}
                                            method="post"
                                            as="button"
                                        >
                                            <i className="fa fa-sign-out me-2"></i>{" "}
                                            Logout
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {isMenuOpen && (
                <div className="custom-mobile-menu d-md-none" ref={menuRef}>
                    <div className="custom-menu-items">
                        <ul>
                            {menus.map((item, index) => (
                                <li key={index}>
                                    <Link
                                        href={item.href}
                                        className={
                                            location.pathname === item.href
                                                ? "active"
                                                : ""
                                        }
                                        onClick={() => setIsMenuOpen(false)}
                                    >
                                        <i
                                            className={item.icon}
                                            style={{
                                                color: "#F26522",
                                                marginRight: "8px",
                                            }}
                                        ></i>
                                        <span>{item.label}</span>
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            )}
        </div>
    );
}
