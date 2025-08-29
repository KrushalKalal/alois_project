import MasterForm from "../MasterForm";

export default function Form({ auth, mainEmail = null }) {
    const fields = [
        {
            name: "email",
            label: "Email Address",
            type: "email",
            required: true,
        },
        {
            name: "name",
            label: "Sender Name",
            type: "text",
            required: false,
        },
        {
            name: "password",
            label: "Password",
            type: "password",
            required: mainEmail ? false : true,
            placeholder: mainEmail
                ? "Leave blank to keep existing password"
                : "Enter password",
        },
        {
            name: "is_active",
            label: "Active Status",
            type: "select",
            required: true,
            defaultValue: mainEmail ? Boolean(mainEmail.is_active) : true,
            options: [
                { value: true, label: "Active" },
                { value: false, label: "Inactive" },
            ],
        },
    ];

    return (
        <MasterForm
            auth={auth}
            masterName="Main Email"
            masterData={mainEmail}
            viewBase="/emails"
            fields={fields}
            customSubmitRoute={
                mainEmail
                    ? `emails.updateMainEmail,${mainEmail.id}`
                    : "emails.storeMainEmail"
            }
        />
    );
}
