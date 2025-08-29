import MasterForm from "../MasterForm";

export default function Form({ auth, company = null }) {
    const fields = [
        { name: "name", label: "Company Name", type: "text", required: true },
        {
            name: "region",
            label: "Region",
            type: "select",
            required: false,
            options: [
                { value: "APAC", label: "APAC" },
                { value: "India", label: "India" },
                { value: "Aegis", label: "Aegis" },
                { value: "EU-UK", label: "EU-UK" },
            ],
        },
        {
            name: "to_emails",
            label: "To Emails",
            type: "text",
            required: false,
            placeholder:
                "Enter emails separated by commas (e.g., email1@example.com, email2@example.com)",
            onChange: (value, setData) => {
                // Update with raw input value to preserve commas
                setData("to_emails", value);
            },
            onBlur: (value, setData) => {
                // Process and validate emails only on blur
                const emails = value
                    .split(",")
                    .map((email) => email.trim())
                    .filter((email) =>
                        email ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) : false
                    );
                setData("to_emails", emails.length ? emails : []);
            },
            valueTransformer: (value) => value || "", // Preserve raw input
        },
        {
            name: "cc_emails",
            label: "CC Emails",
            type: "text",
            required: false,
            placeholder:
                "Enter emails separated by commas (e.g., email1@example.com, email2@example.com)",
            onChange: (value, setData) => {
                // Update with raw input value to preserve commas
                setData("cc_emails", value);
            },
            onBlur: (value, setData) => {
                // Process and validate emails only on blur
                const emails = value
                    .split(",")
                    .map((email) => email.trim())
                    .filter((email) =>
                        email ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) : false
                    );
                setData("cc_emails", emails.length ? emails : []);
            },
            valueTransformer: (value) => value || "", // Preserve raw input
        },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Company Master"
            masterData={company}
            viewBase="/company-masters"
            fields={fields}
        />
    );
}
