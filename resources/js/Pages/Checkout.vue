<script setup>
import { t } from "@/Composables/trans";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { computed } from "vue";
import { loadStripe } from "@stripe/stripe-js";
import axios from "axios";
import InputError from "@/Components/InputError.vue";
import Swal from "sweetalert2";
import Navbar from "@/Components/Navbar.vue";
import Hero from "@/Components/Hero.vue";
import { GoogleMap, Marker } from "vue3-google-map";

const props = defineProps({
    restaurant: Object,
    type: String,
    settings: Object,
    branches: Array,
});

const cart = ref([]);

const total_price = computed(() => {
    return cart.value
        .reduce((acc, item) => acc + Number(item.price), 0)
        .toFixed(2);
});

const discount_price = computed(() => {
    let value = 0;
    if (usePage().props.auth.user) {
        value = total_price.value * 0.07;
    }
    return value.toFixed(2);
});

const processing = ref(false);

const form = useForm({
    name: usePage().props.auth.user ? usePage().props.auth.user.name : "",
    email: usePage().props.auth.user ? usePage().props.auth.user.email : "",
    phone: usePage().props.auth.user ? usePage().props.auth.user.phone : "",
    branch_id: props.restaurant.id,
    address: "",
    table_number: "",
    delivery_method: "from branch",
    delivery_location: 0,
    payment_method: "cash",
});

onMounted(() => {
    getCart();
});

const redirectToCheckout = async () => {
    console.log(form);

    return;
    if (!form.name) {
        form.errors.name = t("Name is required");
        form.hasErrors = true;
    } else {
        form.hasErrors = false;
    }

    if (!form.email) {
        form.errors.email = t("Email is required");
        form.hasErrors = true;
    } else {
        form.hasErrors = false;
    }

    if (!form.phone) {
        form.errors.phone = t("Phone is required");
        form.hasErrors = true;
    } else {
        form.hasErrors = false;
    }

    if (usePage().props.current_order.order_type == "in-restaurant") {
        if (!form.table_number) {
            form.errors.table_number = t("Table number is required");
            form.hasErrors = true;
        } else {
            form.hasErrors = false;
        }
    }

    if (usePage().props.current_order.order_type == "delivery") {
        if (!form.address) {
            form.errors.address = t("Address is required");
            form.hasErrors = true;
        } else {
            form.hasErrors = false;
        }
    }

    if (!form.hasErrors) {
        if (form.payment_method == "cash") {
            processing.value = true;
            const response = await axios.post(route("front.checkout"), form);
            console.log(response);

            if (response.data) {
                Swal.fire({
                    title: "Success",
                    text: "Your order has been placed successfully",
                    icon: "success",
                });
                setTimeout(() => {
                    window.location = route("front.index");
                }, 1000);
            } else {
                console.error("Error in payment");
            }
        } else {
            const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);

            try {
                processing.value = true;
                const response = await axios.post(
                    route("front.checkout"),
                    form
                );
                processing.value = false;
                if (response.data.id) {
                    if (form.payment_method != "cash") {
                        // Redirect to Stripe Checkout
                        await stripe.redirectToCheckout({
                            sessionId: response.data.id,
                        });
                    } else {
                        Swal.fire({
                            title: "Success",
                            text: "Your order has been placed successfully",
                            icon: "success",
                        });
                        setTimeout(() => {
                            window.location = "/";
                        });
                    }
                } else {
                    console.error("Error creating Stripe session:");
                }
            } catch (error) {
                console.error("Error during checkout:", error);
            }
        }
    }
};

const deleteItem = (index) => {
    cart.value.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart.value));
};

const loading = ref(false);

const changeQuantity = (item, action) => {
    let price = Number(item.price / item.quantity);

    if (action == "increment") {
        item.quantity++;
        item.price = Number(item.price) + Number(price);
    } else {
        if (item.quantity < 2) return;
        item.quantity--;
        item.price = Number(item.price) - Number(price);
    }

    localStorage.setItem("cart", JSON.stringify(cart.value));
};

const sendWhatsappOrder = () => {};

const getCart = () => {
    cart.value = JSON.parse(localStorage.getItem("cart")) || [];
};

const currency_symbol = computed(() => {
    if (props.settings.currency == "USD") {
        return "$";
    } else if (props.settings.currency == "EUR") {
        return "€";
    } else if (props.settings.currency == "SAR") {
        return t("SAR");
    } else {
        return "$";
    }
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

    form.address = JSON.stringify({
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
    <Head :title="t('Checkout')" />
    <!-- Start Navbar -->
    <Navbar :restaurant="restaurant" :type="type" :settings="settings" />
    <!--end header-->
    <!-- End Navbar -->
    <Hero :title="t('Checkout')" />
    <div
        class="max-w-6xl max-lg:max-w-2xl mx-auto bg-white dark:bg-slate-600 p-10 md:p-20"
    >
        <div v-if="cart.length != 0" class="grid lg:grid-cols-2 gap-12">
            <div>
                <div class="bg-gray-100 dark:bg-slate-800 p-6 rounded-md">
                    <h2
                        class="text-2xl font-extrabold text-gray-800 dark:text-white"
                    >
                        {{ t("Your Order") }}
                    </h2>
                    <div class="space-y-4 mt-8 divide-y-[1px]">
                        <div
                            v-for="(item, index) in cart"
                            class="flex items-center gap-4"
                        >
                            <div
                                class="w-24 h-24 shrink-0 bg-white p-2 rounded-md"
                            >
                                <img
                                    :src="item.menu_item.image"
                                    class="w-full h-full object-contain"
                                />
                            </div>

                            <div class="w-full">
                                <h3
                                    class="text-base font-semibold text-gray-800 dark:text-white"
                                >
                                    {{
                                        item.menu_item.name[$page.props.locale]
                                    }}
                                </h3>
                                <h5
                                    class="text-gray-800 dark:text-white font-bold cursor-pointer mt-0.5"
                                >
                                    {{ currency_symbol
                                    }}{{ Number(item.price).toFixed(2) }}
                                </h5>
                                <p
                                    v-if="item.size_variation_id"
                                    class="text-xs leading-7 text-black dark:text-white px-4"
                                >
                                    {{ t("Size") }}:
                                    {{
                                        item.menu_item.size_variations.find(
                                            (el) =>
                                                el.id == item.size_variation_id
                                        ).name[$page.props.locale]
                                    }}
                                </p>
                                <p
                                    class="text-xs leading-7 text-black dark:text-white px-4"
                                    v-for="variation in item.variations"
                                >
                                    {{ variation.name[$page.props.locale] }}:
                                    {{ variation.price }}{{ currency_symbol }}
                                </p>
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
                                            @click="deleteItem(index)"
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
                        <span class="font-bold"
                            >{{ currency_symbol }}{{ total_price }}</span
                        >
                    </li>
                    <li class="flex justify-between flex-wrap gap-4 text-sm">
                        {{ t("Membership Discount") }}
                        ({{ 7 }}%)
                        <span class="font-bold"
                            >{{ currency_symbol }}{{ discount_price }}</span
                        >
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
                            >{{ currency_symbol
                            }}{{
                                Number(total_price - discount_price).toFixed(2)
                            }}</span
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
                    <div
                        class="w-full"
                        v-if="branches.length != 0 && type != 'in-restaurant'"
                    >
                        <label
                            for="branch"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Branch") }}
                        </label>
                        <select
                            id="branch"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 disabled:bg-gray-100 ps-7"
                            v-model="form.branch_id"
                        >
                            <option :value="restaurant.id">
                                {{ restaurant.name }}
                            </option>
                            <option
                                v-for="branch in branches"
                                :key="branch.id"
                                :value="branch.id"
                            >
                                {{ branch.address }}
                            </option>
                        </select>
                        <InputError :message="form.errors.phone" />
                    </div>
                    <div class="w-full" v-if="type == 'in-restaurant'">
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
                    <template v-if="type == 'delivery'">
                        <div class="w-full">
                            <label
                                for="address"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ t("Delivery Method") }}
                            </label>

                            <ul class="grid w-full gap-3 md:grid-cols-2">
                                <li>
                                    <input
                                        type="radio"
                                        id="from_branch"
                                        name="hosting"
                                        value="from branch"
                                        class="hidden peer"
                                        required
                                        v-model="form.delivery_method"
                                        checked
                                    />
                                    <label
                                        for="from_branch"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-amber-500 peer-checked:border-amber-600 peer-checked:text-amber-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                                    >
                                        <div class="block">
                                            <div class="w-full font-semibold">
                                                {{ t("From Branch") }}
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
                                            viewBox="0 0 64 64"
                                            style="
                                                enable-background: new 0 0 512
                                                    512;
                                            "
                                            xml:space="preserve"
                                        >
                                            <g>
                                                <path
                                                    d="m61.83 24.45-4-6A1 1 0 0 0 57 18V5a3.009 3.009 0 0 0-3-3H10a3.009 3.009 0 0 0-3 3v13a1 1 0 0 0-.83.45l-4 6a.963.963 0 0 0-.05 1.02A.984.984 0 0 0 3 26v5a5.014 5.014 0 0 0 3 4.58V59a3.009 3.009 0 0 0 3 3h46a3.009 3.009 0 0 0 3-3V35.58A5.014 5.014 0 0 0 61 31v-5a.984.984 0 0 0 .88-.53.963.963 0 0 0-.05-1.02ZM47 4h2v4.59l-.29-.3a1.008 1.008 0 0 0-1.42 0l-.29.3Zm-6 0h4v7a.987.987 0 0 0 .62.92 1 1 0 0 0 1.09-.21l1.29-1.3 1.29 1.3A1.007 1.007 0 0 0 50 12a.838.838 0 0 0 .38-.08A.987.987 0 0 0 51 11V4h3a1 1 0 0 1 1 1v13H41Zm3.62 20-2-4h5.76l2 4ZM51 26v5a3 3 0 0 1-6 0v-5Zm-8 0v5a3 3 0 0 1-6 0v-5Zm-2.62-6 2 4h-5.66l-1.33-4ZM31 4h2v4.59l-.29-.3a1.008 1.008 0 0 0-1.42 0l-.29.3Zm-6 0h4v7a.987.987 0 0 0 .62.92 1 1 0 0 0 1.09-.21l1.29-1.3 1.29 1.3A1.007 1.007 0 0 0 34 12a.838.838 0 0 0 .38-.08A.987.987 0 0 0 35 11V4h4v14H25Zm10 22v5a3 3 0 0 1-6 0v-5Zm-5.61-2 1.33-4h2.56l1.33 4Zm-.78-4-1.33 4h-5.66l2-4Zm-14.99 4 2-4h5.76l-2 4ZM19 26v5a3 3 0 0 1-6 0v-5Zm2 0h6v5a3 3 0 0 1-6 0ZM15 4h2v4.59l-.29-.3a1.008 1.008 0 0 0-1.42 0l-.29.3ZM9 5a1 1 0 0 1 1-1h3v7a.987.987 0 0 0 .62.92 1 1 0 0 0 1.09-.21l1.29-1.3 1.29 1.3A1.007 1.007 0 0 0 18 12a.838.838 0 0 0 .38-.08A.987.987 0 0 0 19 11V4h4v14H9ZM7.54 20h5.84l-2 4H4.87ZM5 31v-5h6v5a3 3 0 0 1-6 0Zm35 29H24V50a8 8 0 0 1 16 0Zm16-1a1 1 0 0 1-1 1H42V50a10 10 0 0 0-20 0v10H9a1 1 0 0 1-1-1V36a4.994 4.994 0 0 0 4-2.03 4.956 4.956 0 0 0 8 0 4.956 4.956 0 0 0 8 0 4.956 4.956 0 0 0 8 0 4.956 4.956 0 0 0 8 0 4.956 4.956 0 0 0 8 0A4.994 4.994 0 0 0 56 36Zm3-28a3 3 0 0 1-6 0v-5h6Zm-6.38-7-2-4h5.84l2.67 4Z"
                                                    fill="currentColor"
                                                    opacity="1"
                                                    data-original="currentColor"
                                                ></path>
                                                <path
                                                    d="M44 43v6a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1Zm2 1h6v4h-6ZM19 42h-8a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1Zm-1 6h-6v-4h6Z"
                                                    fill="currentColor"
                                                    opacity="1"
                                                    data-original="currentColor"
                                                ></path>
                                            </g>
                                        </svg>
                                    </label>
                                </li>
                                <li>
                                    <input
                                        type="radio"
                                        id="delivery"
                                        name="hosting"
                                        value="delivery"
                                        class="hidden peer"
                                        v-model="form.delivery_method"
                                    />
                                    <label
                                        for="delivery"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-amber-500 peer-checked:border-amber-600 peer-checked:text-amber-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                                    >
                                        <div class="block">
                                            <div class="w-full font-semibold">
                                                {{ t("Delivery") }}
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
                                            viewBox="0 0 64 64"
                                            style="
                                                enable-background: new 0 0 512
                                                    512;
                                            "
                                            xml:space="preserve"
                                        >
                                            <g>
                                                <path
                                                    d="M54 41h-.33l-3.61-8.42A5 5 0 0 0 52 33a1 1 0 0 0 1-1v-8a1 1 0 0 0-1-1 5 5 0 0 0-4.71 3.4 3.69 3.69 0 0 0-2.52-1.51 5 5 0 0 0-1.9.12H42c-1.69 0-2.44-1-3.62-2.63l-.59-.83a3.45 3.45 0 0 0 1.7-.55 3.62 3.62 0 0 0 1.45-2.37 2.81 2.81 0 0 0 1.32-1.74 1.79 1.79 0 0 0-.55-1.57 4.77 4.77 0 0 1-.65-2.3A1 1 0 0 0 42 12a8 8 0 0 0-8.11-8A8.05 8.05 0 0 0 26 11.89a7.94 7.94 0 0 0 2.3 5.73 8.14 8.14 0 0 0 .7.62c-3.86.94-5.89 4.56-6.93 7.7a3.18 3.18 0 0 0-2.26-.94H9.19a3.18 3.18 0 0 0-2.68 1.46 1 1 0 0 0 1.68 1.09 1.18 1.18 0 0 1 1-.54H11v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V27h1.81A1.19 1.19 0 0 1 21 28.19v7.63A1.19 1.19 0 0 1 19.81 37H9.19A1.17 1.17 0 0 1 8 36h2a1 1 0 0 0 0-2H8v-2h1a1 1 0 0 0 0-2H4a1 1 0 0 0 0 2h2v2H2a1 1 0 0 0 0 2h4a3.18 3.18 0 0 0 3.17 3h5A6.67 6.67 0 0 0 13 40.54 18.33 18.33 0 0 0 11 49a1 1 0 0 0 1 1h3.07a8 8 0 0 0 15.86 0h15.14A8 8 0 1 0 54 41ZM16 29h-3v-2h3Zm35-3.83v5.66a3 3 0 0 1 0-5.66Zm-6.52 1.69a1.92 1.92 0 0 1 1.6 1.53l.26.61a9.1 9.1 0 0 1-4.51 1 2.64 2.64 0 0 1 1.48-3 2.92 2.92 0 0 1 1.17-.13Zm-4.93-9.76a1 1 0 0 0-.55.9 1.81 1.81 0 0 1-.63 1.32c-.57.38-1.63.29-3-.25a1 1 0 0 0-.22 0l-.15-.13v-3.83A2.11 2.11 0 0 1 37.11 13H39a6.69 6.69 0 0 0 1.17 3.62 2.64 2.64 0 0 1-.62.49ZM28 11.91A6.11 6.11 0 0 1 33.91 6H34a6 6 0 0 1 5.92 5h-2.81A4.12 4.12 0 0 0 33 15.11v2.81a6 6 0 0 1-5-6ZM31 20c3.27 0 4.47 1.71 5.74 3.51.92 1.32 2 2.76 3.8 3.27a5 5 0 0 0-.73 3.06 9.1 9.1 0 0 1-5.95-4.36 1 1 0 1 0-1.71 1 12.17 12.17 0 0 0 1.24 1.72l-.09.06A8.71 8.71 0 0 0 31.39 32H23v-1c.07-.61 1.21-11 8-11Zm-8 15.81V34h9c.2 0 5 .06 5 5v5h-4v-2a4.82 4.82 0 0 0-5-5h-5.23a3.16 3.16 0 0 0 .23-1.19ZM23 55a6 6 0 0 1-5.91-5h2.05a4 4 0 0 0 7.72 0h2.05A6 6 0 0 1 23 55Zm-1.72-5h3.44a2 2 0 0 1-3.44 0ZM13 48a16.09 16.09 0 0 1 1.82-6.59A4.58 4.58 0 0 1 18.93 39H28a2.85 2.85 0 0 1 3 3v3a5 5 0 0 0 1 3Zm23 0a3 3 0 0 1-2.82-2H38a1.88 1.88 0 0 1 2 1.93 8 8 0 0 1-1 .07Zm6.41-1.17a2.58 2.58 0 0 1-.49.43A3.74 3.74 0 0 0 39 44.14V39a6.56 6.56 0 0 0-5.58-6.85 6.76 6.76 0 0 1 1.29-2.45.94.94 0 0 0 .07-.11 10.93 10.93 0 0 0 5.4 2.34L43 44.19a3.56 3.56 0 0 1-.59 2.64ZM45 43.78 42.25 32a10.76 10.76 0 0 0 4.88-1.12l4.72 11L47.49 48H44a5.6 5.6 0 0 0 1-4.22ZM54 48h-1.72a2 2 0 1 1 0 2H54a1 1 0 0 0 0-2Zm-4.06 0 .35-.49a4 4 0 0 0-.15.49ZM54 55a6 6 0 0 1-5.91-5h2.05a4 4 0 1 0 1.22-4l2.13-3H54a6 6 0 0 1 0 12Z"
                                                    fill="currentColor"
                                                    opacity="1"
                                                    data-original="currentColor"
                                                ></path>
                                            </g>
                                        </svg>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="w-full"
                            v-if="form.delivery_method == 'delivery'"
                        >
                            <label
                                for="address"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ t("Delivery Address") }}
                            </label>

                            <template v-if="$page.props.auth.user">
                                <div
                                    class="flex items-center ps-4 border border-gray-200 rounded-lg dark:border-gray-700 hover:bg-gray-100 mb-2"
                                >
                                    <input
                                        id="main-location"
                                        type="radio"
                                        value="0"
                                        name="bordered-radio"
                                        class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 focus:ring-amber-500 dark:focus:ring-amber-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        checked
                                        v-model="form.delivery_location"
                                    />
                                    <label
                                        for="main-location"
                                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer"
                                        >{{ t("Main Location") }}</label
                                    >
                                </div>
                                <div
                                    class="flex items-center ps-4 border border-gray-200 rounded-lg dark:border-gray-700 hover:bg-gray-100 mb-2"
                                    v-for="(address, index) in $page.props.auth
                                        .user.addresses"
                                    :key="index"
                                >
                                    <input
                                        :id="`bordered-radio-${index}`"
                                        type="radio"
                                        :value="address.id"
                                        name="bordered-radio"
                                        class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 focus:ring-amber-500 dark:focus:ring-amber-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        v-model="form.delivery_location"
                                    />
                                    <label
                                        :for="`bordered-radio-${index}`"
                                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer"
                                        >{{
                                            address.name[$page.props.locale]
                                        }}</label
                                    >
                                </div>
                                <div
                                    class="flex items-center ps-4 border border-gray-200 rounded-lg dark:border-gray-700 hover:bg-gray-100 mb-2"
                                >
                                    <input
                                        id="another-location"
                                        type="radio"
                                        value="new"
                                        name="bordered-radio"
                                        class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 focus:ring-amber-500 dark:focus:ring-amber-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        v-model="form.delivery_location"
                                    />
                                    <label
                                        for="another-location"
                                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer"
                                        >{{ t("Another Location") }}</label
                                    >
                                </div>
                                <GoogleMap
                                    v-if="form.delivery_location == 'new'"
                                    api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                                    style="width: 100%; height: 300px"
                                    :center="center"
                                    :zoom="15"
                                    @click="handleClick"
                                >
                                    <Marker :options="markerOptions" />
                                </GoogleMap>
                            </template>
                            <GoogleMap
                                v-else
                                api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                                style="width: 100%; height: 300px"
                                :center="center"
                                :zoom="15"
                                @click="handleClick"
                            >
                                <Marker :options="markerOptions" />
                            </GoogleMap>
                        </div>
                    </template>

                    <div class="w-full">
                        <label
                            for="address"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ t("Payment Method") }}
                        </label>

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
                                        style="
                                            enable-background: new 0 0 512 512;
                                        "
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
                                        style="
                                            enable-background: new 0 0 512 512;
                                        "
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
                            {{
                                processing ? t("Processing") : t("Make Payment")
                            }}
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
                    </div>
                </div>
            </form>
        </div>
        <div v-else class="flex flex-col items-center">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                version="1.1"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                width="512"
                height="512"
                x="0"
                y="0"
                viewBox="0 0 510 510"
                style="enable-background: new 0 0 512 512"
                xml:space="preserve"
                fill-rule="evenodd"
                class="w-72 h-72"
            >
                <g>
                    <path
                        fill="#EFC7A4"
                        d="m204.547 34.087 38.065 142.062c1.532 5.719-1.867 11.607-7.587 13.14l-48.361 12.958c-5.72 1.533-11.607-1.867-13.14-7.586L135.459 52.599c-1.533-5.719 1.867-11.607 7.586-13.14l48.362-12.958c5.719-1.533 11.607 1.867 13.14 7.586z"
                        opacity="1"
                        data-original="#efc7a4"
                        class=""
                    ></path>
                    <path
                        fill="#D5A889"
                        d="M271.917 82.5v147.073c0 5.922-4.808 10.729-10.729 10.729H211.12c-5.921 0-10.728-4.807-10.728-10.729V82.5c0-5.921 4.807-10.728 10.728-10.728h50.068c5.921 0 10.729 4.807 10.729 10.728zM481.944 61.175l-73.537 127.369c-2.961 5.128-9.528 6.888-14.656 3.927l-43.359-25.034c-5.128-2.96-6.888-9.527-3.927-14.655l73.536-127.369c2.961-5.129 9.528-6.888 14.656-3.927l43.36 25.033c5.128 2.961 6.887 9.528 3.927 14.656z"
                        opacity="1"
                        data-original="#d5a889"
                    ></path>
                    <path
                        fill="#EFC7A4"
                        d="M376.863 61.195v147.073c0 5.921-4.808 10.728-10.729 10.728h-50.067c-5.922 0-10.729-4.807-10.729-10.728V61.195c0-5.922 4.807-10.729 10.729-10.729h50.067c5.921 0 10.729 4.807 10.729 10.729z"
                        opacity="1"
                        data-original="#efc7a4"
                        class=""
                    ></path>
                    <path
                        fill="#E4E8EB"
                        d="M399.759 366.788H157.857L89.836 134.731H498.5l-42.126 187.607c-6.374 26.092-29.755 44.45-56.615 44.45z"
                        opacity="1"
                        data-original="#e4e8eb"
                    ></path>
                    <path
                        fill="#E4E8EB"
                        d="M162.224 356.959a7.004 7.004 0 0 1-5.721 10.072c-.066.006-.132.012-.198.016a40.14 40.14 0 0 0-24.975 68.385 40.142 40.142 0 0 0 28.385 11.758h241.703c3.864 0 7 3.136 7 7 0 3.863-3.136 7-7 7H159.715a54.143 54.143 0 0 1-12.817-106.749l-78.4-267.606L15 55.948a7.003 7.003 0 0 1-2.562-9.562A7.004 7.004 0 0 1 22 43.823l55.927 32.29a6.997 6.997 0 0 1 3.217 4.094z"
                        opacity="1"
                        data-original="#e4e8eb"
                    ></path>
                    <g fill="#A9B0BB">
                        <circle
                            cx="218.641"
                            cy="454.19"
                            r="35.762"
                            fill="#A9B0BB"
                            opacity="1"
                            data-original="#a9b0bb"
                        ></circle>
                        <circle
                            cx="383.347"
                            cy="454.19"
                            r="35.762"
                            fill="#A9B0BB"
                            opacity="1"
                            data-original="#a9b0bb"
                        ></circle>
                        <path
                            d="M252.639 195v18.674c0 5.153-4.184 9.337-9.337 9.337h-18.674c-5.153 0-9.337-4.184-9.337-9.337V195c0-5.153 4.184-9.337 9.337-9.337h18.674c5.153 0 9.337 4.184 9.337 9.337zM373.045 195v18.674c0 5.153-4.184 9.337-9.337 9.337h-18.674c-5.153 0-9.337-4.184-9.337-9.337V195c0-5.153 4.184-9.337 9.337-9.337h18.674c5.153 0 9.337 4.184 9.337 9.337z"
                            fill="#A9B0BB"
                            opacity="1"
                            data-original="#a9b0bb"
                        ></path>
                    </g>
                    <path
                        fill="#F76C82"
                        d="M221.921 316.216a7.003 7.003 0 0 1-8.876 4.384 7.003 7.003 0 0 1-4.384-8.876c11.515-33.992 45.41-58.798 85.507-58.798s73.992 24.806 85.507 58.798a7.003 7.003 0 0 1-4.384 8.876 7.003 7.003 0 0 1-8.876-4.384c-9.705-28.649-38.453-49.29-72.247-49.29s-62.542 20.641-72.247 49.29z"
                        opacity="1"
                        data-original="#f76c82"
                    ></path>
                </g>
            </svg>
            <h2 class="text-2xl mt-4 font-semibold">{{ t("Empty Cart") }}</h2>
        </div>
    </div>
</template>
