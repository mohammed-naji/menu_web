<script setup>
import InputError from "@/Components/InputError.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { GoogleMap, Marker } from "vue3-google-map";

const props = defineProps({
    branch: Object,
});

const form = useForm({
    phone: props.branch.phone,
    address: props.branch.address,
    location: props.branch.location,
    _method: "PUT",
});

const center = JSON.parse(props.branch.location);

const markerOptions = ref({
    position: center,
    label: "A",
    title: "Location",
    // icon: "https://maps.google.com/mapfiles/kml/shapes/lighthouse.png",
});

const handleClick = (event) => {
    const lat = event.latLng.lat();
    const lng = event.latLng.lng();

    form.location = JSON.stringify({
        lat,
        lng,
    });

    markerOptions.value = {
        position: {
            lat,
            lng,
        },
        label: "A",
        title: "Location",
    };
};
</script>

<template>
    <Head :title="t('Edit Branch')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Edit Branch") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <form
                        @submit.prevent="
                            form.post(route('branches.update', branch.id))
                        "
                    >
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        for="phone"
                                        class="block text-sm font-medium text-gray-700"
                                        >{{ t("Phone") }}</label
                                    >
                                    <input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        :placeholder="t('Enter Branch Phone')"
                                        v-model="form.phone"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.phone"
                                    />
                                </div>
                                <div>
                                    <label
                                        for="address"
                                        class="block text-sm font-medium text-gray-700"
                                        >{{ t("Address") }}</label
                                    >
                                    <input
                                        type="text"
                                        name="address"
                                        id="address"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        :placeholder="t('Enter Branch Address')"
                                        v-model="form.address"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.address"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <InputLabel
                                        for="location"
                                        :value="t('Google Map Location')"
                                    />
                                    <GoogleMap
                                        api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                                        style="width: 100%; height: 300px"
                                        :center="center"
                                        :zoom="12"
                                        @click="handleClick"
                                    >
                                        <Marker :options="markerOptions" />
                                    </GoogleMap>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.location"
                                    />
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    class="bg-green-600 text-white px-10 py-2 rounded-sm duration-300 hover:bg-green-500"
                                >
                                    {{ t("Update") }}
                                </button>
                                <Link
                                    :href="route('branches.index')"
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
