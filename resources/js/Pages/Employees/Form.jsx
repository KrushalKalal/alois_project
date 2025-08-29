import { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import MasterForm from "../MasterForm";
import Select from "react-select";

export default function Form({ auth, employee = null, companies, checkers }) {
    // Initialize form data
    const { data, setData, errors } = useForm({
        emp_id: employee?.emp_id || "",
        name: employee?.name || "",
        company_id: employee?.company_id?.toString() || "",
        email: employee?.email || "",
        phone: employee?.phone || "",
        role: employee?.role || "",
        checker_id: employee?.checker_id?.toString() || "",
        is_self_checker: employee ? employee.checker_id === employee.id : false,
        designation: employee?.designation || "",
        status: employee?.status || "active",
        old_password: "",
        new_password: "",
    });

    // Log initial props and data changes
    useEffect(() => {
        // console.log("Employee data:", employee);
        // console.log("Checkers prop:", checkers);
        // console.log("Companies prop:", companies);
        // console.log("Form data:", data);
        // console.log("Role value:", data.role);
    }, [data]);

    // Filter checkers based on role and company_id
    const getFilteredCheckers = () => {
        // console.log(
        //     "Filtering checkers with role:",
        //     data.role,
        //     "company_id:",
        //     data.company_id
        // );
        if (!data.role || !data.company_id) return {};
        if (data.role === "maker") {
            return Object.entries(checkers).reduce((acc, [id, checker]) => {
                if (checker.company_id == data.company_id) {
                    acc[id] = checker.label;
                }
                return acc;
            }, {});
        } else if (data.role === "po_maker") {
            return Object.entries(checkers).reduce((acc, [id, checker]) => {
                if (checker.role === "po_checker") {
                    acc[id] = checker.label;
                }
                return acc;
            }, {});
        } else if (data.role === "finance_maker") {
            return Object.entries(checkers).reduce((acc, [id, checker]) => {
                if (checker.role === "finance_checker") {
                    acc[id] = checker.label;
                }
                return acc;
            }, {});
        } else if (data.role === "backout_maker") {
            return Object.entries(checkers).reduce((acc, [id, checker]) => {
                if (checker.role === "backout_checker") {
                    acc[id] = checker.label;
                }
                return acc;
            }, {});
        }
        return {};
    };

    // Compute fields dynamically
    const getFields = () => {
        const isMakerRole = [
            "maker",
            "po_maker",
            "finance_maker",
            "backout_maker",
        ].includes(data.role);
        const filteredCheckers = getFilteredCheckers();
        // console.log("isMakerRole:", isMakerRole);
        // console.log("Filtered checkers:", filteredCheckers);

        return [
            {
                name: "emp_id",
                label: "Employee ID",
                type: "text",
                required: true,
            },
            { name: "name", label: "Name", type: "text", required: true },
            {
                name: "company_id",
                label: "Company",
                type: "select",
                options: Object.entries(companies).map(([id, name]) => ({
                    value: id.toString(),
                    label: name,
                })),
                required: true,
            },
            { name: "email", label: "Email", type: "email" },
            { name: "phone", label: "Phone", type: "text" },
            {
                name: "role",
                label: "Role",
                type: "select",
                options: [
                    { value: "", label: "Select Role (Optional)" },
                    { value: "maker", label: "Maker" },
                    { value: "checker", label: "Checker" },
                    { value: "po_maker", label: "PO Maker" },
                    { value: "po_checker", label: "PO Checker" },
                    { value: "finance_maker", label: "Finance Maker" },
                    { value: "finance_checker", label: "Finance Checker" },
                    { value: "backout_maker", label: "Backout Maker" },
                    { value: "backout_checker", label: "Backout Checker" },
                ],
                required: false,
            },
            ...(isMakerRole && data.role
                ? [
                      {
                          name: "checker_id",
                          label: "Checker",
                          type: "custom",
                          render: ({ value, onChange, errors, formValues }) => {
                              //   console.log("checker_id value:", value);
                              //   console.log("checker_id formValues:", formValues);
                              return (
                                  <Select
                                      classNamePrefix="react-select"
                                      styles={{
                                          control: (provided, state) => ({
                                              ...provided,
                                              minHeight: "38px",
                                              height: "38px",
                                              borderColor: state.isFocused
                                                  ? "#80bdff"
                                                  : errors?.checker_id
                                                  ? "#dc3545"
                                                  : "#ced4da",
                                              boxShadow: state.isFocused
                                                  ? "0 0 0 0.2rem rgba(0, 123, 255, 0.25)"
                                                  : "none",
                                              "&:hover": {
                                                  borderColor: state.isFocused
                                                      ? "#80bdff"
                                                      : "#ced4da",
                                              },
                                              borderRadius: "0.25rem",
                                              fontSize: "1rem",
                                              lineHeight: "1.5",
                                          }),
                                          valueContainer: (provided) => ({
                                              ...provided,
                                              padding: "0.375rem 0.75rem",
                                          }),
                                          input: (provided) => ({
                                              ...provided,
                                              margin: 0,
                                              padding: 0,
                                          }),
                                          placeholder: (provided) => ({
                                              ...provided,
                                              color: "#6c757d",
                                          }),
                                          singleValue: (provided) => ({
                                              ...provided,
                                              color: "#495057",
                                          }),
                                          menu: (provided) => ({
                                              ...provided,
                                              zIndex: 9999,
                                              borderRadius: "0.25rem",
                                              marginTop: "0",
                                          }),
                                          option: (provided, state) => ({
                                              ...provided,
                                              backgroundColor: state.isSelected
                                                  ? "#007bff"
                                                  : state.isFocused
                                                  ? "#f8f9fa"
                                                  : "white",
                                              color: state.isSelected
                                                  ? "white"
                                                  : "#495057",
                                              "&:hover": {
                                                  backgroundColor: "#f8f9fa",
                                                  color: "#495057",
                                              },
                                          }),
                                      }}
                                      options={Object.entries(
                                          filteredCheckers
                                      ).map(([id, label]) => ({
                                          value: id.toString(),
                                          label: label,
                                      }))}
                                      value={
                                          value
                                              ? Object.entries(filteredCheckers)
                                                    .map(([id, label]) => ({
                                                        value: id.toString(),
                                                        label: label,
                                                    }))
                                                    .find(
                                                        (option) =>
                                                            option.value ===
                                                            value
                                                    ) || null
                                              : null
                                      }
                                      onChange={(option) => {
                                          //   console.log(
                                          //       "checker_id onChange triggered with value:",
                                          //       option ? option.value : ""
                                          //   );
                                          onChange(option ? option.value : "");
                                      }}
                                      isSearchable
                                      placeholder="Select Checker"
                                      isClearable
                                      isDisabled={
                                          formValues?.is_self_checker || false
                                      }
                                  />
                              );
                          },
                          required: (formValues) =>
                              !formValues?.is_self_checker,
                      },
                      {
                          name: "is_self_checker",
                          label: "Same as Maker (Self Checker)",
                          type: "checkbox",
                          default:
                              employee && employee.checker_id === employee.id,
                          onChange: (value, setData) => {
                              //   console.log(
                              //       "is_self_checker onChange triggered with value:",
                              //       value
                              //   );
                              setData("is_self_checker", value);
                              if (value) {
                                  setData("checker_id", "");
                              }
                          },
                      },
                  ]
                : []),
            {
                name: "designation",
                label: "Designation",
                type: "select",
                options: [
                    { value: "AM", label: "Account Manager" },
                    { value: "DM", label: "Delivery Manager" },
                    { value: "TL", label: "Team Leader" },
                    { value: "Recruiter", label: "Recruiter" },
                ],
                required: true,
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
            ...(employee
                ? [
                      {
                          name: "old_password",
                          label: "Old Password",
                          type: "password",
                      },
                      {
                          name: "new_password",
                          label: "New Password",
                          type: "password",
                      },
                  ]
                : []),
        ];
    };

    // State to force fields update
    const [fields, setFields] = useState(getFields());

    // Update fields when data.role or data.company_id changes
    useEffect(() => {
        const newFields = getFields();
        setFields(newFields);
        // console.log("Fields updated:", newFields);
    }, [data.role, data.company_id]);

    return (
        <MasterForm
            auth={auth}
            masterName="Employee Master"
            masterData={employee}
            viewBase="/employees"
            fields={fields}
        />
    );
}
