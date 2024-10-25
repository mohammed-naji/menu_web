<script setup>
import InputError from "@/Components/InputError.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    category: Object,
});

const form = useForm({
    name_en: props.category.name.en,
    name_ar: props.category.name.ar,
    description_en: props.category.description.en,
    description_ar: props.category.description.ar,
    image: props.category.image,
    _method: "PUT",
});

const prevImg = ref(props.category.image);

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
</script>

<template>
    <Head :title="t('Edit Category')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Edit Category") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <form
                        @submit.prevent="
                            form.post(route('categories.update', category.id))
                        "
                    >
                        <div class="grid grid-cols-1 gap-6">
                            <div class="flex gap-6">
                                <div class="w-3/4">
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
                                                        'Enter Category English Name'
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
                                                        'Enter Category Arabic Name'
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
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
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
                                                        'Enter Category English Description'
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
                                                        'Enter Category Arabic Description'
                                                    )
                                                "
                                                v-model="form.description_ar"
                                                rows="5"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-1/4">
                                    <div>
                                        <label
                                            for="image"
                                            class="block text-sm font-medium text-gray-700"
                                            >{{ t("Image") }}</label
                                        >
                                        <label for="image" class="mt-1 block">
                                            <img
                                                v-if="
                                                    form.image &&
                                                    form.image.length == 0
                                                "
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
                                            placeholder="Enter Category Name"
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
                                    :href="route('categories.index')"
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
