import { useState, useEffect } from "react";
import { router, useForm, usePage } from "@inertiajs/react";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import MainLayout from "@/Layouts/MainLayout";
import Select from "react-select";

const customSelectStyles = {
    control: (provided, state) => ({
        ...provided,
        minHeight: "38px",
        height: "38px",
        borderColor: state.isFocused
            ? "#80bdff"
            : state.selectProps.error
            ? "#dc3545"
            : "#ced4da",
        boxShadow: state.isFocused
            ? "0 0 0 0.2rem rgba(0, 123, 255, 0.25)"
            : "none",
        "&:hover": { borderColor: state.isFocused ? "#80bdff" : "#ced4da" },
        borderRadius: "0.25rem",
        fontSize: "1rem",
        lineHeight: "1.5",
    }),
    valueContainer: (provided) => ({
        ...provided,
        padding: "0.375rem 0.75rem",
    }),
    input: (provided) => ({ ...provided, margin: 0, padding: 0 }),
    placeholder: (provided) => ({ ...provided, color: "#6c757d" }),
    singleValue: (provided) => ({ ...provided, color: "#495057" }),
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
        color: state.isSelected ? "white" : "#495057",
        "&:hover": { backgroundColor: "#f8f9fa", color: "#495057" },
    }),
};

export default function MasterForm({
    auth,
    masterName,
    masterData = null,
    viewBase,
    fields,
    hasCompanyDropdown = false,
    customSubmitRoute = null,
}) {
    const { props } = usePage();
    const statusField =
        masterName === "Business Unit Master"
            ? "unit_status"
            : masterName === "Branch Master"
            ? "branch_status"
            : masterName === "Client Master"
            ? "client_status"
            : "status";

    const defaultStatus = masterName === "Client Master" ? 0 : 1;

    const initialData = fields.reduce(
        (acc, field) => ({
            ...acc,
            [field.name]: masterData
                ? field.type === "checkbox"
                    ? !!masterData[field.name]
                    : field.name === "to_emails" || field.name === "cc_emails"
                    ? Array.isArray(masterData[field.name])
                        ? masterData[field.name]
                        : []
                    : field.type === "select" &&
                      masterName === "Main Email" &&
                      field.name === "is_active"
                    ? Boolean(masterData[field.name])
                    : field.type === "select" || field.type === "custom"
                    ? masterData[field.name]?.toString() ??
                      field.defaultValue?.toString() ??
                      ""
                    : masterData[field.name] ?? field.defaultValue ?? ""
                : field.type === "select" &&
                  masterName === "Main Email" &&
                  field.name === "is_active"
                ? true
                : field.type === "select"
                ? field.defaultValue?.toString() ?? ""
                : field.name === "to_emails" || field.name === "cc_emails"
                ? []
                : field.type === "checkbox"
                ? false
                : field.defaultValue ?? "",
        }),
        {
            [statusField]: masterData
                ? masterData[statusField] ?? defaultStatus
                : defaultStatus,
        }
    );

    const { data, setData, post, put, processing, errors, reset } =
        useForm(initialData);

    const [files, setFiles] = useState({});

    useEffect(() => {
        // console.log("Initial data:", initialData);
        // console.log("Form data state:", data);
        // console.log("Master data:", masterData);
        if (props.flash?.success) {
            toast.success(props.flash.success);
        }
        if (props.flash?.error) {
            toast.error(props.flash.error);
        }
    }, [props.flash]);

    const handleFileChange = (e, fieldName) => {
        setFiles({ ...files, [fieldName]: e.target.files[0] });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // console.log("Inertia submit triggered");

        const formData = {};
        fields.forEach((field) => {
            if (field.type !== "file") {
                let value = data[field.name];
                if (field.name === "to_emails" || field.name === "cc_emails") {
                    if (Array.isArray(value)) {
                        value = value;
                    } else if (typeof value === "string") {
                        value = value
                            .split(",")
                            .map((email) => email.trim())
                            .filter((email) =>
                                email
                                    ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
                                    : false
                            );
                    } else {
                        value = [];
                    }
                }
                formData[field.name] =
                    field.type === "checkbox"
                        ? value
                            ? "1"
                            : "0"
                        : field.type === "select" &&
                          masterName === "Main Email" &&
                          field.name === "is_active"
                        ? value
                        : field.type === "select"
                        ? value?.toString() ?? ""
                        : value ?? "";
            }
        });
        Object.keys(files).forEach((key) => {
            if (files[key]) formData[key] = files[key];
        });

        // console.log("Form data:", formData);

        let url = masterData ? `${viewBase}/${masterData.id}` : viewBase;

        if (customSubmitRoute) {
            const [routeName, param] = customSubmitRoute.split(",");
            url = param ? route(routeName, param) : route(routeName);
        }

        // const method = masterData ? put : post;

        post(url, formData, {
            onSuccess: () => {
                // console.log("Inertia onSuccess triggered");
                toast.success(
                    `${masterName} ${
                        masterData ? "updated" : "created"
                    } successfully`
                );
                reset();
            },
            onError: (errors) => {
                // console.error("Form errors:", errors);
                toast.error("Failed to submit form");
                Object.values(errors).forEach((error) => toast.error(error));
            },
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <MainLayout
            title={`${masterData ? "Edit" : "Create"} ${masterName}`}
            auth={auth}
            showLoader={processing}
        >
            <ToastContainer />
            <div className="container-fluid dashboard-width">
                <div className="row">
                    <div className="page-header">
                        <h3 className="page-title">{`${
                            masterData ? "Edit" : "Create"
                        } ${masterName}`}</h3>
                    </div>
                    <div className="row">
                        <div className="col-sm-12">
                            <div className="card">
                                <div className="card-body">
                                    <form onSubmit={handleSubmit}>
                                        <div className="row">
                                            {fields.map((field) => (
                                                <div
                                                    key={field.name}
                                                    className="col-12 col-sm-6"
                                                >
                                                    <div className="mb-3">
                                                        <label className="form-label">
                                                            {field.label}
                                                            {field.required && (
                                                                <span className="text-danger">
                                                                    {" "}
                                                                    *
                                                                </span>
                                                            )}
                                                        </label>
                                                        {field.type ===
                                                        "select" ? (
                                                            <Select
                                                                classNamePrefix="react-select"
                                                                styles={{
                                                                    ...customSelectStyles,
                                                                    control: (
                                                                        provided,
                                                                        state
                                                                    ) => ({
                                                                        ...provided,
                                                                        borderColor:
                                                                            errors[
                                                                                field
                                                                                    .name
                                                                            ]
                                                                                ? "#dc3545"
                                                                                : state.isFocused
                                                                                ? "#80bdff"
                                                                                : "#ced4da",
                                                                    }),
                                                                }}
                                                                error={
                                                                    errors[
                                                                        field
                                                                            .name
                                                                    ]
                                                                }
                                                                value={
                                                                    data[
                                                                        field
                                                                            .name
                                                                    ] !==
                                                                    undefined
                                                                        ? field.options.find(
                                                                              (
                                                                                  option
                                                                              ) =>
                                                                                  option.value.toString() ===
                                                                                  data[
                                                                                      field
                                                                                          .name
                                                                                  ].toString()
                                                                          ) ||
                                                                          null
                                                                        : null
                                                                }
                                                                onChange={(
                                                                    selectedOption
                                                                ) => {
                                                                    setData(
                                                                        field.name,
                                                                        selectedOption
                                                                            ? selectedOption.value
                                                                            : ""
                                                                    );
                                                                }}
                                                                options={
                                                                    field.options ||
                                                                    []
                                                                }
                                                                placeholder={`Select ${field.label}`}
                                                                isClearable
                                                                isSearchable
                                                                required={
                                                                    field.required
                                                                }
                                                            />
                                                        ) : field.type ===
                                                          "custom" ? (
                                                            field.render({
                                                                value:
                                                                    data[
                                                                        field
                                                                            .name
                                                                    ] ||
                                                                    (field.name ===
                                                                        "to_emails" ||
                                                                    field.name ===
                                                                        "cc_emails"
                                                                        ? []
                                                                        : ""),
                                                                onChange: (
                                                                    value
                                                                ) =>
                                                                    setData(
                                                                        field.name,
                                                                        value
                                                                    ),
                                                                errors: errors[
                                                                    field.name
                                                                ],
                                                                formValues:
                                                                    data,
                                                            })
                                                        ) : field.type ===
                                                          "file" ? (
                                                            <div>
                                                                <input
                                                                    type="file"
                                                                    className={`form-control ${
                                                                        errors[
                                                                            field
                                                                                .name
                                                                        ]
                                                                            ? "is-invalid"
                                                                            : ""
                                                                    }`}
                                                                    accept={
                                                                        field.accept ||
                                                                        ".pdf,.jpg,.png"
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) =>
                                                                        handleFileChange(
                                                                            e,
                                                                            field.name
                                                                        )
                                                                    }
                                                                />
                                                                {field.existingFile && (
                                                                    <div className="mt-2">
                                                                        {field
                                                                            .existingFile
                                                                            .type ===
                                                                        "image" ? (
                                                                            <img
                                                                                src={
                                                                                    field
                                                                                        .existingFile
                                                                                        .path
                                                                                }
                                                                                alt={
                                                                                    field.label
                                                                                }
                                                                                className="w-32 h-32 object-cover"
                                                                                onError={(
                                                                                    e
                                                                                ) => {
                                                                                    e.target.style.display =
                                                                                        "none";
                                                                                }}
                                                                            />
                                                                        ) : (
                                                                            <a
                                                                                href={
                                                                                    field
                                                                                        .existingFile
                                                                                        .path
                                                                                }
                                                                                target="_blank"
                                                                                rel="noopener noreferrer"
                                                                                className="text-blue-500 hover:underline"
                                                                            >
                                                                                View{" "}
                                                                                {
                                                                                    field.label
                                                                                }
                                                                            </a>
                                                                        )}
                                                                    </div>
                                                                )}
                                                                {errors[
                                                                    field.name
                                                                ] && (
                                                                    <div className="invalid-feedback">
                                                                        {
                                                                            errors[
                                                                                field
                                                                                    .name
                                                                            ]
                                                                        }
                                                                    </div>
                                                                )}
                                                            </div>
                                                        ) : field.type ===
                                                          "checkbox" ? (
                                                            <div className="form-check">
                                                                <input
                                                                    type="checkbox"
                                                                    className={`form-check-input ${
                                                                        errors[
                                                                            field
                                                                                .name
                                                                        ]
                                                                            ? "is-invalid"
                                                                            : ""
                                                                    }`}
                                                                    checked={
                                                                        data[
                                                                            field
                                                                                .name
                                                                        ] ||
                                                                        false
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) =>
                                                                        setData(
                                                                            field.name,
                                                                            e
                                                                                .target
                                                                                .checked
                                                                        )
                                                                    }
                                                                    id={
                                                                        field.name
                                                                    }
                                                                />
                                                                <label
                                                                    className="form-check-label"
                                                                    htmlFor={
                                                                        field.name
                                                                    }
                                                                >
                                                                    {
                                                                        field.label
                                                                    }
                                                                </label>
                                                                {errors[
                                                                    field.name
                                                                ] && (
                                                                    <div className="invalid-feedback">
                                                                        {
                                                                            errors[
                                                                                field
                                                                                    .name
                                                                            ]
                                                                        }
                                                                    </div>
                                                                )}
                                                            </div>
                                                        ) : (
                                                            <input
                                                                type={
                                                                    field.type ||
                                                                    "text"
                                                                }
                                                                className={`form-control ${
                                                                    errors[
                                                                        field
                                                                            .name
                                                                    ]
                                                                        ? "is-invalid"
                                                                        : ""
                                                                }`}
                                                                value={
                                                                    field.valueTransformer
                                                                        ? field.valueTransformer(
                                                                              data[
                                                                                  field
                                                                                      .name
                                                                              ]
                                                                          )
                                                                        : data[
                                                                              field
                                                                                  .name
                                                                          ] ??
                                                                          ""
                                                                }
                                                                onChange={(e) =>
                                                                    field.onChange
                                                                        ? field.onChange(
                                                                              e
                                                                                  .target
                                                                                  .value,
                                                                              setData
                                                                          )
                                                                        : setData(
                                                                              field.name,
                                                                              e
                                                                                  .target
                                                                                  .value
                                                                          )
                                                                }
                                                                onBlur={(e) =>
                                                                    field.onBlur &&
                                                                    field.onBlur(
                                                                        e.target
                                                                            .value,
                                                                        setData
                                                                    )
                                                                }
                                                                required={
                                                                    field.required
                                                                }
                                                                name={
                                                                    field.name
                                                                }
                                                                placeholder={
                                                                    field.placeholder ||
                                                                    ""
                                                                }
                                                            />
                                                        )}
                                                        {errors[field.name] &&
                                                            field.type !==
                                                                "checkbox" && (
                                                                <div className="invalid-feedback">
                                                                    {
                                                                        errors[
                                                                            field
                                                                                .name
                                                                        ]
                                                                    }
                                                                </div>
                                                            )}
                                                    </div>
                                                </div>
                                            ))}
                                            {hasCompanyDropdown && (
                                                <div className="col-12 col-sm-6">
                                                    <div className="mb-3">
                                                        <label className="form-label">
                                                            Status{" "}
                                                            <span className="text-danger">
                                                                {" "}
                                                                *
                                                            </span>
                                                        </label>
                                                        <select
                                                            className={`form-control ${
                                                                errors[
                                                                    statusField
                                                                ]
                                                                    ? "is-invalid"
                                                                    : ""
                                                            }`}
                                                            value={
                                                                data[
                                                                    statusField
                                                                ] ??
                                                                defaultStatus
                                                            }
                                                            onChange={(e) =>
                                                                setData(
                                                                    statusField,
                                                                    parseInt(
                                                                        e.target
                                                                            .value
                                                                    )
                                                                )
                                                            }
                                                            required
                                                        >
                                                            <option value={1}>
                                                                Permanent
                                                            </option>
                                                            <option value={0}>
                                                                Temporary
                                                            </option>
                                                        </select>
                                                        {errors[
                                                            statusField
                                                        ] && (
                                                            <div className="invalid-feedback">
                                                                {
                                                                    errors[
                                                                        statusField
                                                                    ]
                                                                }
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            )}
                                            <div className="col-12">
                                                <button
                                                    type="submit"
                                                    className="btn btn-primary"
                                                    disabled={processing}
                                                    style={{
                                                        backgroundColor:
                                                            "#F26522",
                                                        borderColor: "#F26522",
                                                    }}
                                                >
                                                    {masterData
                                                        ? "Update"
                                                        : "Submit"}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
