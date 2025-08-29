import MasterIndex from "../MasterIndex";

export default function Index({ auth, consultants, filters }) {
    const columns = [
        { key: "code", label: "Consultant Code" },
        { key: "name", label: "Name" },
        { key: "address", label: "Address" },
        { key: "state", label: "State" },
        { key: "city", label: "City" },
        { key: "country", label: "Country" },
        { key: "phone1", label: "Phone 1" },
        { key: "phone2", label: "Phone 2" },
        { key: "email1", label: "Email 1" },
        { key: "email2", label: "Email 2" },
        {
            key: "status",
            label: "Status",
            render: (value) => (
                <span
                    className={`badge ${
                        value === "active"
                            ? "bg-green-500 text-white"
                            : "bg-red-500 text-white"
                    }`}
                >
                    {value}
                </span>
            ),
        },
        {
            key: "aadhaar",
            label: "Aadhaar",
            render: (value) =>
                value ? (
                    <a
                        href={`/storage/${value}`}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View Aadhaar
                    </a>
                ) : (
                    "N/A"
                ),
        },
        {
            key: "pan",
            label: "PAN",
            render: (value) =>
                value ? (
                    <a
                        href={`/storage/${value}`}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View PAN
                    </a>
                ) : (
                    "N/A"
                ),
        },
        {
            key: "po_copy",
            label: "PO Copy",
            render: (value) =>
                value ? (
                    <a
                        href={`/storage/${value}`}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View PO Copy
                    </a>
                ) : (
                    "N/A"
                ),
        },
        {
            key: "extra_doc",
            label: "Extra Doc",
            render: (value) =>
                value ? (
                    <a
                        href={`/storage/${value}`}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View Extra Doc
                    </a>
                ) : (
                    "N/A"
                ),
        },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Consultant Master"
            viewBase="/consultants"
            columns={columns}
            data={consultants}
            filters={filters}
            excelTemplateRoute="consultants.excel-template"
            excelImportRoute="/consultants/excel/import"
        />
    );
}
