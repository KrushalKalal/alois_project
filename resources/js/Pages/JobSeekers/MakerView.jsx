import { useState, useCallback, useEffect } from "react";
import { Link, router } from "@inertiajs/react";
import { toast } from "react-toastify";
import { debounce } from "lodash";
import ViewModal from "./ViewModal";

export default function MakerView({
    jobSeekers,
    setCounts,
    type,
    employeeCompanyId,
    employeeId,
    employeeCheckerId,
    statusFilter: initialStatusFilter = "Pending",
}) {
    const [data, setData] = useState(
        jobSeekers || { data: [], current_page: 1, last_page: 1, total: 0 }
    );
    const [search, setSearch] = useState("");
    const [page, setPage] = useState(data.current_page || 1);
    const [statusFilter, setStatusFilter] = useState(initialStatusFilter);
    const [selectedJobSeeker, setSelectedJobSeeker] = useState(null);

    const fetchData = useCallback(
        debounce((status) => {
            router.get(
                route(`job-seekers.${type.toLowerCase()}.index`),
                { search, page, per_page: 10, status_filter: status },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    onSuccess: (page) => {
                        setData(page.props.jobSeekers);
                        setCounts(page.props.counts);
                        if (page.props.flash?.success) {
                            toast.success(page.props.flash.success);
                        }
                        if (page.props.flash?.error) {
                            toast.error(page.props.flash.error);
                        }
                    },
                    onError: () => toast.error("Failed to fetch Job Seekers"),
                }
            );
        }, 300),
        [search, page, setCounts, type]
    );

    useEffect(() => {
        fetchData(statusFilter);
    }, [statusFilter, fetchData]);

    const handleView = (jobSeeker) => {
        setSelectedJobSeeker(jobSeeker);
    };

    const handlePageChange = (newPage) => {
        if (newPage >= 1 && newPage <= data.last_page) {
            setPage(newPage);
            fetchData(statusFilter);
        }
    };

    const handleTabChange = (status) => {
        setStatusFilter(status);
        setPage(1);
        fetchData(status);
    };

    return (
        <div className="row">
            <div className="col-sm-12">
                <div className="card">
                    <div className="card-header">
                        <div className="row align-items-center">
                            <div className="col-auto">
                                <h4 className="card-title">
                                    {type} Job Seekers List (Maker)
                                </h4>
                            </div>
                            <div className="col-auto ms-auto">
                                <div
                                    className="input-group"
                                    style={{ maxWidth: "300px" }}
                                >
                                    <span className="input-group-text">
                                        <i className="fa fa-magnifying-glass"></i>
                                    </span>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Search ..."
                                        value={search}
                                        onChange={(e) => {
                                            setSearch(e.target.value);
                                            setPage(1);
                                            fetchData(statusFilter);
                                        }}
                                    />
                                </div>
                            </div>
                        </div>
                        <ul className="nav nav-tabs mt-3">
                            {["All", "Pending", "Approved", "Rejected"].map(
                                (status) => (
                                    <li className="nav-item" key={status}>
                                        <button
                                            className={`nav-link ${
                                                statusFilter === status
                                                    ? "active"
                                                    : ""
                                            }`}
                                            onClick={() =>
                                                handleTabChange(status)
                                            }
                                        >
                                            {status}
                                        </button>
                                    </li>
                                )
                            )}
                        </ul>
                    </div>
                    <div className="card-body p-4">
                        <div className="table-responsive">
                            <table className="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Consultant Code</th>
                                        <th>Consultant Name</th>
                                        <th>Client Code</th>
                                        <th>Client Name</th>
                                        <th>Skill</th>
                                        <th>SAP ID</th>
                                        <th>Form Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.length > 0 ? (
                                        data.data.map((jobSeeker, index) => (
                                            <tr key={jobSeeker.id}>
                                                <td>
                                                    {(data.current_page - 1) *
                                                        10 +
                                                        index +
                                                        1}
                                                </td>
                                                <td>
                                                    {jobSeeker.consultant_code ||
                                                        "N/A"}
                                                </td>
                                                <td>
                                                    {jobSeeker.consultant_name ||
                                                        "N/A"}
                                                </td>
                                                <td>
                                                    {jobSeeker.client
                                                        ?.client_code || "N/A"}
                                                </td>
                                                <td>
                                                    {jobSeeker.client
                                                        ?.client_name || "N/A"}
                                                </td>
                                                <td>
                                                    {jobSeeker.skill || "N/A"}
                                                </td>
                                                <td>
                                                    {jobSeeker.sap_id || "N/A"}
                                                </td>
                                                <td>{jobSeeker.form_status}</td>
                                                <td>
                                                    <button
                                                        onClick={() =>
                                                            handleView(
                                                                jobSeeker
                                                            )
                                                        }
                                                        className="btn btn-sm btn-primary me-2"
                                                    >
                                                        View
                                                    </button>
                                                    <Link
                                                        href={route(
                                                            `job-seekers.${type.toLowerCase()}.edit`,
                                                            jobSeeker.id
                                                        )}
                                                        className="btn btn-sm btn-primary me-2"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        onClick={() => {
                                                            if (
                                                                confirm(
                                                                    "Are you sure you want to delete this Job Seeker?"
                                                                )
                                                            ) {
                                                                router.delete(
                                                                    route(
                                                                        `job-seekers.${type.toLowerCase()}.destroy`,
                                                                        jobSeeker.id
                                                                    ),
                                                                    {
                                                                        onSuccess:
                                                                            () =>
                                                                                fetchData(
                                                                                    statusFilter
                                                                                ),
                                                                    }
                                                                );
                                                            }
                                                        }}
                                                        className="btn btn-sm btn-danger"
                                                    >
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                colSpan="9"
                                                className="text-center"
                                            >
                                                No Job Seekers found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {data.last_page > 1 && (
                            <div className="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Showing {data.data.length} of {data.total}{" "}
                                    entries
                                </div>
                                <nav>
                                    <ul className="pagination">
                                        <li
                                            className={`page-item ${
                                                page === 1 ? "disabled" : ""
                                            }`}
                                        >
                                            <button
                                                className="page-link"
                                                onClick={() =>
                                                    handlePageChange(page - 1)
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
                                                        page === i + 1
                                                            ? "active"
                                                            : ""
                                                    }`}
                                                >
                                                    <button
                                                        className="page-link"
                                                        onClick={() =>
                                                            handlePageChange(
                                                                i + 1
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
                                                page === data.last_page
                                                    ? "disabled"
                                                    : ""
                                            }`}
                                        >
                                            <button
                                                className="page-link"
                                                onClick={() =>
                                                    handlePageChange(page + 1)
                                                }
                                            >
                                                Next
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {selectedJobSeeker && (
                <ViewModal
                    jobSeeker={selectedJobSeeker}
                    onClose={() => {
                        setSelectedJobSeeker(null);
                        fetchData(statusFilter);
                    }}
                    showActions={false}
                    onActionSuccess={() => {
                        setSelectedJobSeeker(null);
                        fetchData(statusFilter);
                    }}
                    setCounts={setCounts}
                    type={type}
                />
            )}
        </div>
    );
}
