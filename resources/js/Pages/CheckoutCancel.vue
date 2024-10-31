<script setup>
import { t } from "@/composables/trans";
import FrontLayout from "@/Layouts/FrontLayout.vue";

defineProps({
    session: Object,
});
</script>
<template>
    <FrontLayout>
        <section
            class="relative md:py-44 py-32 bg-[url('../../assets/images/bg/pages.jpg')] bg-no-repeat bg-bottom bg-cover"
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

                    <ul class="tracking-[0.5px] mb-0 inline-block mt-5">
                        <li
                            class="inline-block capitalize font-medium duration-500 ease-in-out text-white/50 hover:text-white"
                        >
                            <a :href="route('front.index')">{{ t("Home") }}</a>
                        </li>
                        <li
                            class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"
                        >
                            <i class="mdi mdi-chevron-right"></i>
                        </li>
                        <li
                            class="inline-block capitalize font-medium duration-500 ease-in-out text-white"
                            aria-current="page"
                        >
                            {{ t("Checkout") }}
                        </li>
                    </ul>
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
            class="font-sans max-w-6xl max-lg:max-w-2xl mx-auto bg-white dark:bg-slate-600 p-20"
        >
            <div
                class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-4"
            >
                <h1 class="text-3xl font-bold text-red-600 mb-4">
                    Payment Canceled
                </h1>
                <p class="text-lg text-gray-700 mb-6 text-center">
                    It seems like your payment was canceled. Don't worry, you
                    can try again or go back to the shop to review your order.
                </p>

                <div class="flex space-x-4">
                    <a
                        :href="route('front.checkout')"
                        class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-blue-600 transition duration-300"
                    >
                        Try Again
                    </a>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
