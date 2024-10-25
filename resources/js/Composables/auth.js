import { usePage } from "@inertiajs/vue3";

export const hasRole = (role) => {
    return usePage().props.auth.roles.includes(role);
};

export const hasPermission = (permission) => {
    return usePage().props.auth.permissions.includes(permission);
};
