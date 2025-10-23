<template>
    <dialog @close="onClose()" ref="share-contact-modal" class="modal">
        <div class="modal-box overflow-y-visible max-h-max">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">Share these
                contacts to:
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
                        <TagInput ref="tagInput" :items="props.items" v-model="form.emails" />
                    </div>

                    <p v-if="errorMessage" class="label text-error">{{ errorMessage }}</p>
                </fieldset>

                <select name="permission" v-model="form.permission" class="select w-full">
                    <option value="viewer" selected>Viewer</option>
                    <option value="editor">Editor</option>
                </select>

                <button class="btn btn-secondary">Share</button>
            </form>
        </div>
    </dialog>
</template>


<script setup lang="ts">
import { ref, useTemplateRef, reactive, onMounted, onBeforeUnmount } from 'vue';
import TagInput from './TagInput.vue';
import { shareMultipleContacts } from '@/services/contact.service';
import { MultiShareContactResponse } from '@/types';

const modal = useTemplateRef("share-contact-modal");
const contactIds = ref<number[]>([]);
const tagInput = ref<InstanceType<typeof TagInput> | null>();

const form = reactive<Partial<{
    emails: string[],
    permission: "viewer" | "editor"
}>>({
    permission: "viewer"
});

const recepients = ref<string[]>([]);
const errorMessage = ref<string | undefined>();


function showModal(selectedContactIds: number[]) {
    modal.value?.showModal();
    contactIds.value = selectedContactIds;
}

function removeRecepients(index: number) {
    recepients.value.splice(index, 1);
}

const close = () => modal.value?.close();

function onClose() {
    // emails.value = [];
    recepients.value = [];
    form.emails = [];
    form.permission = "viewer";
    errorMessage.value = "";

    tagInput.value?.clearTags();
}

async function submit() {
    const response = await shareMultipleContacts(contactIds.value, form.emails ?? [], form.permission)
    emit("onSuccess", response);
}

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

const emit = defineEmits<{
    onSuccess: [response: MultiShareContactResponse]
}>();

const props = defineProps<{
    items: string[]
}>();

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeydown);
});

defineExpose({
    showModal,
    close
});

</script>
