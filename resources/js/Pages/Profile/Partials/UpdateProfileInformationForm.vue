<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { t } from "@/Composables/trans";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import { GoogleMap, Marker } from "vue3-google-map";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone,
    location: user.location,
    image: "",
    _method: "patch",
});

const prevImg = ref(
    `https://ui-avatars.com/api/?background=random&name=${user.name}`
);

const center = user.location
    ? JSON.parse(user.location)
    : { lat: 40.689247, lng: -74.044502 };

const markerOptions = ref({
    position: center,
    label: "A",
    title: "Location",
});

onMounted(() => {
    if (user.image) {
        prevImg.value = user.image;
    }
});

watch(
    () => form.image,
    (value) => {
        if (value) {
            const reader = new FileReader();
            reader.onload = (e) => {
                prevImg.value = e.target.result;
            };
            reader.readAsDataURL(value);
        }
    }
);

const handleClick = (event) => {
    const lat = event.latLng.lat();
    const lng = event.latLng.lng();

    form.location = JSON.stringify({
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
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ t("Profile Information") }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{
                    t(
                        "Update your account's profile information and email address."
                    )
                }}
            </p>
        </header>

        <form @submit.prevent="form.post(route('profile.update'))" class="mt-6">
            <div class="flex w-full gap-x-6">
                <div class="w-[36rem] space-y-6">
                    <div>
                        <InputLabel for="name" :value="t('Name')" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" :value="t('Email')" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="name" :value="t('Phone')" />

                        <TextInput
                            id="phone"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            required
                            autofocus
                            autocomplete="phone"
                        />

                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div
                        v-if="
                            mustVerifyEmail && user.email_verified_at === null
                        "
                    >
                        <p class="mt-2 text-sm text-gray-800">
                            {{ t("Your email address is unverified.") }}
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                {{
                                    t(
                                        "Click here to re-send the verification email."
                                    )
                                }}
                            </Link>
                        </p>

                        <div
                            v-show="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            {{
                                t(
                                    "A new verification link has been sent to your email address."
                                )
                            }}
                        </div>
                    </div>

                    <div>
                        <InputLabel for="location" :value="t('Address')" />
                        <GoogleMap
                            api-key="AIzaSyBIuHQWvrEHg_WXDFR3xYs--TwlqAel8Ds"
                            style="width: 100%; height: 300px"
                            :center="center"
                            :zoom="12"
                            @click="handleClick"
                        >
                            <Marker :options="markerOptions" />
                        </GoogleMap>
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">{{
                            t("Save")
                        }}</PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-if="form.recentlySuccessful"
                                class="text-sm text-gray-600"
                            >
                                {{ t("Saved.") }}
                            </p>
                        </Transition>
                    </div>
                </div>
                <div class="w-36">
                    <div>
                        <InputLabel for="image" :value="t('Image')" />
                        <label for="image">
                            <img
                                :src="prevImg"
                                alt="Avatar"
                                class="w-36 h-36 rounded-full object-cover cursor-pointer"
                            />
                        </label>
                        <input
                            id="image"
                            type="file"
                            class="mt-1 w-full hidden"
                            @change="form.image = $event.target.files[0]"
                        />
                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>
                </div>
            </div>
        </form>
    </section>
</template>
