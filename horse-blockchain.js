/* Juego de Caballos actualizado: Demo/Real + auditoria blockchain-ready */
(function (window, $) {
  'use strict';

  function uuid() {
    if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
    return 'horse-' + Date.now() + '-' + Math.random().toString(16).slice(2);
  }

  function selectedMode() {
    return $('input[name="horse_mode"]:checked').val() || 'real';
  }

  function selectedNumbers() {
    var selected = [];
    for (var i = 1; i <= 6; i++) {
      if ($('input#checkbox-' + i + 'a').is(':checked')) selected.push(i);
    }
    while (selected.length < 3) selected.push(0);
    return selected.slice(0, 3);
  }

  function selectedAmount() {
    var amount = parseFloat($('input[name=radio-choice]:checked').val() || GLOBAL.bid || 0);
    if ($('input#radio-choice-8').is(':checked')) {
      amount = parseFloat($('input#radio-choice-9').val() || 0);
    }
    if (!amount || amount <= 0) amount = parseFloat(GLOBAL.bid || 0);
    return amount;
  }

  function showMsg(msg) {
    $('div#contador').html(msg);
  }

  function refreshBalance(balance) {
    if (balance !== undefined && balance !== null) {
      $('span#balance').html(balance);
      $('span#balance2').html(balance);
    }
  }

  function postService(payload) {
    return $.ajax({ type: 'POST', dataType: 'json', url: 'system.php', async: true, data: payload });
  }

  function syncQueue() {
    if (!window.HorsePwaDB || !navigator.onLine) return;
    HorsePwaDB.pendingQueue().then(function (items) {
      items.forEach(function (item) {
        postService(item.payload)
          .done(function (json) {
            if (json && json.STATUS === 'OK') HorsePwaDB.markSynced(item.client_uuid);
          });
      });
    });
  }

  window.addEventListener('online', syncQueue);

  function betDemo(payload) {
    var localBet = {
      client_uuid: payload.client_uuid,
      amount: payload.amount,
      number1: payload.number1,
      number2: payload.number2,
      number3: payload.number3,
      status: 'local_pending',
      created_at: new Date().toISOString()
    };

    if (window.HorsePwaDB) {
      HorsePwaDB.saveDemoBet(localBet);
      HorsePwaDB.queue({ client_uuid: payload.client_uuid, payload: payload, created_at: localBet.created_at });
    }

    if (!navigator.onLine) {
      showMsg('Apuesta demo guardada localmente. Se sincronizara cuando vuelva internet.');
      clearBid();
      $('div#bid').popup('close');
      return;
    }

    postService(payload)
      .done(function (json) {
        if (json.STATUS === 'OK') {
          showMsg('Demo registrada. Hash: ' + (json.audit ? json.audit.state_hash.substring(0, 14) + '...' : 'pendiente'));
          refreshBalance(json.balance);
          if (window.HorsePwaDB) HorsePwaDB.markSynced(payload.client_uuid);
          clearBid();
          $('div#bid').popup('close');
          if (typeof getLastResult === 'function') getLastResult();
        } else {
          showMsg('* ' + json.INFO);
        }
      })
      .fail(function () { showMsg('Demo guardada localmente. Fallo la sincronizacion.'); });
  }

  function betReal(payload) {
    postService(payload)
      .done(function (json) {
        if (json.STATUS === 'OK') {
          var button = ' <a href="#panel_user" style="display:inline-block;" class="ui-link">Ver apuestas</a>';
          $('div#play').html(json.INFO + button);
          showMsg('Hash auditoria: ' + (json.audit ? json.audit.state_hash.substring(0, 14) + '...' : 'pendiente'));
          refreshBalance(json.balance);
          clearBid();
          $('div#bid').popup('close');
          if (typeof getLastResult === 'function') getLastResult();
        } else {
          showMsg('* ' + json.INFO);
        }
      })
      .fail(function (xhr, status) { showMsg('Error apuesta real: ' + status + ' ' + JSON.stringify(xhr)); });
  }

  window.ApostarHorseBlockchain = function () {
    if (typeof GAME_MODE === 'undefined' || GAME_MODE !== 'horse') return false;

    var amount = selectedAmount();
    var numbers = selectedNumbers();
    if (!amount || amount <= 0) { showMsg('Seleccione un monto valido'); return false; }
    if (numbers[0] === 0 && numbers[1] === 0 && numbers[2] === 0) { showMsg('Seleccione al menos un caballo'); return false; }

    var mode = selectedMode();
    var payload = {
      service: mode === 'demo' ? 'horse.bet.demo' : 'horse.bet.real',
      amount: amount,
      number1: numbers[0],
      number2: numbers[1],
      number3: numbers[2],
      client_uuid: uuid()
    };

    $('div#play').html($LANG && $LANG.connecting ? $LANG.connecting : 'Conectando...');
    if (mode === 'demo') betDemo(payload); else betReal(payload);
    return false;
  };

  var oldApostar = window.Apostar;
  window.Apostar = function () {
    if (typeof GAME_MODE !== 'undefined' && GAME_MODE === 'horse') return window.ApostarHorseBlockchain();
    if (typeof oldApostar === 'function') return oldApostar.apply(window, arguments);
  };

  $(document).on('pagecreate pageshow', function () {
    if (typeof GAME_MODE !== 'undefined' && GAME_MODE === 'horse') syncQueue();
  });
})(window, jQuery);
