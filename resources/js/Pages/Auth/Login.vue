<script setup lang="ts">

import Checkbox from '@/Components/Form/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import SecondaryButton from "@/Components/Buttons/SecondaryButton.vue";
import DiscordLogo from "@/Components/Logos/DiscordLogo.vue";
import Alert from "@/Components/Alert.vue";
import {ref} from "vue";

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};

const showEmailForm = ref(false);
</script>


<template>
    <GuestLayout>
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <div class="grid row-cols-1 gap-4">
            <a
                :href="route('discord.redirect')"
            >
                <PrimaryButton
                    class="w-full text-lg place-content-center !bg-discord hover:!bg-discord focus:!ring-discord !p-4 shadow-md font-medium"
                >
                    <DiscordLogo
                        colour="white"
                        class="!w-16 pr-4"
                    />
                    <span class="text-2xl normal-case">
                        Sign in with Discord
                    </span>
                </PrimaryButton>
            </a>

            <SecondaryButton
                v-if="!showEmailForm"
                class="flex w-1/2"
                @click="showEmailForm = true"
            >
                Sign in With Email
            </SecondaryButton>
        </div>
        <Transition
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
            enter-active-class="transition duration-300"
            leave-active-class="transition duration-300"
        >
            <div
                v-if="showEmailForm"
                class="mt-8"
            >
                <Alert type="warning">
                    Email login can only be used by Portland Works Users.
                </Alert>

                <div class="mt-4 text-sm text-gray-600">
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel
                                for="email"
                                value="Email"
                            />

                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="username"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel
                                for="password"
                                value="Password"
                            />

                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="current-password"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
                        </div>

                        <div class="mt-4 block">
                            <label class="flex items-center">
                                <Checkbox
                                    v-model:checked="form.remember"
                                    name="remember"
                                />
                                <span class="ms-2 text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>

                        <div class="mt-4 flex items-center justify-end">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2"
                            >
                                Forgot your password?
                            </Link>

                            <PrimaryButton
                                class="ms-4 bg-brand"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                type="submit"
                            >
                                Log in
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </GuestLayout>
</template>
