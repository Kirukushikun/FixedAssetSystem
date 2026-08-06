// Bump this on every deploy that changes precached assets to invalidate old caches.
const CACHE_VERSION = 'v1';
const CACHE_NAME = `fixed-asset-cache-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Stable-path shell assets — safe to precache since their URLs don't change
// between builds (unlike the hashed /build/assets/* Vite output).
const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.json',
    '/css/global.css',
    '/js/global.js',
    '/img/Fixed.ico',
    '/img/Fixed.png',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    // Never intercept API/Livewire traffic — those must always hit the network.
    const url = new URL(request.url);
    if (url.pathname.startsWith('/livewire/')) {
        return;
    }

    // HTML navigations: network-first, fall back to cache, then the offline page.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL)))
        );
        return;
    }

    // Static assets (css/js/img/fonts/icons): stale-while-revalidate.
    if (['style', 'script', 'image', 'font'].includes(request.destination)) {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) =>
                cache.match(request).then((cached) => {
                    const networkFetch = fetch(request)
                        .then((response) => {
                            cache.put(request, response.clone());
                            return response;
                        })
                        .catch(() => cached);
                    return cached || networkFetch;
                })
            )
        );
    }
});
