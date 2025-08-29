import MasterIndex from "../MasterIndex";

export default function Index({ auth, companies, filters }) {
    const columns = [
        { key: "name", label: "Name" },
        { key: "region", label: "Region" },
        {
            key: "to_emails",
            label: "To Emails",
            render: (item) => (item.to_emails || []).join(", "),
        },
        {
            key: "cc_emails",
            label: "CC Emails",
            render: (item) => (item.cc_emails || []).join(", "),
        },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Company Master"
            viewBase="/company-masters"
            columns={columns}
            data={companies}
            filters={filters}
            excelTemplateRoute="company-masters.excel-template"
            excelImportRoute="/company-masters/excel/import"
        />
    );
}
