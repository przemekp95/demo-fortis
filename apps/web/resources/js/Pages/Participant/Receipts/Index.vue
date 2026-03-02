<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { entryStatusClass, entryStatusLabel, riskLevel } from '@/utils/entryStatus';
import { Head, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    receipts: Object,
});

const page = usePage();

const form = useForm({
    receipt_number: '',
    purchase_amount: '',
    purchase_date: '',
    device_fingerprint: '',
});

const submit = () => {
    form.post(route('participant.receipts.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Paragony" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Zgłoś paragon</h2>
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
                    class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-4 gap-4"
                >
                    <div>
                        <InputLabel for="receipt_number" value="Nr paragonu" />
                        <TextInput
                            id="receipt_number"
                            class="mt-1 block w-full"
                            v-model="form.receipt_number"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.receipt_number" />
                    </div>
                    <div>
                        <InputLabel for="purchase_amount" value="Kwota" />
                        <TextInput
                            id="purchase_amount"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            v-model="form.purchase_amount"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.purchase_amount" />
                    </div>
                    <div>
                        <InputLabel for="purchase_date" value="Data zakupu" />
                        <TextInput
                            id="purchase_date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.purchase_date"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.purchase_date" />
                    </div>
                    <div class="flex items-end">
                        <PrimaryButton
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
                            class="w-full justify-center"
                        >
                            Wyślij zgłoszenie
                        </PrimaryButton>
                    </div>
                </form>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Nr</th>
                                <th class="text-left px-4 py-3">Kwota</th>
                                <th class="text-left px-4 py-3">Data zakupu</th>
                                <th class="text-left px-4 py-3">Status</th>
                                <th class="text-left px-4 py-3">Ryzyko</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in receipts.data" :key="row.id" class="border-t">
                                <td class="px-4 py-3">{{ row.receipt_number }}</td>
                                <td class="px-4 py-3">{{ row.purchase_amount }}</td>
                                <td class="px-4 py-3">{{ row.purchase_date }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2 py-1 text-xs font-medium',
                                            entryStatusClass(row.entry_status ?? row.status),
                                        ]"
                                    >
                                        {{ entryStatusLabel(row.entry_status ?? row.status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        row.risk_score === null
                                            ? '-'
                                            : `${row.risk_score} (${riskLevel(row.risk_score)})`
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
