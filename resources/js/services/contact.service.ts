import { Contact, ContactTableFormat, MultiShareContactResponse, PaginatedContact, SharedContact } from "@/types";
import axios from "axios";


interface Response {
    message: string
}

interface ErrorResponse {
    error: string
};

interface ContactValidationError {
    firstname: string[];
    lastname: string[];
    company: string[];
    email: string[];
    phone_number: string[];
}

export async function getContacts(): Promise<[PaginatedContact | null, ErrorResponse | null]> {
    try {
        const res = await axios<PaginatedContact>(`/contacts/get-contacts${window.location.search}`);
        return [res.data, null];
    } catch (error) {
        return [null, { error: "An error occured while fetching contacts." }]
    }
}

export async function getContactById(id: number): Promise<Contact> {
    const res = await axios<Contact>(`/contacts/get-contact/${id}`)
    return res.data;
}

export async function createContact(formData: FormData): Promise<[Response | null, ContactValidationError | ErrorResponse | null]> {
    try {
        const res = await axios<Response>(`/contacts/create-contact`, {
            method: "POST",
            data: formData
        });
        return [res.data, null];
    } catch (error) {
        if (axios.isAxiosError(error))
            return [null, error.response?.data.errors as ContactValidationError]

        return [null, { error: "An error occured while creating contact." }]
    }
}

export async function updateContactById(id: number, formData: FormData): Promise<[Response | null, ContactValidationError | ErrorResponse | null]> {
    try {
        const res = await axios<Response>(`/contacts/update-contact/${id}`, {
            method: "POST",
            data: formData
        });

        return [res.data, null];
    } catch (error) {
        if (axios.isAxiosError(error) && "errors" in error.response?.data)
            return [null, error.response?.data.errors as ContactValidationError]

        return [null, { error: "An error occured while updating contact." }]
    }
}

export async function deleteContactById(id: number): Promise<[Response | null, ErrorResponse | null]> {
    try {
        const res = await axios<Response>(`/contacts/delete-contact/${id}`, {
            method: "POST"
        });
        return [res.data, null];
    } catch (error) {
        return [null, { error: "An error occured while deleting contact." }]
    }
}

export async function shareContact(body: Partial<{
    contact_id: number;
    email: string;
    permission: "viewer" | "editor";
}>) {
    const formData = new FormData();


    Object.entries(body).forEach(([key, value]) => {
        if (key !== undefined || value !== undefined)
            formData.append(key, String(value));
    });

    const res = await axios(`/contacts/share-contact`, {
        method: "POST",
        data: formData
    });

    return res.data;
}

export async function shareMultipleContacts(contactIds: number[], emails: string[], permission: "viewer" | "editor" = "viewer") {
    const res = await axios<MultiShareContactResponse>(`/contacts/share-multiple-contacts`, {
        method: "POST",
        data: JSON.stringify({
            contacts: contactIds,
            emails: emails,
            permission
        })
    });

    return res.data
}

export async function deleteMultipleContacts(contactIds: number[]) {
    const res = await axios(`/contacts/delete-multiple-contacts`, {
        method: "POST",
        data: JSON.stringify({
            contacts: contactIds
        })
    });

    return res.data;
}

export async function acceptSharedContactStatus(body: Partial<{
    contact_id: number;
    confirmed: boolean | number;
}>): Promise<[Response | null, ErrorResponse | null]> {
    const formData = new FormData();

    Object.entries(body).forEach(([key, value]) => {
        if (key !== undefined || value !== undefined)
            formData.append(key, String(value));
    });

    try {
        const res = await axios(`/contacts/accept-shared-contact-status`, {
            method: "POST",
            data: formData
        });

        return [res.data, null];
    } catch (error) {
        return [null, { error: "An error occured" }];
    }
}

export async function ignoreSharedContact(contact_id: number) {
    const formData = new FormData();

    formData.append("contact_id", String(contact_id));

    const res = await axios(`/contacts/ignore-shared-contact`, {
        method: "POST",
        data: formData
    });

    return res.data;
}

export async function getUnconfirmedShares(): Promise<SharedContact[]> {
    const res = await axios<SharedContact[]>(`/contacts/get-unconfirmed-shares`);
    return res.data;
}

export function mapContactsForTable(contacts: Contact[]): ContactTableFormat[] {
    const result: ContactTableFormat[] = contacts.map(val => {
        return {
            id: val.id,
            name: val.firstname + " " + val.lastname,
            company: val.company,
            phone_number: val.phone_number,
            email: val.email,
            owner_id: val.owner_id,
            owner_name: val.owner_name,
            permission: val.permission,
        }
    });

    return result;
}
