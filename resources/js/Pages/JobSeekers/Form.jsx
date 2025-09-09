import { useEffect, useMemo, useState, useCallback } from "react";
import { useForm, router, usePage } from "@inertiajs/react";
import Select from "react-select";
import { toast } from "react-toastify";
import MainLayout from "@/Layouts/MainLayout";
import fieldConfig from "./fieldConfig.json";

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

const formatMonthForInput = (monthValue) => {
    return monthValue && monthValue.match(/^\d{4}-\d{2}$/) ? monthValue : "";
};

const generateYearOptions = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 5; year <= currentYear + 5; year++) {
        years.push({ value: year.toString(), label: year.toString() });
    }
    return years;
};

const generateProcessStatusOptions = () => {
    const options = [{ value: "", label: "Select Process Status" }];
    for (let i = 1; i <= 8; i++) {
        options.push({ value: i.toString(), label: i.toString() });
    }
    return options;
};

export default function Form({
    auth,
    masterName = "Job Seeker",
    jobSeeker = null,
    isAdmin = false,
    isMaker = false,
    isPOMaker = false,
    isFinanceMaker = false,
    isBackoutMaker = false,
    isTemporary = false,
    isEdit = false,
    employeeCompanyId,
    employeeRole,
    companies = [],
    branches = [],
    businessUnits = [],
    assistantManagers = [],
    deputyManagers = [],
    teamLeaders = [],
    recruiters = [],
    clients = [],
    statuses = [],
    isLoading = false,
    isReadOnlyReasonOfRejection = false,
}) {
    const { url } = usePage();
    const isValidRoute = url.startsWith(
        `/job-seekers/${isTemporary ? "temporary" : "permanent"}`
    );
    const jobSeekerType = isTemporary ? "temporary" : "permanent";
    const [selectedCompanyId, setSelectedCompanyId] = useState(
        isEdit
            ? jobSeeker?.company_id?.toString()
            : isMaker && employeeCompanyId
            ? employeeCompanyId.toString()
            : ""
    );
    const [country, setCountry] = useState(
        isEdit &&
            jobSeeker?.company?.region &&
            fieldConfig[jobSeeker.company.region]
            ? jobSeeker.company.region
            : isMaker && employeeCompanyId
            ? companies.find((c) => c.id === parseInt(employeeCompanyId))
                  ?.region &&
              fieldConfig[
                  companies.find((c) => c.id === parseInt(employeeCompanyId))
                      ?.region
              ]
                ? companies.find((c) => c.id === parseInt(employeeCompanyId))
                      ?.region
                : "India"
            : ""
    );
    const [fields, setFields] = useState([]);
    const [fetchedBranches, setFetchedBranches] = useState(branches);
    const [fetchedBusinessUnits, setFetchedBusinessUnits] =
        useState(businessUnits);
    const [fetchedClients, setFetchedClients] = useState(clients);
    const [fetchedAssistantManagers, setFetchedAssistantManagers] =
        useState(assistantManagers);
    const [fetchedDeputyManagers, setFetchedDeputyManagers] =
        useState(deputyManagers);
    const [fetchedTeamLeaders, setFetchedTeamLeaders] = useState(teamLeaders);
    const [fetchedRecruiters, setFetchedRecruiters] = useState(recruiters);
    const [fetchedStatuses, setFetchedStatuses] = useState(statuses);
    const [isFetching, setIsFetching] = useState(false);
    const [isInitialized, setIsInitialized] = useState(false);

    const companyField = {
        name: "company_id",
        label: "Company",
        type: "select",
        options: [
            { value: "", label: "Select Company" },
            ...formatOptions(companies, "id", "name"),
        ],
        required: true,
        readOnly: !isAdmin,
        section: "Basic Information",
        onChange: (value, setData) => {
            // console.log("companyField onChange:", { value });
            setData("company_id", value);
            setSelectedCompanyId(value);
            const selectedCompany = companies.find(
                (c) => c.id === parseInt(value)
            );
            const newCountry =
                selectedCompany?.region && fieldConfig[selectedCompany.region]
                    ? selectedCompany.region
                    : "India";
            setCountry(newCountry);
            if (!value) {
                setFields([companyField]);
                setFetchedBranches([]);
                setFetchedBusinessUnits([]);
                setFetchedClients([]);
                setFetchedAssistantManagers([]);
                setFetchedDeputyManagers([]);
                setFetchedTeamLeaders([]);
                setFetchedRecruiters([]);
            }
            if (newCountry === "APAC" && isTemporary && data.hire_type) {
                setData((prevData) => ({
                    ...prevData,
                    hire_type: "",
                    loaded_cost: "0.00",
                }));
                if (data.pay_rate || data.bill_rate) {
                    handleFinancialChange(null, setData, null);
                }
            }
        },
    };

    const fetchData = useCallback(
        async (companyId) => {
            if (!companyId || isNaN(parseInt(companyId))) {
                // console.log("fetchData skipped: Invalid companyId", { companyId });
                return;
            }
            // console.log("fetchData started:", { companyId, jobSeekerType });
            setIsFetching(true);
            try {
                const routePrefix = `job-seekers.${jobSeekerType}`;
                const [
                    branchesRes,
                    businessUnitsRes,
                    clientsRes,
                    employeesRes,
                ] = await Promise.all([
                    fetch(route(`${routePrefix}.branches`, { companyId }), {
                        headers: { Accept: "application/json" },
                    }),
                    fetch(
                        route(`${routePrefix}.business-units`, { companyId }),
                        {
                            headers: { Accept: "application/json" },
                        }
                    ),
                    fetch(route(`${routePrefix}.clients`, { companyId }), {
                        headers: { Accept: "application/json" },
                    }),
                    fetch(route(`${routePrefix}.employees`, { companyId }), {
                        headers: { Accept: "application/json" },
                    }),
                ]);

                if (
                    !branchesRes.ok ||
                    !businessUnitsRes.ok ||
                    !clientsRes.ok ||
                    !employeesRes.ok
                ) {
                    throw new Error("Failed to fetch data");
                }

                const branchesData = await branchesRes.json();
                const businessUnitsData = await businessUnitsRes.json();
                const clientsData = await clientsRes.json();
                const employeesData = await employeesRes.json();

                // console.log("fetchData completed:", {
                //     branchesData,
                //     businessUnitsData,
                //     clientsData,
                //     employeesData,
                // });

                setFetchedBranches(branchesData);
                setFetchedBusinessUnits(businessUnitsData);
                setFetchedClients(clientsData);
                setFetchedAssistantManagers(
                    employeesData.assistantManagers || []
                );
                setFetchedDeputyManagers(employeesData.deputyManagers || []);
                setFetchedTeamLeaders(employeesData.teamLeaders || []);
                setFetchedRecruiters(employeesData.recruiters || []);
                setFetchedStatuses(statuses);
            } catch (error) {
                // console.error("Error fetching data:", error);
                toast.error("Failed to load related data. Please try again.");
            } finally {
                setIsFetching(false);
            }
        },
        [jobSeekerType, statuses]
    );

    const updateFields = useCallback(() => {
        // console.log("updateFields called:", { selectedCompanyId, jobSeekerType, isAdmin, isMaker });
        if (!selectedCompanyId) {
            // console.log("updateFields: No company selected, returning companyField");
            return isAdmin && !isEdit ? [companyField] : [];
        }

        const selectedCountry =
            companies.find((c) => c.id === parseInt(selectedCompanyId))
                ?.region || "India";
        let applicableFields =
            fieldConfig[selectedCountry]?.[jobSeekerType] ||
            fieldConfig.India.temporary;

        if (!fieldConfig[selectedCountry]?.[jobSeekerType]) {
            toast.warn(
                `Configuration for region "${selectedCountry}" not found. Using default India configuration.`
            );
        }

        if (isAdmin) {
            applicableFields = [
                ...applicableFields,
                {
                    name: "form_status",
                    label: "Form Status",
                    type: "select",
                    options: [
                        { value: "", label: "Select Form Status" },
                        { value: "Pending", label: "Pending" },
                        { value: "Approved", label: "Approved" },
                        { value: "Rejected", label: "Rejected" },
                    ],
                    required: true,
                    section: "Admin Controls",
                },
                {
                    name: "process_status",
                    label: "Process Status",
                    type: "select",
                    options: generateProcessStatusOptions(),
                    required: false,
                    section: "Admin Controls",
                },
            ];
        }

        const newFields = applicableFields.map((field) => ({
            ...field,
            options:
                field.type === "select"
                    ? getSelectOptions(field.name)
                    : field.options,
            onChange:
                field.name === "company_id" && isAdmin
                    ? companyField.onChange
                    : field.onChange === "handleClientChange"
                    ? handleClientChange
                    : field.onChange === "handleFinancialChange"
                    ? handleFinancialChange
                    : field.onChange === "handleHireTypeChange"
                    ? handleHireTypeChange
                    : field.onChange,
        }));

        // console.log("updateFields result:", newFields.map((f) => f.name));
        return newFields;
    }, [selectedCompanyId, jobSeekerType, isAdmin, isEdit, companies]);

    const { data, setData, post, put, errors, reset } = useForm(() => {
        const initialData = isEdit ? jobSeeker : {};
        const formData = {};
        const dateFields = [
            "selection_date",
            "offer_date",
            "join_date",
            "qly_date",
            "backout_term_date",
            "po_end_date",
        ];
        const monthFields = [
            "join_month",
            "po_end_month",
            "backout_term_month",
        ];
        const yearFields = ["join_year", "po_end_year", "backout_term_year"];
        Object.keys(initialData).forEach((key) => {
            if (dateFields.includes(key)) {
                formData[key] = formatDateForInput(initialData[key]);
            } else if (monthFields.includes(key)) {
                formData[key] = formatMonthForInput(initialData[key]);
            } else if (yearFields.includes(key)) {
                formData[key] =
                    initialData[key] != null ? initialData[key].toString() : "";
            } else {
                formData[key] =
                    initialData[key] != null ? initialData[key] : "";
            }
        });
        fields.forEach((field) => {
            if (!(field.name in formData)) {
                formData[field.name] = field.default || "";
            }
        });
        if (isMaker && employeeCompanyId && !isEdit) {
            formData.company_id = employeeCompanyId.toString();
        } else if (isEdit && jobSeeker?.company_id) {
            formData.company_id = jobSeeker.company_id.toString();
        }
        if (!formData.pay_rate) formData.pay_rate = "";
        if (!formData.billing_value) formData.billing_value = "";
        if (!formData.loaded_cost) formData.loaded_cost = "0.00";
        if (!formData.loaded_gp) formData.loaded_gp = "0.00";
        if (!formData.final_billing_value)
            formData.final_billing_value = "0.00";
        if (!formData.actual_billing_value) formData.actual_billing_value = "";
        if (!formData.invoice_no) formData.invoice_no = "";
        if (isAdmin && !isEdit) {
            formData.form_status = "Pending";
            formData.process_status = "";
        }
        return formData;
    });

    useEffect(() => {
        let isMounted = true;
        // console.log("Initial useEffect:", { isEdit, isAdmin, isMaker, selectedCompanyId, isValidRoute });
        if (!isValidRoute) {
            // console.log("Resetting state due to invalid route:", url);
            if (isMounted) {
                setSelectedCompanyId("");
                setFields(isAdmin && !isEdit ? [companyField] : []);
                setCountry("");
                setFetchedBranches([]);
                setFetchedBusinessUnits([]);
                setFetchedClients([]);
                setFetchedAssistantManagers([]);
                setFetchedDeputyManagers([]);
                setFetchedTeamLeaders([]);
                setFetchedRecruiters([]);
                setIsInitialized(false);
            }
            return;
        }

        if (!isInitialized) {
            if (isEdit || isMaker) {
                const initialCompanyId = isEdit
                    ? jobSeeker?.company_id?.toString()
                    : isMaker && employeeCompanyId
                    ? employeeCompanyId.toString()
                    : "";
                if (initialCompanyId && isMounted) {
                    // console.log("Initial setup for edit or maker:", { initialCompanyId });
                    const selectedCompany = companies.find(
                        (c) => c.id === parseInt(initialCompanyId)
                    );
                    const selectedCountry = isEdit
                        ? jobSeeker?.company?.region &&
                          fieldConfig[jobSeeker.company.region]
                            ? jobSeeker.company.region
                            : "India"
                        : selectedCompany?.region &&
                          fieldConfig[selectedCompany.region]
                        ? selectedCompany.region
                        : "India";
                    setCountry(selectedCountry);
                    setSelectedCompanyId(initialCompanyId);
                    setFields(updateFields());
                    fetchData(initialCompanyId);
                    setIsInitialized(true);
                }
            } else if (isAdmin && !isEdit && isMounted) {
                // console.log("Admin create mode: Setting companyField only");
                setFields([companyField]);
                setCountry("");
                setSelectedCompanyId("");
                setIsInitialized(true);
            }
        }

        return () => {
            isMounted = false;
        };
    }, [
        isEdit,
        isAdmin,
        isMaker,
        employeeCompanyId,
        jobSeeker,
        isValidRoute,
        url,
        fetchData,
        updateFields,
        isInitialized,
    ]);

    useEffect(() => {
        let isMounted = true;
        // console.log("Company selection useEffect:", { selectedCompanyId, isAdmin, isEdit, isValidRoute });
        if (selectedCompanyId && isValidRoute && isMounted) {
            // console.log("Fetching data and updating fields for:", selectedCompanyId);
            fetchData(selectedCompanyId).then(() => {
                if (isMounted) {
                    // console.log("Fetch completed, updating fields");
                    setFields(updateFields());
                }
            });
        }
        return () => {
            isMounted = false;
        };
    }, [selectedCompanyId, isValidRoute, fetchData, updateFields]);

    useEffect(() => {
        if (isMaker && employeeCompanyId && !isEdit) {
            setData("company_id", employeeCompanyId.toString());
        }
    }, [isMaker, employeeCompanyId, isEdit, setData]);

    const clientName = useMemo(() => {
        const client = fetchedClients.find(
            (c) => c.id === parseInt(data.client_id)
        );
        return client ? client.client_name : "";
    }, [data.client_id, fetchedClients]);

    const statusName = useMemo(() => {
        const status = fetchedStatuses.find(
            (s) => s.id === parseInt(data.status_id)
        );
        return status ? status.status : "";
    }, [data.status_id, fetchedStatuses]);

    const recruiterName = useMemo(() => {
        const recruiter = fetchedRecruiters.find(
            (r) => r.id === parseInt(data.recruiter_id)
        );
        return recruiter ? recruiter.name : "";
    }, [data.recruiter_id, fetchedRecruiters]);

    const joinedStatusId = useMemo(() => {
        const joinedStatus = fetchedStatuses.find((s) => s.status === "Joined");
        return joinedStatus ? joinedStatus.id.toString() : null;
    }, [fetchedStatuses]);

    const handleHireTypeChange = (value, setData) => {
        // console.log("handleHireTypeChange:", { value, country, isTemporary });
        if (
            isTemporary &&
            country === "APAC" &&
            value &&
            ["ABN", "PAGT"].includes(value)
        ) {
            const payRate = parseFloat(data.pay_rate) || 0;
            const loadedCostPercentage = value === "ABN" ? 5 : 8;
            const loadedCost = (payRate * loadedCostPercentage) / 100;
            setData((prevData) => ({
                ...prevData,
                hire_type: value,
                loaded_cost: isNaN(loadedCost) ? "0.00" : loadedCost.toFixed(2),
            }));
            if (data.pay_rate || data.bill_rate) {
                handleFinancialChange(null, setData, null);
            }
        } else {
            setData((prevData) => ({
                ...prevData,
                hire_type: value,
                loaded_cost: "0.00",
            }));
        }
    };

    const handleClientChange = (value, setData) => {
        const clientId = parseInt(value);
        const client = fetchedClients.find((c) => c.id === clientId);
        // console.log("handleClientChange:", {
        //     client_id: clientId,
        //     client: client
        //         ? {
        //               id: client.id,
        //               client_name: client.client_name,
        //               loaded_cost: client.loaded_cost,
        //               qualify_days: client.qualify_days,
        //           }
        //         : null,
        //     pay_rate: data.pay_rate,
        //     billing_value: data.billing_value,
        //     join_date: data.join_date,
        // });
        if (client) {
            const updates = {};
            if (isTemporary && data.pay_rate) {
                const payRate = parseFloat(data.pay_rate) || 0;
                let loadedCostPercentage = parseFloat(client.loaded_cost) || 0;
                if (
                    country === "APAC" &&
                    data.hire_type &&
                    ["ABN", "PAGT"].includes(data.hire_type)
                ) {
                    loadedCostPercentage = data.hire_type === "ABN" ? 5 : 8;
                }
                const loadedCost = (payRate * loadedCostPercentage) / 100;
                updates.loaded_cost = isNaN(loadedCost)
                    ? "0.00"
                    : loadedCost.toFixed(2);
            } else if (!isTemporary && data.billing_value) {
                const billingValue = parseFloat(data.billing_value) || 0;
                const loadedCostPercentage =
                    parseFloat(client.loaded_cost) || 0;
                const loadedGp = (billingValue * loadedCostPercentage) / 100;
                updates.loaded_gp = isNaN(loadedGp)
                    ? "0.00"
                    : loadedGp.toFixed(2);
                updates.final_billing_value = isNaN(billingValue - loadedGp)
                    ? "0.00"
                    : (billingValue - loadedGp).toFixed(2);
            }
            if (data.join_date) {
                const joinDate = new Date(data.join_date);
                if (!isNaN(joinDate.getTime())) {
                    const qlyDate = new Date(joinDate);
                    qlyDate.setDate(
                        joinDate.getDate() + parseInt(client.qualify_days || 0)
                    );
                    updates.qly_date = qlyDate.toISOString().split("T")[0];
                }
            }
            // console.log("Calculated:", {
            //     pay_rate: isTemporary ? parseFloat(data.pay_rate) || 0 : null,
            //     billing_value: !isTemporary ? parseFloat(data.billing_value) || 0 : null,
            //     loaded_cost_percentage: loadedCostPercentage,
            //     loaded_cost: isTemporary ? updates.loaded_cost : null,
            //     loaded_gp: !isTemporary ? updates.loaded_gp : null,
            //     final_billing_value: !isTemporary ? updates.final_billing_value : null,
            //     qly_date: updates.qly_date || "N/A",
            // });
            setData((prevData) => ({
                ...prevData,
                client_id: value,
                ...updates,
            }));
        } else {
            // console.warn("handleClientChange: Missing data", {
            //     client: !!client,
            //     pay_rate: !!data.pay_rate,
            //     billing_value: !!data.billing_value,
            //     join_date: !!data.join_date,
            // });
            setData((prevData) => ({
                ...prevData,
                client_id: value,
                ...(isTemporary
                    ? { loaded_cost: "0.00" }
                    : { loaded_gp: "0.00", final_billing_value: "0.00" }),
                qly_date: "",
            }));
        }
    };

    const handleFinancialChange = (value, setData, fieldName) => {
        // console.log("handleFinancialChange:", { fieldName, value, isTemporary, companyKey: country });
        if (isTemporary) {
            const payRate = parseFloat(data.pay_rate) || 0;
            const billRate = parseFloat(data.bill_rate) || 0;
            const otc = parseFloat(data.otc) || 0;
            const mspFees = parseFloat(data.msp_fees) || 0;
            const actualLoadedCost = parseFloat(data.loaded_cost) || 0;
            const payRate1 = payRate + actualLoadedCost;
            const gpMonth = billRate - payRate;
            const otcSplit = otc / 6;
            const finalGp = gpMonth - actualLoadedCost - otcSplit - mspFees;
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

            // console.log("Temporary calculations:", {
            //     pay_rate: payRate,
            //     bill_rate: billRate,
            //     otc,
            //     msp_fees: mspFees,
            //     actual_loaded_cost: actualLoadedCost,
            //     pay_rate_1: payRate1,
            //     gp_month: gpMonth,
            //     otc_split: otcSplit,
            //     final_gp: finalGp,
            //     percentage_gp: percentageGp,
            //     gp_hour: gpHour,
            //     gp_hour_usd: gpHourUsd,
            // });

            setData((prevData) => ({
                ...prevData,
                pay_rate_1: isNaN(payRate1) ? "0.00" : payRate1.toFixed(2),
                gp_month: isNaN(gpMonth) ? "0.00" : gpMonth.toFixed(2),
                otc_split: isNaN(otcSplit) ? "0.00" : otcSplit.toFixed(2),
                final_gp: isNaN(finalGp) ? "0.00" : finalGp.toFixed(2),
                percentage_gp: isNaN(percentageGp)
                    ? "0.00"
                    : percentageGp.toFixed(2),
                gp_hour: isNaN(gpHour) ? "0.00" : gpHour.toFixed(2),
                gp_hour_usd: isNaN(gpHourUsd) ? "0.00" : gpHourUsd.toFixed(2),
            }));
        }
    };

    useEffect(() => {
        if (
            data.client_id &&
            (isTemporary ? data.pay_rate : data.billing_value)
        ) {
            handleClientChange(data.client_id, setData);
        }
    }, [
        data.client_id,
        data.pay_rate,
        data.billing_value,
        fetchedClients,
        setData,
        isTemporary,
    ]);

    useEffect(() => {
        if (
            isTemporary &&
            ["pay_rate", "bill_rate", "otc", "msp_fees"].some(
                (key) => data[key]
            )
        ) {
            handleFinancialChange(null, setData, null);
        }
    }, [
        data.pay_rate,
        data.bill_rate,
        data.otc,
        data.msp_fees,
        isTemporary,
        country,
        setData,
    ]);

    useEffect(() => {
        const value = isTemporary
            ? data.final_gp || "0"
            : data.final_billing_value || "0";
        const newRemark2 = `${clientName || "N/A"} : ${statusName || "N/A"} : ${
            data.consultant_name || "N/A"
        } ${value} / (${recruiterName || "N/A"})`;
        if (data.remark2 !== newRemark2) {
            setData("remark2", newRemark2);
        }
    }, [
        clientName,
        statusName,
        data.consultant_name,
        data.final_gp,
        data.final_billing_value,
        recruiterName,
        setData,
        isTemporary,
    ]);

    useEffect(() => {
        if (data.client_id && data.join_date) {
            const client = fetchedClients.find(
                (c) => c.id === parseInt(data.client_id)
            );
            if (client && client.qualify_days) {
                const joinDate = new Date(data.join_date);
                if (!isNaN(joinDate.getTime())) {
                    const qlyDate = new Date(joinDate);
                    qlyDate.setDate(
                        joinDate.getDate() + parseInt(client.qualify_days || 0)
                    );
                    const newQlyDate = qlyDate.toISOString().split("T")[0];
                    // console.log("Updating qly_date:", {
                    //     client_id: data.client_id,
                    //     join_date: data.join_date,
                    //     qualify_days: client.qualify_days,
                    //     qly_date: newQlyDate,
                    // });
                    setData("qly_date", newQlyDate);
                } else {
                    // console.warn("Invalid join_date:", data.join_date);
                    setData("qly_date", "");
                }
            } else {
                // console.warn("No client or qualify_days found for qly_date calculation:", {
                //     client_id: data.client_id,
                //     client: client,
                // });
                setData("qly_date", "");
            }
        } else {
            // console.log("Skipping qly_date update: Missing client_id or join_date", {
            //     client_id: data.client_id,
            //     join_date: data.join_date,
            // });
            setData("qly_date", "");
        }
    }, [data.join_date, data.client_id, fetchedClients, setData]);

    const isFieldReadOnly = (field) => {
        const poFields = ["po_end_date", "po_end_month", "po_end_year"];
        const backoutFields = [
            "backout_term_date",
            "backout_term_month",
            "backout_term_year",
            "type_of_attrition",
            "reason_of_attrition",
            "bo_type",
        ];
        const financeFields = ["actual_billing_value", "invoice_no"];
        const tempFinancialFields = ["loaded_cost", "percentage_gp"];
        const permFinancialFields = ["loaded_gp", "final_billing_value"];
        const adminFields = ["form_status", "process_status"];

        // console.log("isFieldReadOnly:", {
        //     field: field.name,
        //     isReadOnly: null,
        //     isMaker,
        //     isPOMaker,
        //     isBackoutMaker,
        //     isFinanceMaker,
        //     isTemporary,
        //     isEdit,
        //     isAdmin,
        // });

        if (isAdmin) {
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly: false });
            return false;
        }

        if (adminFields.includes(field.name)) {
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly: true });
            return true;
        }

        if (isEdit && isPOMaker) {
            const readOnly = !poFields.includes(field.name);
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly });
            return readOnly;
        }

        if (isEdit && isBackoutMaker) {
            const readOnly = !backoutFields.includes(field.name);
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly });
            return readOnly;
        }

        if (isEdit && isFinanceMaker && !isTemporary) {
            const readOnly = !financeFields.includes(field.name);
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly });
            return readOnly;
        }

        if (isMaker) {
            if (isTemporary) {
                if (
                    poFields.includes(field.name) ||
                    tempFinancialFields.includes(field.name)
                ) {
                    // console.log("isFieldReadOnly result:", { field: field.name, readOnly: true });
                    return true;
                }
            } else {
                if (
                    poFields.includes(field.name) ||
                    permFinancialFields.includes(field.name)
                ) {
                    // console.log("isFieldReadOnly result:", { field: field.name, readOnly: true });
                    return true;
                }
            }
            if (field.name === "company_id") {
                // console.log("isFieldReadOnly result:", { field: field.name, readOnly: true });
                return true;
            }
        }

        if (
            isEdit &&
            field.name === "reason_of_rejection" &&
            isReadOnlyReasonOfRejection
        ) {
            // console.log("isFieldReadOnly result:", { field: field.name, readOnly: true });
            return true;
        }

        const readOnly =
            field.readOnly === "!isAdmin" ? !isAdmin : !!field.readOnly;
        // console.log("isFieldReadOnly result:", { field: field.name, readOnly });
        return readOnly;
    };

    const shouldShowReasonOfRejection = () => {
        return (
            isEdit &&
            jobSeeker?.form_status === "Rejected" &&
            data.reason_of_rejection
        );
    };

    const getSelectOptions = (fieldName) => {
        const placeholderOption = {
            value: "",
            label: `Select ${
                fieldConfig[country]?.[jobSeekerType]?.find(
                    (f) => f.name === fieldName
                )?.label || fieldName
            }`,
        };
        switch (fieldName) {
            case "company_id":
                return isAdmin
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
                    ...formatOptions(fetchedBranches, "id", "name"),
                ];
            case "business_unit_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedBusinessUnits, "id", "unit"),
                ];
            case "am_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedAssistantManagers, "id", "name"),
                ];
            case "dm_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedDeputyManagers, "id", "name"),
                ];
            case "tl_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedTeamLeaders, "id", "name"),
                ];
            case "recruiter_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedRecruiters, "id", "name"),
                ];
            case "client_id":
                return [
                    placeholderOption,
                    ...formatOptions(fetchedClients, "id", "client_name"),
                ];
            case "status_id":
                // console.log("Status options:", { fieldName, statuses: fetchedStatuses, country, jobSeekerType });
                return [
                    placeholderOption,
                    ...formatOptions(fetchedStatuses, "id", "status"),
                ];
            case "form_status":
                return [
                    { value: "", label: "Select Form Status" },
                    { value: "Pending", label: "Pending" },
                    { value: "Approved", label: "Approved" },
                    { value: "Rejected", label: "Rejected" },
                ];
            case "process_status":
                return generateProcessStatusOptions();
            case "type_of_attrition":
                return [
                    placeholderOption,
                    { value: "Voluntary", label: "Voluntary" },
                    { value: "Involuntary", label: "Involuntary" },
                ];
            case "bo_type":
                // console.log("Rendering options for bo_type:", { fieldName, country, jobSeekerType });
                return [
                    placeholderOption,
                    { value: "Client BO", label: "Client BO" },
                    { value: "Candidate BO", label: "Candidate BO" },
                ];
            case "hire_type":
                if (isTemporary && country === "APAC") {
                    return [
                        placeholderOption,
                        { value: "ABN", label: "ABN (5%)" },
                        { value: "PAGT", label: "PAGT (8%)" },
                    ];
                }
                return [placeholderOption];
            case "po_end_year":
            case "backout_term_year":
            case "join_year":
                return [placeholderOption, ...generateYearOptions()];
            default:
                return (
                    fieldConfig[country]?.[jobSeekerType]?.find(
                        (f) => f.name === fieldName
                    )?.options || [placeholderOption]
                );
        }
    };

    const renderField = (field) => {
        if (
            field.name === "reason_of_rejection" &&
            !shouldShowReasonOfRejection()
        ) {
            return null;
        }

        const isReadOnly = isFieldReadOnly(field);
        const value =
            field.type === "select"
                ? data[field.name] != null
                    ? data[field.name].toString()
                    : ""
                : field.type === "date"
                ? formatDateForInput(data[field.name])
                : field.type === "month"
                ? formatMonthForInput(data[field.name])
                : data[field.name] || "";
        const error = errors[field.name];

        const handleChange = (e) => {
            let newValue = field.type === "select" ? e.value : e.target.value;
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
                "po_end_year",
                "backout_term_year",
                "join_year",
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
                "process_status",
            ];
            if (numericFields.includes(field.name)) {
                newValue =
                    newValue && !isNaN(newValue) ? parseFloat(newValue) : "";
            } else if (integerFields.includes(field.name)) {
                newValue =
                    newValue && !isNaN(newValue) ? parseInt(newValue) : "";
            }
            // console.log("Field change:", { field: field.name, newValue });
            setData(field.name, newValue);
            if (field.name === "client_id") {
                handleClientChange(newValue, setData);
            }
            if (
                [
                    "pay_rate",
                    "bill_rate",
                    "otc",
                    "msp_fees",
                    "billing_value",
                ].includes(field.name)
            ) {
                handleFinancialChange(newValue, setData, field.name);
            }
            if (field.name === "hire_type") {
                handleHireTypeChange(newValue, setData);
            }
            if (field.onChange) {
                field.onChange(newValue, setData);
            }
        };

        switch (field.type) {
            case "select":
                const options = getSelectOptions(field.name);
                const selectedOption = value
                    ? options.find((option) => option.value === value) || {
                          value: "",
                          label: `Select ${field.label}`,
                      }
                    : { value: "", label: `Select ${field.label}` };
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
            case "month":
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

    const groupedFields = fields.reduce((acc, field) => {
        if (!field || !field.section) {
            console.warn("Invalid field:", field);
            return acc;
        }
        if (!acc[field.section]) acc[field.section] = [];
        acc[field.section].push(field);
        return acc;
    }, {});

    const companyName = useMemo(() => {
        const companyId = isEdit
            ? jobSeeker?.company_id
            : isMaker
            ? employeeCompanyId
            : selectedCompanyId;
        const company = companies.find((c) => c.id === parseInt(companyId));
        return (
            company?.name ||
            (isAdmin && !isEdit && !selectedCompanyId
                ? "Select a Company"
                : "Unknown")
        );
    }, [
        isEdit,
        jobSeeker,
        isMaker,
        employeeCompanyId,
        selectedCompanyId,
        companies,
    ]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isAdmin && !isEdit && !selectedCompanyId) {
            toast.error("Please select a company before submitting.");
            return;
        }

        if (
            data.status_id &&
            data.join_date &&
            joinedStatusId &&
            data.status_id.toString() === joinedStatusId
        ) {
            const joinDate = new Date(data.join_date);
            joinDate.setHours(0, 0, 0, 0);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (joinDate > today) {
                // console.log("Join date validation failed:", {
                //     status_id: data.status_id,
                //     join_date: data.join_date,
                //     today: today.toISOString().split("T")[0],
                //     normalized_join_date: joinDate.toISOString().split("T")[0],
                // });
                toast.error(
                    "Selected status 'Joined' but you are adding a future date in join date."
                );
                return;
            }
        }

        if (data.type_of_attrition && !data.bo_type) {
            toast.error(
                "Please select a BO Type when Type of Attrition is set."
            );
            return;
        }

        const method = isEdit ? put : post;
        const routeName = isEdit
            ? `job-seekers.${jobSeekerType}.update`
            : `job-seekers.${jobSeekerType}.store`;

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
            "po_end_year",
            "backout_term_year",
            "join_year",
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
            "process_status",
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

        if (
            isTemporary &&
            country === "APAC" &&
            data.hire_type &&
            ["ABN", "PAGT"].includes(data.hire_type)
        ) {
            submitData.loaded_cost = data.hire_type === "ABN" ? "5.00" : "8.00";
        }

        // console.log("Submitting data:", submitData);

        method(route(routeName, isEdit ? jobSeeker.id : {}), {
            data: submitData,
            onSuccess: () => {
                // toast.success(`${masterName} ${isEdit ? "updated" : "created"} successfully!`);
                reset();
                // router.visit(route(`job-seekers.${jobSeekerType}.index`), { preserveState: false });
            },
            onError: (errors) => {
                // console.error("Validation errors:", errors);
                toast.error(
                    `Failed to ${
                        isEdit ? "update" : "create"
                    } ${masterName}. Please check the form errors.`
                );
            },
        });
    };

    return (
        <MainLayout
            auth={auth}
            title={`Job Seeker ${isEdit ? "Edit" : "Create"} Form (${
                jobSeekerType.charAt(0).toUpperCase() + jobSeekerType.slice(1)
            }) - ${companyName}`}
        >
            <div className="container-fluid dashboard-width">
                <div className="card">
                    <div className="card-body">
                        {isLoading || isFetching ? (
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
                        ) : fields.length === 0 ? (
                            <p>
                                No fields available.{" "}
                                {isAdmin && !isEdit
                                    ? "Please select a company."
                                    : "Please check field configuration."}
                            </p>
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
                                    {Object.entries(groupedFields).map(
                                        ([section, sectionFields]) => (
                                            <div key={section} className="mb-4">
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
                                                    `job-seekers.${jobSeekerType}.index`
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
