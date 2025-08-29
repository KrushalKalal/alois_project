import MasterIndex from "../MasterIndex";

export default function Index({ auth, employees, filters, companies }) {
    const transformedData = {
        ...employees,
        data: employees.data.map((employee) => ({
            id: employee.id,
            emp_id: employee.emp_id,
            name: employee.name,
            company_name: employee.company?.name || "N/A",
            checker_name:
                employee.checker?.name ||
                (employee.checker_id === employee.id ? employee.name : "-"),
            role: employee.role || "-",
            email: employee.email || "-",
            phone: employee.phone || "-",
            designation: employee.designation || "-",
            status: employee.status || "N/A",
        })),
    };

    const columns = [
        { key: "emp_id", label: "Emp ID" },
        { key: "name", label: "Name" },
        { key: "company_name", label: "Company" },
        { key: "email", label: "Email" },
        { key: "phone", label: "Phone" },
        { key: "role", label: "Role" },
        { key: "checker_name", label: "Checker" },
        { key: "designation", label: "Designation" },
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
            masterName="Employee Master"
            viewBase="/employees"
            columns={columns}
            data={transformedData}
            filters={filters}
            excelTemplateRoute="employees.excel-template"
            excelImportRoute="/employees/excel/import"
        />
    );
}
