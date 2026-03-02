<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    consentVersions: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    birth_date: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    postal_code: '',
    country: 'PL',
    email: '',
    password: '',
    password_confirmation: '',
    accepted_consents: [],
    device_fingerprint: '',
    fax_number: '',
    _form_started_at: Date.now(),
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Rejestracja" />

        <form @submit.prevent="submit" class="space-y-4">
            <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                <label for="fax_number">Nie wypełniaj tego pola</label>
                <input
                    id="fax_number"
                    type="text"
                    tabindex="-1"
                    autocomplete="off"
                    v-model="form.fax_number"
                />
            </div>

            <input type="hidden" v-model="form._form_started_at" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="first_name" value="Imię" />
                    <TextInput
                        id="first_name"
                        class="mt-1 block w-full"
                        v-model="form.first_name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div>
                    <InputLabel for="last_name" value="Nazwisko" />
                    <TextInput
                        id="last_name"
                        class="mt-1 block w-full"
                        v-model="form.last_name"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="phone" value="Telefon" />
                    <TextInput id="phone" class="mt-1 block w-full" v-model="form.phone" required />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div>
                    <InputLabel for="birth_date" value="Data urodzenia" />
                    <TextInput
                        id="birth_date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.birth_date"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.birth_date" />
                </div>
            </div>

            <div>
                <InputLabel for="address_line_1" value="Adres" />
                <TextInput
                    id="address_line_1"
                    class="mt-1 block w-full"
                    v-model="form.address_line_1"
                    required
                />
                <InputError class="mt-2" :message="form.errors.address_line_1" />
            </div>

            <div>
                <InputLabel for="address_line_2" value="Adres (linia 2)" />
                <TextInput
                    id="address_line_2"
                    class="mt-1 block w-full"
                    v-model="form.address_line_2"
                />
                <InputError class="mt-2" :message="form.errors.address_line_2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <InputLabel for="city" value="Miasto" />
                    <TextInput id="city" class="mt-1 block w-full" v-model="form.city" required />
                    <InputError class="mt-2" :message="form.errors.city" />
                </div>
                <div>
                    <InputLabel for="postal_code" value="Kod pocztowy" />
                    <TextInput
                        id="postal_code"
                        class="mt-1 block w-full"
                        v-model="form.postal_code"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.postal_code" />
                </div>
                <div>
                    <InputLabel for="country" value="Kraj (ISO2)" />
                    <TextInput
                        id="country"
                        class="mt-1 block w-full"
                        v-model="form.country"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.country" />
                </div>
            </div>

            <div>
                <InputLabel for="email" value="E-mail" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Hasło" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Potwierdź hasło" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div>
                <InputLabel value="Akceptowane zgody" />
                <div class="mt-2 space-y-2">
                    <label
                        v-for="consent in consentVersions"
                        :key="consent.id"
                        class="flex items-start gap-2 text-sm text-gray-700"
                    >
                        <input
                            type="checkbox"
                            :value="consent.id"
                            v-model="form.accepted_consents"
                            class="rounded border-gray-300 mt-1"
                        />
                        <span>{{ consent.label }} (v{{ consent.version }})</span>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.accepted_consents" />
            </div>

            <div class="flex items-center justify-between pt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    Masz już konto?
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Zarejestruj
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
