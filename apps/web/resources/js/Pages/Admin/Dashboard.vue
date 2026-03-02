<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    snapshot: Object,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel administracyjny</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">KPI ({{ snapshot.bucket_at }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-xs uppercase text-gray-500">Entries</p>
                            <p class="text-2xl font-semibold">
                                {{ snapshot.metrics.entries_total }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-xs uppercase text-gray-500">Flagged</p>
                            <p class="text-2xl font-semibold">
                                {{ snapshot.metrics.entries_flagged }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-xs uppercase text-gray-500">Rejected</p>
                            <p class="text-2xl font-semibold">
                                {{ snapshot.metrics.entries_rejected }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-xs uppercase text-gray-500">Winners</p>
                            <p class="text-2xl font-semibold">
                                {{ snapshot.metrics.winners_total }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
