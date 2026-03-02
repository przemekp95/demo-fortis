<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    campaign: Object,
    stats: Object,
});

const page = usePage();
</script>

<template>
    <Head title="Panel uczestnika" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel uczestnika</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    v-if="page.props.flash.status"
                    class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-md"
                >
                    {{ page.props.flash.status }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-gray-500">Twoje zgłoszenia</p>
                        <p class="text-3xl font-semibold mt-2">{{ stats.entries_total }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-gray-500">Wymaga weryfikacji</p>
                        <p class="text-3xl font-semibold mt-2">{{ stats.entries_flagged }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-5">
                        <p class="text-sm text-gray-500">Wygrane</p>
                        <p class="text-3xl font-semibold mt-2">{{ stats.winners_total }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6" v-if="campaign">
                    <h3 class="text-lg font-semibold">Aktywna kampania: {{ campaign.name }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ campaign.description }}</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Trwa od {{ campaign.starts_at }} do {{ campaign.ends_at }}
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow p-6" v-else>
                    <p class="text-gray-700">Obecnie brak aktywnej kampanii.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
