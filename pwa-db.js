/* PWA local DB para Juego de Caballos
 * Guarda apuestas demo/locales y cola de sincronizacion.
 */
(function (window) {
  'use strict';

  var DB_NAME = 'juega123_horse_pwa';
  var DB_VERSION = 1;

  function openDb() {
    return new Promise(function (resolve, reject) {
      if (!('indexedDB' in window)) {
        reject(new Error('IndexedDB no disponible en este navegador'));
        return;
      }
      var req = indexedDB.open(DB_NAME, DB_VERSION);
      req.onupgradeneeded = function (event) {
        var db = event.target.result;
        if (!db.objectStoreNames.contains('demo_bets')) {
          db.createObjectStore('demo_bets', { keyPath: 'client_uuid' });
        }
        if (!db.objectStoreNames.contains('sync_queue')) {
          db.createObjectStore('sync_queue', { keyPath: 'client_uuid' });
        }
        if (!db.objectStoreNames.contains('events')) {
          db.createObjectStore('events', { keyPath: 'event_code' });
        }
      };
      req.onsuccess = function () { resolve(req.result); };
      req.onerror = function () { reject(req.error); };
    });
  }

  function put(storeName, value) {
    return openDb().then(function (db) {
      return new Promise(function (resolve, reject) {
        var tx = db.transaction(storeName, 'readwrite');
        tx.objectStore(storeName).put(value);
        tx.oncomplete = function () { resolve(value); };
        tx.onerror = function () { reject(tx.error); };
      });
    });
  }

  function remove(storeName, key) {
    return openDb().then(function (db) {
      return new Promise(function (resolve, reject) {
        var tx = db.transaction(storeName, 'readwrite');
        tx.objectStore(storeName).delete(key);
        tx.oncomplete = function () { resolve(true); };
        tx.onerror = function () { reject(tx.error); };
      });
    });
  }

  function all(storeName) {
    return openDb().then(function (db) {
      return new Promise(function (resolve, reject) {
        var tx = db.transaction(storeName, 'readonly');
        var req = tx.objectStore(storeName).getAll();
        req.onsuccess = function () { resolve(req.result || []); };
        req.onerror = function () { reject(req.error); };
      });
    });
  }

  window.HorsePwaDB = {
    put: put,
    remove: remove,
    all: all,
    saveDemoBet: function (bet) { return put('demo_bets', bet); },
    queue: function (item) { return put('sync_queue', item); },
    pendingQueue: function () { return all('sync_queue'); },
    markSynced: function (clientUuid) { return remove('sync_queue', clientUuid); },
    saveEvent: function (event) { return put('events', event); }
  };
})(window);
