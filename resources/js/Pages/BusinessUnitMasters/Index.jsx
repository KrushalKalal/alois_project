import MasterIndex from "../MasterIndex";

export default function Index({ auth, units, filters }) {
    const transformedData = {
        ...units,
        data: units.data.map((unit) => ({
            ...unit,
            company_name: unit.company?.name || "N/A",
        })),
    };
    const columns = [
        { key: "unit", label: "Unit" },
        { key: "company_name", label: "Company" },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Business Unit Master"
            viewBase="/business-unit-masters"
            columns={columns}
            data={transformedData}
            filters={filters}
            excelTemplateRoute="business-unit-masters.excel-template"
            excelImportRoute="/business-unit-masters/excel/import"
            hasTabs={true}
        />
    );
}
