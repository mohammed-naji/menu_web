<script setup>
import Pagination from "@/Components/Pagination.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";

const props = defineProps({
    menuItems: Array,
    categories: Array,
});

const q = ref("");
const category = ref("all");
const selected_ids = ref([]);

const search = () => {
    router.visit(route("menu-items.index"), {
        method: "get",
        data: {
            q: q.value,
            category: category.value,
        },
        preserveScroll: true,
        preserveState: true,
    });
};

const toggleAll = (e) => {
    if (e.target.checked) {
        selected_ids.value = props.menuItems.data.map(
            (category) => category.id
        );
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
            router.visit(route("menu-items.delete_selected"), {
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

const changeActive = (id, status) => {
    router.visit(route("menu-items.update_status", id), {
        method: "post",
        data: {
            status: status == 0 ? 1 : 0,
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="t('Menu Items')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Menu Items") }}
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
                        <div class="flex gap-2">
                            <div class="relative mb-2 md:mb-0">
                                <label for="table-search" class="sr-only"
                                    >Search</label
                                >
                                <div
                                    class="absolute inset-y-2 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                        />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="table-search"
                                    class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    :placeholder="t('Search for items')"
                                    v-model="q"
                                    @keyup="search"
                                />
                            </div>
                            <select
                                v-model="category"
                                @change="search"
                                class="pt-2 rtl:ps-7 text-sm text-gray-900 border border-gray-300 rounded-lg w-32 bg-gray-50"
                            >
                                <option value="all">{{ t("All") }}</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name[$page.props.locale] }}
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="bg-red-600 text-white px-3 inline-block py-2 rounded hover:bg-red-500 duration-300"
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
                                :href="route('variations.index')"
                                class="bg-purple-600 text-white px-5 py-2 inline-block rounded hover:bg-purple-500 duration-300"
                                >{{ t("Variations") }}</Link
                            >
                            <Link
                                :href="route('menu-items.create')"
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
                                        {{ t("Image") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Name") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Price") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Category") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Active") }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ t("Action") }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    v-for="category in menuItems.data"
                                    :key="category.id"
                                >
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input
                                                id="checkbox-table-search-1"
                                                type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                :value="category.id"
                                                v-model="selected_ids"
                                            />
                                            <label
                                                for="checkbox-table-search-1"
                                                class="sr-only"
                                                >checkbox</label
                                            >
                                        </div>
                                    </td>
                                    <th
                                        scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        <img
                                            :src="category.image"
                                            class="w-24 h-20 object-cover border p-1 rounded"
                                            alt=""
                                        />
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ category.name[$page.props.locale] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ category.price }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{
                                            category.category.name[
                                                $page.props.locale
                                            ]
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <label
                                            class="inline-flex items-center cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                value=""
                                                class="sr-only peer"
                                                :checked="category.active"
                                                @click="
                                                    changeActive(
                                                        category.id,
                                                        category.active
                                                    )
                                                "
                                            />
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-0 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"
                                            ></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex">
                                            <Link
                                                :href="
                                                    route(
                                                        'menu-items.edit',
                                                        category.id
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
                                                @click="deleteItem(category.id)"
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
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :pages="menuItems.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
