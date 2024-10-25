import { usePage } from "@inertiajs/vue3";

export function t(value) {
    const array = usePage().props.translations;
    return array[value] != null ? array[value] : value;
}

export function useRoute(value = null) {
    return `/${usePage().props.lang}${value ?? ""}`;
}
