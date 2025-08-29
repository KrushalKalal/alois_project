import MasterIndex from "../MasterIndex";

export default function Index({ auth, statuses, filters }) {
    const columns = [{ key: "status", label: "Status" }];

    return (
        <MasterIndex
            auth={auth}
            masterName="Status Master"
            viewBase="/status-masters"
            columns={columns}
            data={statuses}
            filters={filters}
            excelTemplateRoute="status-masters.excel-template"
            excelImportRoute="/status-masters/excel/import"
        />
    );
}
