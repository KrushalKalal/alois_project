import MasterIndex from "../MasterIndex";

export default function Index({ auth, clients, filters }) {
    const transformedData = {
        ...clients,
        data: clients.data.map((client) => ({
            id: client.id,
            client_code: client.client_code || "-",
            client_name: client.client_name || "-",
            company_name: client.company?.name || "N/A",
            loaded_cost: client.loaded_cost || 0,
            qualify_days: client.qualify_days || 0,
            phone: client.phone || "-",
            email: client.email || "-",
            status: client.status || "N/A",
        })),
    };

    // console.log("Transformed Data:", transformedData);

    const columns = [
        { key: "client_code", label: "Client Code" },
        { key: "client_name", label: "Client Name" },
        { key: "company_name", label: "Company" },
        { key: "loaded_cost", label: "Loaded Cost (%)" },
        { key: "qualify_days", label: "Qualify Days" },
        { key: "phone", label: "Phone" },
        { key: "email", label: "Email" },
        {
            key: "status",
            label: "Status",
            render: (item) => (
               <span
                    className={`badge ${
                        item.status === "active"
                            ? "bg-success text-white"
                            : "bg-danger text-white"
                    }`}
                >
                    {item.status}
                </span>
            ),
        },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Client Master"
            viewBase="/clients"
            columns={columns}
            data={transformedData}
            filters={filters}
            excelTemplateRoute="clients.excel-template"
            excelImportRoute="/clients/excel/import"
            hasTabs={true}
        />
    );
}
