<template>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">

        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>
                        <input aria-label="Select all rows" @change="selectAll($event)" type="checkbox" class="checkbox checkbox-xs checkbox-primary"
                            ref="selectAllCheckbox">
                    </th>
                    <th>#</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Phone Number</th>
                    <th>Email Address</th>
                    <th align="center">Owner</th>
                    <th align="center">Permission</th>
                    <th align="right"></th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(contact, index) in props.contacts">
                    <td>
                        <input type="checkbox" :value="contact.id" v-model="selected"
                            aria-label="Select contact with ID {{ contact.name }}"
                            class="checkbox checkbox-secondary checkbox-xs" />
                    </td>
                    <th>{{ (index + 1) + (props.startingIndex ?? 0) }}</th>

                    <td v-html="contact.name"></td>
                    <td v-html="contact.company"></td>
                    <td v-html="contact.phone_number"></td>
                    <td v-html="contact.email"></td>
                    <td align="center" v-html="contact.owner_name"></td>
                    <td align="center" v-html="contact.permission"></td>
                    <td align="right">
                        <div class="join">
                            <button
                                @click="contactIsOwned(contact.owner_id, page.props.auth.user.id) ? share(contact.id) : null"
                                class="btn join-item btn-sm btn-info btn-soft"
                                :disabled="!contactIsOwned(contact.owner_id, page.props.auth.user.id)">Share</button>
                            <button
                                @click="permittedToEdit(contact, page.props.auth.user.id) ? onEdit(contact.id) : null"
                                class="btn btn-sm join-item btn-success btn-soft"
                                :disabled="!permittedToEdit(contact, page.props.auth.user.id)">Edit</button>
                            <button class="btn join-item btn-error btn-sm btn-soft"
                                @click="onDelete(contact.id)">Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="props.contacts.length == 0" class="w-full flex justify-center p-5">
            <p class="text-info font-semibold uppercase opacity-75">No contacts.</p>
        </div>
    </div>

</template>



<script setup lang="ts">
import { ContactTableFormat } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { useTemplateRef, watch } from 'vue';

const selected = defineModel({
    type: Array,
    default: () => []
})

const selectAllCheckbox = useTemplateRef("selectAllCheckbox");

const page = usePage();

const props = defineProps<{
    contacts: ContactTableFormat[],
    startingIndex?: number
}>()

const emit = defineEmits<{
    share: [contactId: number],
    onDelete: [contactId: number],
    onEdit: [contactId: number]
}>();

const share = (contactId: number) => emit("share", contactId)
const onDelete = (contactId: number) => emit("onDelete", contactId);
const onEdit = (contactId: number) => emit("onEdit", contactId);
const contactIsOwned = (ownerId: number, userId: number) => ownerId == userId;

const permittedToEdit = (contact: ContactTableFormat, userId: number) => {
    if (contact.permission == "editor")
        return true;

    if (contact.owner_id == userId)
        return true;

    return false;
};

function selectAll(evt: Event) {
    const target = <HTMLInputElement>evt.target;

    if (target.checked) {
        selected.value = props.contacts.map(val => val.id);
    } else {
        selected.value = [];
    }
}

function clearSelectedRows() {
    selected.value = [];
}

watch(
    [() => selected.value, () => props.contacts],
    ([newSelected, newContacts]) => {
        if (!selectAllCheckbox.value) return;

        const total = newContacts.length;
        const selectedCount = newSelected.length;

        if (selectedCount === 0) {
            selectAllCheckbox.value.indeterminate = false;
            selectAllCheckbox.value.checked = false;
        } else if (selectedCount === total) {
            selectAllCheckbox.value.indeterminate = false;
            selectAllCheckbox.value.checked = true;
        } else {
            selectAllCheckbox.value.indeterminate = true;
        }
    },
    { immediate: true }
);


defineExpose({
    clearSelectedRows
})

</script>

