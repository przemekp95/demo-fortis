<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="Regulamin loterii" />

    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-white/10 bg-slate-950/80">
            <div
                class="mx-auto flex max-w-5xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8"
            >
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-cyan-300">Fortis</p>
                    <h1 class="mt-1 text-xl font-semibold text-white">
                        Regulamin loterii paragonowej
                    </h1>
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
                    Wersja regulaminu: 1.0 | Data obowiązywania: 27 lutego 2026
                </p>
                <p class="mt-4 leading-relaxed text-slate-200">
                    Niniejszy regulamin jest przykładowym dokumentem dołączonym do projektu
                    demonstracyjnego serwisu internetowego i aplikacji PWA.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">1. Organizator i definicje</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Na potrzeby tego demo przyjęto przykładowe dane organizatora. Uczestnik to osoba
                    fizyczna, która założyła konto, potwierdziła adres e-mail i zaakceptowała
                    wymagane zgody.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">2. Czas trwania i obszar loterii</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Loteria trwa w terminach opublikowanych na stronie głównej kampanii. Udział
                    możliwy jest na terytorium Rzeczypospolitej Polskiej.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">3. Warunki uczestnictwa</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>ukończone 18 lat i pełna zdolność do czynności prawnych,</li>
                    <li>rejestracja konta i weryfikacja adresu e-mail,</li>
                    <li>podanie prawdziwych danych w formularzu profilu,</li>
                    <li>posiadanie ważnego dowodu zakupu (paragonu).</li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">4. Zasady zgłaszania paragonów</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>jedno zgłoszenie odpowiada jednemu paragonowi,</li>
                    <li>uczestnik podaje numer paragonu, kwotę i datę zakupu,</li>
                    <li>zgłoszenie podlega walidacji oraz ocenie antyfraudowej,</li>
                    <li>duplikaty i zgłoszenia niezgodne z zasadami mogą zostać odrzucone.</li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">5. Losowania i nagrody</h2>
                <ul class="mt-3 list-disc space-y-2 pl-6 text-slate-200">
                    <li>losowania odbywają się codziennie oraz w losowaniu finałowym,</li>
                    <li>lista nagród i ich liczba publikowane są na stronie kampanii,</li>
                    <li>wyniki publikowane są z maskowaniem danych zwycięzców.</li>
                </ul>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">
                    6. Weryfikacja i utrata prawa do nagrody
                </h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Organizator może żądać okazania oryginału paragonu i dodatkowych danych
                    potwierdzających. Brak współpracy, podanie nieprawdziwych danych lub
                    stwierdzenie nadużycia skutkuje utratą prawa do nagrody.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">7. Reklamacje</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Reklamacje dotyczące przebiegu loterii można składać elektronicznie na adres
                    <a
                        href="mailto:reklamacje@fortis.test"
                        class="text-cyan-200 hover:text-cyan-100"
                        >reklamacje@fortis.test</a
                    >
                    w terminie 14 dni od zdarzenia będącego podstawą reklamacji.
                </p>
            </section>

            <section class="rounded-3xl border border-white/15 bg-slate-900/70 p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-white">8. Postanowienia końcowe</h2>
                <p class="mt-3 leading-relaxed text-slate-200">
                    Organizator zastrzega możliwość aktualizacji regulaminu z ważnych przyczyn
                    prawnych lub organizacyjnych. Aktualna wersja jest publikowana pod tym adresem.
                </p>
            </section>

            <PublicFooter />
        </main>
    </div>
</template>
