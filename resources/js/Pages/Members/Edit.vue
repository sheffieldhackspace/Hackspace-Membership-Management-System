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
                        <InputLabel for="name" value="Name:" />
                    </div>
                    <div class="col-span-2">
                        <TextInput type="text" v-model="form.name" id="name" class="mt-1 block rounded border-gray-200 w-full" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="knownAs" value="Known As:" />
                    </div>
                    <div class="col-span-2">
                        <TextInput type="text" v-model="form.knownAs" id="knownAs" class="mt-1 block w-full rounded border-gray-200" />
                        <InputError class="mt-2" :message="form.errors.knownAs" />
                    </div>

                    <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Emails</h3>
                        <InputError class="mt-2" :message="form.errors.emailAddresses" />
                    </div>

                    <template v-for="(email, index) in form.emailAddresses" :key="email.id">
                        <div class="col-span-1 content-center">
                            <InputLabel :for="'emailAddresses.' + index + '.email'" :value="'Email ' + (index + 1)" />
                        </div>
                        <div class="col-span-2">
                            <TextInput type="email" v-model="email.emailAddress" :id="'emailAddresses.' + index + '.email'" class="mt-1 block w-full rounded border-gray-200" />
                        </div>
                        <div class="col-span-1 content-center items-center flex">
                            <InputLabel class="mr-2" :for="'emailAddresses.' + index + '.isPrimary'" value="Primary" />
                            <Checkbox name="'emailAddresses.' + index + '.isPrimary'" v-model:checked="email.isPrimary" />
                        </div>
                        <div class="col-span-2 content-center">
                            <DangerButton @click="form.emailAddresses?.splice(index, 1)">Delete</DangerButton>
                        </div>
                    </template>
                    <div class="md:col-span-6 col-span-3">
                        <SecondaryButton @click="addEmailAddress">Add Email</SecondaryButton>
                    </div>

                    <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                        <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Postal Address</h3>
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="line1" value="Line 1" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.line1" id="line1" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="line2" value="Line 2" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.line2" id="line2" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="line3" value="Line 3" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.line3" id="line3" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="city" value="City" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.city" id="city" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="county" value="County" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.county" id="county" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-1 content-center">
                        <InputLabel for="postcode" value="Postcode" />
                    </div>
                    <div class="col-span-2 content-center">
                        <TextInput type="text" v-model="form.postalAddress.postcode" id="postcode" class="mt-1 block w-full" />
                    </div>

                    <template v-if="props.canChangeMembershipType">
                        <div class="md:col-span-6 col-span-3 border-t-2 border-gray-200 pt-4">
                            <h3 class="text-l font-semibold leading-tight text-gray-800 mb-2">Membership</h3>
                        </div>
                        <div class="col-span-1 content-center">
                            <InputLabel for="membershipType" value="Membership Type" />
                        </div>
                        <div class="col-span-2">
                            <select
                                v-model="form.membershipType"
                                class="border border-gray-300 rounded-md p-2 pr-10"
                                id="membershipType"
                            >
                                <option v-for="type in props.membershipTypes" :key="type.label" :value="type.value" >{{ type.label }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.membershipType" />
                        </div>

                        <div class="col-span-3 content-center items-center flex space-x-2">
                            <InputLabel for="trustee" value="Is a Trustee" />
                            <Checkbox name="trustee" v-model:checked="form.trustee" />
                            <InputError class="mt-2" :message="form.errors.trustee" />

                        </div>
                    </template>

                    <div class="md:col-span-6 col-span-3 flex place-content-end content-center space-x-2">
                        <a :href=" route('dashboard')"><SecondaryButton>Cancel</SecondaryButton></a>
                        <PrimaryButton type="submit" @click="updateMember">Save</PrimaryButton>
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
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps< {
    member: MemberData
    membershipTypes: MembershipTypeData[]
    canChangeMembershipType: boolean
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
    membershipType: props.member.membershipType.value,
    trustee: props.member.trusteeHistory ? props.member.trusteeHistory[0]?.resignedAt === null : false
});

const updateMember = () => {
    console.log(form.data());
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
