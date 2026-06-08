<?php
if (!isset($_GET["iframe"])){
    include("./include/gui/game.ui.php");
}
?>
<div data-role="page" id="invitation-pool-page" data-theme="b">
  <div data-role="header" data-position="fixed">
    <h1>Pool de Invitación</h1>
    <a href="index.php?page=caballos" data-icon="back" data-ajax="false">Caballos</a>
  </div>

  <div role="main" class="ui-content">
    <div class="ui-body ui-body-a ui-corner-all" style="margin-bottom:12px">
      <h3>Mi link de invitación</h3>
      <p>Comparte este link. Cada compra válida suma +1 a tu contador competitivo.</p>
      <input type="text" id="referral-url" readonly placeholder="Generando link...">
      <small>Token: <span id="referral-token">-</span></small>
      <div style="margin-top:10px">
        <button id="btn-create-referral" class="ui-btn ui-btn-inline ui-corner-all">Generar / actualizar</button>
        <button id="btn-copy-referral" class="ui-btn ui-btn-inline ui-corner-all">Copiar</button>
        <button id="btn-share-referral" class="ui-btn ui-btn-inline ui-corner-all">Compartir</button>
      </div>
      <div id="referral-qr" style="margin-top:12px"></div>
    </div>

    <div class="ui-body ui-body-a ui-corner-all" style="margin-bottom:12px">
      <h3>Ronda actual</h3>
      <p><b>Ronda:</b> <span id="pool-round-code">-</span></p>
      <p><b>Pool estimado:</b> RD$ <span id="pool-amount">0.00</span></p>
      <p><b>Cierre programado:</b> <span id="pool-close-at">-</span></p>
      <button id="btn-refresh-pool" class="ui-btn ui-btn-inline ui-corner-all">Actualizar</button>
      <button id="btn-close-pool" class="ui-btn ui-btn-inline ui-corner-all">Cerrar rondas vencidas</button>
    </div>

    <div class="ui-body ui-body-a ui-corner-all" style="margin-bottom:12px">
      <h3>Ranking competitivo</h3>
      <div id="pool-leaderboard">Cargando...</div>
    </div>

    <div class="ui-body ui-body-a ui-corner-all">
      <h3>Crédito de regalo bloqueado</h3>
      <p><b>Bloqueado:</b> RD$ <span id="gift-locked">0.00</span></p>
      <p><b>Progreso para liberar:</b> RD$ <span id="gift-progress">0.00 / 0.00</span></p>
      <p><b>Restante:</b> RD$ <span id="gift-remaining">0.00</span></p>
      <small>Cuando el usuario duplique jugando el regalo, el sistema lo libera al balance real mediante una transacción interna.</small>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/invitation-pool.js"></script>
