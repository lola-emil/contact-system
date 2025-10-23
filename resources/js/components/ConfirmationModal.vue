
<template>
    <dialog ref="confirmation-modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Confirmation</h3>
            <p class="py-4">{{ message }}</p>
            <div class="modal-action">
                <button @click="confirm()" class="btn btn-error">Yes</button>
                <form method="dialog">
                    <button class="btn">No</button>
                </form>
            </div>
        </div>
    </dialog>
</template>

<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';

const modal = useTemplateRef("confirmation-modal");

const message = ref("Are you sure?");

function showConfirmationModal(msg: string) {
    message.value = msg;
    modal.value?.showModal();
}

function closeModal() {
    modal.value?.close();
}

function confirm() {
    emit("onConfirm");
}

const emit = defineEmits<{
    onConfirm: []
}>()

defineExpose({
    showConfirmationModal,
    closeModal
});

</script>
