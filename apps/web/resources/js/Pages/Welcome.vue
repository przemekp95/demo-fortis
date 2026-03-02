<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicFooter from '@/Components/PublicFooter.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    campaign: {
        type: Object,
        default: null,
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentDraws: {
        type: Array,
        default: () => [],
    },
    recentWinners: {
        type: Array,
        default: () => [],
    },
});

const safeStats = computed(() => ({
    entries_total: props.stats?.entries_total ?? 0,
    winners_total: props.stats?.winners_total ?? 0,
    active_campaigns: props.stats?.active_campaigns ?? 0,
}));

const campaignProgress = computed(() => {
    if (!props.campaign?.starts_at || !props.campaign?.ends_at) {
        return 0;
    }

    const now = Date.now();
    const start = new Date(props.campaign.starts_at).getTime();
    const end = new Date(props.campaign.ends_at).getTime();

    if (Number.isNaN(start) || Number.isNaN(end) || end <= start) {
        return 0;
    }

    const progress = ((now - start) / (end - start)) * 100;

    return Math.max(0, Math.min(100, Math.round(progress)));
});

const campaignWindow = computed(() => {
    if (!props.campaign) {
        return 'Wkrótce ogłosimy nową kampanię.';
    }

    return `${formatDate(props.campaign.starts_at)} - ${formatDate(props.campaign.ends_at)}`;
});

const campaignBadge = computed(() => {
    return props.campaign ? 'Aktywna kampania' : 'Nowa edycja w przygotowaniu';
});

const formatDate = (value) => {
    if (!value) {
        return 'Brak daty';
    }

    return new Intl.DateTimeFormat('pl-PL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(value));
};

const formatDateTime = (value) => {
    if (!value) {
        return 'Brak daty';
    }

    return new Intl.DateTimeFormat('pl-PL', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const formatMoney = (value) => {
    if (value === null || value === undefined) {
        return 'Wartość do potwierdzenia';
    }

    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        maximumFractionDigits: 0,
    }).format(Number(value));
};

const drawTypeLabel = (type) => (type === 'final' ? 'Finał' : 'Codzienne');

const prizeClass = (drawType) => {
    if (drawType === 'final') {
        return 'border-amber-300/35 bg-amber-300/10';
    }

    return 'border-cyan-300/30 bg-cyan-300/10';
};

const mobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};
</script>

<template>
    <Head title="Fortis | Loteria Paragonowa" />

    <div class="font-body relative min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <div class="bg-grid absolute inset-0 opacity-40"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="relative z-10">
            <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <div class="logo-glow flex h-11 w-11 items-center justify-center rounded-xl border border-cyan-300/40 bg-slate-900/80">
                            <svg viewBox="0 0 28 28" class="h-6 w-6 text-cyan-200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 7.5L14 3L22 7.5V16.5L14 21L6 16.5V7.5Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M14 3V11M22 7.5L14 11M6 7.5L14 11" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M10 19.5H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div>
                            <p class="font-display text-xs uppercase tracking-[0.28em] text-cyan-300">FORTIS</p>
                            <p class="text-base font-semibold text-white sm:text-lg">Loteria Paragonowa 2026</p>
                        </div>
                    </div>

                    <nav class="hidden items-center gap-2 lg:flex">
                        <a href="#jak-dziala" class="nav-chip">Jak to działa</a>
                        <a href="#nagrody" class="nav-chip">Nagrody</a>
                        <a href="#wyniki" class="nav-chip">Wyniki</a>
                    </nav>

                    <div class="hidden items-center gap-2 text-sm sm:flex sm:gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-xl border border-cyan-200/40 px-4 py-2 font-medium text-cyan-100 transition hover:bg-cyan-300/10"
                        >
                            Przejdź do panelu
                        </Link>

                        <template v-else>
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="rounded-xl border border-white/20 px-4 py-2 font-medium text-white transition hover:bg-white/10"
                            >
                                Logowanie
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-xl bg-cyan-300 px-4 py-2 font-semibold text-slate-900 transition hover:bg-cyan-200"
                            >
                                Rejestracja
                            </Link>
                        </template>
                    </div>

                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 text-slate-100 transition hover:bg-white/10 lg:hidden"
                        :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
                        aria-label="Przełącz menu"
                        @click="toggleMobileMenu"
                    >
                        <svg
                            v-if="!mobileMenuOpen"
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M4 7H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            <path d="M4 12H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            <path d="M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        <svg
                            v-else
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <transition name="mobile-panel">
                    <div v-if="mobileMenuOpen" class="border-t border-white/10 bg-slate-950/95 px-4 py-4 sm:px-6 lg:hidden">
                        <div class="mx-auto max-w-7xl space-y-4">
                            <nav class="grid gap-2 text-sm">
                                <a href="#jak-dziala" class="mobile-link" @click="closeMobileMenu">Jak to działa</a>
                                <a href="#nagrody" class="mobile-link" @click="closeMobileMenu">Nagrody</a>
                                <a href="#wyniki" class="mobile-link" @click="closeMobileMenu">Wyniki</a>
                                <Link :href="route('privacy-policy')" class="mobile-link" @click="closeMobileMenu">Polityka prywatności</Link>
                                <Link :href="route('terms')" class="mobile-link" @click="closeMobileMenu">Regulamin</Link>
                            </nav>

                            <div class="grid gap-2 pt-2 text-sm">
                                <Link
                                    v-if="$page.props.auth.user"
                                    :href="route('dashboard')"
                                    class="rounded-xl border border-cyan-200/40 px-4 py-2.5 text-center font-medium text-cyan-100 transition hover:bg-cyan-300/10"
                                    @click="closeMobileMenu"
                                >
                                    Przejdź do panelu
                                </Link>

                                <template v-else>
                                    <Link
                                        v-if="canLogin"
                                        :href="route('login')"
                                        class="rounded-xl border border-white/20 px-4 py-2.5 text-center font-medium text-white transition hover:bg-white/10"
                                        @click="closeMobileMenu"
                                    >
                                        Logowanie
                                    </Link>
                                    <Link
                                        v-if="canRegister"
                                        :href="route('register')"
                                        class="rounded-xl bg-cyan-300 px-4 py-2.5 text-center font-semibold text-slate-900 transition hover:bg-cyan-200"
                                        @click="closeMobileMenu"
                                    >
                                        Rejestracja
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </div>
                </transition>
            </header>

            <main class="mx-auto flex max-w-7xl flex-col gap-10 px-4 pb-10 pt-14 sm:px-6 sm:pt-16 lg:px-8 lg:pt-20">
                <section class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
                    <div class="reveal rounded-3xl border border-white/15 bg-slate-900/70 p-7 shadow-2xl shadow-cyan-950/40 sm:p-9">
                        <p class="inline-flex items-center gap-2 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-cyan-200">
                            <span class="status-dot" aria-hidden="true"></span>
                            <span>{{ campaignBadge }}</span>
                        </p>

                        <h1 class="font-display mt-4 text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                            Jeden paragon. Jedno zgłoszenie. Realna szansa na wygraną.
                        </h1>

                        <p class="mt-5 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                            Weź udział w loterii Fortis, zgłoś paragon online i śledź status swoich zgłoszeń
                            w czasie rzeczywistym. Losowania odbywają się codziennie oraz w finale kampanii.
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <Link
                                v-if="canRegister && !$page.props.auth.user"
                                :href="route('register')"
                                class="rounded-xl bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200"
                            >
                                Dołącz do loterii
                            </Link>
                            <Link
                                v-if="canLogin && !$page.props.auth.user"
                                :href="route('login')"
                                class="rounded-xl border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                            >
                                Mam już konto
                            </Link>
                            <a
                                v-if="campaign?.terms_url"
                                :href="campaign.terms_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-xl border border-emerald-300/30 px-5 py-3 text-sm font-semibold text-emerald-100 transition hover:bg-emerald-300/10"
                            >
                                Regulamin loterii
                            </a>
                        </div>

                        <div class="mt-8 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4">
                                <p class="text-xs uppercase tracking-[0.1em] text-slate-400">Tryb</p>
                                <p class="mt-2 font-semibold text-white">Web + PWA</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4">
                                <p class="text-xs uppercase tracking-[0.1em] text-slate-400">Losowania</p>
                                <p class="mt-2 font-semibold text-white">Codziennie + finał</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4">
                                <p class="text-xs uppercase tracking-[0.1em] text-slate-400">Antyfraud</p>
                                <p class="mt-2 font-semibold text-white">Automatyczny scoring</p>
                            </div>
                        </div>
                    </div>

                    <aside class="reveal delay-1 rounded-3xl border border-white/15 bg-slate-900/70 p-7 sm:p-8">
                        <figure class="mb-6 overflow-hidden rounded-2xl border border-white/15 bg-slate-950/70">
                            <img
                                src="/images/landing/hero-prizes.svg"
                                alt="Wizualizacja nagród loterii Fortis"
                                class="h-52 w-full object-cover object-center"
                            />
                        </figure>

                        <h2 class="font-display text-2xl font-semibold text-white">{{ campaign?.name ?? 'Brak aktywnej kampanii' }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">
                            {{
                                campaign?.description ??
                                'Administrator opublikuje szczegóły kolejnej kampanii po jej aktywacji.'
                            }}
                        </p>

                        <div class="mt-6 rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                            <div class="mb-2 flex items-center justify-between text-xs uppercase tracking-[0.1em] text-slate-400">
                                <span>Postęp kampanii</span>
                                <span>{{ campaignProgress }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-cyan-300 via-sky-300 to-emerald-300 transition-all duration-700"
                                    :style="{ width: `${campaignProgress}%` }"
                                ></div>
                            </div>
                        </div>

                        <dl class="mt-6 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-2">
                                <dt class="text-slate-400">Okno kampanii</dt>
                                <dd class="text-right font-medium text-white">{{ campaignWindow }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-2">
                                <dt class="text-slate-400">Losowanie finałowe</dt>
                                <dd class="text-right font-medium text-white">{{ formatDate(campaign?.final_draw_at) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-2">
                                <dt class="text-slate-400">Limit zgłoszeń / dzień</dt>
                                <dd class="text-right font-medium text-white">{{ campaign?.rule?.max_entries_per_day ?? 'Brak danych' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-2">
                                <dt class="text-slate-400">Minimalna wartość</dt>
                                <dd class="text-right font-medium text-white">{{ formatMoney(campaign?.rule?.min_purchase_amount) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-400">Maks. wiek paragonu</dt>
                                <dd class="text-right font-medium text-white">
                                    {{ campaign?.rule?.max_receipt_age_days ? `${campaign.rule.max_receipt_age_days} dni` : 'Brak danych' }}
                                </dd>
                            </div>
                        </dl>
                    </aside>
                </section>

                <section class="reveal delay-1 grid gap-4 sm:grid-cols-3">
                    <article class="rounded-2xl border border-cyan-300/30 bg-cyan-300/10 p-5">
                        <p class="text-sm text-cyan-100/90">Wszystkie poprawne zgłoszenia</p>
                        <p class="font-display mt-2 text-4xl font-bold text-white">{{ safeStats.entries_total }}</p>
                    </article>
                    <article class="rounded-2xl border border-emerald-300/30 bg-emerald-300/10 p-5">
                        <p class="text-sm text-emerald-100/90">Opublikowani zwycięzcy</p>
                        <p class="font-display mt-2 text-4xl font-bold text-white">{{ safeStats.winners_total }}</p>
                    </article>
                    <article class="rounded-2xl border border-violet-300/30 bg-violet-300/10 p-5">
                        <p class="text-sm text-violet-100/90">Aktywne kampanie</p>
                        <p class="font-display mt-2 text-4xl font-bold text-white">{{ safeStats.active_campaigns }}</p>
                    </article>
                </section>

                <section class="reveal delay-2 grid gap-5 lg:grid-cols-2">
                    <figure class="overflow-hidden rounded-3xl border border-white/15 bg-slate-900/70">
                        <img
                            src="/images/landing/hero-prizes.svg"
                            alt="Nagrody dzienne i finałowe w loterii Fortis"
                            class="h-64 w-full object-cover sm:h-72"
                        />
                    </figure>
                    <figure class="overflow-hidden rounded-3xl border border-white/15 bg-slate-900/70">
                        <img
                            src="/images/landing/receipt-flow.svg"
                            alt="Proces zgłoszenia paragonu w aplikacji"
                            class="h-64 w-full object-cover sm:h-72"
                        />
                    </figure>
                </section>

                <section id="jak-dziala" class="reveal delay-2 rounded-3xl border border-white/15 bg-slate-900/70 p-7 sm:p-8">
                    <h2 class="font-display text-3xl font-semibold text-white">Jak to działa</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <article class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                            <p class="font-display text-4xl font-bold text-cyan-300">01</p>
                            <h3 class="mt-3 text-lg font-semibold text-white">Załóż konto uczestnika</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-300">
                                Rejestracja wymaga podstawowych danych i akceptacji zgód. Po aktywacji
                                maila możesz wysyłać paragony.
                            </p>
                        </article>
                        <article class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                            <p class="font-display text-4xl font-bold text-cyan-300">02</p>
                            <h3 class="mt-3 text-lg font-semibold text-white">Wyślij paragon</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-300">
                                Podaj numer paragonu, kwotę i datę zakupu. Silnik antyfraudowy ocenia
                                ryzyko i nadaje status zgłoszeniu.
                            </p>
                        </article>
                        <article class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                            <p class="font-display text-4xl font-bold text-cyan-300">03</p>
                            <h3 class="mt-3 text-lg font-semibold text-white">Śledź wyniki losowań</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-300">
                                Losowania są wykonywane codziennie i finałowo, a wyniki publikowane
                                w panelu oraz sekcji publicznej.
                            </p>
                        </article>
                    </div>
                </section>

                <section
                    v-if="campaign?.prizes?.length"
                    id="nagrody"
                    class="reveal delay-2 rounded-3xl border border-white/15 bg-slate-900/70 p-7 sm:p-8"
                >
                    <h2 class="font-display text-3xl font-semibold text-white">Pula nagród</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <article
                            v-for="prize in campaign.prizes"
                            :key="prize.id"
                            class="rounded-2xl border p-5"
                            :class="prizeClass(prize.draw_type)"
                        >
                            <p class="text-xs uppercase tracking-[0.1em] text-slate-300">{{ drawTypeLabel(prize.draw_type) }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-white">{{ prize.name }}</h3>
                            <div class="mt-4 flex items-center justify-between text-sm text-slate-200">
                                <span>Ilość: {{ prize.quantity }}</span>
                                <span class="font-semibold text-white">{{ formatMoney(prize.value) }}</span>
                            </div>
                        </article>
                    </div>
                </section>

                <section id="wyniki" class="grid gap-6 xl:grid-cols-2">
                    <article class="reveal delay-3 rounded-3xl border border-white/15 bg-slate-900/70 p-7 sm:p-8">
                        <h2 class="font-display text-3xl font-semibold text-white">Ostatni zwycięzcy</h2>

                        <div v-if="recentWinners.length" class="mt-6 space-y-3">
                            <div
                                v-for="winner in recentWinners"
                                :key="winner.id"
                                class="rounded-2xl border border-white/10 bg-slate-950/70 p-4"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ winner.prize }}</p>
                                        <p class="mt-1 text-sm text-slate-300">
                                            {{ winner.winner.email }}
                                            <span v-if="winner.winner.city"> · {{ winner.winner.city }}</span>
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-400">{{ formatDateTime(winner.published_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <p
                            v-else
                            class="mt-6 rounded-2xl border border-dashed border-white/20 bg-slate-950/60 p-4 text-sm text-slate-300"
                        >
                            Lista zwycięzców pojawi się po pierwszych zakończonych losowaniach.
                        </p>
                    </article>

                    <article class="reveal delay-3 rounded-3xl border border-white/15 bg-slate-900/70 p-7 sm:p-8">
                        <h2 class="font-display text-3xl font-semibold text-white">Historia losowań</h2>

                        <div v-if="recentDraws.length" class="mt-6 space-y-3">
                            <div
                                v-for="draw in recentDraws"
                                :key="draw.id"
                                class="rounded-2xl border border-white/10 bg-slate-950/70 p-4"
                            >
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-white">{{ drawTypeLabel(draw.type) }} #{{ draw.id }}</p>
                                        <p class="mt-1 text-sm text-slate-300">Liczba zwycięzców: {{ draw.winner_count }}</p>
                                    </div>
                                    <p class="text-xs text-slate-400">{{ formatDateTime(draw.finished_at || draw.started_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <p
                            v-else
                            class="mt-6 rounded-2xl border border-dashed border-white/20 bg-slate-950/60 p-4 text-sm text-slate-300"
                        >
                            Brak zakończonych losowań. Wróć tutaj po pierwszym uruchomieniu draw.
                        </p>
                    </article>
                </section>

                <PublicFooter class="reveal delay-3" />
            </main>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.bunny.net/css?family=manrope:400,500,700&family=space-grotesk:500,600,700&display=swap');

.font-body {
    font-family: 'Manrope', sans-serif;
}

.font-display {
    font-family: 'Space Grotesk', sans-serif;
}

.logo-glow {
    box-shadow: 0 0 0 1px rgba(34, 211, 238, 0.15), 0 0 30px rgba(34, 211, 238, 0.22);
}

.nav-chip {
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 0.75rem;
    padding: 0.45rem 0.9rem;
    color: rgb(186 230 253);
    font-size: 0.82rem;
    font-weight: 600;
    transition: background-color 180ms ease, border-color 180ms ease, color 180ms ease;
}

.nav-chip:hover {
    background-color: rgba(34, 211, 238, 0.12);
    border-color: rgba(34, 211, 238, 0.45);
    color: rgb(236 254 255);
}

.mobile-link {
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 0.85rem;
    padding: 0.62rem 0.85rem;
    color: rgb(226 232 240);
    font-weight: 500;
    transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
}

.mobile-link:hover {
    background-color: rgba(148, 163, 184, 0.14);
    border-color: rgba(148, 163, 184, 0.32);
    color: rgb(255 255 255);
}

.mobile-panel-enter-active,
.mobile-panel-leave-active {
    transition: opacity 180ms ease, transform 180ms ease;
}

.mobile-panel-enter-from,
.mobile-panel-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.status-dot {
    position: relative;
    display: inline-flex;
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 9999px;
    background: rgb(110 231 183);
    box-shadow: 0 0 0 2px rgba(110, 231, 183, 0.24);
}

.status-dot::after {
    content: '';
    position: absolute;
    inset: -0.35rem;
    border-radius: 9999px;
    border: 1px solid rgba(110, 231, 183, 0.55);
    animation: pulse-ring 1.8s ease-out infinite;
}

.bg-grid {
    background-image:
        linear-gradient(to right, rgba(148, 163, 184, 0.11) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148, 163, 184, 0.11) 1px, transparent 1px);
    background-size: 36px 36px;
    mask-image: radial-gradient(circle at center, black 35%, transparent 95%);
}

.orb {
    position: absolute;
    border-radius: 9999px;
    filter: blur(70px);
    opacity: 0.35;
    animation: float 11s ease-in-out infinite;
}

.orb-1 {
    top: -90px;
    left: 20%;
    width: 380px;
    height: 380px;
    background: rgba(34, 211, 238, 0.7);
}

.orb-2 {
    top: 25%;
    right: -120px;
    width: 340px;
    height: 340px;
    background: rgba(52, 211, 153, 0.6);
    animation-delay: 2s;
}

.orb-3 {
    bottom: -140px;
    left: -80px;
    width: 320px;
    height: 320px;
    background: rgba(125, 211, 252, 0.55);
    animation-delay: 4s;
}

.reveal {
    animation: reveal 0.7s ease both;
}

.delay-1 {
    animation-delay: 0.1s;
}

.delay-2 {
    animation-delay: 0.2s;
}

.delay-3 {
    animation-delay: 0.3s;
}

@keyframes float {
    0%,
    100% {
        transform: translate3d(0, 0, 0) scale(1);
    }

    50% {
        transform: translate3d(0, -20px, 0) scale(1.04);
    }
}

@keyframes reveal {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-ring {
    0% {
        transform: scale(0.65);
        opacity: 0.95;
    }

    70% {
        transform: scale(1.55);
        opacity: 0;
    }

    100% {
        transform: scale(1.55);
        opacity: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .status-dot::after {
        animation: none;
    }
}
</style>
