import { useEffect, useMemo } from "react";
import { useForm, router } from "@inertiajs/react";
import Select from "react-select";
import { toast } from "react-toastify";
import MainLayout from "@/Layouts/MainLayout";

const customSelectStyles = {
    control: (provided, state) => ({
        ...provided,
        borderColor: state.isFocused
            ? "#80bdff"
            : state.selectProps.className?.includes("is-invalid")
            ? "#dc3545"
            : "#ced4da",
        boxShadow: state.isFocused ? "0 0 0 0.2rem rgba(0,123,255,.25)" : null,
        "&:hover": {
            borderColor: state.isFocused
                ? "#80bdff"
                : state.selectProps.className?.includes("is-invalid")
                ? "#dc3545"
                : "#ced4da",
        },
        minHeight: "38px",
        height: "38px",
        borderRadius: "0.25rem",
        backgroundColor: state.isDisabled ? "#e9ecef" : "#fff",
        opacity: state.isDisabled ? 0.65 : 1,
        cursor: state.isDisabled ? "not-allowed" : "default",
    }),
    valueContainer: (provided) => ({
        ...provided,
        padding: "0.375rem 0.75rem",
    }),
    input: (provided) => ({ ...provided, margin: 0, padding: 0 }),
    singleValue: (provided) => ({ ...provided, color: "#495057" }),
    menu: (provided) => ({ ...provided, zIndex: 9999 }),
    option: (provided, state) => ({
        ...provided,
        backgroundColor: state.isSelected
            ? "#007bff"
            : state.isFocused
            ? "#f8f9fa"
            : null,
        color: state.isSelected ? "#fff" : "#495057",
    }),
};

const formatOptions = (items, valueKey, labelKey) =>
    Array.isArray(items) && items.length > 0
        ? items.map((item) => ({
              value: item[valueKey]?.toString() || "",
              label: item[labelKey] || "Unknown",
          }))
        : [{ value: "", label: "No options available" }];

const formatDateForInput = (isoDate) => {
    if (!isoDate) return "";
    const date = new Date(isoDate);
    return isNaN(date.getTime()) ? "" : date.toISOString().split("T")[0];
};

export default function JobSeekerMasterForm({
    auth,
    masterName,
    masterData = null,
    viewBase,
    fields = [],
    isFieldReadOnly,
    hasCompanyDropdown = false,
    clients = [],
    jobSeekerType,
    country,
    companies = [],
    branches = [],
    businessUnits = [],
    assistantManagers = [],
    deputyManagers = [],
    teamLeaders = [],
    recruiters = [],
    statuses = [],
    employeeCompanyId,
    employeeRole,
    isLoading,
}) {
    const isEdit = !!masterData && masterData.id !== undefined;
    console.log("JobSeekerMasterForm props:", {
        isEdit,
        masterData,
        jobSeekerType,
        employeeCompanyId,
        hasCompanyDropdown,
    });

    const isAdmin = auth.role === "admin";
    const isMaker = employeeRole === "maker";
    const normalizedType = jobSeekerType.toLowerCase();

    const { data, setData, post, put, errors, reset } = useForm(() => {
        const initialData = isEdit ? masterData : {};
        const formData = {};
        const dateFields = [
            "selection_date",
            "offer_date",
            "join_date",
            "qly_date",
            "backout_term_date",
            "po_end_date",
        ];
        Object.keys(initialData).forEach((key) => {
            if (dateFields.includes(key)) {
                formData[key] = formatDateForInput(initialData[key]);
            } else {
                formData[key] =
                    initialData[key] != null ? initialData[key] : "";
            }
        });
        fields.forEach((field) => {
            if (!(field.name in formData)) {
                formData[field.name] = "";
            }
        });
        if (isMaker && employeeCompanyId && !isEdit) {
            formData.company_id = employeeCompanyId.toString();
        }
        return formData;
    });

    useEffect(() => {
        if (isMaker && employeeCompanyId && !isEdit) {
            setData("company_id", employeeCompanyId.toString());
        }
    }, [isMaker, employeeCompanyId, isEdit, setData]);

    const clientName = useMemo(() => {
        const client = clients.find((c) => c.id === parseInt(data.client_id));
        return client ? client.client_name : "";
    }, [data.client_id, clients]);

    const statusName = useMemo(() => {
        const status = statuses.find((s) => s.id === parseInt(data.status_id));
        return status ? status.status : "";
    }, [data.status_id, statuses]);

    const recruiterName = useMemo(() => {
        const recruiter = recruiters.find(
            (r) => r.id === parseInt(data.recruiter_id)
        );
        return recruiter ? recruiter.name : "";
    }, [data.recruiter_id, recruiters]);

    useEffect(() => {
        if (data.client_id && data.join_date) {
            const client = clients.find(
                (c) => c.id === parseInt(data.client_id)
            );
            if (client) {
                const joinDate = new Date(data.join_date);
                const qlyDate = new Date(joinDate);
                qlyDate.setDate(
                    joinDate.getDate() + parseInt(client.qualify_days || 0)
                );
                setData((prevData) => ({
                    ...prevData,
                    qly_date: qlyDate.toISOString().split("T")[0],
                    loaded_cost: client.loaded_cost
                        ? client.loaded_cost.toString()
                        : "0",
                }));
            }
        }
    }, [data.client_id, data.join_date, clients, setData]);

    useEffect(() => {
        if (jobSeekerType === "temporary") {
            const payRate = parseFloat(data.pay_rate) || 0;
            const billRate = parseFloat(data.bill_rate) || 0;
            const otc = parseFloat(data.otc) || 0;
            const mspFees = parseFloat(data.msp_fees) || 0;
            const loadedCostPercentage = parseFloat(data.loaded_cost) || 0;
            const payRateAndLoadedCost = payRate * (loadedCostPercentage / 100);
            const actualLoadedCost = payRateAndLoadedCost;
            const payRate1 = payRate + payRateAndLoadedCost;
            const gpMonth = billRate - payRate;
            const otcSplit = otc / 6;
            const finalGp = gpMonth - payRateAndLoadedCost - otcSplit - mspFees;
            let percentageGp = billRate > 0 ? (finalGp / billRate) * 100 : 0;
            let gpHour = 0;
            let gpHourUsd = 0;

            if (country === "EU-UK") {
                const payRateUsd = parseFloat(data.pay_rate_usd) || 0;
                const billRateUsd = parseFloat(data.bill_rate_usd) || 0;
                gpHour = billRate - payRate;
                gpHourUsd = billRateUsd - payRateUsd;
                percentageGp = billRate > 0 ? (gpHour / billRate) * 100 : 0;
            }

            setData((prevData) => ({
                ...prevData,
                loaded_cost: actualLoadedCost.toFixed(2),
                pay_rate_1: payRate1.toFixed(2),
                gp_month: gpMonth.toFixed(2),
                otc_split: otcSplit.toFixed(2),
                final_gp: finalGp.toFixed(2),
                percentage_gp: percentageGp.toFixed(2),
                gp_hour: gpHour.toFixed(2),
                gp_hour_usd: gpHourUsd.toFixed(2),
            }));
        }
    }, [
        data.pay_rate,
        data.bill_rate,
        data.otc,
        data.msp_fees,
        data.loaded_cost,
        data.pay_rate_usd,
        data.bill_rate_usd,
        jobSeekerType,
        country,
        setData,
    ]);

    useEffect(() => {
        if (jobSeekerType === "permanent") {
            const billingValue = parseFloat(data.billing_value) || 0;
            const ctcOffered = parseFloat(data.ctc_offered) || 0;
            const loadedCost = parseFloat(data.loaded_cost) || 0;
            const loadedGp = (ctcOffered - billingValue) / 12;
            const finalBillingValue = billingValue - loadedGp;

            setData((prevData) => ({
                ...prevData,
                loaded_gp: loadedGp.toFixed(2),
                final_billing_value: finalBillingValue.toFixed(2),
            }));
        }
    }, [
        data.billing_value,
        data.ctc_offered,
        data.loaded_cost,
        jobSeekerType,
        setData,
    ]);

    useEffect(() => {
        const newRemark2 = `${clientName || "N/A"} : ${statusName || "N/A"} : ${
            data.consultant_name || "N/A"
        } ${data.final_gp || "0"} / (${recruiterName || "N/A"})`;
        if (data.remark2 !== newRemark2) {
            setData("remark2", newRemark2);
        }
    }, [
        clientName,
        statusName,
        data.consultant_name,
        data.final_gp,
        recruiterName,
        data.remark2,
        setData,
    ]);

    const handleSubmit = (e) => {
        e.preventDefault();
        const method = isEdit ? put : post;
        const routeName = isEdit ? `${viewBase}.update` : `${viewBase}.store`;

        // Prepare data with type casting
        const submitData = {};
        const numericFields = [
            "pay_rate",
            "bill_rate",
            "pay_rate_1",
            "gp_month",
            "otc",
            "otc_split",
            "msp_fees",
            "loaded_cost",
            "final_gp",
            "percentage_gp",
            "pay_rate_usd",
            "bill_rate_usd",
            "basic_pay_rate",
            "benefits",
            "gp_hour",
            "gp_hour_usd",
            "ctc_offered",
            "billing_value",
            "loaded_gp",
            "final_billing_value",
            "actual_billing_value",
        ];
        const integerFields = [
            "tl_id",
            "am_id",
            "dm_id",
            "recruiter_id",
            "status_id",
            "client_id",
            "company_id",
            "location_id",
            "business_unit_id",
            "po_end_year",
            "backout_term_year",
        ];
        Object.keys(data).forEach((key) => {
            if (numericFields.includes(key)) {
                submitData[key] =
                    data[key] && !isNaN(parseFloat(data[key]))
                        ? parseFloat(data[key])
                        : null;
            } else if (integerFields.includes(key)) {
                submitData[key] =
                    data[key] && !isNaN(parseInt(data[key]))
                        ? parseInt(data[key])
                        : null;
            } else {
                submitData[key] = data[key] || null;
            }
        });

        console.log("Submitting data:", submitData);

        method(route(routeName, isEdit ? masterData.id : {}), {
            data: submitData,
            onSuccess: () => {
                toast.success(
                    `${masterName || "Job Seeker"} ${
                        isEdit ? "updated" : "created"
                    } successfully!`
                );
                // reset();
                // router.visit(route(`${viewBase}.index`), {
                //     preserveState: false,
                // });
            },
            onError: (errors) => {
                console.error("Validation errors:", errors);
                toast.error(
                    `Failed to ${isEdit ? "update" : "create"} ${
                        masterName || "Job Seeker"
                    }. Please check the form errors.`
                );
            },
        });
    };

    const renderField = (field) => {
        const isReadOnly = isFieldReadOnly(field);
        const value =
            field.type === "select"
                ? data[field.name] != null
                    ? data[field.name].toString()
                    : ""
                : data[field.name] || "";
        const error = errors[field.name];

        const handleChange = (e) => {
            let newValue =
                field.type === "select" && e ? e.value : e.target.value;
            const numericFields = [
                "pay_rate",
                "bill_rate",
                "pay_rate_1",
                "gp_month",
                "otc",
                "otc_split",
                "msp_fees",
                "loaded_cost",
                "final_gp",
                "percentage_gp",
                "pay_rate_usd",
                "bill_rate_usd",
                "basic_pay_rate",
                "benefits",
                "gp_hour",
                "gp_hour_usd",
                "ctc_offered",
                "billing_value",
                "loaded_gp",
                "final_billing_value",
                "actual_billing_value",
            ];
            const integerFields = [
                "tl_id",
                "am_id",
                "dm_id",
                "recruiter_id",
                "status_id",
                "client_id",
                "company_id",
                "location_id",
                "business_unit_id",
                "po_end_year",
                "backout_term_year",
            ];
            if (numericFields.includes(field.name)) {
                newValue =
                    newValue && !isNaN(newValue) ? parseFloat(newValue) : "";
            } else if (integerFields.includes(field.name)) {
                newValue =
                    newValue && !isNaN(newValue) ? parseInt(newValue) : "";
            }
            setData(field.name, newValue);
            if (field.onChange) field.onChange(newValue, setData);
        };

        const getSelectOptions = (fieldName) => {
            const placeholderOption = {
                value: "",
                label: `Select ${field.label}`,
            };
            switch (fieldName) {
                case "company_id":
                    return hasCompanyDropdown
                        ? [
                              placeholderOption,
                              ...formatOptions(companies, "id", "name"),
                          ]
                        : [
                              {
                                  value:
                                      (
                                          employeeCompanyId || data.company_id
                                      )?.toString() || "",
                                  label:
                                      companies.find(
                                          (c) =>
                                              c.id ===
                                              parseInt(
                                                  employeeCompanyId ||
                                                      data.company_id
                                              )
                                      )?.name || "N/A",
                              },
                          ];
                case "location_id":
                    return [
                        placeholderOption,
                        ...formatOptions(branches, "id", "name"),
                    ];
                case "business_unit_id":
                    return [
                        placeholderOption,
                        ...formatOptions(businessUnits, "id", "unit"),
                    ];
                case "am_id":
                    return [
                        placeholderOption,
                        ...formatOptions(assistantManagers, "id", "name"),
                    ];
                case "dm_id":
                    return [
                        placeholderOption,
                        ...formatOptions(deputyManagers, "id", "name"),
                    ];
                case "tl_id":
                    return [
                        placeholderOption,
                        ...formatOptions(teamLeaders, "id", "name"),
                    ];
                case "recruiter_id":
                    return [
                        placeholderOption,
                        ...formatOptions(recruiters, "id", "name"),
                    ];
                case "client_id":
                    return [
                        placeholderOption,
                        ...formatOptions(clients, "id", "client_name"),
                    ];
                case "status_id":
                    return [
                        placeholderOption,
                        ...formatOptions(statuses, "id", "status"),
                    ];
                case "currency":
                    return field.options || [placeholderOption];
                case "type_of_attrition":
                    return [
                        placeholderOption,
                        { value: "Voluntary", label: "Voluntary" },
                        { value: "Involuntary", label: "Involuntary" },
                    ];
                default:
                    return field.options || [placeholderOption];
            }
        };

        switch (field.type) {
            case "select":
                const options = getSelectOptions(field.name);
                const placeholderOption = {
                    value: "",
                    label: `Select ${field.label}`,
                };
                const selectedOption = isEdit
                    ? options.find((option) => option.value === value) ||
                      placeholderOption
                    : value
                    ? options.find((option) => option.value === value)
                    : placeholderOption;
                return (
                    <div key={field.name} className="col-md-4 mb-3">
                        <label className="form-label">
                            {field.label}{" "}
                            {field.required && (
                                <span className="text-danger">*</span>
                            )}
                        </label>
                        <Select
                            className={`basic-single ${
                                error ? "is-invalid" : ""
                            }`}
                            classNamePrefix="select"
                            styles={customSelectStyles}
                            value={selectedOption}
                            onChange={handleChange}
                            options={options}
                            isDisabled={isReadOnly}
                            isSearchable
                        />
                        {error && (
                            <div className="invalid-feedback">{error}</div>
                        )}
                    </div>
                );
            case "text":
            case "number":
            case "date":
                return (
                    <div key={field.name} className="col-md-4 mb-3">
                        <label className="form-label">
                            {field.label}{" "}
                            {field.required && (
                                <span className="text-danger">*</span>
                            )}
                        </label>
                        <input
                            type={field.type}
                            className={`form-control ${
                                error ? "is-invalid" : ""
                            }`}
                            value={value}
                            onChange={handleChange}
                            disabled={isReadOnly}
                            required={field.required}
                            style={
                                isReadOnly ? { backgroundColor: "#e9ecef" } : {}
                            }
                        />
                        {error && (
                            <div className="invalid-feedback">{error}</div>
                        )}
                    </div>
                );
            default:
                return null;
        }
    };

    const groupedFields = Array.isArray(fields)
        ? fields.reduce((acc, field) => {
              if (!field || !field.section) {
                  console.warn("Invalid field:", field);
                  return acc;
              }
              if (!acc[field.section]) acc[field.section] = [];
              acc[field.section].push(field);
              return acc;
          }, {})
        : {};

    const companyName = useMemo(() => {
        const companyId = isEdit
            ? masterData?.company_id
            : isMaker
            ? employeeCompanyId
            : data.company_id;
        const company = companies.find((c) => c.id === parseInt(companyId));
        console.log("Computing companyName:", {
            companyId,
            company,
            companies,
        });
        return company?.name || "Select a Country";
    }, [
        isEdit,
        masterData,
        isMaker,
        employeeCompanyId,
        data.company_id,
        companies,
    ]);

    console.log("Rendering title:", {
        title: `Job Seeker ${isEdit ? "Edit" : "Create"} Form (${
            jobSeekerType.charAt(0).toUpperCase() + jobSeekerType.slice(1)
        }) - ${companyName}`,
    });

    return (
        <MainLayout
            auth={auth}
            title={`Job Seeker ${isEdit ? "Edit" : "Create"} Form (${
                jobSeekerType.charAt(0).toUpperCase() + jobSeekerType.slice(1)
            }) - ${companyName}`}
        >
            <div className="container mt-4 jobseeker-container">
                <div className="card">
                    <div className="card-body">
                        {isLoading ? (
                            <div
                                className="loading-spinner"
                                style={{ textAlign: "center", padding: "50px" }}
                            >
                                <div
                                    className="spinner-border text-primary"
                                    style={{ width: "3rem", height: "3rem" }}
                                    role="status"
                                >
                                    <span className="visually-hidden">
                                        Loading...
                                    </span>
                                </div>
                                <p className="mt-2">Loading data...</p>
                            </div>
                        ) : !Array.isArray(fields) || fields.length === 0 ? (
                            <p>No fields available. Please select a company.</p>
                        ) : (
                            <>
                                <h2 className="mb-5">{`Job Seeker ${
                                    isEdit ? "Edit" : "Create"
                                } Form (${
                                    jobSeekerType.charAt(0).toUpperCase() +
                                    jobSeekerType.slice(1)
                                }) - ${companyName}`}</h2>
                                <form
                                    onSubmit={handleSubmit}
                                    className="jobseeker-form"
                                >
                                    {Object.entries(groupedFields).length ===
                                    0 ? (
                                        <p>No valid fields to display.</p>
                                    ) : (
                                        Object.entries(groupedFields).map(
                                            ([section, sectionFields]) => (
                                                <div
                                                    key={section}
                                                    className="mb-4"
                                                >
                                                    <h5 className="card-title">
                                                        {section}
                                                    </h5>
                                                    <div className="row">
                                                        {sectionFields.map(
                                                            renderField
                                                        )}
                                                    </div>
                                                </div>
                                            )
                                        )
                                    )}
                                    <div className="row">
                                        <div className="col-md-12 text-end">
                                            <button
                                                type="submit"
                                                className="btn btn-primary"
                                            >
                                                {isEdit ? "Update" : "Create"}
                                            </button>
                                            <a
                                                href={route(
                                                    `${viewBase}.index`
                                                )}
                                                className="btn btn-secondary ms-2"
                                            >
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
