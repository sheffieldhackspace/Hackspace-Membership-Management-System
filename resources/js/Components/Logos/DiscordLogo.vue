<script setup lang="ts">

import DiscordLogoBlurple from '../../../svg/discord/icon/icon_clyde_blurple_RGB.svg?raw';
import DiscordLogoBlack from '../../../svg/discord/icon/icon_clyde_black_RGB.svg?raw';
import DiscordLogoWhite from '../../../svg/discord/icon/icon_clyde_white_RGB.svg?raw';

withDefaults(
    defineProps<{
        colour?: 'blurple' | 'black' | 'white';
    }>(),
    {
        colour: 'blurple',
    },
);

const svg = {
    blurple: DiscordLogoBlurple,
    black: DiscordLogoBlack,
    white: DiscordLogoWhite,
};

</script>

<template>
    <!-- eslint-disable vue/no-v-html -->
    <div
        class="w-6"
        v-html="svg[colour]"
    />
</template>
