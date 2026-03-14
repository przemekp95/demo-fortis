<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    campaign: Object,
    dsrRequests: Array,
    stats: Object,
});

const page = usePage();
const dsrForm = useForm({
    type: 'export',
});

const submitDsr = (type) => {
    dsrForm.type = type;
    dsrForm.post(route('participant.dsr.store'), {
        preserveScroll: true,
    });
};
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

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow p-6 space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Wnioski RODO</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Złóż wniosek o eksport lub usunięcie danych. Status pojawi się
                                poniżej.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button
                                class="inline-flex justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                                :disabled="dsrForm.processing"
                                @click="submitDsr('export')"
                            >
                                Zamów eksport danych
                            </button>
                            <button
                                class="inline-flex justify-center rounded-md border border-rose-300 px-4 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50"
                                :disabled="dsrForm.processing"
                                @click="submitDsr('delete')"
                            >
                                Zgłoś usunięcie danych
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Ostatnie wnioski</h3>
                        </div>

                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">Typ</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Zgłoszono</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="dsrRequests.length === 0">
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                                        Brak złożonych wniosków.
                                    </td>
                                </tr>
                                <tr
                                    v-for="item in dsrRequests"
                                    :key="item.id"
                                    class="border-t border-gray-100"
                                >
                                    <td class="px-4 py-3 uppercase">{{ item.type }}</td>
                                    <td class="px-4 py-3">{{ item.status }}</td>
                                    <td class="px-4 py-3">{{ item.requested_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
