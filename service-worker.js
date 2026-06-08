const CACHE_NAME = 'juega123-horse-pwa-v1';
const APP_SHELL = [
  './',
  './index.php?page=caballos',
  './manifest.json',
  './css/jquery.mobile-1.4.5.min.css',
  './css/jquery.mobile.icons-1.4.5.min.css',
  './css/jquery.mobile.theme.css',
  './css/style.css',
  './css/dice.css',
  './js/jquery-2.1.4.min.js',
  './js/jquery.mobile-1.4.5.min.js',
  './js/system.js',
  './js/pwa-db.js',
  './js/horseRace.js',
  './js/horse-blockchain.js',
  './js/invitation-pool.js',
  './images/caballo.png',
  './images/bg_horse.png',
  './images/bgm_horse.png',
  './audio/galloping.mp3'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(APP_SHELL.map(url => new Request(url, { cache: 'reload' }))).catch(() => null))
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))))
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const req = event.request;
  if (req.method !== 'GET') return;
  event.respondWith(
    caches.match(req).then(cached => {
      if (cached) return cached;
      return fetch(req).then(response => {
        const copy = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
        return response;
      }).catch(() => caches.match('./index.php?page=caballos'));
    })
  );
});
