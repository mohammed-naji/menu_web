<script setup>
import { Head, Link } from "@inertiajs/vue3";
import UpdateProfileInformationForm from "../Profile/Partials/UpdateProfileInformationForm.vue";
import { t } from "@/Composables/trans";
import { ref } from "vue";
import UpdatePasswordForm from "../Profile/Partials/UpdatePasswordForm.vue";
import LangSwitcher from "@/Components/LangSwitcher.vue";
import moment from "moment";
import Navbar from "@/Components/Navbar.vue";
import Hero from "@/Components/Hero.vue";

defineProps({
    restaurant: Object,
    settings: Object,
    type: {
        type: String,
        default: "in-restaurant",
    },
    orders: Array,
});

const activeTab = ref(0);

const tabs = ref([
    t("Update Information"),
    t("Update Password"),
    t("My Orders"),
]);
</script>

<template>
    <Head :title="t('Menu')" />
    <!-- Start Navbar -->
    <Navbar :restaurant="restaurant" :type="type" :settings="settings" />
    <Hero :title="t('My Account')" />

    <!-- Start -->
    <section class="relative md:py-14 py-10">
        <div class="container relative mt-8 md:px-28 px-3 pb-5">
            <div class="flex">
                <button
                    type="button"
                    class="p-3 px-4 border-b-2 hover:bg-gray-100 duration-300 flex-1 flex-grow"
                    v-for="(tab, i) in tabs"
                    :class="{
                        'border-amber-500 bg-gray-100': activeTab == i,
                        'bg-gray-50': activeTab != i,
                    }"
                    :key="i"
                    @click="activeTab = i"
                >
                    {{ tab }}
                </button>
            </div>

            <div class="pt-8">
                <div v-if="activeTab == 0">
                    <UpdateProfileInformationForm />
                </div>
                <div v-if="activeTab == 1">
                    <div class="max-w-xl">
                        <UpdatePasswordForm />
                    </div>
                </div>
                <div v-if="activeTab == 2">
                    <div class="mt-6 flow-root sm:mt-8">
                        <div
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <div
                                v-if="orders.data.length > 0"
                                class="flex flex-wrap items-center gap-y-4 py-6"
                                v-for="order in orders.data"
                                :key="order.id"
                            >
                                <dl class="w-1/2 sm:w-1/5 lg:w-auto lg:flex-1">
                                    <dt
                                        class="text-base font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ t("Order ID") }}:
                                    </dt>
                                    <dd
                                        class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        <a
                                            :data-collapse-toggle="
                                                'order' + order.id
                                            "
                                            class="hover:underline"
                                            >#ORDER-{{ order.id }}</a
                                        >
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/5 lg:w-auto lg:flex-1">
                                    <dt
                                        class="text-base font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ t("Date") }}:
                                    </dt>
                                    <dd
                                        class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{
                                            moment(order.created_at).format(
                                                "M.D.YYYY"
                                            )
                                        }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/5 lg:w-auto lg:flex-1">
                                    <dt
                                        class="text-base font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ t("Price") }}:
                                    </dt>
                                    <dd
                                        class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ order.total_amount }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/5 lg:w-auto lg:flex-1">
                                    <dt
                                        class="text-base font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ t("Order Type") }}:
                                    </dt>
                                    <dd
                                        class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{
                                            t(
                                                order.order_type.replace(
                                                    "-",
                                                    " "
                                                )
                                            )
                                        }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/5 lg:w-auto lg:flex-1">
                                    <dt
                                        class="text-base font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ t("Status") }}:
                                    </dt>
                                    <dd class="me-2 mt-1.5">
                                        <span
                                            v-if="order.status == 'pending'"
                                            class="inline-flex items-center rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300"
                                        >
                                            <svg
                                                class="me-1 h-3 w-3"
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
                                                    d="M18.5 4h-13m13 16h-13M8 20v-3.333a2 2 0 0 1 .4-1.2L10 12.6a1 1 0 0 0 0-1.2L8.4 8.533a2 2 0 0 1-.4-1.2V4h8v3.333a2 2 0 0 1-.4 1.2L13.957 11.4a1 1 0 0 0 0 1.2l1.643 2.867a2 2 0 0 1 .4 1.2V20H8Z"
                                                />
                                            </svg>
                                            {{ t("Pre-order") }}
                                        </span>

                                        <span
                                            v-else-if="
                                                order.status == 'delivery'
                                            "
                                            class="inline-flex items-center rounded bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300"
                                        >
                                            <svg
                                                class="me-1 h-3 w-3"
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
                                                    d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"
                                                />
                                            </svg>
                                            {{ t("In transit") }}
                                        </span>
                                        <span
                                            v-else-if="
                                                order.status == 'confirmed' ||
                                                order.status == 'in-process'
                                            "
                                            class="inline-flex items-center rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300"
                                        >
                                            <svg
                                                class="me-1 h-3 w-3"
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
                                                    d="M5 11.917 9.724 16.5 19 7.5"
                                                />
                                            </svg>
                                            {{ t("Confirmed") }}
                                        </span>
                                        <span
                                            v-else-if="
                                                order.status == 'cancelled'
                                            "
                                            class="inline-flex items-center rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300"
                                        >
                                            <svg
                                                class="me-1 h-3 w-3"
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
                                                    d="M6 18 17.94 6M18 18 6.06 6"
                                                />
                                            </svg>
                                            {{ t("Cancelled") }}
                                        </span>
                                    </dd>
                                </dl>

                                <div
                                    class="w-full grid sm:grid-cols-2 lg:flex lg:w-64 lg:items-center lg:justify-end gap-4"
                                >
                                    <button
                                        class="w-full inline-flex justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 lg:w-auto"
                                        @click="showDetails('order' + order.id)"
                                    >
                                        {{ t("View details") }}
                                    </button>
                                </div>
                                <div
                                    :id="'order' + order.id"
                                    class="w-2/2 lg:w-full hidden py-2 space-y-2 border p-5 bg-gray-100 divide-y-[1px]"
                                >
                                    <div
                                        v-for="item in order.order_items"
                                        class="flex flex-col lg:flex-row items-center py-6 gap-6 w-full"
                                    >
                                        <div class="img-box max-lg:w-full">
                                            <img
                                                :src="
                                                    '/' + item.menu_item.image
                                                "
                                                :alt="
                                                    item.menu_item.name[
                                                        $page.props.locale
                                                    ]
                                                "
                                                class="aspect-square w-full lg:max-w-[120px] rounded-xl object-cover"
                                            />
                                        </div>
                                        <div
                                            class="flex flex-row items-center w-full"
                                        >
                                            <div class="flex w-full">
                                                <div
                                                    class="flex w-1/3 items-center"
                                                >
                                                    <div class="">
                                                        <h2
                                                            class="font-semibold text-xl leading-8 text-black mb-3"
                                                        >
                                                            {{
                                                                item.menu_item
                                                                    .name[
                                                                    $page.props
                                                                        .locale
                                                                ]
                                                            }}
                                                        </h2>
                                                        <p
                                                            class="font-normal text-lg leading-8 text-gray-500 mb-3"
                                                        >
                                                            {{ t("Category") }}:
                                                            {{
                                                                item.menu_item
                                                                    .category
                                                                    .name[
                                                                    $page.props
                                                                        .locale
                                                                ]
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="w-2/3 grid grid-cols-4"
                                                >
                                                    <div
                                                        class="col-span-5 lg:col-span-2 flex items-center max-lg:mt-3"
                                                    >
                                                        <div
                                                            class="flex gap-3 lg:block"
                                                        >
                                                            <p
                                                                class="font-medium text-base leading-7 text-black pr-4 mr-4"
                                                                v-for="variation in item.variations"
                                                            >
                                                                {{
                                                                    variation
                                                                        .variation
                                                                        .name[
                                                                        $page
                                                                            .props
                                                                            .locale
                                                                    ]
                                                                }}:
                                                                <span
                                                                    class="text-gray-500"
                                                                    >{{
                                                                        variation
                                                                            .name[
                                                                            $page
                                                                                .props
                                                                                .locale
                                                                        ]
                                                                    }}</span
                                                                >
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-span-5 lg:col-span-1 flex items-center max-lg:mt-3"
                                                    >
                                                        <div
                                                            class="flex gap-3 lg:block"
                                                        >
                                                            <p
                                                                class="font-medium text-sm leading-7 text-black"
                                                            >
                                                                {{
                                                                    t(
                                                                        "Quantity"
                                                                    )
                                                                }}
                                                            </p>
                                                            <p
                                                                class="text-base whitespace-nowrap leading-7 lg:mt-3 font-bold"
                                                            >
                                                                {{
                                                                    item.quantity
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-span-5 lg:col-span-1 flex items-center max-lg:mt-3"
                                                    >
                                                        <div
                                                            class="flex gap-3 lg:block"
                                                        >
                                                            <p
                                                                class="font-medium text-sm whitespace-nowrap leading-6 text-black"
                                                            >
                                                                {{ t("Price") }}
                                                            </p>
                                                            <p
                                                                class="text-base whitespace-nowrap leading-7 lg:mt-3 font-bold"
                                                            >
                                                                {{ item.price }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-12">
                                <p>
                                    {{ t("No orders yet!") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end container-->
    </section>
    <!--end section-->
</template>
