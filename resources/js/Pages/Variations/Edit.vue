<script setup>
import InputError from "@/Components/InputError.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    variation: Object,
});

const form = useForm({
    name_en: props.variation.name.en,
    name_ar: props.variation.name.ar,
    variation_values: props.variation.variation_values.map((el) => {
        return {
            id: el.id,
            name_en: el.name.en,
            name_ar: el.name.ar,
            price: el.price,
        };
    }),
    _method: "PUT",
});

const addRow = () => {
    form.variation_values.push({
        id: "",
        name_en: "",
        name_ar: "",
        price: "",
    });
};

const removeRow = (index) => {
    if (form.variation_values[index].id) {
        if (confirm(t("Are you sure ?!"))) {
            router.visit(
                route("variation_values.delete", [
                    props.variation.id,
                    form.variation_values[index].id,
                ])
            );
        }
    } else {
        form.variation_values.splice(index, 1);
    }
};
</script>

<template>
    <Head :title="t('Edit Variation')" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Edit Variation") }}
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="bg-white p-6 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="mb-5 flex justify-between items-center">
                        <h2
                            class="font-semibold text-xl text-gray-800 leading-tight"
                        ></h2>
                        <Link
                            :href="route('variations.index')"
                            class="bg-green-600 text-white px-5 py-2 inline-block rounded hover:bg-green-500 duration-300"
                            >{{ t("All Variations") }}</Link
                        >
                    </div>
                    <form
                        @submit.prevent="
                            form.post(route('variations.update', variation.id))
                        "
                    >
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-2 gap-x-6">
                                <div>
                                    <label
                                        for="name_en"
                                        class="block text-sm font-medium text-gray-700"
                                        >{{ t("English Name") }}</label
                                    >
                                    <input
                                        type="text"
                                        name="name_en"
                                        id="name_en"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter Menu Item Name"
                                        v-model="form.name_en"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.name_en"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="name_ar"
                                        class="block text-sm font-medium text-gray-700"
                                        >{{ t("Arabic Name") }}</label
                                    >
                                    <input
                                        type="text"
                                        name="name_ar"
                                        id="name_ar"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter Menu Item Name"
                                        v-model="form.name_ar"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.name_ar"
                                    />
                                </div>
                            </div>
                            <table
                                class="border-collapse w-full border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm"
                            >
                                <thead class="bg-slate-50 dark:bg-slate-700">
                                    <tr>
                                        <th
                                            class="w-8/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                        >
                                            {{ t("Name") }}
                                        </th>
                                        <th
                                            class="w-3/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                        >
                                            {{ t("Price") }}
                                        </th>
                                        <th
                                            class="w-1/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                        >
                                            {{ t("Delete") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            value, index
                                        ) in form.variation_values"
                                    >
                                        <td
                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                        >
                                            <div
                                                class="grid grid-cols-2 gap-x-5"
                                            >
                                                <input
                                                    type="text"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    :placeholder="
                                                        t('Enter English Name')
                                                    "
                                                    v-model="value.name_en"
                                                />
                                                <input
                                                    type="text"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    :placeholder="
                                                        t('Enter Arabic Name')
                                                    "
                                                    v-model="value.name_ar"
                                                />
                                            </div>
                                        </td>
                                        <td
                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                        >
                                            <input
                                                type="number"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="t('Enter Price')"
                                                v-model="value.price"
                                                step="any"
                                            />
                                        </td>
                                        <td
                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                        >
                                            <button
                                                @click.prevent="
                                                    removeRow(index)
                                                "
                                                class="border p-2 bg-red-100 rounded-sm"
                                            >
                                                <svg
                                                    class="w-5 h-5 text-gray-800 dark:text-white"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                                    />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" @click="addRow">
                                <svg
                                    class="w-6 h-6 text-gray-800 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 12h14m-7 7V5"
                                    />
                                </svg>
                            </button>
                            <div class="flex gap-3">
                                <button
                                    class="bg-green-600 text-white px-10 py-2 rounded-sm duration-300 hover:bg-green-500"
                                >
                                    {{ t("Update") }}
                                </button>
                                <Link
                                    :href="route('variations.index')"
                                    class="bg-gray-300 text-gray-900 px-10 py-2 rounded-sm duration-300 hover:bg-gray-200"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M16.9057 5.68009L5.68009 16.9057C4.62644 15.5506 4 13.8491 4 12C4 7.58172 7.58172 4 12 4C13.8491 4 15.5506 4.62644 16.9057 5.68009ZM7.0943 18.3199L18.3199 7.0943C19.3736 8.44939 20 10.1509 20 12C20 16.4183 16.4183 20 12 20C10.1509 20 8.44939 19.3736 7.0943 18.3199ZM12 2C6.47715 2 2 6.47715 2 12C2 17.5223 6.47771 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47771 17.5223 2 12 2Z"
                                        ></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
