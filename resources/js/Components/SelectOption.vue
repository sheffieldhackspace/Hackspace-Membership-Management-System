<script setup lang="ts">
const props = defineProps<{
    label: string;
}>();
</script>

<template>
    <option>
        {{ props.label }}
    </option>
</template>
