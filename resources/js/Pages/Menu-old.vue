<script setup>
import { Head } from "@inertiajs/vue3";
import { onBeforeUnmount, onMounted, ref } from "vue";

defineProps({
    restaurant: Object,
    categories: Array,
});

const filterOptions = ref(null);
const isDown = ref(false);
const startX = ref(0);
const scrollLeft = ref(0);

const onMouseDown = (e) => {
    isDown.value = true;
    startX.value = e.pageX - filterOptions.value.offsetLeft;
    scrollLeft.value = filterOptions.value.scrollLeft;
};

const onMouseUp = () => {
    isDown.value = false;
};

const onMouseLeave = () => {
    isDown.value = false;
};

const onMouseMove = (e) => {
    if (!isDown.value) return;
    e.preventDefault();
    const x = e.pageX - filterOptions.value.offsetLeft;
    const walk = (x - startX.value) * 2; // Adjust scroll speed
    filterOptions.value.scrollLeft = scrollLeft.value - walk;
};

const onTouchStart = (e) => {
    startX.value = e.touches[0].pageX - filterOptions.value.offsetLeft;
    scrollLeft.value = filterOptions.value.scrollLeft;
};

const onTouchMove = (e) => {
    const x = e.touches[0].pageX - filterOptions.value.offsetLeft;
    const walk = (x - startX.value) * 2; // Adjust scroll speed
    filterOptions.value.scrollLeft = scrollLeft.value - walk;
};

onMounted(() => {
    const element = filterOptions.value;
    element.addEventListener("mousedown", onMouseDown);
    element.addEventListener("mouseleave", onMouseLeave);
    element.addEventListener("mouseup", onMouseUp);
    element.addEventListener("mousemove", onMouseMove);
    element.addEventListener("touchstart", onTouchStart);
    element.addEventListener("touchmove", onTouchMove);
});

onBeforeUnmount(() => {
    const element = filterOptions.value;
    element.removeEventListener("mousedown", onMouseDown);
    element.removeEventListener("mouseleave", onMouseLeave);
    element.removeEventListener("mouseup", onMouseUp);
    element.removeEventListener("mousemove", onMouseMove);
    element.removeEventListener("touchstart", onTouchStart);
    element.removeEventListener("touchmove", onTouchMove);
});
</script>

<template>
    <Head :title="restaurant.name" />
    <div class="bg-gray-50">
        <div>
            <img
                src="/assets/images/hero.jpg"
                alt=""
                class="w-full object-cover h-80"
            />
        </div>
        <div
            class="w-4/5 md:w-3/5 -mt-4 relative z-10 bg-white shadow-md mx-auto p-6 rounded"
        >
            <div class="flex mb-6">
                <div class="w-1/4">
                    <svg
                        class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 20.8995L16.9497 15.9497C19.6834 13.2161 19.6834 8.78392 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.31658 8.78392 4.31658 13.2161 7.05025 15.9497L12 20.8995ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 13C13.1046 13 14 12.1046 14 11C14 9.89543 13.1046 9 12 9C10.8954 9 10 9.89543 10 11C10 12.1046 10.8954 13 12 13ZM12 15C9.79086 15 8 13.2091 8 11C8 8.79086 9.79086 7 12 7C14.2091 7 16 8.79086 16 11C16 13.2091 14.2091 15 12 15Z"
                        ></path>
                    </svg>
                </div>
                <div class="w-2/4 flex justify-center">
                    <a href="">
                        <svg
                            class="w-6 h-6"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="M13 9H17.5L17 11H13V20H11V11H7V9H11V7.12777C11 5.34473 11.1857 4.69816 11.5343 4.04631C11.8829 3.39446 12.3945 2.88288 13.0463 2.53427C13.6982 2.18565 14.3447 2 16.1278 2C16.6498 2 17.1072 2.05 17.5 2.15V4H16.1278C14.8041 4 14.401 4.07784 13.9895 4.29789C13.6862 4.46011 13.4601 4.68619 13.2979 4.98951C13.0778 5.40096 13 5.80407 13 7.12777V9Z"
                            ></path>
                        </svg>
                    </a>
                    <a href="">
                        <svg
                            class="w-6 h-6"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="M8 2H1L9.26086 13.0145L1.44995 21.9999H4.09998L10.4883 14.651L16 22H23L14.3917 10.5223L21.8001 2H19.1501L13.1643 8.88578L8 2ZM17 20L5 4H7L19 20H17Z"
                            ></path>
                        </svg>
                    </a>
                </div>
                <div class="w-1/4">
                    <svg
                        class="w-6 h-6 ms-auto"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM13 12H17V14H11V7H13V12Z"
                        ></path>
                    </svg>
                </div>
            </div>
            <div class="mb-4">
                <ul class="w-full gap-3 filter-options" ref="filterOptions">
                    <li v-for="category in categories" :key="category.id">
                        <a :href="'#' + category.name[$page.props.locale]">
                            <span class="text-lg mb-2 font-semibold block">{{
                                category.name[$page.props.locale]
                            }}</span>
                            <img
                                :src="category.image"
                                :alt="category.name[$page.props.locale]"
                                class="w-36 h-24 object-cover shadow-lg"
                            />
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div
                    class="mb-6"
                    v-for="category in categories"
                    :key="category.id"
                    :id="category.name[$page.props.locale]"
                >
                    <h2 class="text-2xl font-bold text-center mb-2">
                        {{ category.name[$page.props.locale] }}
                    </h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div
                            v-for="item in category.menu_items"
                            :key="item.id"
                            class="shadow-md flex gap-3 p-3 rounded-md relative"
                        >
                            <div class="w-1/4 flex-shrink-0">
                                <img :src="item.image" />
                            </div>
                            <div class="3/4">
                                <h3 class="font-semibold">
                                    {{ item.name[$page.props.locale] }}
                                </h3>
                                <span
                                    class="bg-amber-500 text-white px-2 rounded-sm my-2 inline-block"
                                    >${{ item.price }}</span
                                >
                                <p class="text-sm">
                                    {{ item.description[$page.props.locale] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.filter-options {
    @apply flex gap-4 text-center;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}
.filter-options li {
    flex-shrink: 0;
    white-space: nowrap;
}

.filter-options::-webkit-scrollbar {
    display: none;
}
</style>
