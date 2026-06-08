/* Pool de Invitacion PWA
 * - Detecta ?invite=TOKEN y lo registra.
 * - Genera link de invitacion del usuario.
 * - Muestra ranking y gift wallet.
 */
(function (window, $) {
  'use strict';

  function post(service, data) {
    data = data || {};
    data.service = service;
    return $.ajax({ url: 'system.php', method: 'POST', data: data, dataType: 'json' });
  }

  function sessionKey() {
    var key = localStorage.getItem('juega123_ref_session');
    if (!key) {
      key = 'ref-' + Date.now() + '-' + Math.random().toString(16).slice(2);
      localStorage.setItem('juega123_ref_session', key);
    }
    return key;
  }

  function detectInvite() {
    try {
      var params = new URLSearchParams(window.location.search);
      var token = params.get('invite');
      if (!token) return;
      localStorage.setItem('juega123_invite_token', token);
      post('referral.open', { token: token, session_key: sessionKey() })
        .done(function (res) { console.log('[Referral] invitacion registrada', res); })
        .fail(function (err) { console.warn('[Referral] no se pudo registrar invitacion', err); });
    } catch (e) {}
  }

  function createReferralLink() {
    return post('referral.create').done(function (res) {
      if (res.STATUS !== 'OK') { alert(res.INFO || 'No se pudo crear el link'); return; }
      $('#referral-url').val(res.invite_url);
      $('#referral-token').text(res.token);
      makeQr(res.invite_url);
    });
  }

  function makeQr(url) {
    var qr = 'https://api.qrserver.com/v1/create-qr-code/?size=190x190&data=' + encodeURIComponent(url);
    $('#referral-qr').html('<img alt="QR de invitacion" src="' + qr + '" style="max-width:190px;border-radius:12px;background:white;padding:8px">');
  }

  function copyReferralLink() {
    var url = $('#referral-url').val();
    if (!url) return;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function () { alert('Link copiado'); });
    } else {
      $('#referral-url').select();
      document.execCommand('copy');
      alert('Link copiado');
    }
  }

  function shareReferralLink() {
    var url = $('#referral-url').val();
    if (!url) return;
    var text = 'Entra al Juego de Caballos con mi link: ' + url;
    if (navigator.share) {
      navigator.share({ title: 'Invitacion Juega123', text: text, url: url });
    } else {
      window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
    }
  }

  function renderLeaderboard(rows) {
    var html = '<ol class="pool-ranking">';
    if (!rows || !rows.length) {
      html += '<li>No hay participantes todavia.</li>';
    } else {
      rows.forEach(function (r) {
        html += '<li><b>' + (r.alias || ('Usuario #' + r.id_user)) + '</b> — ' + r.score_count + ' compras validas</li>';
      });
    }
    html += '</ol>';
    $('#pool-leaderboard').html(html);
  }

  function renderGift(wallet) {
    if (!wallet) return;
    $('#gift-locked').text(wallet.locked_balance || '0.00');
    $('#gift-progress').text((wallet.wagering_progress || '0.00') + ' / ' + (wallet.wagering_required || '0.00'));
    $('#gift-remaining').text(wallet.remaining_to_release || '0.00');
  }

  function loadPool() {
    return post('pool.current').done(function (res) {
      if (res.STATUS !== 'OK') { console.warn(res); return; }
      var round = res.round || {};
      $('#pool-round-code').text(round.round_code || '-');
      $('#pool-amount').text(round.pool_amount || '0.00');
      $('#pool-close-at').text(round.scheduled_close_at || '-');
      renderLeaderboard(res.leaderboard || []);
      renderGift(res.gift_wallet);
    });
  }

  function closeDueRounds() {
    return post('pool.close_due', { limit: 5 }).always(loadPool);
  }

  window.InvitationPoolPWA = {
    detectInvite: detectInvite,
    createReferralLink: createReferralLink,
    copyReferralLink: copyReferralLink,
    shareReferralLink: shareReferralLink,
    loadPool: loadPool,
    closeDueRounds: closeDueRounds
  };

  $(function () {
    detectInvite();
    $(document).on('click', '#btn-create-referral', createReferralLink);
    $(document).on('click', '#btn-copy-referral', copyReferralLink);
    $(document).on('click', '#btn-share-referral', shareReferralLink);
    $(document).on('click', '#btn-refresh-pool', loadPool);
    $(document).on('click', '#btn-close-pool', closeDueRounds);
    if ($('#pool-leaderboard').length) {
      createReferralLink().always(loadPool);
      setInterval(loadPool, 15000);
    }
  });
})(window, jQuery);
