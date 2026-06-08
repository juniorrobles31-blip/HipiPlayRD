(function () {
  'use strict';
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('service-worker.js')
        .then(function (reg) { console.log('[PWA] Service Worker registrado', reg.scope); })
        .catch(function (err) { console.warn('[PWA] No se pudo registrar Service Worker', err); });
    });
  }
})();
