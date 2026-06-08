<?php
/************************************************
 * HorseEvent
 * Juego de Caballos actualizado:
 * - evento unico demo/real
 * - commit-reveal
 * - contabilidad demo separada
 * - auditoria por hashes lista para blockchain
 ************************************************/
if (!defined('ROOT')){
    define('ROOT','./include/');
}

class HorseEvent {
    private $db;
    private $gameCode = 'horse';
    private $gameId = 3;
    private $audit;

    public function __construct() {
        require_once(ROOT.'class/dbconfig.php');
        require_once(ROOT.'class/time.php');
        require_once(ROOT.'class/transaction.php');
        require_once(ROOT.'class/hash_audit.php');
        $this->db = new DB();
        $this->audit = new HashAudit();
        $this->gameId = $this->resolveGameId();
    }

    private function resolveGameId() {
        $data = $this->db->select('SELECT id_game FROM gms_game WHERE cd_game = ? LIMIT 1', array($this->gameCode), array('%s'));
        if ($data['count'] == 1) {
            return (int)$data[0]->id_game;
        }
        return 3;
    }

    private function nextPlay() {
        $roll = new ROLL();
        return (int)$roll->nextPlay($this->gameCode);
    }

    private function bytes32($text) {
        return '0x'.hash('sha256', $text);
    }

    private function randomSecret() {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        }
        return hash('sha256', uniqid(mt_rand(), true).microtime(true));
    }

    private function mapEvent($row) {
        if (!$row) { return null; }
        return array(
            'id_event' => (int)$row->id_event,
            'event_code' => $row->event_code,
            'nm_play' => (int)$row->nm_play,
            'status' => $row->status,
            'commit_hash' => $row->commit_hash,
            'server_seed_hash' => $row->server_seed_hash,
            'result' => array(
                (int)$row->result_one,
                (int)$row->result_two,
                (int)$row->result_three,
                (int)$row->result_four,
                (int)$row->result_five,
                (int)$row->result_six
            ),
            'result_hash' => $row->result_hash,
            'blockchain_status' => $row->blockchain_status,
            'blockchain_tx_hash' => $row->blockchain_tx_hash,
            'created_at' => $row->created_at,
            'revealed_at' => $row->revealed_at
        );
    }

    public function currentEvent() {
        $nmPlay = $this->nextPlay();
        return $this->ensureEvent($nmPlay);
    }

    public function ensureEvent($nmPlay) {
        $data = $this->db->select('SELECT * FROM gms_horse_events WHERE nm_play = ? LIMIT 1', array((int)$nmPlay), array('%i'));
        if ($data['count'] == 1) {
            return $this->mapEvent($data[0]);
        }

        $eventCode = 'HORSE-'.$nmPlay;
        $seed = $this->randomSecret();
        $seedHash = $this->bytes32($seed);
        $commitHash = $this->bytes32($seed.'|'.$eventCode.'|'.$nmPlay);

        $id = $this->db->insert('gms_horse_events', array(
            '`event_code`' => $eventCode,
            '`nm_play`' => (int)$nmPlay,
            '`status`' => 'open',
            '`commit_hash`' => $commitHash,
            '`server_seed_hash`' => $seedHash,
            '`server_seed_secret`' => $seed,
            '`source`' => 'server_prng'
        ), array('%s','%i','%s','%s','%s','%s','%s'));

        if (!$id) { throw new Exception('No se pudo crear evento de caballos'); }

        $this->audit->append('horse_event', $id, 'event.created', array(
            'event_code' => $eventCode,
            'nm_play' => (int)$nmPlay,
            'commit_hash' => $commitHash,
            'server_seed_hash' => $seedHash,
            'source' => 'server_prng'
        ), $id, null);

        $data = $this->db->select('SELECT * FROM gms_horse_events WHERE id_event = ? LIMIT 1', array((int)$id), array('%i'));
        return $this->mapEvent($data[0]);
    }

    public function deterministicResult($serverSeed, $eventCode) {
        $horses = array(1,2,3,4,5,6);
        for ($i = count($horses) - 1; $i > 0; $i--) {
            $hash = hash_hmac('sha256', $eventCode.'|'.$i, $serverSeed);
            $num = hexdec(substr($hash, 0, 8));
            $j = $num % ($i + 1);
            $tmp = $horses[$i];
            $horses[$i] = $horses[$j];
            $horses[$j] = $tmp;
        }
        return $horses;
    }

    public function revealEvent($idEvent = null) {
        if ($idEvent === null || (int)$idEvent === 0) {
            $event = $this->currentEvent();
            $idEvent = $event['id_event'];
        }
        $data = $this->db->select('SELECT * FROM gms_horse_events WHERE id_event = ? LIMIT 1', array((int)$idEvent), array('%i'));
        if ($data['count'] != 1) { throw new Exception('Evento no encontrado'); }
        $row = $data[0];

        if ($row->status === 'revealed' || $row->status === 'settled') {
            $this->settleDemo((int)$row->id_event);
            return $this->mapEvent($row);
        }

        $result = $this->deterministicResult($row->server_seed_secret, $row->event_code);
        $resultHash = $this->bytes32(json_encode($result).'|'.$row->commit_hash);
        $now = date('Y-m-d H:i:s');

        // Insertar tambien en la tabla legacy para que el historial viejo pueda leer el resultado.
        $legacyWonId = $this->db->insert('`gms_won_horse`', array(
            '`nm_one`' => $result[0],
            '`nm_two`' => $result[1],
            '`nm_three`' => $result[2],
            '`nm_four`' => $result[3],
            '`nm_five`' => $result[4],
            '`nm_six`' => $result[5],
            '`entry_date`' => $now
        ), array('%i','%i','%i','%i','%i','%i','%s'));

        $this->db->update('gms_horse_events', array(
            '`status`' => 'revealed',
            '`result_one`' => $result[0],
            '`result_two`' => $result[1],
            '`result_three`' => $result[2],
            '`result_four`' => $result[3],
            '`result_five`' => $result[4],
            '`result_six`' => $result[5],
            '`result_hash`' => $resultHash,
            '`revealed_at`' => $now,
            '`closed_at`' => $now
        ), array('%s','%i','%i','%i','%i','%i','%i','%s','%s','%s'), array('`id_event`' => (int)$idEvent), array('%i'));

        $audit = $this->audit->append('horse_event', (int)$idEvent, 'event.revealed', array(
            'event_code' => $row->event_code,
            'nm_play' => (int)$row->nm_play,
            'legacy_won_id' => (int)$legacyWonId,
            'commit_hash' => $row->commit_hash,
            'server_seed_hash' => $row->server_seed_hash,
            'server_seed_reveal' => $row->server_seed_secret,
            'result' => $result,
            'result_hash' => $resultHash
        ), (int)$idEvent, null);

        $this->settleDemo((int)$idEvent);

        $data = $this->db->select('SELECT * FROM gms_horse_events WHERE id_event = ? LIMIT 1', array((int)$idEvent), array('%i'));
        $mapped = $this->mapEvent($data[0]);
        $mapped['audit'] = $audit;
        return $mapped;
    }

    private function getDemoBalance($idUser) {
        $data = $this->db->select('SELECT balance FROM gms_demo_wallet WHERE id_user = ? LIMIT 1', array((int)$idUser), array('%i'));
        if ($data['count'] == 1) {
            return (float)$data[0]->balance;
        }
        $this->db->insert('gms_demo_wallet', array('`id_user`' => (int)$idUser, '`balance`' => '5000.00'), array('%i','%s'));
        return 5000.00;
    }

    private function setDemoBalance($idUser, $balance) {
        $ok = $this->db->update('gms_demo_wallet', array('`balance`' => number_format($balance, 2, '.', ''), '`updated_at`' => date('Y-m-d H:i:s')), array('%s','%s'), array('`id_user`' => (int)$idUser), array('%i'));
        if (!$ok) {
            // Si update no afecto filas por valor igual, no es error critico.
            $this->getDemoBalance($idUser);
        }
    }

    private function normalizeNumbers($n1, $n2, $n3) {
        $nums = array((int)$n1, (int)$n2, (int)$n3);
        foreach ($nums as $n) {
            if ($n < 0 || $n > 6) { throw new Exception('Caballo no permitido'); }
        }
        if ($nums[0] === 0 && $nums[1] === 0 && $nums[2] === 0) { throw new Exception('Debe seleccionar al menos un caballo'); }
        return $nums;
    }

    public function betDemo($idUser, $amount, $n1, $n2, $n3, $clientUuid = null) {
        $amount = (float)$amount;
        if ($amount <= 0) { throw new Exception('Monto invalido'); }
        $nums = $this->normalizeNumbers($n1, $n2, $n3);
        $event = $this->currentEvent();
        $balanceBefore = $this->getDemoBalance($idUser);
        if ($balanceBefore < $amount) { throw new Exception('Balance demo insuficiente'); }
        $balanceAfter = $balanceBefore - $amount;
        $this->setDemoBalance($idUser, $balanceAfter);

        if ($clientUuid === null || $clientUuid === '') {
            $clientUuid = 'demo-'.uniqid('', true);
        }

        $idBet = $this->db->insert('gms_demo_bets', array(
            '`id_event`' => $event['id_event'],
            '`id_user`' => (int)$idUser,
            '`client_uuid`' => $clientUuid,
            '`amount`' => number_format($amount, 2, '.', ''),
            '`nm_one`' => $nums[0],
            '`nm_two`' => $nums[1],
            '`nm_three`' => $nums[2],
            '`status`' => 'synced'
        ), array('%i','%i','%s','%s','%i','%i','%i','%s'));
        if (!$idBet) { throw new Exception('No se pudo registrar apuesta demo'); }

        $audit = $this->audit->append('horse_demo_bet', (int)$idBet, 'bet.demo.created', array(
            'event' => $event,
            'id_demo_bet' => (int)$idBet,
            'id_user' => (int)$idUser,
            'amount' => number_format($amount, 2, '.', ''),
            'selected_horses' => $nums,
            'balance_before' => number_format($balanceBefore, 2, '.', ''),
            'balance_after' => number_format($balanceAfter, 2, '.', ''),
            'client_uuid' => $clientUuid
        ), $event['id_event'], $idUser);

        return array(
            'STATUS' => 'OK',
            'mode' => 'demo',
            'INFO' => 'Apuesta demo registrada',
            'event' => $event,
            'id_demo_bet' => (int)$idBet,
            'balance' => number_format($balanceAfter, 2, '.', ','),
            'audit' => $audit
        );
    }

    public function betReal($idUser, $amount, $n1, $n2, $n3) {
        $amount = (float)$amount;
        if ($amount <= 0) { throw new Exception('Monto invalido'); }
        $nums = $this->normalizeNumbers($n1, $n2, $n3);
        $event = $this->currentEvent();

        $trans = new TRANSACTION();
        $balanceBefore = (float)$trans->getBalance($idUser, false);
        if ($balanceBefore < $amount) { throw new Exception('Balance real insuficiente'); }

        $idTrans = $this->db->insert('gms_transaction', array(
            '`id_user_reg`' => (int)$idUser,
            '`id_user`' => (int)$idUser,
            '`id_game`' => (int)$this->gameId,
            '`id_values`' => 13,
            '`nm_play`' => (int)$event['nm_play'],
            '`nm_one`' => $nums[0],
            '`nm_two`' => $nums[1],
            '`nm_three`' => $nums[2],
            '`amount`' => number_format($amount * -1, 2, '.', ''),
            '`entry_date`' => date('Y-m-d H:i:s')
        ), array('%i','%i','%i','%i','%i','%i','%i','%i','%s','%s'));

        if (!$idTrans) { throw new Exception('No se pudo registrar apuesta real'); }

        $balanceAfter = $trans->getBalance($idUser, true);
        if (isset($_SESSION['balance'])) { $_SESSION['balance'] = $balanceAfter; }

        $audit = $this->audit->append('horse_real_bet', (int)$idTrans, 'bet.real.created', array(
            'event' => $event,
            'id_trans' => (int)$idTrans,
            'id_user' => (int)$idUser,
            'amount' => number_format($amount, 2, '.', ''),
            'selected_horses' => $nums,
            'balance_before' => number_format($balanceBefore, 2, '.', ''),
            'balance_after' => $balanceAfter
        ), $event['id_event'], $idUser);

        // Pool de Invitacion: cada apuesta real suma progreso para liberar credito de regalo bloqueado.
        $giftProgress = null;
        try {
            require_once(ROOT.'class/invitation_pool.php');
            $pool = new InvitationPool();
            $giftProgress = $pool->registerGiftWager($idUser, $amount, 'gms_transaction', (int)$idTrans);
        } catch(Exception $e) {
            $giftProgress = array('STATUS'=>'WARNING', 'INFO'=>$e->getMessage());
        }

        return array(
            'STATUS' => 'OK',
            'mode' => 'real',
            'INFO' => 'Apuesta real registrada',
            'event' => $event,
            'id_trans' => (int)$idTrans,
            'balance' => $balanceAfter,
            'audit' => $audit,
            'gift_progress' => $giftProgress
        );
    }

    public function settleDemo($idEvent) {
        $dataEvent = $this->db->select('SELECT * FROM gms_horse_events WHERE id_event = ? LIMIT 1', array((int)$idEvent), array('%i'));
        if ($dataEvent['count'] != 1) { return false; }
        $event = $dataEvent[0];
        if ((int)$event->result_one < 1) { return false; }
        $winner = (int)$event->result_one;

        $bets = $this->db->select('SELECT * FROM gms_demo_bets WHERE id_event = ? AND status <> "settled"', array((int)$idEvent), array('%i'));
        for ($i = 0; $i < $bets['count']; $i++) {
            $bet = $bets[$i];
            $selected = array((int)$bet->nm_one, (int)$bet->nm_two, (int)$bet->nm_three);
            $payout = 0.00;
            if (in_array($winner, $selected)) {
                $payout = ((float)$bet->amount) * 1.90;
                $balance = $this->getDemoBalance((int)$bet->id_user);
                $this->setDemoBalance((int)$bet->id_user, $balance + $payout);
            }
            $this->db->update('gms_demo_bets', array(
                '`winner`' => $winner,
                '`payout`' => number_format($payout, 2, '.', ''),
                '`status`' => 'settled',
                '`settled_at`' => date('Y-m-d H:i:s')
            ), array('%i','%s','%s','%s'), array('`id_demo_bet`' => (int)$bet->id_demo_bet), array('%i'));
        }
        return true;
    }
}
?>
