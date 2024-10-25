<script setup>
import Pagination from "@/Components/Pagination.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";

const props = defineProps({
    branches: Object,
});

const selected_ids = ref([]);

const toggleAll = (e) => {
    if (e.target.checked) {
        selected_ids.value = props.branches.data.map((branch) => branch.id);
    } else {
        selected_ids.value = [];
    }
};

const deleteSelected = () => {
    Swal.fire({
        title: t("Are you sure?"),
        text: t("You won't be able to revert this!"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: t("Yes, delete it!"),
        cancelButtonText: t("Cancel"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route("branches.delete_selected"), {
                method: "post",
                data: {
                    ids: selected_ids.value,
                },
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: t("Deleted!"),
                        text: t("Your item has been deleted."),
                        icon: "success",
                    });
                },
            });
        } else {
            selected_ids.value = [];
        }
    });
};

const deleteItem = (id) => {
    selected_ids.value.push(id);
    deleteSelected();
};
</script>

<template>
    <Head :title="t('Branches')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Branches") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <div
                        class="pb-4 mb-3 bg-white dark:bg-gray-900 flex flex-col md:flex-row md:items-center justify-between"
                    >
                        <div class="relative mb-2 md:mb-0"></div>
                        <div class="flex">
                            <button
                                class="bg-red-600 text-white px-3 mx-2 inline-block py-2 rounded hover:bg-red-500 duration-300"
                                v-if="selected_ids.length > 0"
                                @click="deleteSelected"
                            >
                                <svg
                                    class="h-6 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z"
                                    ></path>
                                </svg>
                            </button>
                            <Link
                                :href="route('branches.create')"
                                class="bg-green-600 text-white px-5 py-2 inline-block rounded hover:bg-green-500 duration-300"
                                >{{ t("Add New") }}</Link
                            >
                        </div>
                    </div>

                    <div
                        class="relative overflow-x-auto shadow-md sm:rounded-lg mb-4"
                    >
                        <table
                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                        >
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                            >
                                <tr>
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            <input
                                                id="checkbox-all-search"
                                                type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                @click="toggleAll"
                                            />
                                            <label
                                                for="checkbox-all-search"
                                                class="sr-only"
                                                >checkbox</label
                                            >
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Address") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Phone") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Action") }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    v-for="branch in branches.data"
                                    :key="branch.id"
                                >
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input
                                                id="checkbox-table-search-1"
                                                type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                :value="branch.id"
                                                v-model="selected_ids"
                                            />
                                            <label
                                                for="checkbox-table-search-1"
                                                class="sr-only"
                                                >checkbox</label
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ branch.address }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ branch.phone }}
                                    </td>
                                    <td class="px-6 py-4 flex">
                                        <Link
                                            :href="
                                                route(
                                                    'branches.edit',
                                                    branch.id
                                                )
                                            "
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-1"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                            >
                                                <path
                                                    d="M15.7279 9.57627L14.3137 8.16206L5 17.4758V18.89H6.41421L15.7279 9.57627ZM17.1421 8.16206L18.5563 6.74785L17.1421 5.33363L15.7279 6.74785L17.1421 8.16206ZM7.24264 20.89H3V16.6473L16.435 3.21231C16.8256 2.82179 17.4587 2.82179 17.8492 3.21231L20.6777 6.04074C21.0682 6.43126 21.0682 7.06443 20.6777 7.45495L7.24264 20.89Z"
                                                ></path>
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteItem(branch.id)"
                                            class="font-medium text-red-600 dark:text-blue-500 hover:underline mx-1"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                            >
                                                <path
                                                    d="M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z"
                                                ></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :pages="branches.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
