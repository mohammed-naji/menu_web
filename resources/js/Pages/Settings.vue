<script setup>
import InputError from "@/Components/InputError.vue";
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { GoogleMap, Marker } from "vue3-google-map";

const times = [
    {
        day: "Saturday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Sunday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Monday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Tuesday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Wednesday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Thursday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
    {
        day: "Friday",
        from: "",
        to: "",
        open: "",
        close: "",
    },
];
const settings = usePage().props.settings;

const form = useForm({
    name: settings.name,
    email: settings.email,
    phone: settings.phone,
    logo: settings.logo,
    favicon: settings.favicon,
    currency: settings.currency,
    location: settings.location,
    whatsapp: settings.whatsapp,
    facebook: settings.facebook,
    x: settings.x,
    instagram: settings.instagram,
    tiktok: settings.tiktok,
    snapchat: settings.snapchat,
    youtube: settings.youtube,
    working_time: settings.working_time
        ? JSON.parse(settings.working_time)
        : times,
});

const center = form.location
    ? JSON.parse(form.location)
    : { lat: 40.689247, lng: -74.044502 };

const markerOptions = ref({
    position: center,
    label: "A",
    title: "Location",
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

const activeTab = ref(0);

const prevLogo = ref(
    settings.logo ? settings.logo : "/assets/images/upload.webp"
);
const prevFavicon = ref(
    settings.favicon ? settings.favicon : "/assets/images/upload.webp"
);

watch(
    () => form.logo,
    (value) => {
        if (value) {
            const reader = new FileReader();
            reader.onload = (e) => {
                prevLogo.value = e.target.result;
            };
            reader.readAsDataURL(value);
        }
    }
);

watch(
    () => form.favicon,
    (value) => {
        if (value) {
            const reader = new FileReader();
            reader.onload = (e) => {
                prevFavicon.value = e.target.result;
            };
            reader.readAsDataURL(value);
        }
    }
);

const tabs = ref([t("General Settings"), t("Social Media"), t("Working Time")]);
</script>

<template>
    <Head :title="t('Settings')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Settings") }}
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <form @submit.prevent="form.post(route('settings'))">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="max-w-3xl">
                                <button
                                    type="button"
                                    class="p-3 px-4 border-b-2 hover:bg-gray-100 duration-300"
                                    v-for="(tab, i) in tabs"
                                    :class="{
                                        'border-amber-500 bg-gray-100':
                                            activeTab == i,
                                        'bg-gray-50': activeTab != i,
                                    }"
                                    :key="i"
                                    @click="activeTab = i"
                                >
                                    {{ tab }}
                                </button>

                                <div class="pt-8">
                                    <div v-if="activeTab == 0">
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="name"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Name") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="name"
                                                id="name"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.name"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="email"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Email") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="email"
                                                id="email"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.email"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="phone"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Phone") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="phone"
                                                id="phone"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.phone"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="logo"
                                                class="block text-sm font-medium text-gray-700 w-44"
                                                >{{ t("Logo") }}</label
                                            >
                                            <label
                                                for="logo"
                                                class="mt-1 block"
                                            >
                                                <!-- <img
                                                    v-if="form.logo.length == 0"
                                                    src=""
                                                    alt=""
                                                    class="bg-gray-100 p-10 w-full h-32 object-contain shadow"
                                                /> -->
                                                <img
                                                    :src="prevLogo"
                                                    alt=""
                                                    class="w-32 h-32 object-contain shadow"
                                                />
                                            </label>
                                            <input
                                                type="file"
                                                name="logo"
                                                id="logo"
                                                class="hidden"
                                                @change="
                                                    form.logo =
                                                        $event.target.files[0]
                                                "
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="favicon"
                                                class="block text-sm font-medium text-gray-700 w-44"
                                                >{{ t("Favicon") }}</label
                                            >
                                            <label
                                                for="favicon"
                                                class="mt-1 block"
                                            >
                                                <!-- <img
                                                    v-if="
                                                        form.favicon.length == 0
                                                    "
                                                    src="/assets/images/upload.webp"
                                                    alt=""
                                                    class="bg-gray-100 p-2 w-full h-14 object-contain shadow"
                                                /> -->
                                                <img
                                                    :src="prevFavicon"
                                                    alt=""
                                                    class="w-14 h-14 object-cover shadow"
                                                />
                                            </label>
                                            <input
                                                type="file"
                                                name="favicon"
                                                id="favicon"
                                                class="hidden"
                                                @change="
                                                    form.favicon =
                                                        $event.target.files[0]
                                                "
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="currency"
                                                class="block text-sm font-medium text-gray-700 w-44"
                                                >{{ t("Currency") }}</label
                                            >
                                            <select
                                                name="currency"
                                                id="currency"
                                                class="mt-1 ps-7 focus:ring-indigo-500 focus:border-indigo-500 block w-36 shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.currency"
                                            >
                                                <option value="USD">
                                                    {{ t("Dollar") }}
                                                </option>
                                                <option value="EUR">
                                                    {{ t("Euro") }}
                                                </option>
                                                <option value="SAR">
                                                    {{ t("Saudi Riyal") }}
                                                </option>
                                            </select>
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="currency"
                                                class="block text-sm font-medium text-gray-700 w-44"
                                                >{{ t("Location") }}</label
                                            >
                                            <GoogleMap
                                                api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                                                style="
                                                    width: 100%;
                                                    height: 300px;
                                                "
                                                :center="center"
                                                :zoom="12"
                                                @click="handleClick"
                                            >
                                                <Marker
                                                    :options="markerOptions"
                                                />
                                            </GoogleMap>
                                        </div>
                                    </div>
                                    <div v-if="activeTab == 1">
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="whatsapp"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("WhatsApp") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="whatsapp"
                                                id="whatsapp"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.whatsapp"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="facebook"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Facebook") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="facebook"
                                                id="facebook"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.facebook"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="x"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("X") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="x"
                                                id="x"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.x"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="instagram"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Instagram") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="instagram"
                                                id="instagram"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.instagram"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="tiktok"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("TikTok") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="tiktok"
                                                id="tiktok"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.tiktok"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="snapchat"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Snapchat") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="snapchat"
                                                id="snapchat"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.snapchat"
                                            />
                                        </div>
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                        >
                                            <label
                                                for="youtube"
                                                class="block text-sm font-medium text-gray-700 w-64"
                                                >{{ t("Youtube") }}</label
                                            >
                                            <input
                                                type="text"
                                                name="youtube"
                                                id="youtube"
                                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                v-model="form.youtube"
                                            />
                                        </div>
                                    </div>
                                    <div v-if="activeTab == 2">
                                        <div
                                            class="flex items-center flex-col md:flex-row mb-6"
                                            v-for="time in form.working_time"
                                        >
                                            <label
                                                for="whatsapp"
                                                class="block text-sm font-medium text-gray-700 w-40"
                                                >{{ t(time.day) }}</label
                                            >
                                            <div class="flex gap-x-2">
                                                <label>
                                                    {{ t("From") }}
                                                    <input
                                                        type="time"
                                                        class="mx-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        v-model="time.from"
                                                    />
                                                </label>
                                                <label>
                                                    {{ t("To") }}
                                                    <input
                                                        type="time"
                                                        class="mx-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        v-model="time.to"
                                                    />
                                                </label>
                                                <label
                                                    class="flex items-center gap-x-1"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="me-1 ring-0 focus:ring-0 block shadow-sm sm:text-sm border-gray-300"
                                                        value="1"
                                                        v-model="time.open"
                                                    />
                                                    {{ t("Open 24 Hours") }}
                                                </label>
                                                <label
                                                    class="flex items-center gap-x-1"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="me-1 ring-0 focus:ring-0 block shadow-sm sm:text-sm border-gray-300"
                                                        value="1"
                                                        v-model="time.close"
                                                    />
                                                    {{ t("Close") }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    class="bg-green-600 text-white px-10 py-2 rounded-sm duration-300 hover:bg-green-500"
                                >
                                    {{ t("Save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
