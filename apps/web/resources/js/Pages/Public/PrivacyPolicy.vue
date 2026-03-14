<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import PublicFooter from '@/Components/PublicFooter.vue';

const page = usePage();
const legal = computed(() => page.props.legal ?? {});

const formatDate = (value) => {
    if (!value) {
        return 'Do uzupełnienia';
    }

    return new Intl.DateTimeFormat('pl-PL', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value));
};
</script>

<template>
    <Head title="Polityka prywatności" />

    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-white/10 bg-slate-950/80">
            <div
                class="mx-auto flex max-w-5xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8"
            >
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-cyan-300">
                        {{ legal.brand }}
                    </p>
                    <h1 class="mt-1 text-xl font-semibold text-white">Polityka prywatności</h1>
                </div>

                <Link
                    :href="route('home')"
                    class="rounded-xl border border-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/10"
                >
                    Powrót na stronę główną
                </Link>
            </div>
        </header>

        <main class="mx-auto flex max-w-5xl flex-col gap-6 px-4 py-8 sm:px-6 lg:px-8">
            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <p class="text-sm text-slate-300">
                    Data ostatniej aktualizacji: {{ formatDate(legal.privacy_last_updated) }}
                </p>
                <p class="mt-4 leading-relaxed text-slate-200">
                    Niniejsza Polityka prywatności opisuje zasady przetwarzania danych osobowych w
                    ramach platformy loterii paragonowej {{ legal.brand }} oraz usług powiązanych, w
                    tym panelu uczestnika, panelu administracyjnego i publicznego API.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">1. Administrator danych</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Administratorem danych osobowych jest {{ legal.organization_name }}. Kontakt w
                    sprawach ochrony danych oraz realizacji praw osób, których dane dotyczą:
                    <a
                        :href="`mailto:${legal.privacy_email}`"
                        class="text-cyan-200 hover:text-cyan-100"
                        >{{ legal.privacy_email }}</a
                    >.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">2. Zakres danych</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>dane konta użytkownika: imię, nazwisko, e-mail, hasło (hash),</li>
                    <li>dane profilowe i kontaktowe wymagane do udziału w loterii,</li>
                    <li>dane zgłoszeń: numer paragonu, data zakupu, kwota zakupu,</li>
                    <li>
                        dane techniczne i bezpieczeństwa: adres IP, identyfikatory urządzenia, logi
                        audytowe.
                    </li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">3. Cele i podstawy przetwarzania</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>realizacja loterii i obsługa zgłoszeń (art. 6 ust. 1 lit. b RODO),</li>
                    <li>
                        weryfikacja nadużyć i bezpieczeństwo usługi (art. 6 ust. 1 lit. f RODO),
                    </li>
                    <li>
                        spełnienie obowiązków prawnych, w tym podatkowych i księgowych (art. 6 ust.
                        1 lit. c RODO),
                    </li>
                    <li>
                        komunikacja marketingowa wyłącznie na podstawie odrębnej zgody (art. 6 ust.
                        1 lit. a RODO).
                    </li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">4. Czas przechowywania danych</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Dane są przechowywane przez okres trwania kampanii, a następnie przez czas
                    wymagany do rozliczeń i obsługi roszczeń. Po upływie okresu retencji dane
                    podlegają anonimizacji lub usunięciu.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">5. Odbiorcy danych</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Dane mogą być powierzane podmiotom obsługującym infrastrukturę IT, wysyłkę
                    powiadomień oraz wsparcie operacyjne loterii. Każde powierzenie odbywa się na
                    podstawie umowy.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">6. Twoje prawa</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>prawo dostępu do danych, ich sprostowania i usunięcia,</li>
                    <li>prawo ograniczenia przetwarzania oraz prawo sprzeciwu,</li>
                    <li>prawo do przenoszenia danych,</li>
                    <li>prawo wycofania zgody w dowolnym momencie,</li>
                    <li>prawo wniesienia skargi do Prezesa UODO.</li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">7. Cookies i technologie podobne</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Serwis wykorzystuje cookies techniczne niezbędne do działania konta użytkownika,
                    utrzymania sesji i zabezpieczenia formularzy. Opcjonalne cookies analityczne lub
                    marketingowe są aktywowane wyłącznie po uzyskaniu zgody.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">8. Kontakt i żądania RODO</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Żądania dotyczące danych osobowych można składać poprzez panel uczestnika
                    (sekcja DSR) lub wiadomość e-mail na adres:
                    <a
                        :href="`mailto:${legal.privacy_email}`"
                        class="text-cyan-200 hover:text-cyan-100"
                        >{{ legal.privacy_email }}</a
                    >.
                </p>
            </section>

            <PublicFooter />
        </main>
    </div>
</template>
