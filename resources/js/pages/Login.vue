<template>
    <Head>
        <title>Login</title>
    </Head>

    <div class="toast toast-top toast-center">
        <div class="alert alert-success" v-if="successFlashMessage">
            <span>{{ successFlashMessage }}</span>
        </div>
    </div>

    <div class="h-screen w-full flex flex-col justify-center items-center">
        <p class="text-xl font-semibold">Sign In</p>
        <form @submit.prevent="signIn()" class="w-xs">
            <fieldset class="fieldset">
                <legend id="email" class="fieldset-legend">Email</legend>
                <input type="text" aria-labelledby="email" v-model="loginForm.email" class="input w-full" />
                <p class="label text-error" v-if="loginForm.errors.email">{{ loginForm.errors.email }}</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend id="password" class="fieldset-legend">Password</legend>
                <input type="password" aria-labelledby="password" v-model="loginForm.password" class="input w-full" />
                <p class="label"></p>
            </fieldset>

            <button class="btn btn-primary w-full">Sign In</button>
            <br>
            <br>
            <p>
                Don't have an account yet?
                <Link href="/auth/sign-up" class="link link-info font-semibold">Sign Up</Link>
            </p>
        </form>

    </div>
</template>

<script setup lang="ts">
import { Link, useForm, usePage, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';


interface Flash {
    success?: string;
    error?: string;
}

const page = usePage();

const loginForm = useForm({
    email: "",
    password: ""
});

function signIn() {
    loginForm.post("/auth/login");
}

const successFlashMessage = ref<string | undefined>();

watch(() => (<Flash>page.props.flash).success, (newVal) => {
    if (newVal)
        successFlashMessage.value = newVal;

    setTimeout(() => {
        successFlashMessage.value = undefined;
    }, 3000);
}, { immediate: true });
</script>
