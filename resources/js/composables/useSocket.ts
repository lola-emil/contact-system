// composables/useSocket.js
import { io, Socket } from "socket.io-client";
import { ref, onBeforeUnmount, onMounted } from "vue";


type SocketCB = (socket: Socket) => void;

export function useSocket(userId: number) {
  let socket: Socket | null = null;  // Declare a single socket instance
  const connected = ref<boolean>(false);

  const connectToSocket = () => {
    if (!socket || !connected.value)
      socket = io("http://localhost:5000");

    socket.on("connect", () => {
      console.log("connected to socket");
      if (socket) {
        socket.emit("add-user", userId);
        connected.value = true;
      }
    });
  }

  const getSocket = () => {
    if (!socket && !connected.value)
      throw new Error("You need to call connect() first on onMounted hook");

    return socket!;
  }


  onBeforeUnmount(() => {
    if (socket && socket.connected) {
      socket.disconnect();
      connected.value = false;
    }
  });

  return { socket, connectToSocket, connected, getSocket };
}
