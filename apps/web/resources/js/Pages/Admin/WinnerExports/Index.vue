<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    campaigns: Array,
    exports: Object,
});

const page = usePage();
const form = useForm({
    campaign_id: null,
});

const submit = () => {
    if (!form.campaign_id) {
        return;
    }

    form.post(route('admin.winner-exports.store', form.campaign_id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Eksporty zwycięzców" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Eksporty zwycięzców</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    v-if="page.props.flash.status"
                    class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-md"
                >
                    {{ page.props.flash.status }}
                </div>

                <form
                    class="bg-white rounded-lg shadow p-6 flex flex-col gap-4 md:flex-row md:items-end"
                    @submit.prevent="submit"
                >
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700" for="campaign_id">
                            Kampania do eksportu
                        </label>
                        <select
                            id="campaign_id"
                            v-model="form.campaign_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option :value="null">Wybierz kampanię</option>
                            <option
                                v-for="campaign in campaigns"
                                :key="campaign.id"
                                :value="campaign.id"
                            >
                                {{ campaign.name }}
                            </option>
                        </select>
                    </div>

                    <PrimaryButton :disabled="form.processing || !form.campaign_id">
                        Generuj eksport
                    </PrimaryButton>
                </form>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Campaign</th>
                                <th class="px-4 py-3 text-left">Rows</th>
                                <th class="px-4 py-3 text-left">When</th>
                                <th class="px-4 py-3 text-left">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in exports.data" :key="item.id" class="border-t">
                                <td class="px-4 py-3">{{ item.id }}</td>
                                <td class="px-4 py-3">{{ item.campaign?.name }}</td>
                                <td class="px-4 py-3">{{ item.row_count }}</td>
                                <td class="px-4 py-3">{{ item.generated_at }}</td>
                                <td class="px-4 py-3">
                                    <a
                                        :href="route('admin.winner-exports.download', item.id)"
                                        class="underline text-slate-700"
                                    >
                                        Pobierz
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
