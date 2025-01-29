<template>
    <Head title="View Member Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                View {{ member.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                      <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Basic Information</h3>
                      <ul>
                        <li><strong>Name:</strong> {{ member.name }}</li>
                        <li><strong>Known As:</strong> {{ member.knownAs }}</li>
                      </ul>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Email Addresses</h3>
                     <ul>
                        <li v-for="email in member.emailAddresses">
                          {{ email.emailAddress }} <span v-if="email.isPrimary">(Primary)</span>
                        </li>
                      </ul>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                      <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Postal Address</h3>
                      <p v-if="member.postalAddress">
                        {{ member.postalAddress.line1 }}<br>
                        <span v-if="member.postalAddress.line2">{{ member.postalAddress.line2 }}<br></span>
                        <span v-if="member.postalAddress.line3">{{ member.postalAddress.line3 }}<br></span>
                        {{ member.postalAddress.city }}, {{ member.postalAddress.county }}<br>
                        {{ member.postalAddress.postcode }}
                      </p>
                      <p v-else>No postal address available.</p>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                      <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Membership History</h3>
                      <ul>
                        <li v-for="history in member.membershipHistory" :key="history.id">
                          {{ history.membershipType.label}} ({{ history.startDate }})
                        </li>
                      </ul>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                     v-if="member.trusteeHistory && member.trusteeHistory.length > 0">
                     <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Trustee History</h3>
                      <ul>
                        <li v-for="history in member.trusteeHistory" :key="history.id">
                          Elected: {{ history.electedAt }}<br>
                          Resigned: {{ history.resignedAt || 'Still serving' }}
                        </li>
                      </ul>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import MemberData = App.Data.MemberData;
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";

const props = defineProps< {
    member: MemberData
}>();
</script>
