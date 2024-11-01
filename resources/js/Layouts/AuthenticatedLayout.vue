<script setup>
import { computed, onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { t } from "@/Composables/trans";

const showingNavigationDropdown = ref(false);

const locales = computed(() => usePage().props.locales);
const index = computed(
    () =>
        locales.value.findIndex((value) => value == usePage().props.locale) + 1
);
const language = computed(
    () => locales.value[index.value % locales.value.length]
);

const changeLanguage = () => {
    router.visit(route("change_language", language.value), {
        onSuccess: () => {
            document.querySelector("html").lang =
                language.value == "en" ? "ar" : "en";
            document.querySelector("html").dir =
                language.value == "en" ? "rtl" : "ltr";
        },
    });
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <img
                                        class="w-20 h-12 object-contain"
                                        :src="$page.props.settings.logo"
                                        alt=""
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    {{ t("Dashboard") }}
                                </NavLink>
                                <NavLink
                                    :href="route('categories.index')"
                                    :active="$page.url.includes('categories')"
                                >
                                    {{ t("Categories") }}
                                </NavLink>
                                <NavLink
                                    :href="route('menu-items.index')"
                                    :active="$page.url.includes('menu-items')"
                                >
                                    {{ t("Menu Items") }}
                                </NavLink>
                                <!-- <NavLink
                                    :href="route('advertisements.index')"
                                    :active="
                                        $page.url.includes('advertisements')
                                    "
                                >
                                    {{ t("Advertisements") }}
                                </NavLink> -->
                                <NavLink
                                    :href="route('branches.index')"
                                    :active="$page.url.includes('branches')"
                                >
                                    {{ t("Branches") }}
                                </NavLink>
                                <NavLink
                                    :href="route('settings')"
                                    :active="route().current('settings')"
                                >
                                    {{ t("Settings") }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3 flex items-center">
                                <button
                                    class="text-gray-500 flex items-center h-7 pt-1 pb-2 px-2 bg-gray-100"
                                    @click="changeLanguage"
                                >
                                    {{
                                        language == "en" ? "English" : "العربية"
                                    }}
                                </button>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            {{ t("Profile") }}
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            {{ t("Log Out") }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            {{ t("Dashboard") }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('categories.index')"
                            :active="$page.url.includes('categories')"
                        >
                            {{ t("Categories") }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('menu-items.index')"
                            :active="$page.url.includes('menu-items')"
                        >
                            {{ t("Menu Items") }}
                        </ResponsiveNavLink>
                        <!-- <ResponsiveNavLink
                            :href="route('advertisements.index')"
                            :active="$page.url.includes('advertisements')"
                        >
                            {{ t("Advertisements") }}
                        </ResponsiveNavLink> -->
                        <ResponsiveNavLink
                            :href="route('branches.index')"
                            :active="$page.url.includes('branches')"
                        >
                            {{ t("Branches") }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings')"
                            :active="route().current('settings')"
                        >
                            {{ t("Settings") }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ t("Profile") }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                {{ t("Log Out") }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
[dir="rtl"] .space-x-8 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-left: calc(2rem * var(--tw-space-x-reverse));
    margin-right: calc(2rem * calc(1 - var(--tw-space-x-reverse)));
}
</style>
