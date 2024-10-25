<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { t } from "@/Composables/trans";
import LangSwitcher from "@/Components/LangSwitcher.vue";

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    settings: {
        name: "",
        logo: "",
        email: "",
        phone: "",
        facebook: "",
        instagram: "",
        x: "",
    },
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const currentTab = ref(0);

onMounted(() => {
    showTab(currentTab.value);
});

const showTab = (n) => {
    // This function will display the specified tab of the form...
    var x = document.getElementsByClassName("step");
    x[n].style.display = "block";
    //... and fix the Previous/Next buttons:
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == x.length - 1) {
        document.getElementById("nextBtn").innerHTML = t("Submit");
    } else {
        document.getElementById("nextBtn").innerHTML = t("Next");
    }
    //... and run a function that will display the correct step indicator:
    fixStepIndicator(n);
};

const nextPrev = (n) => {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("step");
    // Exit the function if any field in the current tab is invalid:
    if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentTab.value].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab.value = currentTab.value + n;
    // if you have reached the end of the form...
    if (currentTab.value >= x.length) {
        // ... the form gets submitted:
        submit();
        currentTab.value = 0;
        showTab(currentTab.value);
        return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab.value);
};

const validateForm = () => {
    // This function deals with validation of the form fields
    var x,
        y,
        i,
        valid = true;
    x = document.getElementsByClassName("step");

    y = x[currentTab.value].getElementsByTagName("input");
    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {
        // If a field is empty...
        if (y[i].value == "" && !y[i].classList.contains("optional")) {
            // add an "invalid" class to the field:
            y[i].className += " invalid";
            // and set the current valid status to false
            valid = false;
        }
    }
    // If the valid status is true, mark the step as finished and valid:
    if (valid) {
        document.getElementsByClassName("stepIndicator")[
            currentTab.value
        ].className += " finish";
    }
    return valid; // return the valid status
};

const fixStepIndicator = (n) => {
    // This function removes the "active" class of all steps...
    var i,
        x = document.getElementsByClassName("stepIndicator");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class on the current step:
    x[n].className += " active";
};
</script>

<template>
    <GuestLayout>
        <LangSwitcher />
        <Head title="Register" />
        <form
            id="signUpForm"
            class="rounded-2xl bg-white mx-auto mb-8"
            @submit.prevent="submit"
        >
            <!-- start step indicators -->
            <div class="form-header flex gap-3 mb-4 text-xs text-center">
                <span class="stepIndicator flex-1 pb-8 relative">{{
                    t("User Information")
                }}</span>
                <span class="stepIndicator flex-1 pb-8 relative">{{
                    t("Restaurant Details")
                }}</span>
                <span class="stepIndicator flex-1 pb-8 relative">{{
                    t("Social Profiles")
                }}</span>
            </div>
            <!-- end step indicators -->
            <!-- step one -->
            <div class="step">
                <p
                    class="text-md text-gray-700 leading-tight text-center mt-8 mb-5"
                >
                    {{ t("Create your account") }}
                </p>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Name')"
                        name="name"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="mb-6">
                    <input
                        type="email"
                        :placeholder="t('Email')"
                        name="email"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Phone')"
                        name="phone"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.phone"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="mb-6">
                    <input
                        type="password"
                        :placeholder="t('Password')"
                        name="password"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.password"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="mb-6">
                    <input
                        type="password"
                        :placeholder="t('Confirm Password')"
                        name="password"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.password_confirmation"
                    />
                </div>
            </div>
            <!-- step two -->
            <div class="step">
                <p
                    class="text-md text-gray-700 leading-tight text-center mt-8 mb-5"
                >
                    {{ t("Your restaurant information") }}
                </p>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Name')"
                        name="restaurant_name"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.name"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.restaurant_name"
                    />
                </div>
                <div class="mb-6">
                    <label for="restaurant_logo">{{ t("Logo") }}</label>
                    <input
                        type="file"
                        name="restaurant_logo"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        @change="form.settings.logo = $event.target.files[0]"
                    />
                </div>
                <div class="mb-6">
                    <input
                        type="email"
                        :placeholder="t('Email')"
                        name="restaurant_email"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.email"
                    />
                </div>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Phone')"
                        name="restaurant_phone"
                        class="w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.phone"
                    />
                </div>
            </div>
            <!-- step three -->
            <div class="step">
                <p
                    class="text-md text-gray-700 leading-tight text-center mt-8 mb-5"
                >
                    {{ t("Share your restaurant social profiles") }}
                </p>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Facebook')"
                        name="facebook"
                        class="optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.facebook"
                    />
                </div>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('X')"
                        name="x"
                        class="optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.x"
                    />
                </div>
                <div class="mb-6">
                    <input
                        type="text"
                        :placeholder="t('Instagram')"
                        name="instagram"
                        class="optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500"
                        oninput="this.className = 'optional w-full px-4 py-2 rounded-md text-gray-700 font-medium border-solid border-2 border-gray-200 focus:border-amber-500'"
                        v-model="form.settings.instagram"
                    />
                </div>
            </div>
            <!-- start previous / next buttons -->
            <div class="form-footer flex gap-3">
                <button
                    type="button"
                    id="prevBtn"
                    class="flex-1 focus:outline-none border border-gray-300 py-2 px-5 rounded-lg shadow-sm text-center text-gray-700 bg-white hover:bg-gray-100 text-lg"
                    @click="nextPrev(-1)"
                >
                    {{ t("Previous") }}
                </button>
                <button
                    type="button"
                    id="nextBtn"
                    class="flex-1 border border-transparent focus:outline-none p-2 rounded-md text-center text-white bg-amber-600 hover:bg-amber-700 text-lg"
                    @click="nextPrev(1)"
                >
                    {{ t("Next") }}
                </button>
            </div>
            <Link
                :href="route('login')"
                class="rounded-md mt-3 block text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
            >
                {{ t("Already registered?") }}
            </Link>

            <!-- end previous / next buttons -->
        </form>
    </GuestLayout>
</template>

<style scoped>
body {
    font-family: "Open Sans", sans-serif;
}
#signUpForm {
    max-width: 500px;
}

#signUpForm input:focus {
    box-shadow: unset !important;
}
#signUpForm .form-header .stepIndicator.active {
    font-weight: 600;
}
#signUpForm .form-header .stepIndicator.finish {
    font-weight: 600;
    color: #f28c2a;
}
#signUpForm .form-header .stepIndicator::before {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    z-index: 9;
    width: 20px;
    height: 20px;
    background-color: #ffdd86;
    border-radius: 50%;
    border: 3px solid #f3f3f3;
}
#signUpForm .form-header .stepIndicator.active::before {
    background-color: #dd994f;
    border: 3px solid #ffdd86;
}
#signUpForm .form-header .stepIndicator.finish::before {
    background-color: #f28c2a;
    border: 3px solid #ffdd86;
}
#signUpForm .form-header .stepIndicator::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 8px;
    width: 100%;
    height: 3px;
    background-color: #f3f3f3;
}

[dir="rtl"] #signUpForm .form-header .stepIndicator::after {
    right: 50%;
    left: 0;
}

#signUpForm .form-header .stepIndicator.active::after {
    background-color: #dd994f;
}
#signUpForm .form-header .stepIndicator.finish::after {
    background-color: #f28c2a;
}
#signUpForm .form-header .stepIndicator:last-child:after {
    display: none;
}
#signUpForm input.invalid {
    border: 2px solid #ffaba5;
}
#signUpForm .step {
    display: none;
}
</style>
