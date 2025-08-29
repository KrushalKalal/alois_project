import MasterForm from "../MasterForm";

export default function Form({ auth, unit = null, companies = [] }) {
    const companyOptions = companies.map((company) => ({
        value: company.id,
        label: company.name,
    }));
    const fields = [
        { name: "unit", label: "Business Unit", type: "text", required: true },
        {
            name: "company_id",
            label: "Company",
            type: "select",
            options: companyOptions,
            required: true,
        },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Business Unit Master"
            masterData={unit}
            viewBase="/business-unit-masters"
            fields={fields}
            hasCompanyDropdown={true}
        />
    );
}
