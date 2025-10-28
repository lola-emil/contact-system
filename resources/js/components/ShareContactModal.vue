<template>
    <dialog @close="onClose()" ref="share-contact-modal" class="modal">
        <div class="modal-box overflow-y-visible max-h-max">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">Share <span class="text-secondary">{{ selectedContact.email ?? "" }}</span>
                contact to:
            </h3>

            <!-- LIST OF RECIPIENTS -->
            <div class="flex flex-wrap gap-2 mt-5 w-full">
                <div v-for="(value, index) in recepients" class="badge badge-info">
                    {{ value }}
                    <span class="cursor-pointer" @click="removeRecepients(index)">✕</span>
                </div>
            </div>

            <form method="post" @submit.prevent="submit()" class="mt-2 flex flex-col gap-3">

                <fieldset class="fieldset join-item w-full">

                    <!-- AUTOCOMPLETE -->
                    <div class="w-full z-50 dropdown dropdown-center">
                        <TagInput ref="tagInput" v-model="form.emails" :items="props.items"></TagInput>
                    </div>

                    <p v-if="errorMessage" class="label text-error">{{ errorMessage }}</p>
                </fieldset>

                <select name="permission" v-model="form.permission" id="permission-selection" class="select w-full">
                    <option value="viewer" selected>Viewer</option>
                    <option value="editor">Editor</option>
                </select>

                <button class="btn btn-secondary">Share</button>
            </form>

            <div class="mt-5">
                <div v-if="shared.length > 0" role="alert" class="alert alert-success alert-soft flex flex-col">
                    <div class="w-full">
                        <span class="font-bold">Successfully shared contacts: {{ shared.length }}</span>
                    </div>
                </div>
                <br>
                <div v-if="skipped.length > 0" role="alert" class="alert alert-warning alert-soft flex flex-col">
                    <div class="w-full">
                        <span class="font-bold">Skipped contacts</span>
                    </div>
                    <div class="w-full">
                        <ul class="">
                            <li v-for="value in skipped">- {{ value.contact }} - {{ value.reason }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</template>

<script setup lang="ts">
import { ref, useTemplateRef, reactive, onMounted, onBeforeUnmount } from 'vue';
import { getContactById, shareMultipleContacts } from '@/services/contact.service';
import { Contact, MultiShareContactResponse, SharedContact, Skipped } from '@/types';

import TagInput from './TagInput.vue';

const modal = useTemplateRef("share-contact-modal");
// const emailInput = useTemplateRef("email-input");
const tagInput = ref<InstanceType<typeof TagInput> | null>();

const form = reactive<Partial<{
    emails: string[],
    permission: "viewer" | "editor"
}>>({
    permission: "viewer"
})

const selectedContact = ref<Partial<Contact>>({})
const recepients = ref<string[]>([]);
const errorMessage = ref<string | undefined>();

const shared = ref<SharedContact[]>([]);
const skipped = ref<Skipped[]>([]);

function showModal(contactId: number) {
    modal.value?.showModal();
    fetchContact(contactId);
}

const removeRecepients = (index: number) =>
    recepients.value.splice(index, 1);


function onClose() {
    recepients.value = [];
    form.emails = [];
    form.permission = "viewer";
    errorMessage.value = "";

    tagInput.value?.clearTags();

    skipped.value = [];
    shared.value = [];
}

function fetchContact(contactId: number) {
    getContactById(contactId)
        .then(val => {
            selectedContact.value = val;
        })
        .catch(err => {
        })
}

function submit() {
    shareMultipleContacts([selectedContact.value.id!], form.emails ?? [], form.permission)
        .then(response => {
            // emit("onSuccess", response);
            // modal.value?.close();
            skipped.value = response.skipped;
            shared.value = response.shared;

        }).catch(err => {
            const error = err.response.data.error;
            errorMessage.value = error;
        });
}

const close = () => modal.value?.close();


function handleKeydown(evt: KeyboardEvent) {
    if (evt.key == "Escape") {
        if (tagInput.value?.dropdownIsVisible()) {
            evt.preventDefault();
            tagInput.value.hideDropdown();
        } else {
            close();
        }
    }
}

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);

});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeydown);
});

const props = defineProps<{
    items: string[]
}>();

const emit = defineEmits<{
    onSuccess: [response: MultiShareContactResponse]
}>();

defineExpose({
    showModal
});
</script>
