import MainLayout from "@/Layouts/MainLayout";
import { useState } from "react";
import { router } from "@inertiajs/react";

export default function EmployeeDashboard({
    auth,
    dashboardData,
    companyToCountry,
    selectedYear,
    availableYears,
    currentYear,
}) {
    const isMultiCompanyRole = [
        "po_maker",
        "po_checker",
        "backout_maker",
        "backout_checker",
        "finance_maker",
        "finance_checker",
    ].includes(auth.employee?.role);
    const [activeTab, setActiveTab] = useState(isMultiCompanyRole ? 1 : null);

    const handleYearChange = (event) => {
        const year = event.target.value;
        router.get(
            route("employee.dashboard"),
            { year },
            { preserveState: true, preserveScroll: true }
        );
    };

    const handleExport = () => {
        if (["maker", "checker"].includes(auth.employee?.role)) {
            window.location.href = route("employee.dashboard.export", {
                year: selectedYear,
            });
        }
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

    const renderTable6 = (data) => (
        <div className="card mb-4">
            <div className="card-body">
                <h5 className="card-title">{`Finance Summary ${selectedYear} (${data.currency})`}</h5>
                <div className="table-responsive">
                    <table className="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Invoice No</th>
                                <th className="text-center">HC</th>
                                <th className="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.financeData.map((row, index) => (
                                <tr key={index}>
                                    <td>{row.client}</td>
                                    <td>{row.invoiceNo}</td>
                                    <td className="text-center">{row.HC}</td>
                                    <td className="text-center">
                                        {Number(row.total).toFixed(2)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );

    const renderTables = (data) => {
        if (["po_maker", "po_checker"].includes(auth.employee?.role)) {
            return renderTable4(data);
        } else if (
            ["backout_maker", "backout_checker"].includes(auth.employee?.role)
        ) {
            return (
                <>
                    {renderTable2(data)}
                    {renderTable3(data)}
                </>
            );
        } else if (
            ["finance_maker", "finance_checker"].includes(auth.employee?.role)
        ) {
            return renderTable6(data);
        } else {
            // maker/checker: show all tables
            return (
                <>
                    {renderTable1(data)}
                    {renderTable2(data)}
                    {renderTable3(data)}
                    {renderTable4(data)}
                    {renderTable5(data)}
                    {renderTable6(data)}
                </>
            );
        }
    };

    return (
        <MainLayout title="Employee Dashboard" auth={auth}>
            <div className="container-fluid mt-4 dashboard-width">
                <div className="row mb-3">
                   <div className="col-lg-5 col-md-5 col-sm-12">
                        <div className="page-title-box">
                            <h4
                                className="page-title"
                                style={{ color: "#F26522" }}
                            >
                                Employee Dashboard
                            </h4>
                            <p>
                                Welcome, {auth.name} (
                                {auth.employee?.role || "Employee"})
                            </p>
                            </div> </div>
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
                                                value={selectedYear}
                                                onChange={handleYearChange}
                                                style={{ width: "150px" }}
                                            >
                                                {availableYears.map((year) => (
                                                    <option
                                                        key={year}
                                                        value={year}
                                                        disabled={year > currentYear}
                                                    >
                                                        {year}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                   </div>
                                    {["maker", "checker"].includes(
                                        auth.employee?.role
                                    ) && (
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
                                         </div>
                                    )}
                               
                            </div>
                   
                           </div>
                </div>

                <div className="row">
                    <div className="col-12">
                        {isMultiCompanyRole && (
                            <ul className="nav nav-tabs">
                                {Object.entries(companyToCountry).map(
                                    ([companyId, region]) => (
                                        <li
                                            className="nav-item"
                                            key={companyId}
                                        >
                                            <a
                                                className={`nav-link ${
                                                    activeTab == companyId
                                                        ? "active"
                                                        : ""
                                                }`}
                                                href="#"
                                                onClick={() =>
                                                    setActiveTab(
                                                        Number(companyId)
                                                    )
                                                }
                                            >
                                                {region}
                                            </a>
                                        </li>
                                    )
                                )}
                            </ul>
                        )}
                        <div className="tab-content">
                            <div className="tab-pane show active">
                                {isMultiCompanyRole
                                    ? dashboardData[activeTab] &&
                                      renderTables(dashboardData[activeTab])
                                    : renderTables(dashboardData)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
