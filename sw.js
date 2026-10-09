const CACHE_NAME = 'multiplica-4to-v1';
const urlsToCache = [
  './',
  './index.php',
  './menu.php',
  './aprender.php',
  './juegos.php',
  './simce.php',
  './resultados.php',
  './css/style.css',
  './js/sound.js',
  './js/progress.js',
  './js/confetti.js',
  './js/particles.js'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache).catch(err => {
        console.warn('Caché parcial de inicio:', err);
      });
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
