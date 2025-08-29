import MasterIndex from "../MasterIndex";

export default function Index({ auth, branches, filters }) {
    const transformedData = {
        ...branches,
        data: branches.data.map((branch) => ({
            ...branch,
            company_name: branch.company?.name || "N/A",
        })),
    };
    const columns = [
        { key: "name", label: "Branch Name" },
        { key: "company_name", label: "Company" },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Branch Master"
            viewBase="/branch-masters"
            columns={columns}
            data={transformedData}
            filters={filters}
            excelTemplateRoute="branch-masters.excel-template"
            excelImportRoute="/branch-masters/excel/import"
            hasTabs={true}
        />
    );
}
