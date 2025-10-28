<template>

    <Head>
        <title>Contacts</title>
    </Head>

    <Navbar @state-change="contactComposable.fetchContacts" />

    <main class="container mx-auto px-5 lg:px-0">
        <br>
        <div class="flex justify-end">
            <p class="text-sm">Logged in as: <span class="font-bold text-info">
                    {{ userName }}
                </span>
            </p>
        </div>
        <br>
        <div>
            <div class="flex gap-5">
                <h3 class="text-2xl font-semibold">{{ page.props.searchQuery ? "Search Results" : "My Contacts" }}</h3>
            </div>

            <div class="flex items-center justify-between mt-5 w-full">
                <SearchContact :default-search="page.props.searchTerm" />

                <button @click="showContactForm()" class="btn btn-info hidden lg:inline-flex">New Contact</button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="mt-3">
            <ContactTable v-model="selectedContacts" :contacts="contactComposable.contacts.value"
                @share="showShareConfirmationModal" @on-delete="showDeleteConfirmation" @on-edit="showEditContactForm"
                :starting-index="contactComposable.tableStartingIndex.value" />

            <div class="w-full flex justify-between mt-5">
                <div class="flex items-center gap-3">
                    <button class="btn btn-info btn-soft btn-sm"
                        @click="selectedContacts.length > 0 ? shareMultiModal?.showModal(selectedContacts) : null"
                        :disabled="selectedContacts.length == 0">Share Selected</button>

                    <button class="btn btn-error btn-soft btn-sm"
                        @click="selectedContacts.length > 0 ? showMultiDeleteConfirmation() : null"
                        :disabled="selectedContacts.length == 0">Delete Rows</button>
                </div>

                <!-- PAGINATION -->
                <div>
                    <div class="join">
                        <Link :href="appendQueryParam('page', contactComposable.pageNumber.value - 1)"
                            class="join-item btn btn-info btn-sm btn-soft" :class="{
                                'btn-disabled': contactComposable.pageNumber.value == 1
                            }">«</Link>

                        <Link v-for="value in range(contactComposable.pageCount.value)"
                            :href="appendQueryParam('page', value)" class="join-item btn btn-sm btn-info btn-soft"
                            :class="{
                                'btn-disabled': value == contactComposable.pageNumber.value
                            }">
                        {{ value }}
                        </Link>

                        <Link :href="appendQueryParam('page', contactComposable.pageNumber.value + 1)"
                            class="join-item btn btn-sm btn-info btn-soft" :class="{
                                'btn-disabled': contactComposable.pageNumber.value == contactComposable.pageCount.value || contactComposable.pageCount.value == 0
                            }">»
                        </Link>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <ShareMultiContactModal :items="emails" ref="shareMultiModal" @on-success="onShareMultiSuccess" />
    <ShareContactModal :items="emails" ref="shareContactModal" @on-success="onShareSuccess" />

    <ConfirmationModal ref="confirmationModal" @on-confirm="deleteContact" />
    <ConfirmationModal ref="multiDeleteConfirmation" @on-confirm="deleteSelectedContacts" />

    <ContactFormModal ref="contactFormModal" @on-submit="submitForm" @on-close="contactComposable.clearErrors()"
        :error="contactComposable.contactFormErrors" />

    <Toast ref="toast" />

    <div class="fixed bottom-5 right-5 lg:hidden">
        <button @click="showContactForm()" class="btn btn-info rounded-full ">New Contact</button>
    </div>

</template>

<script setup lang="ts">
import { usePage, Head, } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

import { Link } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue';
import ShareMultiContactModal from '@/components/ShareMultiContactModal.vue';
import ShareContactModal from '@/components/ShareContactModal.vue';
import ContactTable from '@/components/ContactTable.vue';
import { Contact, MultiShareContactResponse } from '@/types';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ContactFormModal from '@/components/ContactFormModal.vue';
import SearchContact from '@/components/SearchContact.vue';
import Toast from '@/components/Toast.vue';
import { getEmails } from '@/services/user.service';
import { useContacts } from '@/composables/useContacts';
// import { useSocket } from '@/composables/useSocket';
import { useEcho } from '@laravel/echo-vue';

import {
    range,
    appendQueryParam
} from '@/lib/utils';

const page = usePage();
// const { connectToSocket, getSocket } = useSocket(page.props.auth.user.id);

const selectedContacts = ref<number[]>([]);
const userName = ref<string | undefined>();
const emails = ref<string[]>([]);

const shareContactModal = ref<InstanceType<typeof ShareContactModal> | null>();
const confirmationModal = ref<InstanceType<typeof ConfirmationModal> | null>();
const multiDeleteConfirmation = ref<InstanceType<typeof ConfirmationModal> | null>();
const contactFormModal = ref<InstanceType<typeof ContactFormModal> | null>();
const shareMultiModal = ref<InstanceType<typeof ShareMultiContactModal> | null>();

const contactIdToBeDeleted = ref<number | undefined>();
const toast = ref<InstanceType<typeof Toast> | null>();

const contactComposable = useContacts(toast, page.props.contacts);

const highlightSearchResults = () =>
    contactComposable.contacts.value = contactComposable.contacts.value.map((val) => ({
        id: val.id,
        name: highlightText(val.name),
        company: highlightText(val.company),
        phone_number: highlightText(val.phone_number),
        email: highlightText(val.email),
        owner_id: val.owner_id,
        owner_name: val.owner_name,
        permission: val.permission,
    }));

onMounted(() => {
    const props = page.props;
    const user = props.auth.user;

    userName.value = `${user.firstname} ${user.lastname}`;

    if (page.props.searchTerm)
        highlightSearchResults();

    contactComposable.setPaginationStates({
        pageNumber: props.pageNumber,
        pageCount: props.pageCount,
        limit: props.limit,
        contactCount: props.contactCount,
    });

    fetchEmails();
});

function highlightText(text: string) {
    const searchTerm = page.props.searchTerm ?? text;
    const escaped = searchTerm.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
    const regex = new RegExp(`(${escaped})`, 'gi');

    return text.replace(regex, `<span class='bg-primary rounded'>$1</span>`);
}

const fetchEmails = async () => {
    const response = await getEmails();
    emails.value = response;
}

const deleteContact = async () => {
    await contactComposable.deleteContact(contactIdToBeDeleted.value!);
    confirmationModal.value?.closeModal();

    // This will remove the row from the list of selected rows
    selectedContacts.value = selectedContacts.value.filter(val => val != contactIdToBeDeleted.value);
};

const deleteSelectedContacts = async () => {
    await contactComposable.deleteSelectedContacts(selectedContacts.value);

    multiDeleteConfirmation.value?.closeModal();
    selectedContacts.value = [];

};

const submitForm = async (form: Partial<Contact>) => {
    const response = await contactComposable.submitContact(form);

    if (response) contactFormModal.value?.close();
}


const showShareConfirmationModal = (contactId: number) =>
    shareContactModal.value?.showModal(contactId);

const showMultiDeleteConfirmation = () =>
    multiDeleteConfirmation.value?.showConfirmationModal("Are you sure you want to delete these contacts?");

const showContactForm = () => contactFormModal.value?.showModal({
    title: "Create New Contact"
});

const showEditContactForm = (contactId: number) => contactFormModal.value?.showModal({
    id: contactId,
    title: "Update Contact"
});

const showDeleteConfirmation = (contactId: number) => {
    contactIdToBeDeleted.value = contactId;
    confirmationModal.value?.showConfirmationModal("Are you sure you want to delete this contact?");
}

const onShareSuccess = (response: MultiShareContactResponse) => {
    // const socket = getSocket();

    console.log(page.props.auth.user);

    // socket.emit("contact-sent", response.userIds, page.props.auth.user);

    toast.value?.showToast('Shared successfully.', 'success');
}

function onShareMultiSuccess(response: MultiShareContactResponse) {
    // const socket = getSocket();
    console.log(page.props.auth.user);
    // socket.emit("contact-sent", response.userIds, page.props.auth.user);

    toast.value?.showToast(`
        <span class="font-bold">Shared</span>: ${response.shared.length}; <span class="font-bold">Skipped:</span> ${response.skipped.length}`)
    shareMultiModal.value?.close();
    selectedContacts.value = [];
}

</script>
