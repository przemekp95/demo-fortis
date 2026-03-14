<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    requests: Object,
});

const page = usePage();
</script>

<template>
    <Head title="Wnioski RODO" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Wnioski RODO</h2>
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
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Typ</th>
                                <th class="px-4 py-3 text-left">Użytkownik</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Wynik</th>
                                <th class="px-4 py-3 text-left">Zgłoszono</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in requests.data" :key="item.id" class="border-t">
                                <td class="px-4 py-3">#{{ item.id }}</td>
                                <td class="px-4 py-3 uppercase">{{ item.type }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ item.email }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ item.user?.profile?.first_name }}
                                        {{ item.user?.profile?.last_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ item.status }}</td>
                                <td class="px-4 py-3">{{ item.result_path || '-' }}</td>
                                <td class="px-4 py-3">{{ item.requested_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
