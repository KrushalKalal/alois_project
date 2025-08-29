import { useState, useEffect, useCallback } from "react";
import { Link, usePage, router } from "@inertiajs/react";
import MainLayout from "../../Layouts/MainLayout";
import AdminView from "./AdminView";
import CheckerView from "./CheckerView";
import MakerView from "./MakerView";
import POMakerView from "./POMakerView";
import POCheckerView from "./POCheckerView";
import BackoutMakerView from "./BackoutMakerView";
import BackoutCheckerView from "./BackoutCheckerView";
import FinanceCheckerView from "./FinanceCheckerView";
import FinanceMakerView from "./FinanceMakerView";
import BulkUploadModal from "./BulkUploadModal";
import { toast } from "react-toastify";

export default function Index({
    auth,
    initialCounts,
    jobSeekers,
    isAdmin,
    isMaker,
    isChecker,
    isPOMaker,
    isPOChecker,
    isFinanceMaker,
    isFinanceChecker,
    isBackoutMaker,
    isBackoutChecker,
    employeeRole,
    type,
    employeeCompanyId,
    employeeId,
    backoutStatusId,
    companies,
    selectedCompanyId,
    statusFilter,
}) {
    console.log("Index.jsx props:", {
        auth,
        companies,
        jobSeekers,
        initialCounts,
        statusFilter,
        selectedCompanyId,
        flash: usePage().props.flash,
    });

    // Initialize jobSeekerData as a paginated object
    const initialJobSeekerData =
        jobSeekers && typeof jobSeekers === "object" && "data" in jobSeekers
            ? jobSeekers
            : {
                  data: Array.isArray(jobSeekers) ? jobSeekers : [],
                  current_page: 1,
                  last_page: 1,
                  total: Array.isArray(jobSeekers) ? jobSeekers.length : 0,
              };

    const [jobSeekerData, setJobSeekerData] = useState(initialJobSeekerData);
    const [counts, setCounts] = useState(
        initialCounts || { all: 0, pending: 0, approved: 0, rejected: 0 }
    );
    const [showBulkUploadModal, setShowBulkUploadModal] = useState(false);

    // Fetch job seekers from the server
    const fetchJobSeekers = useCallback(() => {
        console.log("fetchJobSeekers called with params:", {
            status_filter: statusFilter,
            company_id: selectedCompanyId,
            per_page: 10,
        });
        router.get(
            route(`job-seekers.${type.toLowerCase()}.index`),
            {
                status_filter: statusFilter,
                company_id: selectedCompanyId,
                per_page: 10,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: (page) => {
                    console.log("fetchJobSeekers onSuccess:", page.props);
                    const newJobSeekerData = page.props.jobSeekers || {
                        data: [],
                        current_page: 1,
                        last_page: 1,
                        total: 0,
                    };
                    setJobSeekerData({ ...newJobSeekerData }); // Create new object to ensure re-render
                    setCounts(page.props.counts || counts);
                },
                onError: (errors) => {
                    console.error("fetchJobSeekers error:", errors);
                    toast.error("Failed to fetch job seekers");
                },
            }
        );
    }, [type, statusFilter, selectedCompanyId, counts]);

    // Handle flash messages (only for non-import actions to avoid duplicate toasts)
    const { flash } = usePage().props;
    useEffect(() => {
        const currentPath = window.location.pathname;
        const viewBase = `/job-seekers/${type.toLowerCase()}`;
        if (flash?.success && currentPath === viewBase) {
            if (!flash.success.includes("imported successfully")) {
                toast.success(flash.success);
            }
        }
        if (flash?.error && currentPath === viewBase) {
            toast.error(flash.error);
        }
    }, [flash, type]);

    // Sync counts when initialCounts changes
    useEffect(() => {
        setCounts(
            initialCounts || { all: 0, pending: 0, approved: 0, rejected: 0 }
        );
        console.log(
            `Index.jsx rendered for ${type} at:`,
            new Date().toISOString()
        );
    }, [initialCounts, type]);

    // Callback for successful upload
    const handleUploadSuccess = (newJobSeekers) => {
        console.log("handleUploadSuccess called with:", { newJobSeekers });
        // Fetch fresh data from the server
        fetchJobSeekers();
        // Fallback: Merge new job seekers locally if fetch fails
        if (newJobSeekers && newJobSeekers.data) {
            setJobSeekerData((prev) => {
                const prevData = prev.data || [];
                const newData =
                    newJobSeekers.data ||
                    (Array.isArray(newJobSeekers) ? newJobSeekers : []);
                const updatedData = [...newData, ...prevData]; // Prepend new job seekers
                return {
                    data: updatedData,
                    current_page: 1, // Reset to page 1
                    last_page: Math.ceil(updatedData.length / 10),
                    total: updatedData.length,
                };
            });
        }
        setShowBulkUploadModal(false);
    };

    return (
        <MainLayout title={`${type} Job Seekers`} auth={auth}>
            <div className="container-fluid dashboard-width">
                <div className="row align-items-center">
                    <div class="col-lg-12">
                        <div
                            class="row"
                            style={{ padding: "0px 23px 0px 0px" }}
                        >
                            <div class="col-lg-6 col-md-6  col-sm-12 mb-2">
                                <h4 className="page-title">
                                    {type} Job Seekers
                                </h4>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                {(isMaker || isAdmin) && (
                                    <div className="col-auto ms-auto d-flex align-items-center justify-content-end">
                                        <button
                                            className="btn btn-primary me-2 d-flex align-items-center"
                                            onClick={() =>
                                                setShowBulkUploadModal(true)
                                            }
                                        >
                                            <i className="fa fa-upload me-2"></i>
                                            Bulk Upload
                                        </button>
                                        <Link
                                            href={route(
                                                `job-seekers.${type.toLowerCase()}.create`
                                            )}
                                            className="btn btn-primary d-flex align-items-center j"
                                        >
                                            <i className="fa fa-plus-circle me-2"></i>
                                            Add {type} Job Seeker
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                    <div className="row mb-4">
                        <div className="col-md-3">
                            <div className="card">
                                <div className="card-body">
                                    <h5 className="card-title">
                                        Total Job Seekers
                                    </h5>
                                    <p>{counts.all ?? 0}</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="card">
                                <div className="card-body">
                                    <h5 className="card-title">Pending</h5>
                                    <p>{counts.pending ?? 0}</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="card">
                                <div className="card-body">
                                    <h5 className="card-title">Approved</h5>
                                    <p>{counts.approved ?? 0}</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="card">
                                <div className="card-body">
                                    <h5 className="card-title">Rejected</h5>
                                    <p>{counts.rejected ?? 0}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {isAdmin && (
                        <AdminView
                            jobSeekers={jobSeekerData}
                            setCounts={setCounts}
                            type={type}
                            employeeCompanyId={employeeCompanyId}
                            statusFilter={statusFilter}
                        />
                    )}
                    {isChecker && !isAdmin && (
                        <CheckerView
                            jobSeekers={jobSeekerData}
                            setCounts={setCounts}
                            type={type}
                            employeeCompanyId={employeeCompanyId}
                            employeeId={employeeId}
                            backoutStatusId={backoutStatusId}
                            statusFilter={statusFilter}
                        />
                    )}
                    {isMaker && !isAdmin && !isChecker && (
                        <MakerView
                            jobSeekers={jobSeekerData}
                            setCounts={setCounts}
                            type={type}
                            employeeCompanyId={employeeCompanyId}
                            employeeId={employeeId}
                            statusFilter={statusFilter}
                        />
                    )}
                    {isPOMaker && !isAdmin && !isChecker && !isMaker && (
                        <POMakerView
                            jobSeekers={jobSeekerData}
                            setCounts={setCounts}
                            type={type}
                            employeeCompanyId={employeeCompanyId}
                            employeeId={employeeId}
                            companies={companies}
                            selectedCompanyId={selectedCompanyId}
                            statusFilter={statusFilter}
                        />
                    )}
                    {isPOChecker &&
                        !isAdmin &&
                        !isChecker &&
                        !isMaker &&
                        !isPOMaker && (
                            <POCheckerView
                                jobSeekers={jobSeekerData}
                                setCounts={setCounts}
                                type={type}
                                employeeCompanyId={employeeCompanyId}
                                employeeId={employeeId}
                                companies={companies}
                                selectedCompanyId={selectedCompanyId}
                                statusFilter={statusFilter}
                            />
                        )}
                    {isFinanceMaker &&
                        !isAdmin &&
                        !isChecker &&
                        !isMaker &&
                        !isPOMaker &&
                        !isPOChecker && (
                            <FinanceMakerView
                                jobSeekers={jobSeekerData}
                                setCounts={setCounts}
                                type={type}
                                employeeCompanyId={employeeCompanyId}
                                employeeId={employeeId}
                                companies={companies}
                                selectedCompanyId={selectedCompanyId}
                                statusFilter={statusFilter}
                            />
                        )}
                    {isFinanceChecker &&
                        !isAdmin &&
                        !isChecker &&
                        !isMaker &&
                        !isPOMaker &&
                        !isPOChecker &&
                        !isFinanceMaker && (
                            <FinanceCheckerView
                                jobSeekers={jobSeekerData}
                                setCounts={setCounts}
                                type={type}
                                employeeCompanyId={employeeCompanyId}
                                employeeId={employeeId}
                                companies={companies}
                                selectedCompanyId={selectedCompanyId}
                                statusFilter={statusFilter}
                            />
                        )}
                    {isBackoutMaker &&
                        !isAdmin &&
                        !isChecker &&
                        !isMaker &&
                        !isPOMaker &&
                        !isPOChecker &&
                        !isFinanceMaker &&
                        !isFinanceChecker && (
                            <BackoutMakerView
                                jobSeekers={jobSeekerData}
                                setCounts={setCounts}
                                type={type}
                                employeeCompanyId={employeeCompanyId}
                                employeeId={employeeId}
                                companies={companies}
                                selectedCompanyId={selectedCompanyId}
                                statusFilter={statusFilter}
                            />
                        )}
                    {isBackoutChecker &&
                        !isAdmin &&
                        !isChecker &&
                        !isMaker &&
                        !isPOMaker &&
                        !isPOChecker &&
                        !isFinanceMaker &&
                        !isFinanceChecker &&
                        !isBackoutMaker && (
                            <BackoutCheckerView
                                jobSeekers={jobSeekerData}
                                setCounts={setCounts}
                                type={type}
                                employeeCompanyId={employeeCompanyId}
                                employeeId={employeeId}
                                companies={companies}
                                selectedCompanyId={selectedCompanyId}
                                statusFilter={statusFilter}
                            />
                        )}
                    {(isMaker || isAdmin) && (
                        <BulkUploadModal
                            show={showBulkUploadModal}
                            onClose={() => setShowBulkUploadModal(false)}
                            type={type}
                            employeeCompanyId={employeeCompanyId}
                            companies={companies}
                            onUploadSuccess={handleUploadSuccess}
                        />
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
