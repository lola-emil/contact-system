<template>
    <div class="toast toast-top toast-center">

        <div v-for="(toast, index) in toasts" class="alert" :class="{
            'alert-success': toast.type === 'success',
            'alert-error': toast.type === 'error',
        }">
            <span v-html="toast.message"></span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

interface Toast {
    id: number
    message: string
    type: 'success' | 'error'
}


const toasts = ref<Toast[]>([]);

let counter = 0

function showToast(message: string, type: 'success' | 'error' = 'success', duration = 3000) {
    const id = counter++
    toasts.value.push({ id, message, type })

    setTimeout(() => {
        toasts.value = toasts.value.filter(toast => toast.id !== id)
    }, duration)
}

defineExpose({
    showToast
});

</script>
