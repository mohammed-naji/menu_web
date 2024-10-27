<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import LangSwitcher from "@/Components/LangSwitcher.vue";
import { t } from "@/Composables/trans";

const props = defineProps({
    restaurant: {
        type: Array,
    },
    settings: {
        type: Array,
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
    form.post(route("customer.register", props.restaurant.code), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="t('Register')" />
    <nav id="topnav" class="defaultscroll is-sticky">
        <div class="flex items-center justify-between py-2 px-10">
            <!-- Logo container-->
            <Link class="logo" :href="route('menu', [restaurant.code, type])">
                <img
                    class="w-20 h-12 object-contain"
                    :src="settings.logo"
                    alt=""
                />
            </Link>
            <!-- End Logo container-->

            <ul class="navigation-menu flex gap-x-6 nav-light justify-end">
                <li>
                    <Link
                        class="sub-menu-item"
                        :href="route('customer.login', restaurant.code)"
                        >{{ t("Login") }}</Link
                    >
                </li>
                <li>
                    <Link
                        class="sub-menu-item"
                        :href="route('customer.register', restaurant.code)"
                        >{{ t("Register") }}</Link
                    >
                </li>
            </ul>
        </div>
        <!--end container-->
    </nav>
    <!--end header-->
    <!-- End Navbar -->
    <!-- Start Hero -->
    <section
        class="relative md:py-16 py-14 bg-[url('../../assets/images/bg/pages.jpg')] bg-no-repeat bg-bottom bg-cover"
    >
        <LangSwitcher />
        <div
            class="absolute inset-0 bg-gradient-to-t from-slate-900 to-slate-900/70"
        ></div>
        <div class="container relative">
            <div class="grid grid-cols-1 text-center mt-6">
                <div>
                    <h5
                        class="md:text-4xl text-3xl md:leading-normal leading-normal font-medium text-white mb-0"
                    >
                        {{ t("Register") }}
                    </h5>
                </div>
            </div>
        </div>
    </section>
    <!--end section-->
    <div class="relative">
        <div
            class="shape absolute sm:-bottom-px -bottom-[2px] start-0 end-0 overflow-hidden z-1 text-white dark:text-slate-900"
        >
            <svg
                class="w-full h-auto scale-[2.0] origin-top"
                viewBox="0 0 2880 48"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z"
                    fill="currentColor"
                ></path>
            </svg>
        </div>
    </div>
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
