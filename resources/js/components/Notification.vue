<template>
    <div class="dropdown dropdown-end">
        <div class="indicator">
            <div v-if="props.items.length > 0" class="indicator-item badge badge-secondary">{{ props.items.length }}</div>
                    <div tabindex="0" role="button" aria-label="Notification" class="btn btn-square m-1 btn-ghost btn-sm">
            <span>
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M9.042 19.003h5.916a3 3 0 0 1-5.916 0Zm2.958-17a7.5 7.5 0 0 1 7.5 7.5v4l1.418 3.16A.95.95 0 0 1 20.052 18h-16.1a.95.95 0 0 1-.867-1.338l1.415-3.16V9.49l.005-.25A7.5 7.5 0 0 1 12 2.004Z"
                        fill="#ffffff" />
                </svg>
            </span>
        </div>
        </div>

        <ul class="list dropdown-content w-sm lg:w-lg bg-base-200 rounded-box shadow-md">

            <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">Notifications</li>

            <li v-if="props.items.length == 0" class="list-row flex justify-center">
                <!-- <div><img class="size-10 rounded-box" src="https://img.daisyui.com/images/profile/demo/1@94.webp" /></div> -->
                <div class="flex flex-col justify-center items-center">
                    <div class="text-xs uppercase font-semibold opacity-60">No notifications yet</div>
                    <div class="opacity-60">Nothing to see here… for now.</div>
                </div>
            </li>

            <li v-for="value in props.items" class="list-row flex justify-between hover:bg-base-100">
                <!-- <div><img class="size-10 rounded-box" src="https://img.daisyui.com/images/profile/demo/1@94.webp" /></div> -->
                <div>
                    <div class="text-xs uppercase font-semibold opacity-60">
                        {{ value.owner }}
                    </div>
                    <div>Shared a Contact for <span class="text-secondary font-bold">
                            {{ value.contact_name }}
                        </span></div>
                </div>

                <div class="flex gap-2">
                    <button @click="onAccept(value.contact_id)"
                        class="btn btn-sm btn-outline btn-success">Accept</button>
                    <button @click="onIgnore(value.contact_id)" class="btn btn-sm btn-outline btn-error">Ignore</button>
                </div>
            </li>
        </ul>
    </div>

</template>

<script setup lang="ts">
import { SharedContact } from '@/types';


function onAccept(contactId: number) {
    emit("onAccept", contactId);
}

function onIgnore(contactId: number) {
    emit("onIgnore", contactId);
}

const props = defineProps<{
    items: SharedContact[]
}>();

const emit = defineEmits<{
    onAccept: [contactId: number],
    onIgnore: [contactId: number]
}>();

</script>
