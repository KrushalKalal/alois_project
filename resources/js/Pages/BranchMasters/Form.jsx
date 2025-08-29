import MasterForm from "../MasterForm";

export default function Form({ auth, branch = null, companies = [] }) {
    const companyOptions = companies.map((company) => ({
        value: company.id,
        label: company.name,
    }));
    const fields = [
        { name: "name", label: "Branch Name", type: "text", required: true },
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
            masterName="Branch Master"
            masterData={branch}
            viewBase="/branch-masters"
            fields={fields}
            hasCompanyDropdown={true}
        />
    );
}
