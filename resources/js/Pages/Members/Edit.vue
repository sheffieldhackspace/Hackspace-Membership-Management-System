<template>
    <Head title="Edit Member Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit {{ member.name }}
            </h2>
        </template>

        <div class="py-2 mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <form @submit.prevent="updateMember" class="">
                <div class="mt-10 grid gap-x-6 gap-y-8 grid-cols-3 md:grid-cols-6  bg-white p-4 shadow rounded-lg sm:p-8">
                    <div class="md:col-span-6 col-span-3 ">
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Names</h3>
                    </div>

                    <div class="col-span-1 content-center">
                        <label for="name">Name:</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" v-model="form.name" id="name" class="mt-1 block rounded border-gray-200 w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <label for="knownAs">Known As:</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" v-model="form.knownAs" id="knownAs" class="mt-1 block w-full rounded border-gray-200" />
                    </div>

                    <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Emails</h3>
                    </div>

                    <template v-for="(email, index) in form.emailAddresses" :key="email.id">
                        <div class="col-span-1 content-center">
                            <label :for="'email_' + index">Email</label>
                        </div>
                        <div class="col-span-2">
                            <input type="email" v-model="email.emailAddress" :id="'email_' + index" class="mt-1 block w-full rounded border-gray-200" />
                        </div>
                        <div class="col-span-1 content-center items-center flex">
                            <label class="mr-2" :for="'isPrimary_' + index">Primary</label>
                            <input type="checkbox" v-model="email.isPrimary" :id="'isPrimary_' + index" class="mt-1 block rounded border-gray-200" />

                        </div>
                        <div class="col-span-2 content-center">
                            <button type="button" @click="form.emailAddresses?.splice(index, 1)" class="bg-red-500 text-white rounded-md p-2">Remove</button>
                        </div>
                    </template>
                    <div class="md:col-span-6 col-span-3">
                        <button type="button" @click="addEmailAddress" class="bg-gray-900 text-white rounded-md p-2">Add Email</button>
                    </div>

                    <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Postal Address</h3>
                    </div>
                    <div class="col-span-1 content-center">
                        <label for="line1">Line 1</label>
                    </div>

                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.line1" id="line1" class="mt-1 block w-full" />
                    </div>

                    <div class="col-span-1 content-center">
                        <label for="line2">Line 2</label>
                    </div>
                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.line2" id="line2" class="mt-1 block w-full" />
                    </div>

                    <div class="col-span-1 content-center">
                        <label for="line3">Line 3</label>
                    </div>
                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.line3" id="line3" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <label for="city">City</label>
                    </div>
                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.city" id="city" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <label for="county">County</label>
                    </div>
                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.county" id="county" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <label for="postcode">Postcode</label>
                    </div>
                    <div class="col-span-2 content-center">
                        <input type="text" v-model="form.postalAddress.postcode" id="postcode" class="mt-1 block w-full" />
                    </div>

                    <HasPermission :permissions="['change-membership-type']">
                        <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                            <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Membership</h3>
                        </div>
                        <div class="col-span-1 content-center">
                            <label for="membershipType">Membership Type</label>
                        </div>
                        <div class="col-span-2">
                            <select
                                v-model="form.membershipType.value"
                                class="border border-gray-300 rounded-md p-2 pr-10"
                                id="membershipType"
                            >
                                <option v-for="type in props.membershipTypes" :key="type.label" :value="type.value" >{{ type.label }}</option>
                            </select>
                        </div>

                        <div class="col-span-3 content-center items-center flex space-x-2">
                            <label for="trustee">Is a Trustee</label>
                            <input type="checkbox" v-model="form.trustee" id="trustee" class="mt-1 block rounded border-gray-200" />
                        </div>
                    </HasPermission>

                    <div class="md:col-span-6 col-span-3 flex place-content-end content-center space-x-2">
                        <a :href="route('dashboard')"> <button type="button" class="bg-gray-900 text-white rounded-md p-2">Cancel</button></a>
                        <button type="submit" @click="updateMember" class="rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MemberData = App.Data.MemberData;
import MembershipTypeData = App.Data.MembershipTypeData;
import { Head } from '@inertiajs/vue3';
import HasPermission from "@/Components/HasPermission.vue";

const props = defineProps< {
    member: MemberData
    membershipTypes: MembershipTypeData
}>();

const form = useForm({
    name: props.member.name,
    knownAs: props.member.knownAs,
    emailAddresses: props.member.emailAddresses,
    postalAddress: {
        line1: props.member.postalAddress?.line1 ?? '',
        line2: props.member.postalAddress?.line2 ?? '',
        line3: props.member.postalAddress?.line3 ?? '',
        city: props.member.postalAddress?.city ?? '',
        county: props.member.postalAddress?.county ?? '',
        postcode: props.member.postalAddress?.postcode ?? ''
    },
    membershipType: props.member.membershipType,
    trustee: props.member.trusteeHistory ? props.member.trusteeHistory[0]?.resignedAt === null : false
});

const updateMember = () => {
    form.patch(route('member.update', props.member.id));
};

const addEmailAddress = () => {
    if (!form.emailAddresses) {
        form.emailAddresses = [];
    }
    form.emailAddresses.push({
        id: null,
        memberId: props.member.id,
        emailAddress: '',
        isPrimary: false,
        verifiedAt: null
    });
}

</script>
