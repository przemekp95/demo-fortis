<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

defineProps({
    entries: Object,
});

const page = usePage();

const decide = (entryId, decision) => {
    router.post(route('admin.fraud.review', entryId), { decision });
};
</script>

<template>
    <Head title="Fraud Review" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fraud Review</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    v-if="page.props.flash.status"
                    class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-md"
                >
                    {{ page.props.flash.status }}
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Entry</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Risk</th>
                                <th class="px-4 py-3 text-left">Signals</th>
                                <th class="px-4 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in entries.data" :key="entry.id" class="border-t">
                                <td class="px-4 py-3">#{{ entry.id }}</td>
                                <td class="px-4 py-3">{{ entry.user?.email }}</td>
                                <td class="px-4 py-3">{{ entry.risk_score }}</td>
                                <td class="px-4 py-3">{{ entry.fraud_signals?.length ?? 0 }}</td>
                                <td class="px-4 py-3 flex gap-2">
                                    <button
                                        class="text-emerald-700 underline"
                                        @click="decide(entry.id, 'approved')"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        class="text-rose-700 underline"
                                        @click="decide(entry.id, 'rejected')"
                                    >
                                        Reject
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
