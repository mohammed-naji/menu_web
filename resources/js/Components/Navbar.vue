<script setup>
import { t } from "@/Composables/trans";
import { Link } from "@inertiajs/vue3";

defineProps({
    restaurant: Object,
    type: String,
    settings: Object,
});
</script>
<template>
    <nav id="topnav" class="defaultscroll is-sticky">
        <div class="flex items-center justify-between py-2 px-10">
            <!-- Logo container-->
            <Link class="logo" :href="route('menu', [restaurant.code, type])">
                <!-- <img src="/assets/images/logo.png" class="w-20" alt="" /> -->
                <img
                    class="w-20 h-12 object-contain"
                    :src="settings.logo"
                    alt=""
                />
            </Link>
            <!-- End Logo container-->

            <ul class="navigation-menu flex gap-x-6 nav-light justify-end">
                <template v-if="!$page.props.auth.user">
                    <li>
                        <Link
                            class="sub-menu-item"
                            :href="
                                route('customer.login', [restaurant.code, type])
                            "
                            >{{ t("Login") }}</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="sub-menu-item"
                            :href="
                                route('customer.register', [
                                    restaurant.code,
                                    type,
                                ])
                            "
                            >{{ t("Register") }}</Link
                        >
                    </li>
                </template>
                <template v-else>
                    <li>
                        <Link
                            class="sub-menu-item"
                            :href="
                                route('customer.index', [restaurant.code, type])
                            "
                            >{{ t("My Account") }}</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="sub-menu-item"
                            :href="
                                route('customer.logout', [
                                    restaurant.code,
                                    type,
                                ])
                            "
                            method="post"
                            >{{ t("Logout") }}</Link
                        >
                    </li>
                </template>
            </ul>
        </div>
        <!--end container-->
    </nav>
</template>
