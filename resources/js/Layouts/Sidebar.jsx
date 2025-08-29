import { Link, usePage } from "@inertiajs/react";
import { useEffect } from "react";

export default function Sidebar({ isSidebarOpen }) {
    const { props } = usePage();
    const auth = props.auth || {
        name: "Guest",
        role: "employee",
        employee: { role: null },
    };

    useEffect(() => {
        // console.log("Sidebar auth:", auth);
    }, [auth]);

    const role = auth?.role || "employee";
    const employeeRole = auth?.employee?.role || null;
    const userName = auth?.name || "Guest";
    const userDesignation =
        role === "admin"
            ? "System Admin"
            : employeeRole === "maker"
            ? "Maker"
            : employeeRole === "checker"
            ? "Checker"
            : employeeRole === "po_maker"
            ? "PO Maker"
            : employeeRole === "po_checker"
            ? "PO Checker"
            : employeeRole === "finance_maker"
            ? "Finance Maker"
            : employeeRole === "finance_checker"
            ? "Finance Checker"
            : employeeRole === "backout_maker"
            ? "Backout Maker"
            : employeeRole === "backout_checker"
            ? "Backout Checker"
            : "Employee";

    const adminMenus = [
        {
            icon: "fa fa-home",
            label: "Dashboard",
            href: route("admin.dashboard"),
        },
        {
            icon: "fa-solid fa-envelope",
            label: "Main Mail",
            href: route("emails.index"),
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
    const logoRedirectUrl =
        role === "admin"
            ? route("admin.dashboard")
            : route("employee.dashboard");

    return (
        <div
            className={`sidebar ${isSidebarOpen ? "active" : ""}`}
            id="sidebar"
        >
            <div className="sidebar-logo">
                <Link href={logoRedirectUrl} className="logo logo-normal">
                    <img
                        src="/assets/img/logo_alois.png"
                        alt="Logo" className="img-fluid"
                       
                    />
                </Link>
                <Link href={logoRedirectUrl} className="logo logo-small">
                    <img src="/assets/img/logo-small.svg" alt="Logo" />
                </Link>
            </div>

            <div className="modern-profile p-3 pb-0">
                <div className="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div className="avatar avatar-lg online mb-3">
                        <img
                            src="/assets/img/profiles/avatar-02.jpg"
                            alt="User Profile"
                            className="img-fluid rounded-circle"
                        />
                    </div>
                    <h6 className="fs-12 fw-normal mb-1">{userName}</h6>
                    <p className="fs-10">{userDesignation}</p>
                </div>
                <div className="sidebar-nav mb-3">
                    <ul
                        className="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                        role="tablist"
                    >
                        <li className="nav-item">
                            <Link className="nav-link active border-0" href="#">
                                Menu
                            </Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link border-0" href="/chat">
                                Chats
                            </Link>
                        </li>
                        <li className="nav-item">
                            <Link className="nav-link border-0" href="/email">
                                Inbox
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div className="sidebar-inner">
                <div id="sidebar-menu" className="sidebar-menu">
                    <ul>
                        <li className="menu-title"></li>
                        {menus.map((item, index) => (
                            <li key={index}>
                                <Link
                                    href={item.href}
                                    className={
                                        location.pathname === item.href
                                            ? "active"
                                            : ""
                                    }
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
        </div>
    );
}
