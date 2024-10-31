<script setup>
import Hero from "@/Components/Hero.vue";
import Navbar from "@/Components/Navbar.vue";
import { t } from "@/Composables/trans";

defineProps({
    session: Object,
    restaurant: Object,
    type: String,
    settings: Object,
});
</script>
<template>
    <Navbar :restaurant="restaurant" :settings="settings" :type="type" />
    <Hero :title="t('Success')" />

    <div class="md:max-w-lg mt-8 mx-auto">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            version="1.1"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            x="0"
            y="0"
            viewBox="0 0 24 24"
            style="enable-background: new 0 0 512 512"
            xml:space="preserve"
            class="w-52 mx-auto block"
        >
            <g>
                <rect
                    width="20"
                    height="16"
                    x="2"
                    y="2"
                    fill="#FCBF49"
                    rx="3"
                    opacity="1"
                    data-original="#fcbf49"
                    class=""
                ></rect>
                <path
                    fill="#0B4F6C"
                    d="M2 5.25h20v1.5H2z"
                    opacity="1"
                    data-original="#0b4f6c"
                    class=""
                ></path>
                <circle
                    cx="12"
                    cy="16"
                    r="6"
                    fill="#20BF55"
                    opacity="1"
                    data-original="#20bf55"
                    class=""
                ></circle>
                <path
                    fill="#EDEBEA"
                    d="M11.5 18a.747.747 0 0 1-.53-.22l-1.5-1.5a.75.75 0 1 1 1.06-1.06l.97.97 1.97-1.97a.75.75 0 1 1 1.06 1.06l-2.5 2.5a.747.747 0 0 1-.53.22z"
                    opacity="1"
                    data-original="#edebea"
                    class=""
                ></path>
            </g>
        </svg>
    </div>

    <div
        class="font-sans max-w-6xl md:max-w-2xl mt-8 mx-auto bg-white dark:bg-slate-600 p-20"
    >
        <div class="bg-green-100 text-green-700 p-10">
            <h1>Payment Successful!</h1>
            <p>
                Thank you for your order,
                {{ session.customer_details.name }}!
            </p>

            <h2 class="mt-5 font-bold">Order Summary:</h2>
            <ul>
                <li>
                    <strong>Amount Paid:</strong> ${{
                        (session.amount_total / 100).toFixed(2)
                    }}
                </li>
                <li>
                    <strong>Payment Method:</strong>
                    {{ session.payment_method_types[0] }}
                </li>
                <li>
                    <strong>Email:</strong>
                    {{ session.customer_details.email }}
                </li>
            </ul>
            <h2 class="mt-5 font-bold">Purchased Items:</h2>
            <ul>
                <li v-for="item in session.line_items.data" :key="item.id">
                    {{ item.description }} - ${{
                        (item.amount_total / 100).toFixed(2)
                    }}
                </li>
            </ul>
        </div>
    </div>
</template>
