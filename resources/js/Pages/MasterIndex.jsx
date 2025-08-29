import { useState, useEffect } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import MainLayout from "@/Layouts/MainLayout";

export default function MasterIndex({
    auth,
    masterName,
    viewBase,
    columns,
    data,
    filters,
    excelTemplateRoute = "",
    excelImportRoute = "",
    hasTabs = false,
}) {
    const { props } = usePage();
    const [search, setSearch] = useState(filters.search || "");
    const [activeTab, setActiveTab] = useState(filters.tab || "permanent");
    const [showModal, setShowModal] = useState(false);
    const [file, setFile] = useState(null);
    const [toastTriggered, setToastTriggered] = useState(false);

    useEffect(() => {
        const flash = props.flash || {};
        const currentPath = window.location.pathname;
        if (flash.success && currentPath === viewBase && !toastTriggered) {
            toast.success(flash.success);
        }
        if (flash.error && currentPath === viewBase) {
            toast.error(flash.error);
        }
        setToastTriggered(false);
    }, [props.flash, viewBase, toastTriggered]);

    const handleDelete = (id) => {
        if (confirm(`Are you sure you want to delete this ${masterName}?`)) {
            router.delete(`${viewBase}/${id}`, {
                onSuccess: () => setToastTriggered(true),
                onError: () => toast.error(`Failed to delete ${masterName}`),
                preserveState: true,
            });
        }
    };

    const handleBulkUpload = (e) => {
        e.preventDefault();
        if (!file) {
            toast.error("Please select a file");
            return;
        }
        const formData = new FormData();
        formData.append("file", file);

        router.post(excelImportRoute, formData, {
            onSuccess: () => {
                setToastTriggered(true);
                setShowModal(false);
                setFile(null);
            },
            onError: (errors) => toast.error(errors.file || "Failed to import"),
        });
    };

    const handleDownloadTemplate = () => {
        window.location.href = route(excelTemplateRoute);
    };

    const handleSearch = (e) => {
        setSearch(e.target.value);
        router.get(
            viewBase,
            {
                search: e.target.value,
                per_page: filters.per_page,
                tab: activeTab,
            },
            { preserveState: true }
        );
    };

    const handleTabChange = (tab) => {
        setActiveTab(tab);
        router.get(
            viewBase,
            { search, per_page: filters.per_page, tab },
            { preserveState: true }
        );
    };

    return (
        <MainLayout title={masterName} auth={auth}>
            <ToastContainer />
            <div className="container-fluid dashboard-width">
                <div className="row">
                    <div className="page-header">
                        <div className="row align-items-center">
                            <div className="col-auto">
                                <h2 className="page-title mx-1">
                                    {masterName}
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-sm-12">
                            <div className="card">
                                <div className="card-header card-design-class">
                                    {hasTabs && (
                                        <ul className="nav nav-tabs">
                                            <li className="nav-item">
                                                <button
                                                    className={`nav-link ${
                                                        activeTab ===
                                                        "permanent"
                                                            ? "active"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        handleTabChange(
                                                            "permanent"
                                                        )
                                                    }
                                                >
                                                    Permanent
                                                </button>
                                            </li>
                                            <li className="nav-item">
                                                <button
                                                    className={`nav-link ${
                                                        activeTab ===
                                                        "temporary"
                                                            ? "active"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        handleTabChange(
                                                            "temporary"
                                                        )
                                                    }
                                                >
                                                    Temporary
                                                </button>
                                            </li>
                                        </ul>
                                    )}
                                    <div className="row align-items-center mt-3">
                                        <div className="col-lg-3 col-md-3">
                                        <div className="col-auto">
                                            <h4 className="card-title">
                                                {masterName} List
                                            </h4>
                                        </div>
                                        </div>
                                        <div className="col-lg-9 col-md-9">

                                        <div className="bulk-area">
                                     <div className="bulk-btn">
                                             <div
                                                className="input-group" style={{ maxWidth: "300px" }}
                                               
                                            >
                                                <span className="input-group-text">
                                                    <i className="fa fa-magnifying-glass"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    placeholder="Search..."
                                                    value={search}
                                                    onChange={handleSearch}
                                                />
                                            </div>
                                         </div>

                                            {excelTemplateRoute &&
                                                excelImportRoute && (
                                                    <button
                                                        className="btn btn-primary  d-flex align-items-center"
                                                        style={{
                                                            backgroundColor:
                                                                "#F26522",
                                                            borderColor:
                                                                "#F26522",
                                                        }}
                                                        onClick={() =>
                                                            setShowModal(true)
                                                        }
                                                    >
                                                        <i className="fa fa-file-upload me-1"></i>
                                                        Bulk Upload
                                                    </button>
                                                )}
                                            <Link
                                                href={`${viewBase}/create`}
                                                className="btn btn-primary d-flex align-items-center"
                                                style={{
                                                    backgroundColor: "#F26522",
                                                    borderColor: "#F26522",
                                                }}
                                            >
                                                <i className="fa fa-plus-circle me-1"></i>
                                                Add {masterName.split(" ")[0]}
                                            </Link>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="card-body p-4">
                                    <div className="table-responsive">
                                        <table className="table datatable table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    {columns.map((col) => (
                                                        <th key={col.key}>
                                                            {col.label}
                                                        </th>
                                                    ))}
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {data.data.map((item) => (
                                                    <tr key={item.id}>
                                                        {columns.map((col) => (
                                                            <td key={col.key}>
                                                                {col.render
                                                                    ? col.render(
                                                                          item
                                                                      )
                                                                    : item[
                                                                          col
                                                                              .key
                                                                      ]}
                                                            </td>
                                                        ))}
                                                        <td className="action-column">
                                                            <Link
                                                                href={`${viewBase}/${item.id}/edit`}
                                                                className="btn btn-sm btn-theme me-2"
                                                            >
                                                                <i className="fa fa-pen-to-square"></i>
                                                            </Link>
                                                            <button
                                                                className="btn btn-sm btn-theme"
                                                                onClick={() =>
                                                                    handleDelete(
                                                                        item.id
                                                                    )
                                                                }
                                                            >
                                                                <i className="fa fa-trash-can"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav className="pagination justify-content-end mt-3">
                                        <ul className="pagination">
                                            <li
                                                className={`page-item ${
                                                    data.current_page === 1
                                                        ? "disabled"
                                                        : ""
                                                }`}
                                            >
                                                <button
                                                    className="page-link"
                                                    onClick={() =>
                                                        router.get(viewBase, {
                                                            search,
                                                            page:
                                                                data.current_page -
                                                                1,
                                                            per_page:
                                                                filters.per_page,
                                                            tab: activeTab,
                                                        })
                                                    }
                                                >
                                                    Previous
                                                </button>
                                            </li>
                                            {[...Array(data.last_page)].map(
                                                (_, i) => (
                                                    <li
                                                        key={i}
                                                        className={`page-item ${
                                                            data.current_page ===
                                                            i + 1
                                                                ? "active"
                                                                : ""
                                                        }`}
                                                    >
                                                        <button
                                                            className="page-link"
                                                            onClick={() =>
                                                                router.get(
                                                                    viewBase,
                                                                    {
                                                                        search,
                                                                        page:
                                                                            i +
                                                                            1,
                                                                        per_page:
                                                                            filters.per_page,
                                                                        tab: activeTab,
                                                                    }
                                                                )
                                                            }
                                                        >
                                                            {i + 1}
                                                        </button>
                                                    </li>
                                                )
                                            )}
                                            <li
                                                className={`page-item ${
                                                    data.current_page ===
                                                    data.last_page
                                                        ? "disabled"
                                                        : ""
                                                }`}
                                            >
                                                <button
                                                    className="page-link"
                                                    onClick={() =>
                                                        router.get(viewBase, {
                                                            search,
                                                            page:
                                                                data.current_page +
                                                                1,
                                                            per_page:
                                                                filters.per_page,
                                                            tab: activeTab,
                                                        })
                                                    }
                                                >
                                                    Next
                                                </button>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    {excelTemplateRoute && excelImportRoute && showModal && (
                        <div
                            className={`modal fade ${
                                showModal ? "show d-block" : ""
                            }`}
                            style={{ display: showModal ? "block" : "none" }}
                        >
                            <div className="modal-dialog popu-dilog">
                                <div className="modal-content ">
                                    <div className="modal-header">
                                        <h5 className="modal-title">
                                            Bulk {masterName} Upload
                                        </h5>
                                        <button
                                            type="button"
                                            className="btn-close"
                                            onClick={() => {
                                                setShowModal(false);
                                                setFile(null);
                                            }}
                                        ></button>
                                    </div>
                                    <div className="modal-body">
                                        <form onSubmit={handleBulkUpload}>
                                            <div className="mb-3">
                                                <label className="form-label">
                                                    Upload Excel
                                                </label>
                                                <input
                                                    type="file"
                                                    className="form-control"
                                                    accept=".xlsx,.xls"
                                                    onChange={(e) =>
                                                        setFile(
                                                            e.target.files[0]
                                                        )
                                                    }
                                                />
                                            </div>
                                            <div className="mb-3">
                                                <button
                                                    type="button"
                                                    className="btn btn-outline-primary"
                                                    onClick={
                                                        handleDownloadTemplate
                                                    }
                                                >
                                                    <i className="fa fa-download me-1"></i>
                                                    Download Template
                                                </button>
                                            </div>
                                            <button
                                                type="submit"
                                                className="btn btn-primary"
                                                style={{
                                                    backgroundColor: "#F26522",
                                                    borderColor: "#F26522",
                                                }}
                                            >
                                                Upload
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                    {excelTemplateRoute && excelImportRoute && showModal && (
                        <div className="modal-backdrop fade show"></div>
                    )}
                </div>
            </div>
            <style jsx>{`
          
                .table tbody tr {
                    height: 40px;
                }
                .table td,
                .table th {
                    vertical-align: middle;
                }
                .action-column .btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 28px;
                    height: 28px;
                    padding: 0;
                }
                .action-column .btn i {
                    font-size: 1rem;
                }
                .input-group-text {
                    display: flex;
                    align-items: center;
                }
                .input-group-text i {
                    font-size: 1.2rem;
                }
                .btn i {
                    font-size: 1.2rem;
                }
                .btn-theme {
                    background-color: #f26522 !important;
                    color: white !important;
                    border: none;
                }
                .btn-theme:hover {
                    background-color: #d9541e !important;
                    color: white !important;
                }
                .nav-tabs .nav-link {
                    cursor: pointer;
                }
            `}</style>
        </MainLayout>
    );
}
