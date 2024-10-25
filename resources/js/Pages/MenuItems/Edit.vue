<script setup>
import InputError from "@/Components/InputError.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    menuItem: Object,
    categories: Array,
    variations: Array,
    variation_options: Array,
});

const form = useForm({
    name_en: props.menuItem.name.en,
    name_ar: props.menuItem.name.ar,
    description_en: props.menuItem.description.en,
    description_ar: props.menuItem.description.ar,
    image: props.menuItem.image,
    category_id: props.menuItem.category_id,
    price: props.menuItem.price,
    discount: props.menuItem.discount,
    order: props.menuItem.order,
    has_size_variation:
        props.menuItem.size_variations.length > 0 ? true : false,
    size_variations: props.menuItem.size_variations.map((el) => {
        return {
            id: el.id,
            name_en: el.name.en,
            name_ar: el.name.ar,
            price: el.price,
            default: el.default,
        };
    }),
    has_variation: props.menuItem.variations.length > 0 ? true : false,
    variation_ids: props.menuItem.variations.map((el) => el.id),
    variation_options: props.variation_options,
    _method: "PUT",
});

const prevImg = ref(props.menuItem.image);

watch(
    () => form.image,
    (value) => {
        if (value) {
            const reader = new FileReader();
            reader.onload = (e) => {
                prevImg.value = e.target.result;
            };
            reader.readAsDataURL(value);
        }
    }
);

const addRow = () => {
    form.variation_options.push({
        id: form.variation_options.length,
        variation_id: "",
        options: [],
    });
};

const addSizeRow = () => {
    form.size_variations.push({
        id: null,
        name_en: "",
        name_ar: "",
        price: "",
        default: false,
    });
};

const removeRow = (index) => {
    form.variation_ids = form.variation_ids.filter(
        (id) =>
            !form.variation_options[index].options.find((rm) => rm.id === id)
    );
    form.variation_options.splice(index, 1);
};

const removeSizeRow = (index, id) => {
    if (id) {
        router.visit(route("menu_items.delete_variation", id), {
            preserveScroll: true,
            onSuccess: () => {
                form.size_variations.splice(index, 1);
            },
        });
    } else {
        form.size_variations.splice(index, 1);
    }
};

const getVariationValues = (index, e) => {
    let values = props.variations.find((el) => el.id == e.target.value);
    form.variation_options[index].options = values.variation_values;
};

const selectAll = (e, ids) => {
    if (e.target.checked) {
        ids.forEach((el) => {
            form.variation_ids.push(el.id);
        });
    } else {
        form.variation_ids = form.variation_ids.filter(
            (id) => !ids.find((el) => el.id === id)
        );
    }
};

const changeDefault = (index) => {
    form.size_variations.forEach((el) => (el.default = 0));
    form.size_variations[index].default = 1;
};
</script>

<template>
    <Head :title="t('Edit Menu Item')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Edit Menu Item") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <form
                        @submit.prevent="
                            form.post(route('menu-items.update', menuItem.id))
                        "
                    >
                        <div class="grid grid-cols-1 gap-6">
                            <div class="flex gap-6">
                                <div class="w-full sm:w-3/4">
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6"
                                    >
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
                                                :placeholder="
                                                    t(
                                                        'Enter Menu Item English Name'
                                                    )
                                                "
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
                                                :placeholder="
                                                    t(
                                                        'Enter Menu Item Arabic Name'
                                                    )
                                                "
                                                v-model="form.name_ar"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.name_ar"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6"
                                    >
                                        <div>
                                            <label
                                                for="description_en"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{
                                                    t("English Description")
                                                }}</label
                                            >
                                            <textarea
                                                name="description_en"
                                                id="description_en"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="
                                                    t(
                                                        'Enter Menu Item English Description'
                                                    )
                                                "
                                                v-model="form.description_en"
                                                rows="5"
                                            ></textarea>
                                        </div>
                                        <div>
                                            <label
                                                for="description_ar"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{
                                                    t("Arabic Description")
                                                }}</label
                                            >
                                            <textarea
                                                name="description_ar"
                                                id="description_ar"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="
                                                    t(
                                                        'Enter Menu Item Arabic Description'
                                                    )
                                                "
                                                v-model="form.description_ar"
                                                rows="5"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6"
                                    >
                                        <div>
                                            <label
                                                for="price"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{ t("Price") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="price"
                                                id="price"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="
                                                    t('Enter Menu Item Price')
                                                "
                                                v-model="form.price"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.price"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="discount"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{
                                                    t("Discount Percentage")
                                                }}
                                                %</label
                                            >
                                            <input
                                                type="text"
                                                name="discount"
                                                id="discount"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="
                                                    t(
                                                        'Enter Menu Item Discount Percentage'
                                                    )
                                                "
                                                v-model="form.discount"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.discount"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6"
                                    >
                                        <div>
                                            <label
                                                for="category_id"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{ t("Category") }}</label
                                            >
                                            <select
                                                name="category_id"
                                                id="category_id"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.category_id"
                                            >
                                                <option
                                                    v-for="category in categories"
                                                    :key="category.id"
                                                    :value="category.id"
                                                >
                                                    {{
                                                        category.name[
                                                            $page.props.locale
                                                        ]
                                                    }}
                                                </option>
                                            </select>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.category_id
                                                "
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="order"
                                                class="block text-sm font-medium text-gray-700"
                                                >{{ t("Order") }}</label
                                            >
                                            <input
                                                type="number"
                                                name="order"
                                                id="order"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                :placeholder="
                                                    t('Enter Menu Item Order')
                                                "
                                                v-model="form.order"
                                            />
                                            <InputError
                                                class="mt-2"
                                                :message="form.errors.order"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3 mb-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            ><input
                                                type="checkbox"
                                                v-model="
                                                    form.has_size_variation
                                                "
                                            />
                                            {{ t("Size") }}</label
                                        >

                                        <div v-if="form.has_size_variation">
                                            <table
                                                class="mt-2 border-collapse w-full border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm"
                                            >
                                                <thead
                                                    class="bg-slate-50 dark:bg-slate-700"
                                                >
                                                    <tr>
                                                        <th
                                                            class="w-8/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Size") }}
                                                        </th>
                                                        <th
                                                            class="w-3/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Price") }}
                                                        </th>
                                                        <th
                                                            class="w-1/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{
                                                                t(
                                                                    "Default Size"
                                                                )
                                                            }}
                                                        </th>
                                                        <th
                                                            class="w-1/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Remove") }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(
                                                            size, index
                                                        ) in form.size_variations"
                                                    >
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <div
                                                                class="grid grid-cols-2 gap-x-6 col-span-6 sm:col-span-3"
                                                            >
                                                                <div>
                                                                    <input
                                                                        type="text"
                                                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                                        :placeholder="
                                                                            t(
                                                                                'Enter Size English Name'
                                                                            )
                                                                        "
                                                                        v-model="
                                                                            size.name_en
                                                                        "
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <input
                                                                        type="text"
                                                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                                        :placeholder="
                                                                            t(
                                                                                'Enter Size Arabic Name'
                                                                            )
                                                                        "
                                                                        v-model="
                                                                            size.name_ar
                                                                        "
                                                                    />
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <div>
                                                                <input
                                                                    type="text"
                                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                                    :placeholder="
                                                                        t(
                                                                            'Enter Size Price'
                                                                        )
                                                                    "
                                                                    v-model="
                                                                        size.price
                                                                    "
                                                                />
                                                            </div>
                                                        </td>
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <input
                                                                type="radio"
                                                                name="default"
                                                                v-model="
                                                                    size.default
                                                                "
                                                                :checked="
                                                                    size.default ==
                                                                    1
                                                                "
                                                                @click="
                                                                    changeDefault(
                                                                        index
                                                                    )
                                                                "
                                                                value="1"
                                                            />
                                                        </td>
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <button
                                                                @click.prevent="
                                                                    removeSizeRow(
                                                                        index,
                                                                        size.id
                                                                    )
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
                                            <button
                                                type="button"
                                                @click="addSizeRow"
                                            >
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
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            ><input
                                                type="checkbox"
                                                v-model="form.has_variation"
                                            />
                                            {{ t("Variations") }}</label
                                        >

                                        <div v-if="form.has_variation">
                                            <table
                                                class="mt-2 border-collapse w-full border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm"
                                            >
                                                <thead
                                                    class="bg-slate-50 dark:bg-slate-700"
                                                >
                                                    <tr>
                                                        <th
                                                            class="w-5/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Variation") }}
                                                        </th>
                                                        <th
                                                            class="w-6/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Values") }}
                                                        </th>
                                                        <th
                                                            class="w-7/12 border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left rtl:text-right"
                                                        >
                                                            {{ t("Remove") }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="(
                                                            value, index
                                                        ) in form.variation_options"
                                                    >
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <select
                                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                                v-model="
                                                                    value.variation_id
                                                                "
                                                                @change="
                                                                    getVariationValues(
                                                                        index,
                                                                        $event
                                                                    )
                                                                "
                                                            >
                                                                <option
                                                                    value=""
                                                                >
                                                                    {{
                                                                        t(
                                                                            "Select Variation"
                                                                        )
                                                                    }}
                                                                </option>
                                                                <option
                                                                    v-for="variation in variations"
                                                                    :key="
                                                                        variation.id
                                                                    "
                                                                    :value="
                                                                        variation.id
                                                                    "
                                                                >
                                                                    {{
                                                                        variation
                                                                            .name[
                                                                            $page
                                                                                .props
                                                                                .locale
                                                                        ]
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <label
                                                                class="block text-sm font-medium text-gray-700"
                                                                v-if="
                                                                    value
                                                                        .options
                                                                        .length >
                                                                    0
                                                                "
                                                            >
                                                                <input
                                                                    type="checkbox"
                                                                    class="focus:ring-offset-0 focus:ring-0"
                                                                    @click="
                                                                        selectAll(
                                                                            $event,
                                                                            value.options
                                                                        )
                                                                    "
                                                                />
                                                                {{
                                                                    t(
                                                                        "Select All"
                                                                    )
                                                                }}
                                                            </label>
                                                            <label
                                                                class="block text-sm font-medium text-gray-700"
                                                                v-for="option in value.options"
                                                            >
                                                                <input
                                                                    type="checkbox"
                                                                    class="focus:ring-offset-0 focus:ring-0"
                                                                    :value="
                                                                        option.id
                                                                    "
                                                                    v-model="
                                                                        form.variation_ids
                                                                    "
                                                                />
                                                                {{
                                                                    option.name[
                                                                        $page
                                                                            .props
                                                                            .locale
                                                                    ]
                                                                }}
                                                                -
                                                                {{
                                                                    option.price
                                                                }}
                                                            </label>
                                                        </td>
                                                        <td
                                                            class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                                        >
                                                            <button
                                                                @click.prevent="
                                                                    removeRow(
                                                                        index
                                                                    )
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
                                            <button
                                                type="button"
                                                @click="addRow"
                                            >
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
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full sm:w-1/4">
                                    <div>
                                        <label
                                            for="image"
                                            class="block text-sm font-medium text-gray-700"
                                            >{{ t("Image") }}</label
                                        >
                                        <label for="image" class="mt-1 block">
                                            <img
                                                v-if="form.image.length == 0"
                                                src="/assets/images/upload.webp"
                                                alt=""
                                                class="bg-gray-100 p-10 w-full h-52 object-contain shadow"
                                            />
                                            <img
                                                v-else
                                                :src="prevImg"
                                                alt=""
                                                class="w-full h-52 object-cover shadow"
                                            />
                                        </label>
                                        <input
                                            type="file"
                                            name="image"
                                            id="image"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 w-full shadow-sm sm:text-sm border-gray-300 rounded-md hidden"
                                            placeholder="Enter Menu Item Name"
                                            @input="
                                                form.image =
                                                    $event.target.files[0]
                                            "
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.image"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    class="bg-green-600 text-white px-10 py-2 rounded-sm duration-300 hover:bg-green-500"
                                >
                                    {{ t("Update") }}
                                </button>
                                <Link
                                    :href="route('menu-items.index')"
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
