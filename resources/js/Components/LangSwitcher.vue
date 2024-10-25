<template>
    <button
        class="text-gray-500 flex items-center h-7 pt-1 pb-2 px-2 bg-gray-100 absolute z-10 top-5 start-5 ms-10"
        @click="changeLanguage"
    >
        {{ language == "en" ? "English" : "العربية" }}
    </button>
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

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
