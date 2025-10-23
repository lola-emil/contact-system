import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function appendQueryParam(key: string, value: any, url = window.location.href) {
    const urlObj = new URL(url);

    urlObj.searchParams.set(key, value);
    return urlObj.toString();
}


export const range = (size: number) => Array.from({ length: size }, (_, i) => i + 1);
