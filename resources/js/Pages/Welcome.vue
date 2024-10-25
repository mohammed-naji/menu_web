<template>
    <Head title="Home" />
    <div class="bg-white flex justify-center items-center h-screen">
        <LangSwitcher />
        <div class="relative isolate px-6 lg:px-8">
            <div
                class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                aria-hidden="true"
            >
                <div
                    class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#fff5d0] to-[#fdc89d] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                    style="
                        clip-path: polygon(
                            74.1% 44.1%,
                            100% 61.6%,
                            97.5% 26.9%,
                            85.5% 0.1%,
                            80.7% 2%,
                            72.5% 32.5%,
                            60.2% 62.4%,
                            52.4% 68.1%,
                            47.5% 58.3%,
                            45.2% 34.5%,
                            27.5% 76.7%,
                            0.1% 64.9%,
                            17.9% 100%,
                            27.6% 76.8%,
                            76.1% 97.7%,
                            74.1% 44.1%
                        );
                    "
                />
            </div>
            <div class="mx-auto max-w-2xl">
                <div class="text-center">
                    <img
                        src="/assets/images/logo.png"
                        width="150"
                        class="block mx-auto"
                        alt=""
                    />
                    <h1
                        class="text-balance text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl"
                    >
                        {{ t("New Generation Of Online Menu") }}
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        {{ t("System") }}
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            :href="route('login')"
                            class="rounded-md bg-amber-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600"
                            >{{ t("Login") }}</Link
                        >
                        <Link
                            :href="route('register')"
                            class="text-sm font-semibold leading-6 text-gray-900"
                            >{{ t("Register") }}
                            <span
                                aria-hidden="true"
                                class="rtl:rotate-180 inline-block"
                                >→</span
                            ></Link
                        >
                    </div>
                </div>
            </div>
            <div
                class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                aria-hidden="true"
            >
                <div
                    class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#fdc89d] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                    style="
                        clip-path: polygon(
                            74.1% 44.1%,
                            100% 61.6%,
                            97.5% 26.9%,
                            85.5% 0.1%,
                            80.7% 2%,
                            72.5% 32.5%,
                            60.2% 62.4%,
                            52.4% 68.1%,
                            47.5% 58.3%,
                            45.2% 34.5%,
                            27.5% 76.7%,
                            0.1% 64.9%,
                            17.9% 100%,
                            27.6% 76.8%,
                            76.1% 97.7%,
                            74.1% 44.1%
                        );
                    "
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import LangSwitcher from "@/Components/LangSwitcher.vue";
import { t } from "@/Composables/trans";
import { Head, Link } from "@inertiajs/vue3";
</script>
