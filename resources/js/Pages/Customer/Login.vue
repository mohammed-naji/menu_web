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
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("customer.login", [props.restaurant.code, props.type]), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="t('Login')" />
    <Navbar :restaurant="restaurant" :type="type" :settings="settings" />
    <Hero :title="t('Login')" />
    <div
        class="mt-14 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md mx-auto sm:rounded-lg"
    >
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="t('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
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

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">{{
                        t("Remember me")
                    }}</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    {{ t("Forgot your password?") }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t("Login") }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
