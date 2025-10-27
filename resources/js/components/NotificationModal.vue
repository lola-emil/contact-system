<template>
    <dialog ref="notif-modal" id="my_modal_2" class="modal">
        <div class="modal-box">
            <div class="flex justify-between items-center">
                <h3 class="font-bold">Notifications</h3>
                <div>
                    <button class="btn btn-ghost">
                        <span>Mark all as read</span>
                        <CheckCheck :size="18" />
                    </button>

                </div>
            </div>
            <!-- <hr class="bg-base-300"> -->
            <div class="h-px w-full bg-base-content/30 my-2"></div>
            <div>
                <ul class="bg-base-100 rounded-box">

                    <li v-for="value in props.items" class=" p-3 flex justify-between items-center hover:bg-base-300 cursor-pointer">
                        <div>
                            <div class="">{{ value.title }}</div>
                            <div class="text-xs font-semibold opacity-60">{{ value.message }}</div>
                        </div>

                        <div>
                            <div class="h-2 w-2 rounded-full bg-primary">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>

<script setup lang="ts">
import { onMounted, useTemplateRef } from "vue";
import { CheckCheck } from "lucide-vue-next";
import { Notifications } from "@/types";

const notifModal = useTemplateRef("notif-modal");

function showModal() {
    notifModal.value?.showModal();
}

function closeModal() {
    notifModal.value?.close();
}

const props = defineProps<{
    items: Notifications[]
}>()

defineExpose({
    showModal,
    closeModal
});

</script>