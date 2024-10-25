<script setup>
import { computed, onMounted, ref } from "vue";
import Shuffle from "shufflejs";
import { t } from "@/Composables/trans";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { GoogleMap, Marker } from "vue3-google-map";
import LangSwitcher from "@/Components/LangSwitcher.vue";

const props = defineProps({
    restaurant: Object,
    categories: Array,
    type: String,
});

const center = usePage().props.settings.location
    ? JSON.parse(usePage().props.settings.location)
    : { lat: 40.689247, lng: -74.044502 };

const markerOptions = ref({
    position: center,
    label: "A",
    title: "Location",
});

const shuffleInstance = ref(null);
const shuffleContainer = ref(null);
const selectedCategory = ref("all");
const showCart = ref(false);
const showModal = ref(false);
const showCustomizationModal = ref(false);
const showLocationModal = ref(false);
const cart = ref([]);
const menu_item = ref({});

const item = ref({
    menu_item: "",
    price: "",
    quantity: 1,
    variations: [],
    size_variation_id: "",
    type: props.type,
});

onMounted(() => {
    shuffleInstance.value = new Shuffle(shuffleContainer.value, {
        itemSelector: ".grid-item",
        sizer: null,
    });

    getCart();
});

const filter = (category) => {
    selectedCategory.value = category;
    if (category === "all") {
        shuffleInstance.value.filter(Shuffle.ALL_ITEMS);
    } else {
        shuffleInstance.value.filter((element) =>
            element.classList.contains(category)
        );
    }
};

const addToCart = (added_item = null, action = null) => {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (added_item) {
        const el = {
            menu_item: added_item,
            price: Number(
                added_item.discount && added_item.discount > 0
                    ? added_item.price -
                          added_item.price * (added_item.discount / 100)
                    : added_item.price
            ).toFixed(2),
            quantity: 1,
        };
        cart.push(el);
    } else {
        item.value.price = subtotal.value;
        cart.push(item.value);
    }

    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: "success",
        title: t("Item added successfully"),
    });

    localStorage.setItem("cart", JSON.stringify(cart));

    getCart();

    if (action == "close") {
        closeModal();
    }
};

const getCart = () => {
    cart.value = JSON.parse(localStorage.getItem("cart")) || [];
};

const deleteItem = (index) => {
    cart.value.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart.value));
};

const total_price = computed(() => {
    return cart.value
        .reduce((acc, item) => acc + Number(item.price), 0)
        .toFixed(2);
});

const showItemVariation = (id) => {
    menu_item.value = props.restaurant.menu_items.find(
        (menuItem) => menuItem.id === id
    );
    item.value.menu_item = menu_item.value;
    item.value.price = subtotal.value;
    showCustomizationModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    showCustomizationModal.value = false;
    showLocationModal.value = false;
    resetItem();
};

const resetItem = () => {
    item.value.quantity = 1;
    item.value.price = "";
    item.value.variations = [];

    let inputs = document.querySelectorAll("table input[type=radio]");
    inputs.forEach((input) => {
        input.checked = false;
    });
};

const changeItemPrice = (price) => {
    menu_item.value.price = price;
};

const subtotal = computed(() => {
    let subtotal = Number(menu_item.value.price);
    if (item.value.variations.length > 0) {
        item.value.variations.forEach((variation) => {
            subtotal += Number(variation.price);
        });
    }
    return subtotal * item.value.quantity;
});

const addOrderVariation = (variation) => {
    const variationGroupIndex = item.value.variations.findIndex(
        (el) => el.variation_id === variation.variation_id
    );

    if (variationGroupIndex === -1) {
        item.value.variations.push(variation);
    } else {
        const variationIndex = item.value.variations.findIndex(
            (el) =>
                el.variation_id === variation.variation_id &&
                el.id === variation.id
        );

        if (variationIndex !== -1) {
            item.value.variations.splice(variationIndex, 1);
            const inputElement = document.querySelector(
                `table input[value="${variation.id}"]`
            );
            if (inputElement) {
                inputElement.checked = false;
            }
        } else {
            if (variation.price == 0) {
                item.value.variations.push(variation);
            } else {
                item.value.variations.splice(variationGroupIndex, 1);
                item.value.variations.push(variation);
            }
        }
    }
};
</script>

<template>
    <Head :title="t('Menu')" />
    <!-- Start Navbar -->
    <nav id="topnav" class="defaultscroll is-sticky">
        <div class="flex items-center justify-between py-2 px-10">
            <!-- Logo container-->
            <Link class="logo" :href="route('menu', [restaurant.code, type])">
                <!-- <img src="/assets/images/logo.png" class="w-20" alt="" /> -->
                <img
                    class="w-20 h-12 object-contain"
                    :src="$page.props.settings.logo"
                    alt=""
                />
            </Link>
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
        <LangSwitcher />
        <div
            class="absolute inset-0 bg-gradient-to-t from-slate-900 to-slate-900/70"
        ></div>
        <div class="container relative">
            <div class="grid grid-cols-1 text-center mt-6">
                <div>
                    <h5
                        class="md:text-4xl text-3xl md:leading-normal leading-normal font-medium text-white mb-0"
                    >
                        {{ $page.props.settings.name }}
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
    <div class="flex mt-10 md:px-28 px-3">
        <div class="w-1/4 flex gap-2">
            <svg
                class="w-6 h-6 cursor-pointer"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                @click="showLocationModal = true"
            >
                <path
                    d="M12 20.8995L16.9497 15.9497C19.6834 13.2161 19.6834 8.78392 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.31658 8.78392 4.31658 13.2161 7.05025 15.9497L12 20.8995ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 13C13.1046 13 14 12.1046 14 11C14 9.89543 13.1046 9 12 9C10.8954 9 10 9.89543 10 11C10 12.1046 10.8954 13 12 13ZM12 15C9.79086 15 8 13.2091 8 11C8 8.79086 9.79086 7 12 7C14.2091 7 16 8.79086 16 11C16 13.2091 14.2091 15 12 15Z"
                ></path>
            </svg>
            <a
                v-if="$page.props.settings.whatsapp"
                :href="`https://wa.me/${$page.props.settings.whatsapp}`"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#25D366]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M7.25361 18.4944L7.97834 18.917C9.18909 19.623 10.5651 20 12.001 20C16.4193 20 20.001 16.4183 20.001 12C20.001 7.58172 16.4193 4 12.001 4C7.5827 4 4.00098 7.58172 4.00098 12C4.00098 13.4363 4.37821 14.8128 5.08466 16.0238L5.50704 16.7478L4.85355 19.1494L7.25361 18.4944ZM2.00516 22L3.35712 17.0315C2.49494 15.5536 2.00098 13.8345 2.00098 12C2.00098 6.47715 6.47813 2 12.001 2C17.5238 2 22.001 6.47715 22.001 12C22.001 17.5228 17.5238 22 12.001 22C10.1671 22 8.44851 21.5064 6.97086 20.6447L2.00516 22ZM8.39232 7.30833C8.5262 7.29892 8.66053 7.29748 8.79459 7.30402C8.84875 7.30758 8.90265 7.31384 8.95659 7.32007C9.11585 7.33846 9.29098 7.43545 9.34986 7.56894C9.64818 8.24536 9.93764 8.92565 10.2182 9.60963C10.2801 9.76062 10.2428 9.95633 10.125 10.1457C10.0652 10.2428 9.97128 10.379 9.86248 10.5183C9.74939 10.663 9.50599 10.9291 9.50599 10.9291C9.50599 10.9291 9.40738 11.0473 9.44455 11.1944C9.45903 11.25 9.50521 11.331 9.54708 11.3991C9.57027 11.4368 9.5918 11.4705 9.60577 11.4938C9.86169 11.9211 10.2057 12.3543 10.6259 12.7616C10.7463 12.8783 10.8631 12.9974 10.9887 13.108C11.457 13.5209 11.9868 13.8583 12.559 14.1082L12.5641 14.1105C12.6486 14.1469 12.692 14.1668 12.8157 14.2193C12.8781 14.2457 12.9419 14.2685 13.0074 14.2858C13.0311 14.292 13.0554 14.2955 13.0798 14.2972C13.2415 14.3069 13.335 14.2032 13.3749 14.1555C14.0984 13.279 14.1646 13.2218 14.1696 13.2222V13.2238C14.2647 13.1236 14.4142 13.0888 14.5476 13.097C14.6085 13.1007 14.6691 13.1124 14.7245 13.1377C15.2563 13.3803 16.1258 13.7587 16.1258 13.7587L16.7073 14.0201C16.8047 14.0671 16.8936 14.1778 16.8979 14.2854C16.9005 14.3523 16.9077 14.4603 16.8838 14.6579C16.8525 14.9166 16.7738 15.2281 16.6956 15.3913C16.6406 15.5058 16.5694 15.6074 16.4866 15.6934C16.3743 15.81 16.2909 15.8808 16.1559 15.9814C16.0737 16.0426 16.0311 16.0714 16.0311 16.0714C15.8922 16.159 15.8139 16.2028 15.6484 16.2909C15.391 16.428 15.1066 16.5068 14.8153 16.5218C14.6296 16.5313 14.4444 16.5447 14.2589 16.5347C14.2507 16.5342 13.6907 16.4482 13.6907 16.4482C12.2688 16.0742 10.9538 15.3736 9.85034 14.402C9.62473 14.2034 9.4155 13.9885 9.20194 13.7759C8.31288 12.8908 7.63982 11.9364 7.23169 11.0336C7.03043 10.5884 6.90299 10.1116 6.90098 9.62098C6.89729 9.01405 7.09599 8.4232 7.46569 7.94186C7.53857 7.84697 7.60774 7.74855 7.72709 7.63586C7.85348 7.51651 7.93392 7.45244 8.02057 7.40811C8.13607 7.34902 8.26293 7.31742 8.39232 7.30833Z"
                    ></path>
                </svg>
            </a>
        </div>
        <div class="w-2/4 flex gap-2 justify-center">
            <a
                v-if="$page.props.settings.facebook"
                :href="$page.props.settings.facebook"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#3b5998]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M13 9H17.5L17 11H13V20H11V11H7V9H11V7.12777C11 5.34473 11.1857 4.69816 11.5343 4.04631C11.8829 3.39446 12.3945 2.88288 13.0463 2.53427C13.6982 2.18565 14.3447 2 16.1278 2C16.6498 2 17.1072 2.05 17.5 2.15V4H16.1278C14.8041 4 14.401 4.07784 13.9895 4.29789C13.6862 4.46011 13.4601 4.68619 13.2979 4.98951C13.0778 5.40096 13 5.80407 13 7.12777V9Z"
                    ></path>
                </svg>
            </a>
            <a
                v-if="$page.props.settings.x"
                :href="$page.props.settings.x"
                target="_blank"
            >
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
            <a
                v-if="$page.props.settings.instagram"
                :href="$page.props.settings.instagram"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#c32aa3]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M12.001 9C10.3436 9 9.00098 10.3431 9.00098 12C9.00098 13.6573 10.3441 15 12.001 15C13.6583 15 15.001 13.6569 15.001 12C15.001 10.3427 13.6579 9 12.001 9ZM12.001 7C14.7614 7 17.001 9.2371 17.001 12C17.001 14.7605 14.7639 17 12.001 17C9.24051 17 7.00098 14.7629 7.00098 12C7.00098 9.23953 9.23808 7 12.001 7ZM18.501 6.74915C18.501 7.43926 17.9402 7.99917 17.251 7.99917C16.5609 7.99917 16.001 7.4384 16.001 6.74915C16.001 6.0599 16.5617 5.5 17.251 5.5C17.9393 5.49913 18.501 6.0599 18.501 6.74915ZM12.001 4C9.5265 4 9.12318 4.00655 7.97227 4.0578C7.18815 4.09461 6.66253 4.20007 6.17416 4.38967C5.74016 4.55799 5.42709 4.75898 5.09352 5.09255C4.75867 5.4274 4.55804 5.73963 4.3904 6.17383C4.20036 6.66332 4.09493 7.18811 4.05878 7.97115C4.00703 9.0752 4.00098 9.46105 4.00098 12C4.00098 14.4745 4.00753 14.8778 4.05877 16.0286C4.0956 16.8124 4.2012 17.3388 4.39034 17.826C4.5591 18.2606 4.7605 18.5744 5.09246 18.9064C5.42863 19.2421 5.74179 19.4434 6.17187 19.6094C6.66619 19.8005 7.19148 19.9061 7.97212 19.9422C9.07618 19.9939 9.46203 20 12.001 20C14.4755 20 14.8788 19.9934 16.0296 19.9422C16.8117 19.9055 17.3385 19.7996 17.827 19.6106C18.2604 19.4423 18.5752 19.2402 18.9074 18.9085C19.2436 18.5718 19.4445 18.2594 19.6107 17.8283C19.8013 17.3358 19.9071 16.8098 19.9432 16.0289C19.9949 14.9248 20.001 14.5389 20.001 12C20.001 9.52552 19.9944 9.12221 19.9432 7.97137C19.9064 7.18906 19.8005 6.66149 19.6113 6.17318C19.4434 5.74038 19.2417 5.42635 18.9084 5.09255C18.573 4.75715 18.2616 4.55693 17.8271 4.38942C17.338 4.19954 16.8124 4.09396 16.0298 4.05781C14.9258 4.00605 14.5399 4 12.001 4ZM12.001 2C14.7176 2 15.0568 2.01 16.1235 2.06C17.1876 2.10917 17.9135 2.2775 18.551 2.525C19.2101 2.77917 19.7668 3.1225 20.3226 3.67833C20.8776 4.23417 21.221 4.7925 21.476 5.45C21.7226 6.08667 21.891 6.81333 21.941 7.8775C21.9885 8.94417 22.001 9.28333 22.001 12C22.001 14.7167 21.991 15.0558 21.941 16.1225C21.8918 17.1867 21.7226 17.9125 21.476 18.55C21.2218 19.2092 20.8776 19.7658 20.3226 20.3217C19.7668 20.8767 19.2076 21.22 18.551 21.475C17.9135 21.7217 17.1876 21.89 16.1235 21.94C15.0568 21.9875 14.7176 22 12.001 22C9.28431 22 8.94514 21.99 7.87848 21.94C6.81431 21.8908 6.08931 21.7217 5.45098 21.475C4.79264 21.2208 4.23514 20.8767 3.67931 20.3217C3.12348 19.7658 2.78098 19.2067 2.52598 18.55C2.27848 17.9125 2.11098 17.1867 2.06098 16.1225C2.01348 15.0558 2.00098 14.7167 2.00098 12C2.00098 9.28333 2.01098 8.94417 2.06098 7.8775C2.11014 6.8125 2.27848 6.0875 2.52598 5.45C2.78014 4.79167 3.12348 4.23417 3.67931 3.67833C4.23514 3.1225 4.79348 2.78 5.45098 2.525C6.08848 2.2775 6.81348 2.11 7.87848 2.06C8.94514 2.0125 9.28431 2 12.001 2Z"
                    ></path>
                </svg>
            </a>
            <a
                v-if="$page.props.settings.tiktok"
                :href="$page.props.settings.tiktok"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#ee1d52]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M11.0004 2V8.41396C10.5947 8.33909 10.1768 8.3 9.75039 8.3C5.96724 8.3 2.90039 11.3668 2.90039 15.15C2.90039 18.9332 5.96724 22 9.75039 22C13.5335 22 16.6004 18.9332 16.6004 15.15V11.4136C17.6366 11.8539 18.7662 12.1 20.0005 12.1H21.0005V6.5H20.0005C18.0966 6.5 16.6004 4.96259 16.6004 3V2H11.0004ZM13.0004 4H14.688C15.0818 6.22009 16.7673 7.99607 19.0005 8.4091V10.0282C17.9624 9.87602 17.0253 9.48645 16.1567 8.905L14.6004 7.86327V15.15C14.6004 17.8286 12.429 20 9.75039 20C7.07181 20 4.90039 17.8286 4.90039 15.15C4.90039 12.4714 7.07181 10.3 9.75039 10.3C9.83431 10.3 9.91769 10.3021 10.0005 10.3063V11.9095C9.91795 11.9032 9.83454 11.9 9.75039 11.9C7.95547 11.9 6.50039 13.3551 6.50039 15.15C6.50039 16.9449 7.95547 18.4 9.75039 18.4C11.5453 18.4 13.0004 16.9449 13.0004 15.15C13.0004 11.4334 12.9992 7.71665 13.0004 4ZM8.50039 15.15C8.50039 14.4596 9.06003 13.9 9.75039 13.9C10.4407 13.9 11.0004 14.4596 11.0004 15.15C11.0004 15.8404 10.4407 16.4 9.75039 16.4C9.06003 16.4 8.50039 15.8404 8.50039 15.15Z"
                    ></path>
                </svg>
            </a>
            <a
                v-if="$page.props.settings.snapchat"
                :href="$page.props.settings.snapchat"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#fffc00]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M15.3826 10.5792C15.3904 10.4826 15.3969 10.4035 15.4025 10.3311C15.4137 10.1844 15.4243 10.0268 15.4344 9.85059C15.4662 9.29326 15.4853 8.67669 15.4853 8.00554C15.4853 7.1344 15.191 6.51928 14.571 5.94177C13.9117 5.3277 13.1124 5 11.9819 5C10.8441 5 10.0229 5.33486 9.4705 5.88766C8.77543 6.58319 8.51358 7.10616 8.51358 7.98803C8.51358 8.50939 8.57456 9.98166 8.60927 10.6065C8.63818 11.1269 8.46325 11.6252 8.14058 12.0076C8.19484 12.1062 8.24561 12.2117 8.29289 12.3251C8.59378 13.0961 8.4912 13.8677 8.1413 14.5963C7.74878 15.4136 7.41022 15.9889 6.7313 16.7497C6.52804 16.9775 6.31412 17.1923 6.08941 17.3927C6.25199 17.5489 6.38901 17.7334 6.49251 17.9404C6.49542 17.9462 6.49827 17.9519 6.50106 17.9575C7.01006 17.9137 7.52238 17.93 8.08587 18.032C8.89536 18.1784 9.41032 18.4661 10.2803 19.0933L10.2958 19.1042C10.4079 19.184 10.4079 19.184 10.5093 19.2562C11.1281 19.6957 11.3863 19.802 11.9819 19.802C12.5908 19.802 12.892 19.6808 13.5054 19.2501C13.554 19.2159 13.6109 19.1756 13.7125 19.1036C14.5885 18.4722 15.1194 18.1765 15.9433 18.0281C16.4093 17.9491 16.9362 17.9154 17.5022 17.9538C17.517 17.9255 17.5331 17.8957 17.551 17.8635C17.6502 17.685 17.7747 17.525 17.9192 17.3873C17.7052 17.1946 17.4999 16.9885 17.3032 16.7705C16.8745 16.2954 16.5148 15.7932 16.2164 15.2892C16.0348 14.9826 15.9084 14.7317 15.8559 14.611C15.4931 13.8621 15.3867 13.0718 15.7191 12.29C15.7598 12.1923 15.8038 12.1005 15.8509 12.0139C15.5196 11.6223 15.3447 11.1099 15.3826 10.5792ZM5.06689 10.4776C5.48626 10.4776 5.66798 10.7826 6.28626 10.7826C6.44278 10.7826 6.54713 10.7478 6.61235 10.7174C6.60365 10.5609 6.51358 8.73083 6.51358 7.98803C6.51358 6.29966 7.23253 5.29775 8.05583 4.47391C8.87912 3.65008 10.1616 3 11.9819 3C13.8022 3 15.0481 3.65305 15.9341 4.47826C16.8201 5.30347 17.4853 6.4089 17.4853 8.00554C17.4853 9.56082 17.3863 10.6 17.3776 10.7217C17.4341 10.7522 17.5294 10.787 17.6558 10.787C18.2866 10.787 18.2866 10.4776 18.9863 10.4776C19.6715 10.4776 19.9689 11.0468 19.9689 11.3C19.9689 11.9213 19.1368 12.2675 18.6384 12.4261C18.2703 12.5433 17.708 12.7174 17.5645 13.0609C17.4906 13.2348 17.521 13.4609 17.6558 13.7391C17.6597 13.747 18.9167 16.6217 21.5863 17.0652C21.821 17.1 21.9776 17.3065 21.9776 17.5478C21.9776 17.8805 21.608 18.1652 21.2515 18.3304C20.808 18.5304 20.1602 18.7 19.2993 18.8348C19.2558 18.913 19.1648 19.3201 19.0645 19.7217C18.9292 20.2636 18.2633 20.0881 18.0732 20.0478C17.2413 19.8716 16.5341 19.9565 16.2776 20C15.795 20.087 15.3645 20.3783 14.8819 20.7261C14.1689 21.2304 13.4172 21.802 11.9819 21.802C10.5466 21.802 9.83843 21.2304 9.12539 20.7261C8.64278 20.3783 8.22066 20.0888 7.72974 20C6.83117 19.8374 6.15981 20.0357 5.93409 20.0565C5.70837 20.0773 5.09257 20.3009 4.93843 19.7304C4.89359 19.5645 4.74713 18.9217 4.70365 18.8348C3.84713 18.7 3.19496 18.5217 2.75148 18.3217C2.38626 18.1565 2.02539 17.8787 2.02539 17.5435C2.02539 17.3078 2.18336 17.1034 2.41669 17.0609C5.06017 16.5785 6.18185 14.0565 6.33843 13.7304C6.47054 13.4554 6.4993 13.2304 6.42974 13.0522C6.28626 12.7087 5.72539 12.5391 5.35583 12.4174C5.25148 12.3826 4.02067 12.0445 4.02067 11.3C4.02067 11.06 4.22508 10.7269 4.60241 10.5704C4.76382 10.5035 4.9609 10.4776 5.06689 10.4776Z"
                    ></path>
                </svg>
            </a>
            <a
                v-if="$page.props.settings.youtube"
                :href="$page.props.settings.youtube"
                target="_blank"
            >
                <svg
                    class="w-6 h-6 text-[#ff0000]"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M19.6069 6.99482C19.5307 6.69695 19.3152 6.47221 19.0684 6.40288C18.6299 6.28062 16.501 6 12.001 6C7.50098 6 5.37252 6.28073 4.93225 6.40323C4.68776 6.47123 4.4723 6.69593 4.3951 6.99482C4.2863 7.41923 4.00098 9.19595 4.00098 12C4.00098 14.804 4.2863 16.5808 4.3954 17.0064C4.47126 17.3031 4.68676 17.5278 4.93251 17.5968C5.37252 17.7193 7.50098 18 12.001 18C16.501 18 18.6299 17.7194 19.0697 17.5968C19.3142 17.5288 19.5297 17.3041 19.6069 17.0052C19.7157 16.5808 20.001 14.8 20.001 12C20.001 9.2 19.7157 7.41923 19.6069 6.99482ZM21.5442 6.49818C22.001 8.28 22.001 12 22.001 12C22.001 12 22.001 15.72 21.5442 17.5018C21.2897 18.4873 20.547 19.2618 19.6056 19.5236C17.8971 20 12.001 20 12.001 20C12.001 20 6.10837 20 4.39637 19.5236C3.45146 19.2582 2.70879 18.4836 2.45774 17.5018C2.00098 15.72 2.00098 12 2.00098 12C2.00098 12 2.00098 8.28 2.45774 6.49818C2.71227 5.51273 3.45495 4.73818 4.39637 4.47636C6.10837 4 12.001 4 12.001 4C12.001 4 17.8971 4 19.6056 4.47636C20.5505 4.74182 21.2932 5.51636 21.5442 6.49818ZM10.001 15.5V8.5L16.001 12L10.001 15.5Z"
                    ></path>
                </svg>
            </a>
        </div>
        <div class="w-1/4">
            <svg
                class="w-6 h-6 ms-auto cursor-pointer"
                @click="showModal = !showModal"
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

    <!-- Start -->
    <section class="relative md:py-14 py-10">
        <div class="container relative">
            <div class="grid grid-cols-1 items-center gap-6">
                <div class="filters-group-wrap text-center">
                    <div class="filters-group">
                        <ul
                            class="mb-0 list-none container-filter-border-bottom filter-options flex md:justify-center gap-4"
                        >
                            <li
                                class="inline-block text-sm uppercase font-semibold mb-3 cursor-pointer relative border-b border-transparent text-slate-700 duration-500 text-center"
                                @click="filter('all')"
                            >
                                <span
                                    class="block mb-1"
                                    :class="{
                                        'text-amber-500':
                                            selectedCategory === 'all',
                                    }"
                                    >{{ t("All") }}</span
                                >
                                <img
                                    src="/uploads/all.jpg"
                                    alt="All"
                                    class="w-28 h-20 object-cover block mx-auto"
                                />
                            </li>
                            <li
                                v-for="category in categories"
                                :key="category.id"
                                class="inline-block text-sm uppercase font-semibold mb-3 cursor-pointer relative border-b border-transparent text-slate-700 duration-500 text-center"
                                @click="filter(`category-${category.id}`)"
                            >
                                <span
                                    class="block mb-1"
                                    :class="{
                                        'text-amber-500':
                                            selectedCategory ===
                                            `category-${category.id}`,
                                    }"
                                    >{{
                                        category.name[$page.props.locale]
                                    }}</span
                                >
                                <img
                                    :src="category.image"
                                    :alt="category.name[$page.props.locale]"
                                    class="w-28 h-20 object-cover block mx-auto"
                                />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--grid-->
        </div>
        <!--end container-->
        <div class="container relative mt-8 md:px-28 px-3 pb-5">
            <div
                id="grid"
                class="md:flex flex-wrap justify-center gap-6"
                ref="shuffleContainer"
            >
                <div
                    v-for="item in restaurant.menu_items"
                    :key="item.id"
                    :class="`group relative grid-item lg:w-1/5 md:w-1/3 w-1/2 picture-item p-3 shadow mt-6 category-${item.category_id}`"
                >
                    <img
                        :src="item.image"
                        class="rounded-full size-32 mx-auto object-cover group-hover:animate-[spin_10s_linear_infinite]"
                        alt=""
                    />

                    <div class="mt-4 text-center">
                        <h2
                            class="text-lg h5 block hover:text-amber-500 duration-500"
                        >
                            {{ item.name[$page.props.locale] }}
                        </h2>
                        <span class="text-slate-400 mt-2 block">{{
                            item.description[$page.props.locale]
                        }}</span>

                        <h5
                            class="text-white px-2 font-medium flex justify-center items-center gap-2 absolute left-0 bg-red-500 top-0"
                        >
                            <span
                                >${{
                                    Number(
                                        item.price * (1 - item.discount / 100)
                                    ).toFixed(2)
                                }}</span
                            >
                            <del
                                class="text-sm text-gray-200 font-normal"
                                v-if="item.discount && item.discount != 0"
                                >${{ item.price }}</del
                            >
                        </h5>
                        <div class="flex justify-center">
                            <button
                                @click="addToCart(item)"
                                class="bg-amber-500 hover:bg-amber-600 duration-300 text-white px-2 py-1 rounded m-1"
                            >
                                <svg
                                    class="w-6 h-6"
                                    xmlns="http://www.w3.org/2000/svg"
                                    version="1.1"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="512"
                                    height="512"
                                    x="0"
                                    y="0"
                                    viewBox="0 0 48 48"
                                    style="enable-background: new 0 0 512 512"
                                    xml:space="preserve"
                                >
                                    <g>
                                        <path
                                            d="M20 45a4 4 0 1 1 4-4 4 4 0 0 1-4 4zm0-6a2 2 0 1 0 2 2 2 2 0 0 0-2-2zM33 45a4 4 0 1 1 4-4 4 4 0 0 1-4 4zm0-6a2 2 0 1 0 2 2 2 2 0 0 0-2-2zM13 31a1 1 0 0 1-1-1V7.541a1.994 1.994 0 0 0-1.671-1.973l-3.494-.582a1 1 0 0 1 .33-1.972l3.493.582A3.988 3.988 0 0 1 14 7.541V30a1 1 0 0 1-1 1z"
                                            fill="currentColor"
                                            opacity="1"
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M31.836 32H13a1 1 0 0 1-1-1V10a1 1 0 0 1 2 0v20h17.836a2.992 2.992 0 0 0 1.61-.469l5.164-3.283A2.987 2.987 0 0 0 40 23.717V12a1 1 0 0 1 2 0v11.717a4.977 4.977 0 0 1-2.317 4.219l-5.164 3.283a5 5 0 0 1-2.683.781z"
                                            fill="currentColor"
                                            opacity="1"
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M27 7a9 9 0 1 0 9 9 9.014 9.014 0 0 0-9-9zm4 10h-3v3a1 1 0 0 1-2 0v-3h-3a1 1 0 0 1 0-2h3v-3a1 1 0 0 1 2 0v3h3a1 1 0 0 1 0 2zM39 39H13.5a4.5 4.5 0 0 1 0-9H25a1 1 0 0 1 0 2H13.5a2.5 2.5 0 0 0 0 5H39a1 1 0 0 1 0 2z"
                                            fill="currentColor"
                                            opacity="1"
                                            data-original="currentColor"
                                        ></path>
                                    </g>
                                </svg>
                            </button>
                            <button
                                v-if="item.variations.length != 0"
                                @click="showItemVariation(item.id)"
                                class="bg-red-500 hover:bg-red-600 duration-300 text-white px-2 py-1 rounded m-1"
                            >
                                <svg
                                    class="w-6 h-6"
                                    xmlns="http://www.w3.org/2000/svg"
                                    version="1.1"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="512"
                                    height="512"
                                    x="0"
                                    y="0"
                                    viewBox="0 0 60 60"
                                    style="enable-background: new 0 0 512 512"
                                    xml:space="preserve"
                                >
                                    <g>
                                        <path
                                            d="M48 0H30a12.013 12.013 0 0 0-12 12 3.96 3.96 0 0 0 .817 2.4A1.993 1.993 0 0 0 18 16v3a2 2 0 0 0 .994 1.719A4.412 4.412 0 0 0 18 23.5a4.6 4.6 0 0 0 .055.5H5a5.006 5.006 0 0 0-5 5v26a5.006 5.006 0 0 0 5 5h34a5.006 5.006 0 0 0 5-5V40h6a10.014 10.014 0 0 0 10-10 3.978 3.978 0 0 0-1.547-3.136c.071-.063.151-.113.218-.181a4.443 4.443 0 0 0 .338-5.966A1.994 1.994 0 0 0 60 19v-3a1.993 1.993 0 0 0-.817-1.6A3.96 3.96 0 0 0 60 12 12.013 12.013 0 0 0 48 0Zm10 16v3H42.96a2.967 2.967 0 0 0-2.339 1.123L37.5 24h-9l-3.115-3.87A2.97 2.97 0 0 0 23.04 19H20v-3h38Zm0 7.5a2.516 2.516 0 0 1-2.5 2.5H42.974a4.986 4.986 0 0 0-2.986-1.9l2.2-2.73a.968.968 0 0 1 .772-.37H55.5a2.5 2.5 0 0 1 2.5 2.5ZM30 2h18a10.011 10.011 0 0 1 10 10 2 2 0 0 1-2 2H22a2 2 0 0 1-2-2A10.011 10.011 0 0 1 30 2ZM20 23.5a2.516 2.516 0 0 1 2.5-2.5h.54a.971.971 0 0 1 .78.377L25.931 24h-5.877a2.494 2.494 0 0 1-.054-.5ZM42 55a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V29a3 3 0 0 1 3-3h34a3 3 0 0 1 3 3Zm14-27a2 2 0 0 1 2 2 8.013 8.013 0 0 1-8 8h-6v-9a5 5 0 0 0-.1-1H56Z"
                                            fill="currentColor"
                                            opacity="1"
                                            data-original="currentColor"
                                        ></path>
                                        <path
                                            d="M30.012 21H37a1 1 0 0 0 0-2h-5.906C29.7 19 29 19 29 20a.968.968 0 0 0 1.012 1ZM24 10a1 1 0 0 0 .707-.293l1-1a1 1 0 1 0-1.414-1.414l-1 1A1 1 0 0 0 24 10ZM30 7a1 1 0 0 0 .707-.293l1-1a1 1 0 1 0-1.414-1.414l-1 1A1 1 0 0 0 30 7ZM32.293 10.707a1 1 0 0 0 1.414 0l1-1a1 1 0 0 0-1.414-1.414l-1 1a1 1 0 0 0 0 1.414ZM53.293 9.707a1 1 0 0 0 1.414-1.414l-1-1a1 1 0 1 0-1.414 1.414ZM47.293 6.707a1 1 0 0 0 1.414-1.414l-1-1a1 1 0 0 0-1.414 1.414ZM44.293 10.707a1 1 0 0 0 1.414-1.414l-1-1a1 1 0 0 0-1.414 1.414ZM39 7a1 1 0 0 0 1-1V5a1 1 0 0 0-2 0v1a1 1 0 0 0 1 1ZM38 31H15.858a3.981 3.981 0 0 0-7.716 0H6a1 1 0 0 0 0 2h2.142a3.981 3.981 0 0 0 7.716 0H38a1 1 0 0 0 0-2Zm-26 3a2 2 0 1 1 2-2 2 2 0 0 1-2 2ZM38 41h-4.142a3.981 3.981 0 0 0-7.716 0H6a1 1 0 0 0 0 2h20.142a3.981 3.981 0 0 0 7.716 0H38a1 1 0 0 0 0-2Zm-8 3a2 2 0 1 1 2-2 2 2 0 0 1-2 2ZM38 51H21.858a3.981 3.981 0 0 0-7.716 0H6a1 1 0 0 0 0 2h8.142a3.981 3.981 0 0 0 7.716 0H38a1 1 0 0 0 0-2Zm-20 3a2 2 0 1 1 2-2 2 2 0 0 1-2 2Z"
                                            fill="currentColor"
                                            opacity="1"
                                            data-original="currentColor"
                                        ></path>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
        </div>
        <!--end container-->
    </section>
    <!--end section-->
    <!-- End -->

    <li
        class="dropdown flex justify-center fixed w-full bottom-5 z-10 left-0 right-0 ps-0.5"
    >
        <button
            @click="showCart = !showCart"
            class="dropdown-toggle size-9 w-4/5 sm:w-2/3 inline-flex items-center justify-center tracking-wide align-middle duration-300 text-base text-center rounded bg-amber-500 hover:bg-amber-600 border border-amber-500 text-white"
            type="button"
        >
            <i data-feather="shopping-cart" class="h-4 w-4"></i>
            <span class="mx-1">{{ t("Total Order") }}</span>
            <span>({{ total_price }})</span>
        </button>
        <!-- Dropdown menu -->
        <div
            class="dropdown-menu absolute left-[1/3] bottom-full m-0 mt-4 z-10 w-4/5 sm:w-2/3 rounded-md bg-white dark:bg-slate-900 shadow dark:shadow-gray-800"
            onclick="event.stopPropagation();"
            v-if="showCart"
        >
            <div v-if="cart.length > 0">
                <ul
                    class="py-3 text-start divide-y-2 max-h-[300px] md:max-h-[400px] overflow-y-auto"
                    aria-labelledby="dropdownDefault"
                >
                    <li
                        class="py-3 px-4 relative"
                        v-for="(item, index) in cart"
                        :key="index"
                    >
                        <div
                            class="flex items-center space-x-4 rtl:space-x-reverse"
                        >
                            <div class="flex-shrink-0">
                                <img
                                    class="w-8 h-8 rounded-full"
                                    :src="item.menu_item.image"
                                    alt="Neil image"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 truncate dark:text-white"
                                >
                                    {{
                                        item.menu_item.name[$page.props.locale]
                                    }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 truncate dark:text-gray-400"
                                >
                                    {{ t("Quantity") }}: {{ item.quantity }}
                                </p>
                                <p
                                    v-for="variation in item.variations"
                                    :key="variation.id"
                                    class="text-xs text-gray-500 truncate dark:text-gray-400"
                                >
                                    {{ variation.name[$page.props.locale] }}
                                </p>
                            </div>
                            <div
                                class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ item.price }}
                            </div>
                        </div>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-3 fill-red-500 inline cursor-pointer absolute right-3 bottom-1 rtl:left-3 rtl:right-auto"
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
                    </li>
                </ul>
                <ul
                    class="pb-3 pt-0 text-start divide-y-2"
                    aria-labelledby="dropdownDefault"
                >
                    <li class="py-1.5 flex items-center gap-x-2 px-4">
                        <span class="text-lg font-bold">{{ t("Total") }}:</span>
                        <span class="text-lg font-bold"
                            >{{ total_price }}
                        </span>
                    </li>
                    <li class="py-1.5 flex items-center gap-x-2 px-4">
                        <Link
                            class="bg-red-500 hover:bg-red-600 duration-300 text-white px-2 py-1 rounded m-1 text-center w-full"
                            :href="route('checkout')"
                            >{{ t("Checkout") }}</Link
                        >
                    </li>
                </ul>
            </div>
            <ul
                v-else
                class="py-3 text-start"
                aria-labelledby="dropdownDefault"
            >
                <li class="py-1.5 px-4">
                    {{ t("Empty Cart") }}
                </li>
            </ul>
        </div>
    </li>
    <div
        @click="closeModal"
        class="backdrop-blur-[1px] bg-gray-900 bg-opacity-75 fixed z-[999999] flex items-center overflow-y-auto h-screen top-0 w-full left-0"
        :class="{ hidden: !showModal }"
    >
        <div
            class="bg-white max-w-2xl sm:min-w-96 sm:max-w-sm sm:w-full w-full mx-3 sm:mx-auto p-6 rounded-lg shadow-lg duration-300"
            :class="{ 'scale-100': showModal, 'scale-0': !showModal }"
            @click.stop
        >
            <button
                class="bg-gray-50 p-2 absolute top-2 hover:bg-gray-100"
                @click="showModal = !showModal"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="w-4 h-4"
                >
                    <path
                        d="M10.5859 12L2.79297 4.20706L4.20718 2.79285L12.0001 10.5857L19.793 2.79285L21.2072 4.20706L13.4143 12L21.2072 19.7928L19.793 21.2071L12.0001 13.4142L4.20718 21.2071L2.79297 19.7928L10.5859 12Z"
                    ></path>
                </svg>
            </button>
            <h2 class="font-bold mb-4 text-center">{{ t("Working Time") }}</h2>
            <div
                v-for="time in JSON.parse($page.props.settings.working_time)"
                class="flex items-center justify-between mb-2"
            >
                <template
                    v-if="time.open || time.from || time.to || time.close"
                >
                    <strong>{{ t(time.day) }}</strong>
                    <span v-if="time.open">{{ t("Open 24 Hours") }}</span>
                    <span v-else-if="time.close">{{ t("Closed") }}</span>
                    <span v-else-if="time.from && time.to"
                        >{{ t("From") }} {{ time.from }} - {{ t("To") }}
                        {{ time.to }}</span
                    >
                    <span v-else>{{ t("Not Determined") }}</span>
                </template>
            </div>
        </div>
    </div>

    <div
        @click="closeModal"
        class="backdrop-blur-[1px] fixed z-[10] overflow-y-auto h-screen top-0 w-full left-0"
        :class="{ hidden: !showCustomizationModal }"
    >
        <div
            class="flex items-center justify-center min-height-100vh pt-4 px-4 pb-20 text-center sm:block sm:p-0"
        >
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-900 opacity-75" />
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                >&#8203;</span
            >
            <div
                @click.stop
                class="inline-block align-center bg-white rounded-lg text-left rtl:text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full w-full"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-headline"
            >
                <div
                    class="bg-white dark:bg-gray-700 px-4 pt-5 pb-4 sm:p-6 sm:pb-4"
                >
                    <button
                        type="button"
                        class="text-2xl absolute right-2 rtl:left-2 rtl:right-auto top-2 text-gray-600"
                        @click="closeModal"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 dark:text-white"
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
                    </button>
                    <h2 class="ltr:font-sans text-2xl font-bold">
                        {{
                            menu_item.name
                                ? menu_item.name[$page.props.locale]
                                : ""
                        }}
                    </h2>
                    <h3 class="ltr:font-sans my-2 text-xl font-semibold">
                        {{ t("Price") }}: {{ menu_item.price }}
                    </h3>
                    <div
                        class="mb-4 flex flex-col sm:flex-row gap-x-2 sm:gap-x-6"
                    >
                        <div class="w-1/3 sm:w-1/5">
                            <label for="quantity">{{ t("Quantity") }}</label>
                            <br />
                            <input
                                min="1"
                                type="number"
                                id="quantity"
                                class="p-2 border border-gray-300 py-1 rounded-lg focus:outline-none focus :focus:border-blue-600 focus:ring-1 focus:ring-blue-600 w-full text-sm md:text-base"
                                v-model="item.quantity"
                            />
                        </div>
                        <div
                            class="w-full sm:w-4/5"
                            v-if="
                                menu_item.size_variations &&
                                menu_item.size_variations.length > 0
                            "
                        >
                            <label for="quantity">{{ t("Size") }}</label>
                            <br />
                            <ul
                                class="flex flex-wrap w-full gap-1 sm:gap-3 md:grid-cols-2"
                            >
                                <li v-for="option in menu_item.size_variations">
                                    <input
                                        type="radio"
                                        name="size_variation"
                                        :id="'option-' + option.id"
                                        :value="option.id"
                                        class="hidden peer"
                                        v-model="item.size_variation_id"
                                        @click="changeItemPrice(option.price)"
                                        :checked="option.default"
                                        :disabled="option.price == 0"
                                    />
                                    <label
                                        :for="`option-${option.id}`"
                                        class="inline-flex items-center justify-between w-full py-1 px-3 text-gray-500 border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-amber-500 peer-checked:border-amber-600 peer-checked:text-amber-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm md:text-base"
                                        :class="{
                                            'pointer-events-none bg-gray-100':
                                                option.price == 0,
                                        }"
                                    >
                                        <div class="block">
                                            <div class="w-full">
                                                {{
                                                    option.name[
                                                        $page.props.locale
                                                    ]
                                                }}
                                                -
                                                <span class="text-sm">{{
                                                    option.price == 0
                                                        ? t("Not Available")
                                                        : option.price
                                                }}</span>
                                            </div>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <table
                            class="mt-2 border-collapse w-full border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm"
                        >
                            <tbody>
                                <tr>
                                    <td
                                        class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400 w-1/2"
                                    >
                                        <template
                                            v-for="(
                                                variation, index
                                            ) in menu_item.variations"
                                        >
                                            <label
                                                class="block text-sm font-medium py-2 text-gray-700 dark:text-white"
                                                v-if="variation.price != 0"
                                            >
                                                <input
                                                    @click="
                                                        addOrderVariation(
                                                            variation
                                                        )
                                                    "
                                                    type="radio"
                                                    :name="`paid-${variation.variation_id}`"
                                                    :value="variation.id"
                                                />
                                                {{
                                                    variation.name[
                                                        $page.props.locale
                                                    ]
                                                }}
                                                -
                                                {{ variation.price }}
                                            </label>
                                        </template>
                                    </td>
                                    <td
                                        class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400"
                                    >
                                        <template
                                            v-for="(
                                                variation, index
                                            ) in menu_item.variations"
                                        >
                                            <label
                                                class="block text-sm font-medium py-1 text-gray-700 dark:text-white"
                                                v-if="variation.price == 0"
                                            >
                                                <input
                                                    @click="
                                                        addOrderVariation(
                                                            variation
                                                        )
                                                    "
                                                    type="radio"
                                                    :name="`paid-${variation.variation_id}`"
                                                    :value="variation.id"
                                                />
                                                {{
                                                    variation.name[
                                                        $page.props.locale
                                                    ]
                                                }}
                                                -
                                                {{ variation.price }}
                                            </label>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div
                    class="bg-gray-200 dark:bg-slate-800 flex justify-between items-center px-4 py-3 md:text-right"
                >
                    <h2 class="ltr:font-sans font-bold text-amber-500 text-2xl">
                        {{ t("Subtotal") }}: {{ subtotal }}
                    </h2>
                    <div>
                        <button
                            type="button"
                            @click="addToCart()"
                            class="md:py-2 md:px-4 py-1 px-2 bg-red-500 text-white rounded font-medium hover:bg-red-700 mr-2 transition duration-300"
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
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 12h14m-7 7V5"
                                />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="addToCart(null, 'close')"
                            class="md:py-2 md:px-4 py-1 px-2 bg-amber-500 text-white rounded font-medium hover:bg-amber-700 mr-2 transition duration-300"
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
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 11.917 9.724 16.5 19 7.5"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        @click="closeModal"
        class="backdrop-blur-[1px] bg-gray-900 bg-opacity-75 fixed z-[999999] flex items-center overflow-y-auto h-screen top-0 w-full left-0"
        :class="{ hidden: !showLocationModal }"
    >
        <div
            @click.stop
            class="inline-block align-center bg-white rounded-lg text-left rtl:text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full w-full mx-4 sm:mx-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-headline"
        >
            <GoogleMap
                api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                style="width: 100%; height: 300px"
                :center="center"
                :zoom="12"
            >
                <Marker :options="markerOptions" />
            </GoogleMap>
        </div>
    </div>
</template>

<style scoped>
.filter-options {
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
