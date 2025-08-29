import MasterForm from "../MasterForm";

export default function Form({ auth, status = null }) {
    const fields = [
        { name: "status", label: "Status", type: "text", required: true },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Status Master"
            masterData={status}
            viewBase="/status-masters"
            fields={fields}
        />
    );
}
