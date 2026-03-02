import { describe, expect, it, vi } from 'vitest';
import { isServiceWorkerSupported, registerProductionServiceWorker } from './pwa';

describe('pwa utilities', () => {
    it('detects service worker support', () => {
        expect(isServiceWorkerSupported(undefined)).toBe(false);
        expect(isServiceWorkerSupported({})).toBe(false);
        expect(isServiceWorkerSupported({ serviceWorker: {} })).toBe(true);
    });

    it('skips registration outside production or without support', async () => {
        const register = vi.fn();

        await expect(
            registerProductionServiceWorker({
                navigatorTarget: { serviceWorker: { register } },
                isProd: false,
            }),
        ).resolves.toBe(false);

        await expect(
            registerProductionServiceWorker({
                navigatorTarget: {},
                isProd: true,
            }),
        ).resolves.toBe(false);

        expect(register).not.toHaveBeenCalled();
    });

    it('returns true when registration succeeds', async () => {
        const register = vi.fn().mockResolvedValue({});

        await expect(
            registerProductionServiceWorker({
                navigatorTarget: { serviceWorker: { register } },
                isProd: true,
                path: '/custom-sw.js',
            }),
        ).resolves.toBe(true);

        expect(register).toHaveBeenCalledWith('/custom-sw.js');
    });

    it('returns false when registration fails', async () => {
        const register = vi.fn().mockRejectedValue(new Error('boom'));

        await expect(
            registerProductionServiceWorker({
                navigatorTarget: { serviceWorker: { register } },
                isProd: true,
            }),
        ).resolves.toBe(false);
    });
});
