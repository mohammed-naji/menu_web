<script setup>
import { t } from "@/Composables/trans";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { computed } from "vue";
import { loadStripe } from "@stripe/stripe-js";
import axios from "axios";
import InputError from "@/Components/InputError.vue";
import Swal from "sweetalert2";

const total_price = computed(() => {
    let total = 0;
    cart.value.forEach((item) => {
        total += Number(item.price);
    });
    return total;
});

const cart = ref([]);

const processing = ref(false);

const form = useForm({
    name: usePage().props.auth.user ? usePage().props.auth.user.name : "",
    email: usePage().props.auth.user ? usePage().props.auth.user.email : "",
    phone: usePage().props.auth.user ? usePage().props.auth.user.phone : "",
    address: "",
    table_number: "",
    payment_method: "cash",
});

onMounted(() => {
    getCart();
});

const redirectToCheckout = async () => {};

const deleteItem = (id, order_id, price) => {};

const loading = ref(false);

const changeQuantity = (item, action) => {};

const sendWhatsappOrder = () => {};

const getCart = () => {
    cart.value = JSON.parse(localStorage.getItem("cart")) || [];
};
</script>

<template>
    <Head :title="t('Checkout')" />
    <!-- Start Navbar -->
    <nav id="topnav" class="defaultscroll is-sticky">
        <div class="flex items-center justify-between py-1 px-10">
            <!-- Logo container-->
            <a class="logo" href="index.html">
                <img src="/assets/images/logo.png" class="w-20" alt="" />
            </a>
            <!-- End Logo container-->

            <ul class="navigation-menu nav-light justify-end">
                <li>
                    <a href="reservation.html" class="sub-menu-item">Login</a>
                </li>
            </ul>
        </div>
        <!--end container-->
    </nav>
    <!--end header-->
    <!-- End Navbar -->
    <!-- Start Hero -->
    <section
        class="relative md:py-28 py-20 bg-[url('../../assets/images/bg/pages.jpg')] bg-no-repeat bg-bottom bg-cover"
    >
        <div
            class="absolute inset-0 bg-gradient-to-t from-slate-900 to-slate-900/70"
        ></div>
        <div class="container relative">
            <div class="grid grid-cols-1 text-center mt-6">
                <div>
                    <h5
                        class="md:text-4xl text-3xl md:leading-normal leading-normal font-medium text-white mb-0"
                    >
                        {{ t("Checkout") }}
                    </h5>
                </div>
            </div>
        </div>
    </section>
    <!--end section-->
    <div class="relative">
        <div
            class="shape absolute sm:-bottom-px -bottom-[2px] start-0 end-0 overflow-hidden z-1 text-white dark:text-slate-900"
        >
            <svg
                class="w-full h-auto scale-[2.0] origin-top"
                viewBox="0 0 2880 48"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z"
                    fill="currentColor"
                ></path>
            </svg>
        </div>
    </div>
    <!-- End Hero -->
    <div
        class="font-sans max-w-6xl max-lg:max-w-2xl mx-auto bg-white dark:bg-slate-600 p-10 md:p-20"
    >
        <div class="grid lg:grid-cols-2 gap-12">
            <div>
                <div class="bg-gray-100 dark:bg-slate-800 p-6 rounded-md">
                    <h2
                        class="text-2xl font-extrabold text-gray-800 dark:text-white"
                    >
                        {{ t("Your Order") }}
                    </h2>
                    <div class="space-y-4 mt-8 divide-x-[1px]">
                        <div
                            v-for="item in cart"
                            class="flex items-center gap-4"
                        >
                            <div
                                class="w-24 h-24 shrink-0 bg-white p-2 rounded-md"
                            >
                                <img
                                    :src="item.image"
                                    class="w-full h-full object-contain"
                                />
                            </div>

                            <div class="w-full">
                                <h3
                                    class="text-base font-semibold text-gray-800 dark:text-white"
                                >
                                    {{ item.name[$page.props.locale] }}
                                </h3>
                                <h5
                                    class="text-gray-800 dark:text-white font-bold cursor-pointer mt-0.5"
                                >
                                    ${{ item.price }}
                                </h5>
                                <!-- <p
                                    class="font-medium text-sm leading-7 text-black dark:text-white pr-4 mr-4"
                                    v-for="variation in item.variations"
                                >
                                    {{ variation.variation.trans_name }}:
                                    <span
                                        class="text-gray-500 dark:text-gray-400"
                                        >{{ variation.trans_value }}</span
                                    >
                                </p> -->
                                <div class="flex gap-4 mt-4">
                                    <div>
                                        <button
                                            type="button"
                                            class="flex items-center px-2.5 py-1.5 border border-gray-300 text-gray-800 dark:text-white text-xs outline-none bg-transparent rounded-md"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-2.5 fill-current"
                                                viewBox="0 0 124 124"
                                                @click="
                                                    changeQuantity(
                                                        item,
                                                        'decrement'
                                                    )
                                                "
                                            >
                                                <path
                                                    d="M112 50H12C5.4 50 0 55.4 0 62s5.4 12 12 12h100c6.6 0 12-5.4 12-12s-5.4-12-12-12z"
                                                    data-original="currentColor"
                                                ></path>
                                            </svg>

                                            <span class="mx-2.5">{{
                                                item.quantity ?? 1
                                            }}</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-2.5 fill-current"
                                                viewBox="0 0 42 42"
                                                @click="
                                                    changeQuantity(
                                                        item,
                                                        'increment'
                                                    )
                                                "
                                            >
                                                <path
                                                    d="M37.059 16H26V4.941C26 2.224 23.718 0 21 0s-5 2.224-5 4.941V16H4.941C2.224 16 0 18.282 0 21s2.224 5 4.941 5H16v11.059C16 39.776 18.282 42 21 42s5-2.224 5-4.941V26h11.059C39.776 26 42 23.718 42 21s-2.224-5-4.941-5z"
                                                    data-original="currentColor"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="ml-auto rtl:mr-auto rtl:ml-0">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 fill-red-500 inline cursor-pointer"
                                            viewBox="0 0 24 24"
                                            @click="
                                                deleteItem(
                                                    item.id,
                                                    item.order_id,
                                                    item.price
                                                )
                                            "
                                        >
                                            <path
                                                d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1Zm1-3h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z"
                                                data-original="currentColor"
                                            ></path>
                                            <path
                                                d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Zm4 0v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z"
                                                data-original="currentColor"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap justify-center gap-4">
                    <img
                        src="https://readymadeui.com/images/master.webp"
                        alt="card1"
                        class="w-12 object-contain"
                    />
                    <img
                        src="https://readymadeui.com/images/visa.webp"
                        alt="card2"
                        class="w-12 object-contain"
                    />
                    <img
                        src="https://readymadeui.com/images/american-express.webp"
                        alt="card3"
                        class="w-12 object-contain"
                    />
                </div>
            </div>

            <form @submit.prevent="redirectToCheckout()">
                <h2
                    class="text-2xl font-extrabold text-gray-800 dark:text-white"
                >
                    {{ t("Payment Details") }}
                </h2>
                <ul class="text-gray-800 dark:text-white mt-8 space-y-4">
                    <li class="flex justify-between flex-wrap gap-4 text-sm">
                        {{ t("Subtotal") }}
                        <span class="font-bold">${{ total_price }}</span>
                    </li>
                    <li class="flex justify-between flex-wrap gap-4 text-sm">
                        {{ t("Membership Discount") }}
                        ({{ 7 }}%)
                        <span class="font-bold">$8</span>
                    </li>
                    <!-- <li class="flex flex-wrap gap-4 text-sm">
                            Tax <span class="ml-auto font-bold">{{ $page.props.settings.currency_symbol }}4.00</span>
                        </li> -->
                    <hr class="border-gray-300" />
                    <li
                        class="flex justify-between flex-wrap gap-4 text-sm font-bold"
                    >
                        {{ t("Total") }}
                        <span class="font-bold"
                            >${{ Number(total_price).toFixed(2) }}</span
                        >
                    </li>
                </ul>

                <h3
                    class="mb-5 mt-10 text-lg font-medium text-gray-900 dark:text-white"
                >
                    {{ t("User Information") }}
                </h3>

                <div class="grid grid-cols-1 gap-3">
                    <div class="w-full">
                        <label
                            for="full_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Name") }}
                        </label>
                        <input
                            id="full_name"
                            type="text"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 disabled:bg-gray-100"
                            :disabled="usePage().props.auth.user"
                            v-model="form.name"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="w-full">
                        <label
                            for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Email") }}
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 disabled:bg-gray-100"
                            :disabled="usePage().props.auth.user"
                            v-model="form.email"
                            required
                        />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div class="w-full">
                        <label
                            for="phone"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Phone") }}
                        </label>
                        <input
                            id="phone"
                            type="text"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 disabled:bg-gray-100"
                            :disabled="usePage().props.auth.user"
                            v-model="form.phone"
                            required
                        />
                        <InputError :message="form.errors.phone" />
                    </div>
                    <div class="w-full">
                        <label
                            for="table_number"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Table Number") }}
                        </label>
                        <input
                            id="table_number"
                            type="number"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500"
                            v-model="form.table_number"
                            required
                        />
                        <InputError :message="form.errors.table_number" />
                    </div>
                    <div class="w-full">
                        <label
                            for="table_number"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Delivery Address") }}
                        </label>
                        <input
                            id="table_number"
                            type="text"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500"
                            required
                            v-model="form.address"
                        />
                        <InputError :message="form.errors.address" />
                    </div>
                </div>
                <h3
                    class="mb-5 mt-10 text-lg font-medium text-gray-900 dark:text-white"
                >
                    {{ t("Payment Method") }}
                </h3>
                <ul class="grid w-full gap-3 md:grid-cols-2">
                    <li>
                        <input
                            type="radio"
                            id="hosting-small"
                            name="hosting"
                            value="cash"
                            class="hidden peer"
                            required
                            v-model="form.payment_method"
                            checked
                        />
                        <label
                            for="hosting-small"
                            class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-amber-500 peer-checked:border-amber-600 peer-checked:text-amber-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <div class="block">
                                <div class="w-full font-semibold">
                                    {{ t("Cash on Delivery") }}
                                </div>
                            </div>
                            <svg
                                class="w-7 h-7"
                                xmlns="http://www.w3.org/2000/svg"
                                version="1.1"
                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="512"
                                height="512"
                                x="0"
                                y="0"
                                viewBox="0 0 682.667 682.667"
                                style="enable-background: new 0 0 512 512"
                                xml:space="preserve"
                            >
                                <g>
                                    <defs>
                                        <clipPath
                                            id="a"
                                            clipPathUnits="userSpaceOnUse"
                                        >
                                            <path
                                                d="M0 512h512V0H0Z"
                                                fill="currentColor"
                                                opacity="1"
                                                data-original="currentColor"
                                            ></path>
                                        </clipPath>
                                    </defs>
                                    <path
                                        d="M0 0v112.88h246.905V-9.501"
                                        style="
                                            stroke-linecap: round;
                                            stroke-linejoin: round;
                                            stroke-miterlimit: 10;
                                            stroke-dasharray: none;
                                            stroke-opacity: 1;
                                        "
                                        transform="matrix(1.33333 0 0 -1.33333 152.91 492.01)"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="20px"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-miterlimit="10"
                                        stroke-dasharray="none"
                                        stroke-opacity=""
                                        data-original="currentColor"
                                    ></path>
                                    <path
                                        d="m0 0-37.836 16.821L-76.898 0v59.119H0Z"
                                        style="
                                            stroke-linecap: round;
                                            stroke-linejoin: round;
                                            stroke-miterlimit: 10;
                                            stroke-dasharray: none;
                                            stroke-opacity: 1;
                                        "
                                        transform="matrix(1.33333 0 0 -1.33333 368.779 420.328)"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="20px"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-miterlimit="10"
                                        stroke-dasharray="none"
                                        stroke-opacity=""
                                        data-original="currentColor"
                                    ></path>
                                    <g
                                        clip-path="url(#a)"
                                        transform="matrix(1.33333 0 0 -1.33333 0 682.667)"
                                    >
                                        <path
                                            d="M0 0h46.083a57.116 57.116 0 0 1 46.021 23.289l33.332 45.348a23.055 23.055 0 0 0 27.863 7.449c12.421-5.466 17.459-20.422 10.877-32.288l-14.28-25.746C125.37-26.165 78.788-53.596 28.225-53.596h-189.278"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(235.735 63.596)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c10.296 29.759 40.137 51.129 73.397 51.129 28.442 0 53.234-15.628 66.259-38.765v-.001h52.629c12.828 0 23.228-10.399 23.228-23.227 0-12.829-10.4-23.229-23.228-23.229H129.94h40.991"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(74.683 97.693)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M74.683 136.777H10V10h64.683z"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c26.132 32.85 66.079 52.503 108.877 52.503h189.278"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(139.163 449.497)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c27.519 0 49.828 22.309 49.828 49.828"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(64.635 399.156)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c0 27.52-22.309 49.828-49.828 49.828"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(114.463 296.324)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c-27.519 0-49.827-22.309-49.827-49.828"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(363.518 346.152)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0h-102v153.309h259.724"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(166 296)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0v-82.53h-108.153"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(364.153 378.53)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c-9.466-12.21-22.731-21.654-38.693-26.32-27.299-7.982-55.481.06-74.474 18.613v.001l-50.515-14.769c-12.313-3.599-25.213 3.464-28.813 15.778-3.6 12.312 3.464 25.213 15.777 28.812l87.95 25.713-67.454-19.721"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(436.393 407.924)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M437.317 375.223H502V502h-64.683z"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0c0 17.312-14.034 31.346-31.346 31.346-17.312 0-31.345-14.034-31.345-31.346 0-17.312 14.033-31.346 31.345-31.346C-14.034-31.346 0-17.312 0 0Z"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(245.422 372.654)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M0 0v0"
                                            style="
                                                stroke-linecap: round;
                                                stroke-linejoin: round;
                                                stroke-miterlimit: 10;
                                                stroke-dasharray: none;
                                                stroke-opacity: 1;
                                            "
                                            transform="translate(211 296.073)"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="20px"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-miterlimit="10"
                                            stroke-dasharray="none"
                                            stroke-opacity=""
                                            data-original="currentColor"
                                        ></path>
                                    </g>
                                </g>
                            </svg>
                        </label>
                    </li>
                    <li>
                        <input
                            type="radio"
                            id="hosting-big"
                            name="hosting"
                            value="credit"
                            class="hidden peer"
                            v-model="form.payment_method"
                        />
                        <label
                            for="hosting-big"
                            class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-amber-500 peer-checked:border-amber-600 peer-checked:text-amber-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <div class="block">
                                <div class="w-full font-semibold">
                                    {{ t("Credit Card") }}
                                </div>
                            </div>
                            <svg
                                class="w-7 h-7"
                                xmlns="http://www.w3.org/2000/svg"
                                version="1.1"
                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="512"
                                height="512"
                                x="0"
                                y="0"
                                viewBox="0 0 512.002 512.002"
                                style="enable-background: new 0 0 512 512"
                                xml:space="preserve"
                            >
                                <g>
                                    <path
                                        d="M502.903 96.829c-6.634-7.842-15.924-12.632-26.161-13.487L116.185 53.236c-10.238-.855-20.192 2.328-28.035 8.961-7.811 6.607-12.594 15.85-13.476 26.037L67.42 156.29H38.455C17.251 156.29 0 173.541 0 194.745v225.702c0 21.204 17.251 38.455 38.455 38.455h361.813c21.205 0 38.456-17.251 38.456-38.455v-36.613l12.839 1.072c1.083.09 2.16.135 3.228.135 19.768 0 36.62-15.209 38.294-35.257l18.781-224.919c.854-10.237-2.329-20.193-8.963-28.036zM38.455 176.29h361.813c10.176 0 18.456 8.279 18.456 18.455v20.566H20v-20.566c0-10.176 8.279-18.455 18.455-18.455zM20 235.311h398.724V276.8H20zm380.268 203.591H38.455c-10.176 0-18.455-8.279-18.455-18.455V296.8h398.724v123.647c0 10.176-8.28 18.455-18.456 18.455zM491.935 123.2l-18.781 224.919c-.847 10.141-9.788 17.706-19.927 16.856l-14.503-1.211V194.745c0-21.204-17.251-38.455-38.456-38.455H87.534l7.039-66.04c.008-.076.015-.151.021-.228.847-10.141 9.783-17.705 19.927-16.855l360.558 30.106c4.913.41 9.372 2.709 12.555 6.473s4.711 8.541 4.301 13.454z"
                                        fill="currentColor"
                                        opacity="1"
                                        data-original="currentColor"
                                    ></path>
                                    <path
                                        d="M376.873 326.532h-96.242c-5.523 0-10 4.477-10 10v62.789c0 5.523 4.477 10 10 10h96.242c5.523 0 10-4.477 10-10v-62.789c0-5.523-4.477-10-10-10zm-10 62.789h-76.242v-42.789h76.242z"
                                        fill="currentColor"
                                        opacity="1"
                                        data-original="currentColor"
                                    ></path>
                                </g>
                            </svg>
                        </label>
                    </li>
                </ul>
                <button
                    class="mt-8 text-sm px-4 py-3 w-full font-semibold tracking-wide duration-300 bg-amber-600 hover:bg-amber-700 text-white rounded-md"
                    :disabled="processing"
                >
                    {{ processing ? t("Processing") : t("Make Payment") }}
                </button>
                <button
                    type="button"
                    class="mt-2 text-sm px-4 py-3 w-full font-semibold tracking-wide duration-300 bg-[#25D366] hover:bg-[#219a4d] text-white rounded-md flex items-center justify-center gap-1"
                    @click="sendWhatsappOrder"
                >
                    <svg
                        class="w-6 h-6 text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z"
                            clip-rule="evenodd"
                        />
                        <path
                            fill="currentColor"
                            d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z"
                        />
                    </svg>
                    <span>{{
                        processing
                            ? t("Processing")
                            : t("Send Order to Whatsapp")
                    }}</span>
                </button>
            </form>
        </div>
    </div>
</template>
