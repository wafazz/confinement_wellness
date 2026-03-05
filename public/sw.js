const CACHE_VERSION = 'cw-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_URL
];

// Install — precache offline page
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then(cache => cache.addAll(PRECACHE_ASSETS))
    );
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_VERSION).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch strategy
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Network-only for POST and AJAX/DataTable
    if (request.method !== 'GET') return;
    if (request.headers.get('X-Requested-With') === 'XMLHttpRequest') return;
    if (url.searchParams.has('draw')) return; // DataTable AJAX

    // CDN — stale-while-revalidate
    if (['cdn.jsdelivr.net', 'cdnjs.cloudflare.com', 'code.jquery.com'].includes(url.hostname)) {
        event.respondWith(
            caches.open(CACHE_VERSION).then(cache =>
                cache.match(request).then(cached => {
                    const fetched = fetch(request).then(response => {
                        if (response.ok) cache.put(request, response.clone());
                        return response;
                    }).catch(() => cached);
                    return cached || fetched;
                })
            )
        );
        return;
    }

    // Static assets — cache-first
    if (/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$/i.test(url.pathname)) {
        event.respondWith(
            caches.open(CACHE_VERSION).then(cache =>
                cache.match(request).then(cached => {
                    if (cached) return cached;
                    return fetch(request).then(response => {
                        if (response.ok) cache.put(request, response.clone());
                        return response;
                    });
                })
            )
        );
        return;
    }

    // HTML navigation — network-first, fallback to offline page
    if (request.mode === 'navigate' || request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }
});
