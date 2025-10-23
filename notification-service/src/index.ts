import http from "http";
import { Server } from "socket.io"

interface User {
    id: number;

    firstname: string;
    lastname: string;

    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

const httpServer = http.createServer();

const io = new Server(httpServer, {
    cors: {
        origin: "*",
        methods: ['GET', 'POST'],
        credentials: true
    }
});

const usersMap = new Map<number, string>();

io.on("connection", socket => {
    socket.on("add-user", id => {
        usersMap.set(id, socket.id);

        if (usersMap.has(id))
            usersMap.set(id, socket.id);
    });


    socket.on("contact-sent", (ids: number[], sender: User) => {
        for (let i = 0; i < ids.length; i++) {
            let matchedUser = usersMap.get(ids[i]!);
            
            if (matchedUser)
                socket.to(matchedUser).emit("refresh-notif", sender);
        }
    });


    socket.on("disconnect", () => {
        for (const [key, value] of usersMap) {
            if (value === socket.id) {
                usersMap.delete(key);
                break;
            }

        }
    });
});

httpServer.listen(5000, () =>
    console.log(`Notification signalling server running on port ${5000}`));
