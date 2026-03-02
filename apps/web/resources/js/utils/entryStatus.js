const LABELS = {
    submitted: 'przyjete',
    accepted: 'zaakceptowane',
    approved: 'zaakceptowane',
    flagged: 'do weryfikacji',
    rejected: 'odrzucone',
    drawn: 'wylosowane',
    winner: 'zwycieskie',
};

const BADGE_CLASSES = {
    submitted: 'bg-slate-100 text-slate-700',
    accepted: 'bg-emerald-100 text-emerald-700',
    approved: 'bg-emerald-100 text-emerald-700',
    flagged: 'bg-amber-100 text-amber-700',
    rejected: 'bg-rose-100 text-rose-700',
    drawn: 'bg-indigo-100 text-indigo-700',
    winner: 'bg-yellow-100 text-yellow-700',
};

export function entryStatusLabel(status) {
    if (status === null || status === undefined || status === '') {
        return 'nieznany';
    }

    return LABELS[status] ?? 'nieznany';
}

export function entryStatusClass(status) {
    if (status === null || status === undefined || status === '') {
        return 'bg-slate-100 text-slate-700';
    }

    return BADGE_CLASSES[status] ?? 'bg-slate-100 text-slate-700';
}

export function riskLevel(score, { flagThreshold = 60, rejectThreshold = 90 } = {}) {
    if (score === null || score === undefined) {
        return 'brak';
    }

    const parsed = Number(score);
    if (Number.isNaN(parsed)) {
        return 'brak';
    }

    if (parsed >= rejectThreshold) {
        return 'wysokie';
    }

    if (parsed >= flagThreshold) {
        return 'srednie';
    }

    return 'niskie';
}
