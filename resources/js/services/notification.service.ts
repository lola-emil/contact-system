import { Notifications } from "@/types";
import axios from "axios";

export async function getUserNotification(userId: number) {
    const res = await axios<Notifications[]>(`/notifications/get-user-notifications/${userId}`);
    return res;
}