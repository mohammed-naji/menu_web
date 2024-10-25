<script setup>
import { t } from "@/Composables/trans";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    restaurant: Object,
});
</script>

<template>
    <Head :title="t('Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ t("Dashboard") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6"
                >
                    <div class="grid sm:grid-cols-3 grid-cols-1 gap-6">
                        <div class="shadow p-4">
                            <h2
                                class="text-lg text-center font-bold text-gray-900"
                            >
                                {{ t("In Restaurant") }}
                            </h2>
                            <Link
                                :href="
                                    route('menu', [
                                        restaurant.code,
                                        'in-restaurant',
                                    ])
                                "
                            >
                                <img
                                    :src="`https://api.qrserver.com/v1/create-qr-code/?data=${route(
                                        'menu',
                                        [restaurant.code, 'in-restaurant']
                                    )}&size=500&charset-source=ISO-8859-1&ecc=H&color=eab123&margin=10&qzone=&format=png`"
                                    class="w-full"
                                    alt=""
                                />
                            </Link>
                        </div>
                        <div class="shadow p-4">
                            <h2
                                class="text-lg text-center font-bold text-gray-900"
                            >
                                {{ t("Take Away") }}
                            </h2>
                            <Link
                                :href="
                                    route('menu', [restaurant.code, 'takeaway'])
                                "
                            >
                                <img
                                    :src="`https://api.qrserver.com/v1/create-qr-code/?data=${route(
                                        'menu',
                                        [restaurant.code, 'takeaway']
                                    )}&size=500&charset-source=ISO-8859-1&ecc=H&color=eab123&margin=10&qzone=&format=png`"
                                    class="w-full"
                                    alt=""
                                />
                            </Link>
                        </div>
                        <div class="shadow p-4">
                            <h2
                                class="text-lg text-center font-bold text-gray-900"
                            >
                                {{ t("Delivery") }}
                            </h2>
                            <Link
                                :href="
                                    route('menu', [restaurant.code, 'delivery'])
                                "
                            >
                                <img
                                    :src="`https://api.qrserver.com/v1/create-qr-code/?data=${route(
                                        'menu',
                                        [restaurant.code, 'delivery']
                                    )}&size=500&charset-source=ISO-8859-1&ecc=H&color=eab123&margin=10&qzone=&format=png`"
                                    class="w-full"
                                    alt=""
                                />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
