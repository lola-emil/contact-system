import http from "http";
import { Server } from "socket.io";
const httpServer = http.createServer();
const io = new Server(httpServer, {
    cors: {
        origin: "*",
        methods: ['GET', 'POST'],
        credentials: true
    }
});
const usersMap = new Map();
io.on("connection", socket => {
    socket.on("add-user", id => {
        usersMap.set(id, socket.id);
        if (usersMap.has(id))
            usersMap.set(id, socket.id);
        console.log(usersMap);
    });
    socket.on("contact-sent", (ids, sender) => {
        for (let i = 0; i < ids.length; i++) {
            let matchedUser = usersMap.get(ids[i]);
            console.log("send to", matchedUser);
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
httpServer.listen(5000, () => console.log(`Notification signalling server running on port ${5000}`));
//# sourceMappingURL=index.js.map