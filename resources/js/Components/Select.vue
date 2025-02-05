<script setup lang="ts">
import {onMounted, ref} from 'vue';

const model = defineModel<string>({required: true});

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({focus: () => input.value?.focus()});
</script>

<template>
    <select
        ref="input"
        v-model="model"
        class="rounded-md border-gray-300 shadow-sm focus:ring-brand"
    >
        <slot />
    </select>
</template>
