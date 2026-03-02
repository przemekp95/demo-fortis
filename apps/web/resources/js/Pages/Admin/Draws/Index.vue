<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    schedules: Object,
});

const form = useForm({
    campaign_id: '',
    type: 'daily',
});

const submit = () => {
    form.post(route('admin.draws.run-now'));
};
</script>

<template>
    <Head title="Losowania" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Losowania</h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <form
                    @submit.prevent="submit"
                    class="bg-white rounded-lg shadow p-4 flex gap-4 items-end"
                >
                    <div>
                        <label class="block text-sm text-gray-600">Campaign ID</label>
                        <input v-model="form.campaign_id" class="rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Typ</label>
                        <select v-model="form.type" class="rounded-md border-gray-300">
                            <option value="daily">daily</option>
                            <option value="final">final</option>
                        </select>
                    </div>
                    <button class="bg-slate-900 text-white rounded-md px-4 py-2">Uruchom</button>
                </form>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Campaign</th>
                                <th class="px-4 py-3 text-left">Typ</th>
                                <th class="px-4 py-3 text-left">Run at</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="schedule in schedules.data"
                                :key="schedule.id"
                                class="border-t"
                            >
                                <td class="px-4 py-3">{{ schedule.campaign?.name }}</td>
                                <td class="px-4 py-3">{{ schedule.type }}</td>
                                <td class="px-4 py-3">{{ schedule.run_at }}</td>
                                <td class="px-4 py-3">{{ schedule.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
