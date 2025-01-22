<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import MemberData = App.Data.MemberData;
import MembershipTypeData = App.Data.MembershipTypeData;

const props = defineProps<{
    members: {
        data: MemberData[];
        links: {
            url: string;
            label: string;
            active: boolean;
        }[];
    };
    membershipTypes: MembershipTypeData[];
    filters: {
        search?: string;
        membershipType?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const membershipType = ref(props.filters.membershipType ?? '');

const filterMembers = () => {
    router.get('/members', { search: search.value, membership_type: membershipType.value, page: 1 });
};
</script>

<template>
    <Head title="Members" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Members
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div class="mb-4 flex space-x-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search members..."
                            class="border border-gray-300 rounded-md p-2"
                        />
                        <select
                            v-model="membershipType"
                            class="border border-gray-300 rounded-md p-2 pr-10"
                        >
                            <option value="">All Membership Types</option>
                            <option v-for="type in membershipTypes" :key="type.label" :value="type.value" >{{ type.label }}</option>
                        </select>
                        <button @click="filterMembers" class="bg-brand text-white rounded-md p-2 ">Search</button>
                    </div>
                    <div class="grid grid-cols-12 gap-y-3 divide-y divide-gray-300 items-stretch">
                        <div class="col-span-4 text-sm text-gray-900 pt-3 border-none"><strong>Name</strong></div>
                        <div class="col-span-2 text-sm text-gray-900 pt-3 border-none"><strong>Known As</strong></div>
                        <div class="col-span-2 text-sm text-gray-900 pt-3 border-none"><strong>Membership Type</strong></div>
                        <div class="col-span-2 text-sm text-gray-900 pt-3 border-none"><strong>Active Membership</strong></div>
                        <div class="col-span-2 text-sm text-gray-900 pt-3 border-none content-end pr-4"><strong>Actions</strong></div>

                        <template v-for="member in members.data" :key="member.id" >
                            <div class="col-span-4 text-sm text-gray-900 pt-3 flex items-center">{{ member.name }}</div>
                            <div class="col-span-2 text-sm text-gray-900 pt-3 flex items-center">{{ member.knownAs }}</div>
                            <div class="col-span-2 text-sm text-gray-900 pt-3 flex items-center">{{ member.membershipType }}</div>
                            <div class="col-span-2 text-sm text-gray-900 pt-3 flex items-center">{{ member.hasActiveMembership ? 'Yes' : 'No' }}</div>
                            <div class="col-span-2 pt-3 pr-4 justify-self-stretch content-end">
                                <a :href="route('member.show',[member.id])"><button class="bg-gray-700 text-white rounded-md p-2 mr-2">View</button></a>
                                <a :href="route('member.edit',[member.id])"><button class="bg-gray-700 text-white rounded-md p-2 ">Edit</button></a>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <nav class="flex items-center justify-between">
                        <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link v-for="link in members.links" :key="link.label" :href="link.url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" :class="{ 'bg-gray-200': link.active }" v-html="link.label"/>
                            </nav>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
