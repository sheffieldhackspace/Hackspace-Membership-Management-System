<template>
    <slot v-if="hasPermission()" />
    <slot name="else" v-if="!hasPermission()" />
</template>

<script setup lang="ts">
import {usePage} from "@inertiajs/vue3";
const props = defineProps<{
    permissions: string[];
}>();

const hasPermission = () => {
    const userPermissions  = usePage().props.auth.permissions;
    return userPermissions.filter((permission: string) => {
        return props.permissions.includes(permission);
    }).length > 0;
};


</script>
