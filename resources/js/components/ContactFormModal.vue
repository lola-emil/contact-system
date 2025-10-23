<template>
    <dialog @close="onModalClose()" ref="contact-form-modal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">{{ modalTitle }}</h3>

            <form @submit.prevent="submit(form)" action="./create-contact" method="post" class="card">
                <div class="card-body w-full">
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Firstname</legend>
                        <input v-model="form.firstname" type="text" class="input w-full" :disabled="isLoading" />
                        <p class="label text-error">{{ props.error?.firstname ?? "" }}</p>

                        <legend class="fieldset-legend">Lastname</legend>
                        <input v-model="form.lastname" type="text" class="input w-full" :disabled="isLoading" />
                        <p class="label text-error">{{ props.error?.lastname ?? "" }}</p>

                        <legend class="fieldset-legend">Company</legend>
                        <input v-model="form.company" type="text" class="input w-full" :disabled="isLoading" />
                        <p class="label text-error">{{ props.error?.company ?? "" }}</p>

                        <legend class="fieldset-legend">Phone Number</legend>
                        <input v-model="form.phone_number" type="tel" class="input w-full" :disabled="isLoading" />
                        <p class="label text-error">{{ props.error?.phone_number ?? "" }}</p>


                        <legend class="fieldset-legend">Email</legend>
                        <input v-model="form.email" type="text" class="input w-full" :disabled="isLoading" />
                        <p class="label text-error">{{ props.error?.email ?? "" }}</p>
                    </fieldset>

                    <button class="btn btn-primary" :disabled="isLoading">{{ isLoading ? "Loading" : "Submit"
                        }}</button>
                </div>
            </form>
        </div>
    </dialog>


</template>

<script setup lang="ts">
import { Contact, ContactError } from '@/types';
import axios from 'axios';
import { onMounted, reactive, ref, useTemplateRef } from 'vue';

const modal = useTemplateRef("contact-form-modal");
const form = reactive<Partial<Contact>>({})
const modalTitle = ref("Contact Form");
const isLoading = ref<boolean>();

function showModal(opt?: {
    id?: number,
    modalTitle?: string
}) {
    if (opt?.modalTitle) modalTitle.value = opt.modalTitle;

    if (opt?.id) {
        fetchContact(opt?.id);
    }

    modal.value?.showModal();
}

function submit(form: Partial<Contact>) {
    emit("onSubmit", form);
}

function fetchContact(contactId: number) {
    isLoading.value = true;

    axios<Contact>(`/contacts/get-contact/${contactId}`)
        .then(val => {
            const fetchedContact = val.data;

            form.id = fetchedContact.id;
            form.firstname = fetchedContact.firstname;
            form.lastname = fetchedContact.lastname;
            form.email = fetchedContact.email;
            form.company = fetchedContact.company;
            form.phone_number = fetchedContact.phone_number;

            isLoading.value = false;
        }).catch(err => {
            console.error(err);
            isLoading.value = false;
        });
}


function changeLoadingState(state: boolean) {
    isLoading.value = state;
}

function close() {
    modal.value?.close();
}

function onModalClose() {
    // Remove input values
    Object.keys(form).forEach(key => {
        form[key as keyof Contact] = undefined;
    });

    emit("onClose");
}

const emit = defineEmits<{
    onSubmit: [form: Partial<Contact>],
    onClose: []
}>();

const props = defineProps<{
    data?: Partial<Contact>,
    error?: Partial<ContactError>,
    modalTitle?: string,
}>();


onMounted(() => {
    const { data } = props;

    if (data) {
        form.firstname = data.firstname;
        form.lastname = data.lastname;
        form.email = data.email;
        form.company = data.company;
        form.phone_number = data.phone_number
    }
});

defineExpose({
    showModal,
    close,
    changeLoadingState
});
</script>
