
<script setup lang="ts">
import { Link, useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    firstname: "",
    lastname: "",
    email: "",
    password: "",
    confirmPassword: ""
});

const signUp = () => form.post("/auth/sign-up");

</script>

<template>

    <Head>
        <title>Register</title>
    </Head>

    <div class="h-screen w-full flex flex-col justify-center items-center">
        <p class="text-xl font-semibold">Sign Up</p>
        <form action="" @submit.prevent="signUp()" class="w-sm">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Firstname</legend>
                <input type="text" v-model="form.firstname" class="input w-full" />
                <p class="label text-error" v-if="form.errors.firstname">{{ form.errors.firstname }}</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Lastname</legend>
                <input type="text" v-model="form.lastname" class="input w-full" />
                <p class="label text-error" v-if="form.errors.lastname">{{ form.errors.lastname }}</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Email</legend>
                <input type="text" v-model="form.email" class="input w-full" />
                <p class="label text-error" v-if="form.errors.email">{{ form.errors.email }}</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Password</legend>
                <input type="password" v-model="form.password" class="input w-full" />
                <p class="label text-error" v-if="form.errors.password">{{ form.errors.password }}</p>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Confirm Password</legend>
                <input type="password" v-model="form.confirmPassword" class="input w-full" />
                <p class="label text-error" v-if="form.errors.confirmPassword">{{ form.errors.confirmPassword }}</p>
            </fieldset>

            <br>

            <button class="btn btn-primary w-full">Sign Up</button>

            <br>
            <br>

            <p>
                Already have an account?
                <Link href="/auth/login" class="link link-info font-semibold">Sign In</Link>
            </p>
        </form>
    </div>
</template>

