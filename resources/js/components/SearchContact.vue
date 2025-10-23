<template>
    <form @submit.prevent="submit" method="get" class="join w-full">
        <input type="text" v-model="form.query" class="input join-item w-full lg:w-xs" aria-label="Search Contact"
            name="search_query">
        <button class="btn btn-info rounded-r-md">Search</button>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const form = useForm<Partial<{
    query: string
}>>({});

function submit() {
    form.get(`/contacts?query=${form.query ?? ""}`);
}

onMounted(() => {
    form.query = props.defaultSearch
});

const props = defineProps<{
    defaultSearch?: string
}>();

</script>
