import { useState } from "react";
import { router } from "@inertiajs/react";
import { toast } from "react-toastify";

export default function BulkUploadModal({
    show,
    onClose,
    type,
    employeeCompanyId,
    companies,
    onUploadSuccess,
}) {
    const [companyId, setCompanyId] = useState(employeeCompanyId || "");
    const [file, setFile] = useState(null);

    const resetForm = () => {
        setCompanyId(employeeCompanyId || "");
        setFile(null);
    };

    const handleDownloadTemplate = () => {
        if (!companyId) {
            toast.error("Please select a company");
            return;
        }

        const link = document.createElement("a");
        link.href = `${route(
            `job-seekers.${type.toLowerCase()}.template`
        )}?company_id=${companyId}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const handleUpload = (e) => {
        e.preventDefault();
        if (!file || !companyId) {
            toast.error("Please select a company and file");
            return;
        }

        const formData = new FormData();
        formData.append("file", file);
        formData.append("company_id", companyId.toString());

        // console.log("BulkUploadModal handleUpload: Sending POST request", {
        //     companyId,
        //     file: file.name,
        // });

        router.post(
            route(`job-seekers.${type.toLowerCase()}.import`),
            formData,
            {
                preserveState: true,
                preserveScroll: true,
                forceFormData: true,
                onSuccess: (page) => {
                    // console.log("BulkUploadModal onSuccess:", page);
                    const { flash } = page.props;
                    onUploadSuccess();
                    toast.success(
                        flash?.success || "Job seekers imported successfully"
                    );
                },
                onError: (errors) => {
                    // console.error("BulkUploadModal router.post error:", errors);
                    toast.error(
                        errors.file ||
                            errors.company_id ||
                            errors.error ||
                            "Failed to import job seekers"
                    );
                },
            }
        );
    };

    return (
        <div
            className={`modal fade ${show ? "show d-block" : ""}`}
            tabIndex="-1"
            style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
        >
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">
                            Bulk Upload Job Seekers ({type})
                        </h5>
                        <button
                            type="button"
                            className="btn-close"
                            onClick={() => {
                                resetForm();
                                onClose();
                            }}
                        ></button>
                    </div>
                    <div className="modal-body">
                        <form onSubmit={handleUpload}>
                            {!employeeCompanyId ? (
                                <div className="mb-3">
                                    <label className="form-label">
                                        Select Company
                                    </label>
                                    <select
                                        className="form-select"
                                        value={companyId}
                                        onChange={(e) =>
                                            setCompanyId(e.target.value)
                                        }
                                        required
                                    >
                                        <option value="">
                                            Select a company
                                        </option>
                                        {companies.map((company) => (
                                            <option
                                                key={company.id}
                                                value={company.id}
                                            >
                                                {company.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            ) : (
                                <div className="mb-3">
                                    <label className="form-label">
                                        Company
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        value={
                                            companies.find(
                                                (c) =>
                                                    c.id === employeeCompanyId
                                            )?.name || "Unknown Company"
                                        }
                                        disabled
                                    />
                                    <input
                                        type="hidden"
                                        name="company_id"
                                        value={companyId}
                                    />
                                </div>
                            )}
                            <div className="mb-3">
                                <label className="form-label">
                                    Upload Excel File
                                </label>
                                <input
                                    type="file"
                                    className="form-control"
                                    accept=".xlsx"
                                    onChange={(e) => setFile(e.target.files[0])}
                                    required
                                />
                            </div>
                            <button
                                type="button"
                                className="btn btn-secondary me-2"
                                onClick={handleDownloadTemplate}
                                disabled={!companyId}
                            >
                                Download Template
                            </button>
                            <button type="submit" className="btn btn-primary">
                                Upload
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}
