<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
const page = usePage();

const legal = computed(() => page.props.legal ?? {});
</script>

<template>
    <footer class="rounded-3xl border border-white/15 bg-slate-900/75 px-6 py-8 sm:px-8">
        <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
            <section>
                <p class="text-xs uppercase tracking-[0.22em] text-cyan-300">{{ legal.brand }}</p>
                <h3 class="mt-2 text-lg font-semibold text-white">Platforma loterii paragonowej</h3>
                <p class="mt-3 text-sm leading-relaxed text-slate-300">
                    Obsługa kampanii promocyjnych, zgłoszeń paragonów, publikacji wyników oraz żądań
                    RODO w jednym, spójnym panelu operacyjnym.
                </p>
            </section>

            <section>
                <h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-200">
                    Skróty
                </h4>
                <ul class="mt-3 space-y-2 text-sm">
                    <li>
                        <a
                            :href="`${route('home')}#jak-dziala`"
                            class="text-slate-300 transition hover:text-white"
                            >Jak to działa</a
                        >
                    </li>
                    <li>
                        <a
                            :href="`${route('home')}#nagrody`"
                            class="text-slate-300 transition hover:text-white"
                            >Nagrody</a
                        >
                    </li>
                    <li>
                        <a
                            :href="`${route('home')}#wyniki`"
                            class="text-slate-300 transition hover:text-white"
                            >Wyniki</a
                        >
                    </li>
                </ul>
            </section>

            <section>
                <h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-200">
                    Dokumenty
                </h4>
                <ul class="mt-3 space-y-2 text-sm">
                    <li>
                        <a
                            :href="route('privacy-policy')"
                            class="text-slate-300 transition hover:text-white"
                            >Polityka prywatności</a
                        >
                    </li>
                    <li>
                        <a :href="route('terms')" class="text-slate-300 transition hover:text-white"
                            >Regulamin loterii</a
                        >
                    </li>
                </ul>
            </section>

            <section>
                <h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-200">
                    Kontakt
                </h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-300">
                    <li>
                        E-mail:
                        <a
                            :href="`mailto:${legal.support_email}`"
                            class="text-cyan-200 hover:text-cyan-100"
                            >{{ legal.support_email }}</a
                        >
                    </li>
                    <li>Infolinia: {{ legal.support_phone }}</li>
                    <li>{{ legal.support_hours }}</li>
                </ul>
            </section>
        </div>

        <div class="mt-8 border-t border-white/10 pt-4 text-xs text-slate-400">
            © {{ currentYear }} {{ legal.organization_name }}. Wszelkie prawa zastrzeżone.
        </div>
    </footer>
</template>
