<script setup lang="ts">
import {InformationCircleIcon, ExclamationTriangleIcon} from "@heroicons/vue/24/solid";

const props = withDefaults(
    defineProps<{
        type?: 'info' | 'danger' | 'success' | 'warning' | 'dark';
    }>(),
    {
        type: 'info',
    },
);

const colourClasses = () => {
    switch (props.type) {
        case 'info': {
            return 'text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800'
        }
        case 'danger': {
            return 'text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800'
        }
        case 'success': {
            return 'text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800'
        }
        case 'warning': {
            return 'text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800'
        }
        case 'dark': {
            return 'text-gray-800 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600'
        }
    }
}

const icon = () => {
    switch (props.type) {
        case 'info': {
            return InformationCircleIcon
        }
        case 'danger': {
            return ExclamationTriangleIcon
        }
        case 'success': {
            return InformationCircleIcon
        }
        case 'warning': {
            return ExclamationTriangleIcon
        }
        case 'dark': {
            return InformationCircleIcon
        }
    }
}


</script>

<template>
    <div
        class="flex items-center p-4 mb-4 text-sm  dark:bg-gray-800 "
        :class="colourClasses()"
        role="alert"
    >
        <component
            :is="icon()"
            class="h-5 w-5 mr-2"
        />
        <span class="sr-only">Info</span>
        <div>
            <slot />
        </div>
    </div>
</template>
