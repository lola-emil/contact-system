<template>
    <Head>
        <title>Profile</title>
    </Head>

    <Navbar />

    <br>
    <br>

    <main class="container mx-auto px-5 lg:px-0">
        <div class="flex flex-col gap-5 items-center justify-center">
            <p class="text-2xl">Profile Settings</p>
            <div class="w-sm">
                <br>
                <p class="text-lg">Personal Info</p>
                <fieldset class="fieldset">
                    <label class="fieldset-legend">Firstname</label>
                    <div class="join join-horizontal">
                        <input v-model="firstname" type="text" class="input w-full">
                        <button class="btn btn-square btn-info btn-soft"
                            :disabled="isFirstnameUnchangedOrInvalid || firstnameIsUpdating" @click="updateFirstname">
                            <div v-if="firstnameIsUpdating" class="loading loading-spinner loading-xs"></div>
                            <Check v-else :size="17"></Check>
                        </button>
                    </div>
                    <p class="label text-success">{{ firstnameMessage }}</p>

                    <label class="fieldset-legend">Lastname</label>
                    <div class="join join-horizontal">
                        <input v-model="lastname" type="text" class="input w-full">
                        <button class="btn btn-square btn-info btn-soft"
                            :disabled="isLastnameUnchangedOrInvalid || lastnameIsUpdating" @click="updateLastname">
                            <div v-if="lastnameIsUpdating" class="loading loading-spinner loading-xs"></div>
                            <Check v-else :size="17"></Check>
                        </button>
                    </div>
                    <p class="label text-success">{{ lastnameMessage }}</p>


                </fieldset>
                <br>

                <p class="text-lg">Account</p>

                <fieldset class="fieldset">

                    <label class="fieldset-legend">Email</label>
                    <div class="join join-horizontal">
                        <input v-model="email" @input="checkEmail" type="text" class="input w-full">

                        <button class="btn btn-square btn-info btn-soft"
                            :class="{ 'pointer-events-none': checkingEmail || isUpdating }"
                            :disabled="isEmailUnchangedOrInvalid || isUpdating" @click="updateEmail">
                            <span v-if="checkingEmail || isUpdating" class="loading loading-spinner loading-xs"></span>
                            <Check v-else :size="17"></Check>
                        </button>
                    </div>
                    <p class="label">{{ checkingEmail ? "Checking email availablity/validity" : emailMessage ?? "" }}
                    </p>

                </fieldset>
                <br>
                <form class="fieldset" @submit.prevent="submitUpdatePasswordForm">
                    <label class="fieldset-legend">Update password</label>

                    <input v-model="updatePasswordForm.current_password" type="password" class="input w-full"
                        placeholder="Current Password">
                    <p class="label text-error">{{ updatePasswordErrors.current_password ?? "" }}</p>
                    <input v-model="updatePasswordForm.new_password" type="password" class="input w-full"
                        placeholder="New Password">
                    <p class="label text-error">{{ updatePasswordErrors.new_password ?? "" }}</p>

                    <button class="btn btn-info btn-soft" :disabled="updatePasswordIsLoading">
                        <div v-if="updatePasswordIsLoading" class="loading loading-spinner loading-xs"></div>
                        <span v-else>
                            Update Password
                        </span>
                    </button>
                </form>
            </div>

            <div>
            </div>

        </div>
    </main>

    <Toast ref="toast" />
</template>


<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import Toast from '@/components/Toast.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';
import { Check } from "lucide-vue-next";
import { checkEmailAvailability, updatePassword, updateProfile } from '@/services/user.service';

const toast = ref<InstanceType<typeof Toast>>();

const updatePasswordIsLoading = ref<boolean>(false);

const updatePasswordForm = reactive({
    current_password: "",
    new_password: ""
});
const updatePasswordErrors = reactive({
    current_password: "",
    new_password: ""
});


async function submitUpdatePasswordForm() {
    updatePasswordIsLoading.value = true;

    const [res, err] = await updatePassword(updatePasswordForm);


    if (err) {
        const error = (<{
            errors: {
                current_password?: string[]
                new_password?: string[]
            },
            message: string
        }>err.response?.data).errors;

        updatePasswordErrors.current_password = error.current_password ? error.current_password[0] : "";
        updatePasswordErrors.new_password = error.new_password ? error.new_password[0] : "";
    }


    // If succes ni siya
    if (res) {
        updatePasswordErrors.new_password = "";
        updatePasswordErrors.current_password = "";

        updatePasswordForm.new_password = "";
        updatePasswordForm.current_password = "";

        toast.value?.showToast(res.message, "success");
    }


    updatePasswordIsLoading.value = false;
}

const page = usePage();

const originalFirstname = ref<string | undefined>(page.props.profile?.firstname);
const firstname = ref<string | undefined>();
const firstnameIsUpdating = ref<boolean>(false);
const firstnameMessage = ref<string | undefined>();

const isFirstnameUnchangedOrInvalid = computed(() => firstname.value === originalFirstname.value || firstname.value == "")


async function updateFirstname() {
    firstnameIsUpdating.value = true;

    const res = await updateProfile({ firstname: firstname.value });

    originalFirstname.value = res.input.firstname;
    firstname.value = res.input.firstname;

    firstnameMessage.value = "Firstname updated."

    setTimeout(() => firstnameMessage.value = "", 1000);

    firstnameIsUpdating.value = false;
}

const originalLastname = ref<string | undefined>(page.props.profile?.lastname);
const lastname = ref<string | undefined>();
const lastnameIsUpdating = ref<boolean>(false);
const lastnameMessage = ref<string | undefined>();

const isLastnameUnchangedOrInvalid = computed(() => lastname.value === originalLastname.value || lastname.value == "");


async function updateLastname() {
    lastnameIsUpdating.value = true;

    const res = await updateProfile({ lastname: lastname.value });

    originalLastname.value = res.input.lastname;
    lastname.value = res.input.lastname;

    lastnameMessage.value = "Lastname updated.";

    setTimeout(() => lastnameMessage.value = "", 1000);

    lastnameIsUpdating.value = false;
}


const originalEmail = ref<string | undefined>(page.props.profile?.email);
const email = ref<string | undefined>('');
const checkingEmail = ref(false);
const emailIsInvalid = ref(false);
const emailMessage = ref<string | undefined>();
const isUpdating = ref<boolean>(false);

const isEmailUnchangedOrInvalid = computed(() => {
    return email.value === originalEmail.value || emailIsInvalid.value || checkingEmail.value;
});

const checkEmail = async () => {
    checkingEmail.value = true;
    const [res, err] = await checkEmailAvailability(email.value ?? "");

    checkingEmail.value = false;
    emailMessage.value = ''; // Clear previous messages

    if (err?.status === 422) {
        emailMessage.value = (<{ message: string }>err.response?.data).message || 'Invalid email format';
        emailIsInvalid.value = true;
        return;
    }

    if (res?.exists && res.email !== page.props.profile?.email) {
        emailMessage.value = 'Email already taken';
        emailIsInvalid.value = true;
        return;
    }

    emailIsInvalid.value = false;
};

async function updateEmail() {
    isUpdating.value = true;

    const res = await updateProfile({
        email: email.value
    });

    emailMessage.value = "Email updated.";

    // Update ang state sa email
    email.value = res.input.email;
    originalEmail.value = res.input.email;

    setTimeout(() => emailMessage.value = "", 1000);

    isUpdating.value = false;
}

onMounted(() => {
    const profile = page.props.profile;

    firstname.value = profile?.firstname;
    lastname.value = profile?.lastname;
    email.value = profile?.email;
});
</script>
