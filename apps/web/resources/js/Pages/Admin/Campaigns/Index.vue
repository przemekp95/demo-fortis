<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    campaigns: Array,
});

const page = usePage();

const form = useForm({
    name: '',
    slug: '',
    status: 'draft',
    description: '',
    timezone: 'Europe/Warsaw',
    starts_at: '',
    ends_at: '',
    final_draw_at: '',
    terms_url: '',
    rule: {
        max_entries_per_day: 5,
        velocity_per_hour: 3,
        max_receipt_age_days: 14,
        min_purchase_amount: 1,
        deduplicate_receipts: true,
        risk_score_flag_threshold: 60,
        risk_score_reject_threshold: 90,
    },
    prizes: [
        { name: 'Nagroda dzienna', draw_type: 'daily', quantity: 1, value: 100 },
        { name: 'Nagroda główna', draw_type: 'final', quantity: 1, value: 5000 },
    ],
});

const submit = () => {
    form.post(route('admin.campaigns.store'), {
        preserveScroll: true,
    });
};

const activate = (id) => {
    form.post(route('admin.campaigns.activate', id));
};
</script>

<template>
    <Head title="Kampanie" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kampanie</h2>
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
                    @submit.prevent="submit"
                    class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <input
                        v-model="form.name"
                        placeholder="Nazwa"
                        class="rounded-md border-gray-300"
                    />
                    <input
                        v-model="form.slug"
                        placeholder="Slug"
                        class="rounded-md border-gray-300"
                    />
                    <select v-model="form.status" class="rounded-md border-gray-300">
                        <option value="draft">draft</option>
                        <option value="active">active</option>
                    </select>
                    <input v-model="form.timezone" class="rounded-md border-gray-300" />
                    <input
                        v-model="form.starts_at"
                        type="datetime-local"
                        class="rounded-md border-gray-300"
                    />
                    <input
                        v-model="form.ends_at"
                        type="datetime-local"
                        class="rounded-md border-gray-300"
                    />
                    <input
                        v-model="form.final_draw_at"
                        type="datetime-local"
                        class="rounded-md border-gray-300"
                    />
                    <input
                        v-model="form.terms_url"
                        placeholder="https://..."
                        class="rounded-md border-gray-300"
                    />
                    <textarea
                        v-model="form.description"
                        class="rounded-md border-gray-300 md:col-span-2"
                        placeholder="Opis kampanii"
                    />
                    <button class="bg-slate-900 text-white rounded-md px-4 py-2 md:col-span-2">
                        Utwórz kampanię
                    </button>
                </form>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Nazwa</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Okres</th>
                                <th class="px-4 py-3 text-left">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="campaign in campaigns" :key="campaign.id" class="border-t">
                                <td class="px-4 py-3">{{ campaign.name }}</td>
                                <td class="px-4 py-3">{{ campaign.status }}</td>
                                <td class="px-4 py-3">
                                    {{ campaign.starts_at }} - {{ campaign.ends_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        v-if="campaign.status !== 'active'"
                                        class="text-slate-700 underline"
                                        @click="activate(campaign.id)"
                                    >
                                        Aktywuj
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
