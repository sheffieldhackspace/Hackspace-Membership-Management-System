<script setup lang="ts">
const props = defineProps<{
    key: string;
    value: string;
    label: string;
}>();
</script>

<template>
    <option :key="props.key" :value="props.value" >{{ props.label }}</option>
</template>
