import MasterForm from "../MasterForm";

export default function Form({ auth, consultant = null }) {
    const isImage = (path) => {
        if (!path) return false;
        const imageExtensions = [".jpg", ".jpeg", ".png"];
        return imageExtensions.some((ext) => path.toLowerCase().endsWith(ext));
    };

    const fields = [
        {
            name: "code",
            label: "Consultant Code",
            type: "text",
            required: true,
        },
        { name: "name", label: "Name", type: "text", required: true },
        { name: "address", label: "Address", type: "textarea" },
        { name: "state", label: "State", type: "text" },
        { name: "city", label: "City", type: "text" },
        { name: "country", label: "Country", type: "text" },
        { name: "phone1", label: "Phone 1", type: "text", required: true },
        { name: "phone2", label: "Phone 2", type: "text" },
        { name: "email1", label: "Email 1", type: "email", required: true },
        { name: "email2", label: "Email 2", type: "email" },
        {
            name: "status",
            label: "Status",
            type: "select",
            options: [
                { value: "active", label: "Active" },
                { value: "inactive", label: "Inactive" },
            ],
            default: "active",
            required: true,
        },
        {
            name: "aadhaar",
            label: "Aadhaar Document",
            type: "file",
            accept: ".pdf,.jpg,.png",
            existingFile: consultant?.aadhaar
                ? {
                      path: `/storage/${consultant.aadhaar}`,
                      type: isImage(consultant.aadhaar) ? "image" : "file",
                  }
                : null,
        },
        {
            name: "pan",
            label: "PAN Document",
            type: "file",
            accept: ".pdf,.jpg,.png",
            existingFile: consultant?.pan
                ? {
                      path: `/storage/${consultant.pan}`,
                      type: isImage(consultant.pan) ? "image" : "file",
                  }
                : null,
        },
        {
            name: "po_copy",
            label: "PO Copy",
            type: "file",
            accept: ".pdf,.jpg,.png",
            existingFile: consultant?.po_copy
                ? {
                      path: `/storage/${consultant.po_copy}`,
                      type: isImage(consultant.po_copy) ? "image" : "file",
                  }
                : null,
        },
        {
            name: "extra_doc",
            label: "Extra Document",
            type: "file",
            accept: ".pdf,.jpg,.png",
            existingFile: consultant?.extra_doc
                ? {
                      path: `/storage/${consultant.extra_doc}`,
                      type: isImage(consultant.extra_doc) ? "image" : "file",
                  }
                : null,
        },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Consultant Master"
            masterData={consultant}
            viewBase="/consultants"
            fields={fields}
        />
    );
}
