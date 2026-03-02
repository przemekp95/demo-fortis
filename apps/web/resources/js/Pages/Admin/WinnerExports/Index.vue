<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    exports: Object,
});
</script>

<template>
    <Head title="Eksporty zwycięzców" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Eksporty zwycięzców</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                    <Link
                                        :href="route('admin.winner-exports.download', item.id)"
                                        class="underline text-slate-700"
                                    >
                                        Pobierz
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
