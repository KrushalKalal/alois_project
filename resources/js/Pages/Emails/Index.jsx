import MasterIndex from "../MasterIndex";
import { router } from "@inertiajs/react";

export default function Index({ auth, mainEmails, filters }) {
    const mainEmailColumns = [
        { key: "email", label: "Email" },
        { key: "name", label: "Name" },
        {
            key: "is_active",
            label: "Status",
            render: (item) =>
                item.is_active ? (
                    <span className="badge bg-success">Active</span>
                ) : (
                    <span className="badge bg-secondary">Inactive</span>
                ),
        },
    ];

    return (
        <MasterIndex
            auth={auth}
            masterName="Main Email"
            viewBase="/emails"
            columns={mainEmailColumns}
            data={{ data: mainEmails }}
            filters={filters}
            excelTemplateRoute=""
            excelImportRoute=""
        />
    );
}
