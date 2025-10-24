export interface Auth {
    user: User;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    contacts: Contact[];
    unconfirmedContacts: SharedContact[]

    pageNumber: number;
    limit: number;
    pageCount: number;
    contactCount: number;

    searchTerm?: string;

    emails?: string[];


    profile?: User
};

export interface User {
    id: number;

    firstname: string;
    lastname: string;

    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}



export interface Contact {
    id: number;
    firstname: string;
    lastname: string;
    company: string;
    email: string;
    phone_number: string;

    owner_id: number;
    owner_name: string;
    permission: "viewer" | "editor",

    created_at?: string;
    owner: User
}

export interface Notifications {
    id: number;
    title: string;
    message: string;
    user_id: number;

    created_at?: string;
    updated_at?: string;
};

export interface ContactTableFormat {
    id: number;

    name: string;

    company: string;
    email: string;
    phone_number: string;

    owner_id: number;
    owner_name: string;
    permission: "viewer" | "editor",
}

export interface SharedContact {
    user_id: number;
    contact_id: number;
    permission: "viewer" | "editor",
    confirmed: number;
    shared_at: string;
    created_at: string;
    updated_at: string;
    // contact_name: string;
    // owner: string;

    contact: Contact,
}


interface PaginatedContact {
    contacts: Contact[],
    limit: number;
    pageNumber: number;
    pageCount: number;
    contactCount: number;

};


export interface ContactError {
    firstname: string;
    lastname: string;
    company: string;
    email: string;
    phone_number: string;
}


export interface Skipped {
    contact: Contact;
    reason: string;
};

export interface MultiShareContactResponse {
    message: string;
    skipped: Skipped[],
    shared: SharedContact[],
    userIds: number[]
}

export interface Response {
    message: string
}