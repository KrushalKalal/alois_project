import { useState, useEffect } from "react";
import { router, usePage } from "@inertiajs/react";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import MainLayout from "@/Layouts/MainLayout";

export default function AdminDashboard({
    auth,
    dashboardData,
    companyToCountry,
    selectedYear,
    availableYears,
    currentYear,
}) {
    const { flash } = usePage().props || { flash: {} };
    const [activeTab, setActiveTab] = useState(1);
    const [year, setYear] = useState(selectedYear || new Date().getFullYear());

    useEffect(() => {
        // console.log("Props received:", {
        //     auth,
        //     dashboardData,
        //     companyToCountry,
        //     selectedYear,
        //     availableYears,
        //     currentYear,
        //     flash,
        // });
        if (flash.success) {
            toast.success(flash.success, { autoClose: 5000 });
        }
        if (flash.error) {
            toast.error(flash.error, { autoClose: 5000 });
        }
    }, [flash]);

    const handleYearChange = (event) => {
        const year = event.target.value;
        setYear(year);
        router.get(
            route("admin.dashboard"),
            { year },
            { preserveState: true, preserveScroll: true }
        );
    };

    const handleExport = () => {
        window.location.href = route("admin.dashboard.export", {
            company_id: activeTab,
            year: selectedYear,
        });
    };

    const handleSendEmail = (companyId) => {
        router.post(
            route("admin.dashboard.send-email"),
            { company_id: companyId, year: selectedYear },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ["flash"], // Update only the flash message without changing the URL
            }
        );
    };

    const renderTable1 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`Perm Data ${selectedYear} (${data.currency})`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Months</th>
                                <th className="text-center">Selected</th>
                                <th className="text-center">Backout</th>
                                <th className="text-center">Terminated</th>
                                <th className="text-center">Offered</th>
                                <th className="text-center">Joined</th>
                                <th className="text-center">FTE Conv Fees</th>
                                <th className="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.permData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.month}</td>
                                    <td className="text-center">
                                        {row.Selected === null
                                            ? ""
                                            : Number(row.Selected).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Backout === null
                                            ? ""
                                            : Number(row.Backout).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Terminated === null
                                            ? ""
                                            : Number(row.Terminated).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Offered === null
                                            ? ""
                                            : Number(row.Offered).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Joined === null
                                            ? ""
                                            : Number(row.Joined).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.FTEConversionFees === null
                                            ? ""
                                            : Number(
                                                  row.FTEConversionFees
                                              ).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Total === null
                                            ? ""
                                            : Number(row.Total).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const renderTable2 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`${
                    data.region === "APAC"
                        ? "Daily"
                        : data.region === "EU-UK"
                        ? "Hourly"
                        : "Monthly"
                } Contracting Backouts ${selectedYear} (${data.currency})`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Months</th>
                                <th className="text-center">HC</th>
                                <th className="text-center">BR</th>
                                <th className="text-center">PR</th>
                                <th className="text-center">Final_GP</th>
                                <th className="text-center">GP %</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.backoutData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.month}</td>
                                    <td className="text-center">
                                        {row.HC === null ? "" : row.HC}
                                    </td>
                                    <td className="text-center">
                                        {row.BR === null
                                            ? ""
                                            : Number(row.BR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.PR === null
                                            ? ""
                                            : Number(row.PR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Final_GP === null
                                            ? ""
                                            : Number(row.Final_GP).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row["GP%"] === null
                                            ? ""
                                            : Number(row["GP%"]).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const renderTable3 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`${
                    data.region === "APAC"
                        ? "Daily"
                        : data.region === "EU-UK"
                        ? "Hourly"
                        : "Monthly"
                } Contracting Termination ${selectedYear} (${
                    data.currency
                })`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Months</th>
                                <th className="text-center">HC</th>
                                <th className="text-center">BR</th>
                                <th className="text-center">PR</th>
                                <th className="text-center">Final_GP</th>
                                <th className="text-center">GP %</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.terminationData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.month}</td>
                                    <td className="text-center">
                                        {row.HC === null ? "" : row.HC}
                                    </td>
                                    <td className="text-center">
                                        {row.BR === null
                                            ? ""
                                            : Number(row.BR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.PR === null
                                            ? ""
                                            : Number(row.PR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Final_GP === null
                                            ? ""
                                            : Number(row.Final_GP).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row["GP%"] === null
                                            ? ""
                                            : Number(row["GP%"]).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const renderTable4 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`PO Not Available/Expiry Summary ${selectedYear} (${data.currency})`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Po End Year</th>
                                <th>Client</th>
                                <th>Po End Month</th>
                                <th className="text-center">HC</th>
                                <th className="text-center">BR</th>
                                <th className="text-center">PR</th>
                                <th className="text-center">Final_GP</th>
                                <th className="text-center">GP %</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.poExpiryData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.poEndYear}</td>
                                    <td>{row.client}</td>
                                    <td>{row.poEndMonth}</td>
                                    <td className="text-center">
                                        {row.HC === null ? "" : row.HC}
                                    </td>
                                    <td className="text-center">
                                        {row.BR === null
                                            ? ""
                                            : Number(row.BR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.PR === null
                                            ? ""
                                            : Number(row.PR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row.Final_GP === null
                                            ? ""
                                            : Number(row.Final_GP).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {row["GP%"] === null
                                            ? ""
                                            : Number(row["GP%"]).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const renderTable5 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`${
                    data.region === "APAC"
                        ? "Daily"
                        : data.region === "EU-UK"
                        ? "Hourly"
                        : "Monthly"
                } Contracting (Joined/Offer/Selected) ${selectedYear} (${
                    data.currency
                })`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Client</th>
                                <th className="text-center">HC</th>
                                <th className="text-center">BR</th>
                                <th className="text-center">PR</th>
                                <th className="text-center">Final_GP</th>
                                <th className="text-center">GP %</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.contractData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.status || ""}</td>
                                    <td>{row.client}</td>
                                    <td className="text-center">{row.HC}</td>
                                    <td className="text-center">
                                        {Number(row.BR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {Number(row.PR).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {Number(row.Final_GP).toFixed(2)}
                                    </td>
                                    <td className="text-center">
                                        {Number(row["GP%"]).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const companies =
        companyToCountry && typeof companyToCountry === "object"
            ? companyToCountry
            : {};

    return (
        <MainLayout title="Admin Dashboard" auth={auth}>
            <ToastContainer position="top-right" />
            <div className="container-fluid dashboard-width">
                <div className="row mb-5" >
                    <div className="col-lg-5 col-md-5 col-sm-12">
                        <div className="page-title-box">
                            <h4
                                className="page-title"
                                style={{ color: "#F26522" }}
                            >
                                Admin Dashboard
                            </h4>
                            <p>Welcome, {auth.name} (Admin)</p>
                            </div>
                    </div>
                     <div className="col-lg-7 col-md-7 col-sm-12">
                         <div className="yearselect-btn"> 
                            <div className="">
                                <div className="label-join-select">
                                <label
                                    htmlFor="yearSelect"
                                    className="form-label"
                                >
                                    Select Year:
                                </label>
                                <select
                                    id="yearSelect"
                                    className="form-select"
                                    value={year}
                                    onChange={handleYearChange}
                                    style={{ width: "150px" }}
                                >
                                    {availableYears &&
                                    availableYears.length > 0 ? (
                                        availableYears.map((year) => (
                                            <option
                                                key={year}
                                                value={year}
                                                disabled={year > currentYear}
                                            >
                                                {year}
                                            </option>
                                        ))
                                    ) : (
                                        <option value={year}>{year}</option>
                                    )}
                                </select>
                                </div>
                            </div>
                            <div className="orange-btn">
                            <button
                                className="btn btn-primary "
                                style={{
                                    backgroundColor: "#F26522",
                                    borderColor: "#F26522",
                                }}
                                onClick={handleExport}
                            >
                                Export to Excel
                            </button>
                            <button
                                className="btn btn-primary "
                                style={{
                                    backgroundColor: "#F26522",
                                    borderColor: "#F26522",
                                }}
                                onClick={() => handleSendEmail(activeTab)}
                            >
                                Send Email
                            </button>
                            </div>
                         </div>   
                        </div>
             </div>

                <div className="row">
                    <div className="col-12">
                        <ul className="nav nav-tabs">
                            {Object.entries(companies).map(
                                ([companyId, region]) => (
                                    <li className="nav-item" key={companyId}>
                                        <a
                                            className={`nav-link ${
                                                activeTab == companyId
                                                    ? "active"
                                                    : ""
                                            }`}
                                            href="#"
                                            onClick={() =>
                                                setActiveTab(Number(companyId))
                                            }
                                        >
                                            {region}
                                        </a>
                                    </li>
                                )
                            )}
                        </ul>
                        <div className="tab-content">
                            <div className="tab-pane show active">
                                {dashboardData && dashboardData[activeTab] ? (
                                    <>
                                        {renderTable1(dashboardData[activeTab])}
                                        {renderTable2(dashboardData[activeTab])}
                                        {renderTable3(dashboardData[activeTab])}
                                        {renderTable4(dashboardData[activeTab])}
                                        {renderTable5(dashboardData[activeTab])}
                                    </>
                                ) : (
                                    <p>
                                        No data available for the selected
                                        company.
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
         </div>

        </MainLayout>
    );
}
