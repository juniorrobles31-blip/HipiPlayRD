<?php
/************************************************
 * InvitationPool
 * Pool de Invitacion minuto a minuto:
 * - links de referido
 * - contador competitivo acumulado
 * - cierre cada 60 segundos
 * - premio como credito regalo bloqueado
 * - liberacion al duplicar jugando
 * - auditoria hash compatible con blockchain
 ************************************************/
if (!defined('ROOT')){
    define('ROOT','./include/');
}

class InvitationPool {
    private $db;
    private $audit;

    public function __construct() {
        require_once(ROOT.'class/dbconfig.php');
        require_once(ROOT.'class/hash_audit.php');
        $this->db = new DB();
        $this->audit = new HashAudit();
    }

    private function now() { return date('Y-m-d H:i:s'); }

    private function config() {
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_config WHERE id_config = 1 LIMIT 1', '', '');
        if ($data['count'] == 1) { return $data[0]; }
        $this->db->insert('gms_invitation_pool_config', array(
            '`id_config`' => 1,
            '`pool_percentage`' => '5.00',
            '`min_pool_amount`' => '0.00',
            '`min_purchase_amount`' => '1.00',
            '`round_seconds`' => 60,
            '`tie_rule`' => 'first_to_score',
            '`active`' => 1
        ), array('%i','%s','%s','%s','%i','%s','%i'));
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_config WHERE id_config = 1 LIMIT 1', '', '');
        return $data[0];
    }

    private function baseUrl() {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $scheme = $https ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
        $basePath = rtrim(str_replace('system.php', '', dirname($script).'/'), '/');
        if ($basePath === '') { $basePath = ''; }
        return $scheme.'://'.$host.$basePath.'/';
    }

    private function makeToken($idUser) {
        $seed = $idUser.'|'.microtime(true).'|'.mt_rand();
        if (function_exists('random_bytes')) { $seed .= bin2hex(random_bytes(16)); }
        return substr(hash('sha256', $seed), 0, 48);
    }

    private function getLinkByToken($token) {
        $data = $this->db->select('SELECT * FROM gms_referral_links WHERE token = ? AND status = "active" LIMIT 1', array($token), array('%s'));
        if ($data['count'] != 1) { throw new Exception('Token de invitacion no valido o inactivo'); }
        return $data[0];
    }

    public function createLink($idUser, $expiresDays = 30) {
        $idUser = (int)$idUser;
        if ($idUser <= 0) { throw new Exception('Usuario no valido'); }

        $data = $this->db->select('SELECT * FROM gms_referral_links WHERE inviter_user_id = ? AND status = "active" ORDER BY id_referral_link DESC LIMIT 1', array($idUser), array('%i'));
        if ($data['count'] == 1) {
            return array(
                'STATUS' => 'OK',
                'INFO' => 'Link activo existente',
                'token' => $data[0]->token,
                'invite_url' => $data[0]->invite_url
            );
        }

        $token = $this->makeToken($idUser);
        $url = $this->baseUrl().'index.php?page=caballos&invite='.$token;
        $expiresAt = date('Y-m-d H:i:s', strtotime('+'.max(1,(int)$expiresDays).' days'));

        $id = $this->db->insert('gms_referral_links', array(
            '`inviter_user_id`' => $idUser,
            '`token`' => $token,
            '`invite_url`' => $url,
            '`status`' => 'active',
            '`expires_at`' => $expiresAt
        ), array('%i','%s','%s','%s','%s'));
        if (!$id) { throw new Exception('No se pudo crear el link de invitacion'); }

        $this->audit->append('referral_link', (int)$id, 'referral.link.created', array(
            'id_referral_link' => (int)$id,
            'inviter_user_id' => $idUser,
            'token_hash' => '0x'.hash('sha256', $token),
            'expires_at' => $expiresAt
        ), 0, $idUser, 'invitation-pool');

        return array('STATUS'=>'OK','INFO'=>'Link creado','token'=>$token,'invite_url'=>$url);
    }

    public function openInvite($token, $sessionKey = null) {
        $link = $this->getLinkByToken($token);
        if (!empty($link->expires_at) && strtotime($link->expires_at) < time()) { throw new Exception('Token de invitacion expirado'); }

        if ($sessionKey === null || $sessionKey === '') {
            $sessionKey = session_id() ? session_id() : substr(hash('sha256', microtime(true).mt_rand()), 0, 32);
        }
        $_SESSION['invite_token'] = $token;

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $idVisit = $this->db->insert('gms_referral_visits', array(
            '`token`' => $token,
            '`inviter_user_id`' => (int)$link->inviter_user_id,
            '`session_key`' => $sessionKey,
            '`ip_address`' => $ip,
            '`user_agent`' => $ua,
            '`status`' => 'opened'
        ), array('%s','%i','%s','%s','%s','%s'));

        $this->db->update('gms_referral_links', array(
            '`total_opens`' => ((int)$link->total_opens) + 1,
            '`updated_at`' => $this->now()
        ), array('%i','%s'), array('`id_referral_link`' => (int)$link->id_referral_link), array('%i'));

        return array(
            'STATUS' => 'OK',
            'INFO' => 'Invitacion registrada',
            'token' => $token,
            'id_visit' => (int)$idVisit,
            'inviter_user_id' => (int)$link->inviter_user_id
        );
    }

    public function registerInvited($token, $invitedUserId) {
        $link = $this->getLinkByToken($token);
        $invitedUserId = (int)$invitedUserId;
        if ($invitedUserId <= 0) { throw new Exception('Usuario invitado no valido'); }
        if ($invitedUserId == (int)$link->inviter_user_id) { throw new Exception('El usuario no puede invitarse a si mismo'); }

        $this->db->update('gms_referral_visits', array(
            '`invited_user_id`' => $invitedUserId,
            '`status`' => 'registered',
            '`registered_at`' => $this->now()
        ), array('%i','%s','%s'), array('`token`' => $token, '`status`' => 'opened'), array('%s','%s'));

        $_SESSION['invite_token'] = $token;
        return array('STATUS'=>'OK','INFO'=>'Usuario vinculado al referido','token'=>$token,'inviter_user_id'=>(int)$link->inviter_user_id,'invited_user_id'=>$invitedUserId);
    }

    public function confirmPurchase($token, $invitedUserId, $amount, $purchaseReference = null, $source = 'manual') {
        $link = $this->getLinkByToken($token);
        $invitedUserId = (int)$invitedUserId;
        $amount = (float)$amount;
        $cfg = $this->config();
        if ($invitedUserId <= 0) { throw new Exception('Usuario comprador no valido'); }
        if ($amount < (float)$cfg->min_purchase_amount) { throw new Exception('Compra menor al minimo permitido'); }
        if ($invitedUserId == (int)$link->inviter_user_id) { throw new Exception('La compra propia no cuenta como referido'); }
        if ($purchaseReference === null || $purchaseReference === '') {
            $purchaseReference = $source.'-'.$invitedUserId.'-'.time().'-'.mt_rand(1000,9999);
        }

        $existing = $this->db->select('SELECT id_purchase FROM gms_referral_purchases WHERE purchase_reference = ? LIMIT 1', array($purchaseReference), array('%s'));
        if ($existing['count'] == 1) {
            return array('STATUS'=>'OK','INFO'=>'Compra ya registrada','duplicated'=>true,'id_purchase'=>(int)$existing[0]->id_purchase);
        }

        $idPurchase = $this->db->insert('gms_referral_purchases', array(
            '`token`' => $token,
            '`inviter_user_id`' => (int)$link->inviter_user_id,
            '`invited_user_id`' => $invitedUserId,
            '`amount`' => number_format($amount, 2, '.', ''),
            '`purchase_reference`' => $purchaseReference,
            '`source`' => $source
        ), array('%s','%i','%i','%s','%s','%s'));
        if (!$idPurchase) { throw new Exception('No se pudo registrar compra referida'); }

        $this->db->update('gms_referral_links', array(
            '`total_purchases`' => ((int)$link->total_purchases) + 1,
            '`total_purchase_amount`' => number_format(((float)$link->total_purchase_amount) + $amount, 2, '.', ''),
            '`updated_at`' => $this->now()
        ), array('%i','%s','%s'), array('`id_referral_link`' => (int)$link->id_referral_link), array('%i'));

        $this->db->update('gms_referral_visits', array(
            '`invited_user_id`' => $invitedUserId,
            '`status`' => 'purchased',
            '`purchased_at`' => $this->now()
        ), array('%i','%s','%s'), array('`token`' => $token), array('%s'));

        $this->incrementCounter((int)$link->inviter_user_id, $amount);

        $audit = $this->audit->append('referral_purchase', (int)$idPurchase, 'referral.purchase.confirmed', array(
            'id_purchase' => (int)$idPurchase,
            'inviter_user_id' => (int)$link->inviter_user_id,
            'invited_user_id' => $invitedUserId,
            'amount' => number_format($amount, 2, '.', ''),
            'purchase_reference' => $purchaseReference,
            'source' => $source,
            'token_hash' => '0x'.hash('sha256', $token)
        ), 0, (int)$link->inviter_user_id, 'invitation-pool');

        return array('STATUS'=>'OK','INFO'=>'Compra referida registrada','id_purchase'=>(int)$idPurchase,'audit'=>$audit);
    }

    private function incrementCounter($idUser, $amount) {
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_counters WHERE id_user = ? LIMIT 1', array((int)$idUser), array('%i'));
        if ($data['count'] == 1) {
            $row = $data[0];
            $firstScoreAt = empty($row->first_score_at) || (int)$row->score_count === 0 ? $this->now() : $row->first_score_at;
            $this->db->update('gms_invitation_pool_counters', array(
                '`score_count`' => ((int)$row->score_count) + 1,
                '`total_purchases`' => ((int)$row->total_purchases) + 1,
                '`total_purchase_amount`' => number_format(((float)$row->total_purchase_amount) + (float)$amount, 2, '.', ''),
                '`first_score_at`' => $firstScoreAt,
                '`last_purchase_at`' => $this->now(),
                '`updated_at`' => $this->now()
            ), array('%i','%i','%s','%s','%s','%s'), array('`id_user`' => (int)$idUser), array('%i'));
        } else {
            $this->db->insert('gms_invitation_pool_counters', array(
                '`id_user`' => (int)$idUser,
                '`score_count`' => 1,
                '`total_purchases`' => 1,
                '`total_purchase_amount`' => number_format((float)$amount, 2, '.', ''),
                '`first_score_at`' => $this->now(),
                '`last_purchase_at`' => $this->now(),
                '`updated_at`' => $this->now()
            ), array('%i','%i','%i','%s','%s','%s','%s'));
        }
    }

    private function roundWindow($timestamp = null) {
        $timestamp = $timestamp === null ? time() : (int)$timestamp;
        $startTs = floor($timestamp / 60) * 60;
        return array(
            'round_code' => date('YmdHi', $startTs),
            'started_at' => date('Y-m-d H:i:s', $startTs),
            'scheduled_close_at' => date('Y-m-d H:i:s', $startTs + 60)
        );
    }

    public function ensureCurrentRound() {
        $cfg = $this->config();
        $w = $this->roundWindow();
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_rounds WHERE round_code = ? LIMIT 1', array($w['round_code']), array('%s'));
        if ($data['count'] == 1) { return $this->mapRound($data[0]); }
        $id = $this->db->insert('gms_invitation_pool_rounds', array(
            '`round_code`' => $w['round_code'],
            '`started_at`' => $w['started_at'],
            '`scheduled_close_at`' => $w['scheduled_close_at'],
            '`pool_percentage`' => number_format((float)$cfg->pool_percentage, 2, '.', ''),
            '`tie_rule`' => $cfg->tie_rule
        ), array('%s','%s','%s','%s','%s'));
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_rounds WHERE id_round = ? LIMIT 1', array((int)$id), array('%i'));
        return $this->mapRound($data[0]);
    }

    private function mapRound($row) {
        return array(
            'id_round' => (int)$row->id_round,
            'round_code' => $row->round_code,
            'started_at' => $row->started_at,
            'scheduled_close_at' => $row->scheduled_close_at,
            'closed_at' => $row->closed_at,
            'gross_profit' => number_format((float)$row->gross_profit, 2, '.', ''),
            'pool_percentage' => number_format((float)$row->pool_percentage, 2, '.', ''),
            'pool_amount' => number_format((float)$row->pool_amount, 2, '.', ''),
            'status' => $row->status,
            'winner_user_id' => $row->winner_user_id === null ? null : (int)$row->winner_user_id,
            'winner_score' => (int)$row->winner_score,
            'tie_rule' => $row->tie_rule,
            'id_audit' => $row->id_audit === null ? null : (int)$row->id_audit
        );
    }

    public function leaderboard($limit = 20) {
        $limit = max(1, min(100, (int)$limit));
        $data = $this->db->select('SELECT C.id_user, C.score_count, C.total_purchases, C.total_purchase_amount, C.first_score_at, C.last_purchase_at, U.alias FROM gms_invitation_pool_counters C LEFT JOIN gms_user U ON U.id_user = C.id_user ORDER BY C.score_count DESC, C.first_score_at ASC, C.last_purchase_at ASC LIMIT '.$limit, '', '');
        $rows = array();
        for ($i=0; $i<$data['count']; $i++) {
            $rows[] = array(
                'id_user' => (int)$data[$i]->id_user,
                'alias' => isset($data[$i]->alias) ? $data[$i]->alias : null,
                'score_count' => (int)$data[$i]->score_count,
                'total_purchases' => (int)$data[$i]->total_purchases,
                'total_purchase_amount' => number_format((float)$data[$i]->total_purchase_amount, 2, '.', ''),
                'first_score_at' => $data[$i]->first_score_at,
                'last_purchase_at' => $data[$i]->last_purchase_at
            );
        }
        return $rows;
    }

    public function current($idUser = null) {
        $round = $this->ensureCurrentRound();
        $leaderboard = $this->leaderboard(20);
        $gift = $idUser ? $this->giftWallet((int)$idUser) : null;
        return array('STATUS'=>'OK','INFO'=>'Pool actual','round'=>$round,'leaderboard'=>$leaderboard,'gift_wallet'=>$gift,'server_time'=>$this->now());
    }

    private function grossProfit($from, $to) {
        $data = $this->db->select('SELECT GREATEST(IFNULL(SUM(amount),0) * -1, 0) AS gross_profit FROM gms_transaction WHERE entry_date >= ? AND entry_date < ? AND id_game > 0', array($from, $to), array('%s','%s'));
        if ($data['count'] == 1) { return (float)$data[0]->gross_profit; }
        return 0.00;
    }

    public function closeDueRounds($limit = 10) {
        $limit = max(1, min(60, (int)$limit));
        $data = $this->db->select('SELECT id_round FROM gms_invitation_pool_rounds WHERE status = "open" AND scheduled_close_at <= NOW() ORDER BY id_round ASC LIMIT '.$limit, '', '');
        $closed = array();
        for ($i=0; $i<$data['count']; $i++) {
            $closed[] = $this->closeRound((int)$data[$i]->id_round);
        }
        // Asegura que exista la ronda actual despues del cierre.
        $this->ensureCurrentRound();
        return array('STATUS'=>'OK','INFO'=>'Rondas procesadas','closed'=>$closed);
    }

    public function closeRound($idRound = null) {
        if ($idRound === null || (int)$idRound === 0) {
            $this->ensureCurrentRound();
            $data = $this->db->select('SELECT id_round FROM gms_invitation_pool_rounds WHERE status = "open" AND scheduled_close_at <= NOW() ORDER BY id_round ASC LIMIT 1', '', '');
            if ($data['count'] != 1) { return array('STATUS'=>'OK','INFO'=>'No hay rondas vencidas'); }
            $idRound = (int)$data[0]->id_round;
        }
        $data = $this->db->select('SELECT * FROM gms_invitation_pool_rounds WHERE id_round = ? LIMIT 1', array((int)$idRound), array('%i'));
        if ($data['count'] != 1) { throw new Exception('Ronda no encontrada'); }
        $round = $data[0];
        if ($round->status !== 'open') { return $this->mapRound($round); }

        $grossProfit = $this->grossProfit($round->started_at, $round->scheduled_close_at);
        $poolAmount = round($grossProfit * ((float)$round->pool_percentage / 100), 2);
        $cfg = $this->config();
        $leaderboard = $this->leaderboard(50);
        $winner = count($leaderboard) > 0 && (int)$leaderboard[0]['score_count'] > 0 ? $leaderboard[0] : null;

        $position = 1;
        foreach ($leaderboard as $entry) {
            $this->db->insert('gms_invitation_pool_entries', array(
                '`id_round`' => (int)$idRound,
                '`id_user`' => (int)$entry['id_user'],
                '`score_count`' => (int)$entry['score_count'],
                '`ranking_position`' => $position,
                '`is_winner`' => ($winner && (int)$entry['id_user'] === (int)$winner['id_user']) ? 1 : 0
            ), array('%i','%i','%i','%i','%i'));
            $position++;
        }

        $status = 'closed';
        $winnerUserId = null;
        $winnerScore = 0;
        $auditPayload = array(
            'id_round' => (int)$idRound,
            'round_code' => $round->round_code,
            'started_at' => $round->started_at,
            'scheduled_close_at' => $round->scheduled_close_at,
            'gross_profit' => number_format($grossProfit, 2, '.', ''),
            'pool_percentage' => number_format((float)$round->pool_percentage, 2, '.', ''),
            'pool_amount' => number_format($poolAmount, 2, '.', ''),
            'tie_rule' => $round->tie_rule,
            'leaderboard_snapshot' => $leaderboard
        );

        if (!$winner || $poolAmount < (float)$cfg->min_pool_amount || $poolAmount <= 0) {
            $status = 'no_winner';
            $auditPayload['winner'] = null;
            $auditPayload['reason'] = !$winner ? 'no_competitors' : 'pool_without_amount';
        } else {
            $winnerUserId = (int)$winner['id_user'];
            $winnerScore = (int)$winner['score_count'];
            $gift = $this->awardGift($winnerUserId, $poolAmount, (int)$idRound, $winnerScore);
            $this->resetWinnerCounter($winnerUserId);
            $auditPayload['winner'] = array(
                'id_user' => $winnerUserId,
                'score_count' => $winnerScore,
                'gift_locked' => number_format($poolAmount, 2, '.', ''),
                'wagering_required' => number_format($poolAmount * 2, 2, '.', ''),
                'gift_wallet' => $gift
            );
        }

        $audit = $this->audit->append('invitation_pool_round', (int)$idRound, 'pool.round.closed', $auditPayload, 0, $winnerUserId, 'invitation-pool');

        $this->db->update('gms_invitation_pool_rounds', array(
            '`closed_at`' => $this->now(),
            '`gross_profit`' => number_format($grossProfit, 2, '.', ''),
            '`pool_amount`' => number_format($poolAmount, 2, '.', ''),
            '`status`' => $status,
            '`winner_user_id`' => $winnerUserId,
            '`winner_score`' => $winnerScore,
            '`id_audit`' => (int)$audit['id_audit']
        ), array('%s','%s','%s','%s','%i','%i','%i'), array('`id_round`' => (int)$idRound), array('%i'));

        $updated = $this->db->select('SELECT * FROM gms_invitation_pool_rounds WHERE id_round = ? LIMIT 1', array((int)$idRound), array('%i'));
        $mapped = $this->mapRound($updated[0]);
        $mapped['audit'] = $audit;
        return $mapped;
    }

    private function resetWinnerCounter($idUser) {
        return $this->db->update('gms_invitation_pool_counters', array(
            '`score_count`' => 0,
            '`first_score_at`' => null,
            '`updated_at`' => $this->now()
        ), array('%i','%s','%s'), array('`id_user`' => (int)$idUser), array('%i'));
    }

    public function giftWallet($idUser) {
        $idUser = (int)$idUser;
        $data = $this->db->select('SELECT * FROM gms_gift_wallet WHERE id_user = ? LIMIT 1', array($idUser), array('%i'));
        if ($data['count'] == 1) {
            return array(
                'id_user' => $idUser,
                'locked_balance' => number_format((float)$data[0]->locked_balance, 2, '.', ''),
                'released_balance' => number_format((float)$data[0]->released_balance, 2, '.', ''),
                'wagering_required' => number_format((float)$data[0]->wagering_required, 2, '.', ''),
                'wagering_progress' => number_format((float)$data[0]->wagering_progress, 2, '.', ''),
                'remaining_to_release' => number_format(max(0, (float)$data[0]->wagering_required - (float)$data[0]->wagering_progress), 2, '.', '')
            );
        }
        $this->db->insert('gms_gift_wallet', array('`id_user`'=>$idUser), array('%i'));
        return array('id_user'=>$idUser,'locked_balance'=>'0.00','released_balance'=>'0.00','wagering_required'=>'0.00','wagering_progress'=>'0.00','remaining_to_release'=>'0.00');
    }

    private function awardGift($idUser, $amount, $idRound, $winnerScore) {
        $wallet = $this->giftWallet($idUser);
        $locked = (float)$wallet['locked_balance'] + (float)$amount;
        $required = (float)$wallet['wagering_required'] + ((float)$amount * 2);
        $this->db->update('gms_gift_wallet', array(
            '`locked_balance`' => number_format($locked, 2, '.', ''),
            '`wagering_required`' => number_format($required, 2, '.', ''),
            '`updated_at`' => $this->now()
        ), array('%s','%s','%s'), array('`id_user`'=>(int)$idUser), array('%i'));

        $this->db->insert('gms_gift_ledger', array(
            '`id_user`' => (int)$idUser,
            '`id_round`' => (int)$idRound,
            '`type`' => 'credit_locked',
            '`amount`' => number_format((float)$amount, 2, '.', ''),
            '`wagering_required_delta`' => number_format((float)$amount * 2, 2, '.', ''),
            '`wagering_progress_delta`' => '0.00',
            '`status`' => 'locked',
            '`reference_table`' => 'gms_invitation_pool_rounds',
            '`reference_id`' => (int)$idRound
        ), array('%i','%i','%s','%s','%s','%s','%s','%s','%i'));

        return $this->giftWallet($idUser);
    }

    public function registerGiftWager($idUser, $amount, $referenceTable = 'gms_transaction', $referenceId = 0) {
        $idUser = (int)$idUser;
        $amount = abs((float)$amount);
        if ($idUser <= 0 || $amount <= 0) { return array('STATUS'=>'OK','INFO'=>'Sin progreso de regalo'); }
        $wallet = $this->giftWallet($idUser);
        if ((float)$wallet['locked_balance'] <= 0 || (float)$wallet['wagering_required'] <= (float)$wallet['wagering_progress']) {
            return array('STATUS'=>'OK','INFO'=>'No hay regalo bloqueado pendiente','gift_wallet'=>$wallet);
        }

        if ((int)$referenceId > 0) {
            $existing = $this->db->select('SELECT id_gift_ledger FROM gms_gift_ledger WHERE id_user = ? AND type = "wager_progress" AND reference_table = ? AND reference_id = ? LIMIT 1', array($idUser, $referenceTable, (int)$referenceId), array('%i','%s','%i'));
            if ($existing['count'] == 1) { return array('STATUS'=>'OK','INFO'=>'Progreso ya registrado','gift_wallet'=>$wallet); }
        }

        $newProgress = min((float)$wallet['wagering_required'], (float)$wallet['wagering_progress'] + $amount);
        $delta = $newProgress - (float)$wallet['wagering_progress'];
        $this->db->update('gms_gift_wallet', array(
            '`wagering_progress`' => number_format($newProgress, 2, '.', ''),
            '`updated_at`' => $this->now()
        ), array('%s','%s'), array('`id_user`'=>$idUser), array('%i'));

        $this->db->insert('gms_gift_ledger', array(
            '`id_user`' => $idUser,
            '`type`' => 'wager_progress',
            '`amount`' => '0.00',
            '`wagering_required_delta`' => '0.00',
            '`wagering_progress_delta`' => number_format($delta, 2, '.', ''),
            '`status`' => 'processed',
            '`reference_table`' => $referenceTable,
            '`reference_id`' => (int)$referenceId
        ), array('%i','%s','%s','%s','%s','%s','%s','%i'));

        $wallet = $this->giftWallet($idUser);
        if ((float)$wallet['locked_balance'] > 0 && (float)$wallet['wagering_progress'] >= (float)$wallet['wagering_required']) {
            $wallet = $this->releaseGift($idUser);
        }
        return array('STATUS'=>'OK','INFO'=>'Progreso de regalo actualizado','gift_wallet'=>$wallet);
    }

    public function releaseGift($idUser) {
        require_once(ROOT.'class/transaction.php');
        $idUser = (int)$idUser;
        $wallet = $this->giftWallet($idUser);
        $locked = (float)$wallet['locked_balance'];
        if ($locked <= 0) { return $wallet; }
        if ((float)$wallet['wagering_progress'] < (float)$wallet['wagering_required']) { throw new Exception('El regalo aun no cumple el requisito de duplicar jugando'); }

        // Se acredita al balance real como recarga interna usando id_values=9.
        $idTrans = $this->db->insert('gms_transaction', array(
            '`id_user_reg`' => $idUser,
            '`id_user`' => $idUser,
            '`id_game`' => 0,
            '`id_values`' => 9,
            '`nm_play`' => 0,
            '`nm_one`' => 0,
            '`amount`' => number_format($locked, 2, '.', ''),
            '`entry_date`' => $this->now()
        ), array('%i','%i','%i','%i','%i','%i','%s','%s'));

        $released = (float)$wallet['released_balance'] + $locked;
        $this->db->update('gms_gift_wallet', array(
            '`locked_balance`' => '0.00',
            '`released_balance`' => number_format($released, 2, '.', ''),
            '`wagering_required`' => '0.00',
            '`wagering_progress`' => '0.00',
            '`updated_at`' => $this->now()
        ), array('%s','%s','%s','%s','%s'), array('`id_user`'=>$idUser), array('%i'));

        $this->db->insert('gms_gift_ledger', array(
            '`id_user`' => $idUser,
            '`type`' => 'release_to_balance',
            '`amount`' => number_format($locked, 2, '.', ''),
            '`status`' => 'released',
            '`reference_table`' => 'gms_transaction',
            '`reference_id`' => (int)$idTrans
        ), array('%i','%s','%s','%s','%s','%i'));

        $audit = $this->audit->append('gift_wallet', $idUser, 'gift.released', array(
            'id_user' => $idUser,
            'released_amount' => number_format($locked, 2, '.', ''),
            'credit_transaction_id' => (int)$idTrans
        ), 0, $idUser, 'invitation-pool');

        $wallet = $this->giftWallet($idUser);
        $wallet['release_transaction_id'] = (int)$idTrans;
        $wallet['audit'] = $audit;
        return $wallet;
    }

    public function setConfig($percentage, $minPoolAmount = null, $minPurchaseAmount = null, $tieRule = null) {
        $percentage = max(1, min(10, (float)$percentage));
        $fields = array('`pool_percentage`'=>number_format($percentage,2,'.',''), '`updated_at`'=>$this->now());
        $formats = array('%s','%s');
        if ($minPoolAmount !== null) { $fields['`min_pool_amount`'] = number_format((float)$minPoolAmount,2,'.',''); $formats[] = '%s'; }
        if ($minPurchaseAmount !== null) { $fields['`min_purchase_amount`'] = number_format((float)$minPurchaseAmount,2,'.',''); $formats[] = '%s'; }
        if ($tieRule !== null && in_array($tieRule, array('first_to_score','split'))) { $fields['`tie_rule`'] = $tieRule; $formats[] = '%s'; }
        $this->db->update('gms_invitation_pool_config', $fields, $formats, array('`id_config`'=>1), array('%i'));
        return array('STATUS'=>'OK','INFO'=>'Configuracion actualizada','config'=>$this->config());
    }
}
?>
