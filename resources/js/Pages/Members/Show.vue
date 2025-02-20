<template>
    <Head title="View Member Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                View {{ member.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div
                        v-if="member.discordUser?.avatarUrl"
                        class="float-right"
                    >
                        <img
                            :src="member.discordUser.avatarUrl"
                            :alt="member.discordUser.username"
                            class="rounded-full h-16 w-16"
                        >
                    </div>
                    <div>
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">
                            Personal Information
                        </h3>
                        <ul>
                            <li><strong>Name:</strong> {{ member.name }}</li>
                            <li><strong>Known As:</strong> {{ member.knownAs }}</li>
                            <template v-if="member.discordUser">
                                <li><strong>Discord Username:</strong> {{ member.discordUser.username }}</li>
                                <li><strong>Discord Name:</strong> {{ member.discordUser.nickname }}</li>
                            </template>
                            <li v-else>
                                <strong>Discord User:</strong> Not linked
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">
                        Email Addresses
                    </h3>
                    <ul>
                        <li
                            v-for="email in member.emailAddresses"
                            :key="email.emailAddress"
                        >
                            {{ email.emailAddress }} <span v-if="email.isPrimary">(Primary)</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">
                        Postal Address
                    </h3>
                    <p v-if="member.postalAddress">
                        {{ member.postalAddress.line1 }}<br>
                        <span v-if="member.postalAddress.line2">{{ member.postalAddress.line2 }}<br></span>
                        <span v-if="member.postalAddress.line3">{{ member.postalAddress.line3 }}<br></span>
                        {{ member.postalAddress.city }}, {{ member.postalAddress.county }}<br>
                        {{ member.postalAddress.postcode }}
                    </p>
                    <p v-else>
                        No postal address available.
                    </p>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">
                        Membership History
                    </h3>
                    <ul>
                        <li
                            v-for="history in member.membershipHistory"
                            :key="history.id"
                        >
                            {{ history.membershipType.label }} ({{ history.startDate }})
                        </li>
                    </ul>
                </div>

                <div
                    v-if="member.trusteeHistory && member.trusteeHistory.length > 0"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">
                        Trustee History
                    </h3>
                    <ul>
                        <li
                            v-for="history in member.trusteeHistory"
                            :key="history.id"
                        >
                            Elected: {{ history.electedAt }}<br>
                            Resigned: {{ history.resignedAt || 'Still serving' }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 mt-8 flex gap-1">
                <a :href="route('member.edit', member.id)">
                    <PrimaryButton>
                        Edit Member
                    </PrimaryButton>
                </a>
                <SecondaryButton
                    onclick="history.back()"
                    class=""
                >
                    Back
                </SecondaryButton>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import MemberData = App.Data.MemberData;
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import SecondaryButton from "@/Components/Buttons/SecondaryButton.vue";

defineProps<{
    member: MemberData
}>();
</script>
