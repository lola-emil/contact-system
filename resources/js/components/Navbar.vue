<template>
    <header class="w-full">
        <div class="navbar bg-base-100 shadow-sm">
            <div class="flex-1">
                <Link href="/" class="btn btn-ghost text-xl">Contact System</Link>
            </div>
            <div class="flex items-center gap-3">
                <Notification :items="unconfirmedContacts" @on-accept="onAcceptSharedContact"
                    @on-ignore="onIgnoreContact" />

                <!-- <button class="btn btn-ghost btn-square" @click="showNotificationModal()">
                    <Bell :size="20" />
                </button> -->
                <div>
                    <Link aria-label="profile" class="btn btn-ghost btn-square" href="/users/profile">
                    <UserIcon />
                    </Link>
                </div>

                <a href="/auth/logout" class="link">Logout</a>
                <ul class="menu menu-horizontal px-1 gap-3">
                    <li></li>
                </ul>
            </div>
        </div>
    </header>

    <NotificationModal :items="notifications" ref="notifModal" />
</template>

<script setup lang="ts">
import { acceptSharedContactStatus, getUnconfirmedShares, ignoreSharedContact } from '@/services/contact.service';
import { Notifications, SharedContact, User } from '@/types';
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { User as UserIcon } from "lucide-vue-next";
import { usePage } from '@inertiajs/vue3';
import { useWebNotification } from '@vueuse/core';
import { useEcho } from '@laravel/echo-vue';
import NotificationModal from './NotificationModal.vue';
import Notification from './Notification.vue';


const { isSupported, permissionGranted, show } = useWebNotification({
    title: "Notification",
    body: "Contact share to you.",
    lang: 'en',
    renotify: true,
    tag: 'test',
});

const page = usePage();


const contactSharedEvent = useEcho(`share.contact.${page.props.auth.user.id}`, "ContactShared", (payload: { sharer: User }) => {
    // const sender = payload.sharer;
    fetchNotif();
    showWebNotification();
})

const notifModal = ref<InstanceType<typeof NotificationModal>>();
const notifications = ref<Notifications[]>([]);

const unconfirmedContacts = ref<SharedContact[]>([]);

async function fetchNotif() {
    const data = await getUnconfirmedShares()
    unconfirmedContacts.value = data;
    // const userId = page.props.auth.user.id;
    // const data = await NotifService.getUserNotification(userId);
    // notifications.value = data;
}

function onAcceptSharedContact(contactId: number) {
    acceptSharedContactStatus({
        contact_id: contactId,
        confirmed: 1
    }).then(_ => {
        fetchNotif();
        emit("stateChange");
    });
}

function onIgnoreContact(contactId: number) {
    ignoreSharedContact(contactId).then(_ => {
        fetchNotif();
    });
}

function showWebNotification() {
    if (isSupported.value && permissionGranted.value)
        show()
}


function showNotificationModal() {
    notifModal.value?.showModal();
}

onMounted(() => {
    fetchNotif();
    contactSharedEvent.listen();

    const userId = page.props.auth.user.id;

});

const emit = defineEmits<{
    stateChange: []
}>();


</script>
