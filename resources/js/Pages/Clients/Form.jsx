import MasterForm from "../MasterForm";

export default function Form({ auth, client = null, companies }) {
    const fields = [
        {
            name: "client_code",
            label: "Client Code",
            type: "text",
            required: false,
        },
        {
            name: "client_name",
            label: "Client Name",
            type: "text",
            required: true,
        },
        {
            name: "company_id",
            label: "Company",
            type: "select",
            options: Object.entries(companies).map(([id, name]) => ({
                value: id,
                label: name,
            })),
            required: true,
        },
        {
            name: "client_status",
            label: "Client Status",
            type: "select",
            options: [
                { value: "0", label: "Temporary" },
                { value: "1", label: "Permanent" },
            ],
            required: true,
        },
        {
            name: "loaded_cost",
            label: "Loaded Cost (%)",
            type: "number",
            required: true,
            placeholder: "e.g., 15%",
        },
        {
            name: "qualify_days",
            label: "Qualify Days",
            type: "number",
            required: true,
            placeholder: "e.g., 30",
        },
        {
            name: "phone",
            label: "Phone",
            type: "text",
            required: false,
        },
        {
            name: "email",
            label: "Email",
            type: "email",
            required: false,
        },
        {
            name: "status",
            label: "Status",
            type: "select",
            options: [
                { value: "", label: "Select Status" },
                { value: "active", label: "Active" },
                { value: "inactive", label: "Inactive" },
            ],
            default: "active",
            required: false,
        },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Client Master"
            masterData={client}
            viewBase="/clients"
            fields={fields}
        />
    );
}
