<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import LangSwitcher from "@/Components/LangSwitcher.vue";
import { t } from "@/Composables/trans";
import Navbar from "@/Components/Navbar.vue";
import Hero from "@/Components/Hero.vue";

const props = defineProps({
    restaurant: {
        type: Array,
    },
    settings: {
        type: Array,
    },
    type: {
        type: String,
    },
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    restaurant_id: props.restaurant.id,
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("customer.register", [props.restaurant.code, props.type]), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="t('Register')" />
    <Navbar :restaurant="restaurant" :type="type" :settings="settings" />
    <Hero :title="t('Register')" />
    <div
        class="mt-14 mb-14 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md mx-auto sm:rounded-lg"
    >
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" :value="t('Name')" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" :value="t('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" :value="t('Phone')" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="t('Password')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="t('Confirm Password')" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="current-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t("Register") }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
