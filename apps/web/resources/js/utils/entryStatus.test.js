import { describe, expect, it } from 'vitest';
import { entryStatusClass, entryStatusLabel, riskLevel } from './entryStatus';

describe('entryStatus utilities', () => {
    it('returns known labels and fallback for unknown statuses', () => {
        expect(entryStatusLabel('approved')).toBe('zaakceptowane');
        expect(entryStatusLabel('flagged')).toBe('do weryfikacji');
        expect(entryStatusLabel('winner')).toBe('zwycieskie');
        expect(entryStatusLabel('unknown')).toBe('nieznany');
        expect(entryStatusLabel(null)).toBe('nieznany');
        expect(entryStatusLabel('')).toBe('nieznany');
    });

    it('returns badge classes with fallback', () => {
        expect(entryStatusClass('rejected')).toContain('rose');
        expect(entryStatusClass('flagged')).toContain('amber');
        expect(entryStatusClass('unknown')).toContain('slate');
        expect(entryStatusClass(undefined)).toContain('slate');
    });

    it('maps numeric score to risk level', () => {
        expect(riskLevel(91)).toBe('wysokie');
        expect(riskLevel(60)).toBe('srednie');
        expect(riskLevel(59.99)).toBe('niskie');
        expect(riskLevel(null)).toBe('brak');
        expect(riskLevel('abc')).toBe('brak');
    });
});
