import { useState } from "react";
import { router } from "@inertiajs/react";
import { toast } from "react-toastify";
import fieldConfig from "./fieldConfig.json"; // Adjust path as needed

// Log fieldConfig to verify import
// console.log("fieldConfig:", fieldConfig);

// Helper function to format dates (YYYY-MM-DD)
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date) ? "Invalid Date" : date.toISOString().split("T")[0];
};

// Helper function to trim whitespace and \r\n
const trimString = (str) => {
    if (typeof str !== "string") {
        // console.warn("trimString: Non-string value received:", str);
        return str != null ? String(str) : "N/A";
    }
    return str.replace(/(\r\n|\n|\r)/gm, "").trim();
};

// Helper function to get field value based on type
const getFieldValue = (jobSeeker, field) => {
    // console.log(
    //     `Processing field: ${field.name}, value:`,
    //     jobSeeker[field.name]
    // ); // Debug
    const value = jobSeeker[field.name];
    if (value === null || value === undefined) return "N/A";

    // Use relationship fields for names
    if (field.name === "company_id" || field.name === "company") {
        return (
            jobSeeker.company?.name ||
            `Unknown Company (${jobSeeker.company_id})`
        );
    }
    if (field.name === "status_id" || field.name === "status") {
        return jobSeeker.status?.status || `Unknown Status (${value})`;
    }
    if (field.name === "client_id" || field.name === "client") {
        return jobSeeker.client?.client_name || `Unknown Client (${value})`;
    }
    if (field.name === "location_id" || field.name === "location") {
        return jobSeeker.location?.name || `Unknown Location (${value})`;
    }
    if (field.name === "business_unit_id" || field.name === "business_unit") {
        return (
            jobSeeker.business_unit?.unit || `Unknown Business Unit (${value})`
        );
    }
    if (field.name === "am_id" || field.name === "assistant_manager") {
        return (
            jobSeeker.assistant_manager?.name ||
            `Unknown Account Manager (${value})`
        );
    }
    if (field.name === "dm_id" || field.name === "deputy_manager") {
        return (
            jobSeeker.deputy_manager?.name ||
            `Unknown Deputy Manager (${value})`
        );
    }
    if (field.name === "tl_id" || field.name === "team_leader") {
        return jobSeeker.team_leader?.name || `Unknown Team Leader (${value})`;
    }
    if (field.name === "recruiter_id" || field.name === "recruiter") {
        return jobSeeker.recruiter?.name || `Unknown Recruiter (${value})`;
    }
    if (field.name === "hire_type" || field.name === "job_seeker_type") {
        return jobSeeker.hire_type || jobSeeker.job_seeker_type || "ALOIS";
    }

    // Handle other field types
    if (field.type === "date") return formatDate(value);
    if (field.type === "text" || field.type === "textarea") {
        return trimString(value);
    }
    if (field.type === "select" && field.options) {
        const option = field.options.find((opt) => opt.value === value);
        return option ? option.label : trimString(value);
    }
    return trimString(value);
};

export default function ViewModal({
    jobSeeker,
    onClose,
    showActions = false,
    onActionSuccess,
    setCounts,
    type,
}) {
    const [isProcessing, setIsProcessing] = useState(false);
    const [reasonOfRejection, setReasonOfRejection] = useState("");
    const [showRejectInput, setShowRejectInput] = useState(false);

    // Debugging logs
    // console.log("ViewModal props:", { jobSeeker, type, showActions });

    // Check if jobSeeker is valid
    if (!jobSeeker || Object.keys(jobSeeker).length === 0) {
        return (
            <div
                className="modal fade show d-block"
                tabIndex="-1"
                style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
            >
                <div className="modal-dialog modal-xl">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">
                                {type} Job Seeker Details
                            </h5>
                            <button
                                type="button"
                                className="btn-close"
                                onClick={onClose}
                            ></button>
                        </div>
                        <div className="modal-body">
                            <p className="text-danger">
                                No job seeker data available.
                            </p>
                        </div>
                        <div className="modal-footer">
                            <button
                                onClick={onClose}
                                className="btn btn-secondary"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // Get region from jobSeeker's company, fallback to "India"
    const companyRegion =
        jobSeeker.company?.region && fieldConfig[jobSeeker.company.region]
            ? jobSeeker.company.region
            : "India";
    const jobSeekerType =
        jobSeeker.job_seeker_type?.toLowerCase() || "temporary";
    const fields = fieldConfig[companyRegion]?.[jobSeekerType] || [];

    // Debugging fields
    // console.log("Field config:", { companyRegion, jobSeekerType, fields });

    // Group fields by section
    const groupedFields = fields.reduce((acc, field) => {
        const section = field.section || "Other";
        acc[section] = acc[section] || [];
        acc[section].push(field);
        return acc;
    }, {});

    // Fallback if no fields are available
    if (Object.keys(groupedFields).length === 0) {
        return (
            <div
                className="modal fade show d-block"
                tabIndex="-1"
                style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
            >
                <div className="modal-dialog modal-xl">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">
                                {type} Job Seeker Details
                            </h5>
                            <button
                                type="button"
                                className="btn-close"
                                onClick={onClose}
                            ></button>
                        </div>
                        <div className="modal-body">
                            <p className="text-danger">
                                No fields configured for {companyRegion}/
                                {jobSeekerType}.
                            </p>
                            <pre>{JSON.stringify(jobSeeker, null, 2)}</pre>
                        </div>
                        <div className="modal-footer">
                            <button
                                onClick={onClose}
                                className="btn btn-secondary"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // Filter fields for reason_of_rejection
    const filteredFields = Object.keys(groupedFields).reduce((acc, section) => {
        acc[section] = groupedFields[section].filter((field) => {
            if (field.name === "reason_of_rejection") {
                return (
                    (jobSeeker.form_status === "Rejected" &&
                        jobSeeker.reason_of_rejection) ||
                    (showActions && showRejectInput)
                );
            }
            return true;
        });
        return acc;
    }, {});

    // Determine if user is a checker
    const isChecker = typeof showActions === "function" || showActions === true;

    // Handle Approve
    const handleApprove = () => {
        if (isProcessing) return;
        setIsProcessing(true);
        router.post(
            route(`job-seekers.${type.toLowerCase()}.approve`, jobSeeker.id),
            {},
            {
                preserveState: false,
                preserveScroll: true,
                onSuccess: (page) => {
                    setCounts(page.props.counts);
                    onActionSuccess();
                },
                onError: (errors) => {
                    // console.error("Approve error:", errors);
                    toast.error(errors.error || "Failed to approve Job Seeker");
                },
                onFinish: () => setIsProcessing(false),
            }
        );
    };

    // Handle Reject
    const handleReject = () => {
        if (showRejectInput && !reasonOfRejection.trim()) {
            toast.error("Please provide a reason for rejection");
            return;
        }
        if (isProcessing) return;
        setIsProcessing(true);
        router.post(
            route(`job-seekers.${type.toLowerCase()}.reject`, jobSeeker.id),
            { reason_of_rejection: reasonOfRejection },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    toast.success(
                        page.props.flash?.success ||
                            "Job Seeker rejected successfully"
                    );
                    setCounts(page.props.counts);
                    onActionSuccess();
                },
                onError: (errors) => {
                    // console.error("Reject error:", errors);
                    toast.error(errors.error || "Failed to reject Job Seeker");
                },
                onFinish: () => {
                    setIsProcessing(false);
                    setShowRejectInput(false);
                    setReasonOfRejection("");
                },
            }
        );
    };

    // Toggle reject input visibility
    const toggleRejectInput = () => {
        setShowRejectInput(true);
    };

    return (
        <div
            className="modal fade show d-block"
            tabIndex="-1"
            style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
        >
            <div className="modal-dialog modal-xl">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">
                            {type} Job Seeker Details
                        </h5>
                        <button
                            type="button"
                            className="btn-close"
                            onClick={onClose}
                        ></button>
                    </div>
                    <div
                        className="modal-body"
                        style={{ maxHeight: "70vh", overflowY: "auto" }}
                    >
                        {Object.keys(filteredFields).map((section) => (
                            <div key={section}>
                                <h6 className="mt-3">{section}</h6>
                                <div className="row">
                                    {filteredFields[section].map((field) => (
                                        <div
                                            key={field.name}
                                            className="col-md-6"
                                        >
                                            <p>
                                                <strong>{field.label}:</strong>{" "}
                                                {field.name ===
                                                    "reason_of_rejection" &&
                                                isChecker &&
                                                showActions &&
                                                showRejectInput ? (
                                                    <textarea
                                                        className="form-control"
                                                        value={
                                                            reasonOfRejection
                                                        }
                                                        onChange={(e) =>
                                                            setReasonOfRejection(
                                                                e.target.value
                                                            )
                                                        }
                                                        placeholder="Enter reason for rejection"
                                                        disabled={isProcessing}
                                                    />
                                                ) : (
                                                    getFieldValue(
                                                        jobSeeker,
                                                        field
                                                    )
                                                )}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="modal-footer d-flex justify-content-between">
                        <div>
                            {isChecker &&
                                (typeof showActions === "function"
                                    ? showActions(jobSeeker)
                                    : showActions) &&
                                jobSeeker.form_status === "Pending" && (
                                    <>
                                        <button
                                            onClick={handleApprove}
                                            className="btn btn-success me-2"
                                            disabled={isProcessing}
                                        >
                                            Approve
                                        </button>
                                        {!showRejectInput ? (
                                            <button
                                                onClick={toggleRejectInput}
                                                className="btn btn-danger me-2"
                                                disabled={isProcessing}
                                            >
                                                Reject
                                            </button>
                                        ) : (
                                            <button
                                                onClick={handleReject}
                                                className="btn btn-danger me-2"
                                                disabled={isProcessing}
                                            >
                                                Submit Rejection
                                            </button>
                                        )}
                                    </>
                                )}
                        </div>
                        <button onClick={onClose} className="btn btn-secondary">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
