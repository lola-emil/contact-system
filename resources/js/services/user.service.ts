import axios, { AxiosError } from "axios";
import { Response } from "@/types";

export async function checkEmail(email: string): Promise<Response> {
    const res = await axios<Response>(`/users/check-email?email=${email}`);
    return res.data;
}

export async function searchEmails(params?: {
    query?: string,
    signal?: AbortController
}): Promise<string[]> {
    if (params) {
        params.query = params.query ?? "";
    }

    const query = new URLSearchParams(Object.fromEntries(
        Object.entries(params ?? {}).map(([key, value]) => [key, String(value)])
    ));

    const res = await axios<string[]>(`/users/search-emails?${query.toString()}`);
    return res.data;
}

export async function getEmails() {
    const res = await axios<string[]>(`/users/get-emails`);
    return res.data;
}


export async function updateProfile(data: Partial<{
    firstname: string;
    lastname: string;
    email: string;
}>) {
    const res = await axios(`/users/update-profile`, {
        method: "POST",
        data: JSON.stringify(data)
    });

    return res.data;
}

export async function updatePassword(data: Partial<{
    current_password: string;
    new_password: string;
}>): Promise<[{
    message: string
} | null, AxiosError | null]> {

    const formData = new FormData();

    formData.append("current_password", data.current_password ?? "");
    formData.append("new_password", data.new_password ?? "");

    try {
        const res = await axios(`/users/update-password`, {
            method: "POST",
            data: formData
        });

        return [res.data, null];
    } catch (err) {
        const error = (<AxiosError>err);
        return [null, error];
    }
}

export async function checkEmailAvailability(email: string): Promise<[
    {
        email: string;
        exists: boolean;
    } | null,
    AxiosError | null
]> {
    const formData = new FormData();

    formData.append("email", email);

    try {
        const res = await axios(`/users/check-email-availability`, {
            method: "POST",
            data: formData
        });

        return [res.data, null];
    } catch (err) {
        const error = (<AxiosError>err);
        return [null, error];
    }
}
