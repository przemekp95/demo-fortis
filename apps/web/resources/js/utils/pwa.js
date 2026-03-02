export function isServiceWorkerSupported(navigatorTarget = globalThis.navigator) {
    return (
        typeof navigatorTarget !== 'undefined' &&
        navigatorTarget !== null &&
        'serviceWorker' in navigatorTarget
    );
}

export async function registerProductionServiceWorker({
    navigatorTarget = globalThis.navigator,
    isProd = false,
    path = '/sw.js',
} = {}) {
    if (!isProd || !isServiceWorkerSupported(navigatorTarget)) {
        return false;
    }

    try {
        await navigatorTarget.serviceWorker.register(path);
        return true;
    } catch {
        return false;
    }
}
