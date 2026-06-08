<?php
/************************************************
 * HashAudit
 * Cadena interna de auditoria compatible con blockchain.
 * Cada evento genera un state_hash bytes32 en formato 0x...
 ************************************************/
if (!defined('ROOT')){
    define('ROOT','./include/');
}

class HashAudit {
    private $db;

    public function __construct() {
        require_once(ROOT.'class/dbconfig.php');
        $this->db = new DB();
    }

    private function normalize($value) {
        if (is_array($value)) {
            ksort($value);
            foreach ($value as $k => $v) {
                $value[$k] = $this->normalize($v);
            }
        }
        return $value;
    }

    public function canonicalJson($payload) {
        $payload = $this->normalize($payload);
        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function lastHash($chainKey = 'horse-main') {
        $data = $this->db->select(
            'SELECT state_hash FROM gms_audit_chain WHERE chain_key = ? ORDER BY id_audit DESC LIMIT 1',
            array($chainKey),
            array('%s')
        );
        if ($data['count'] == 1) {
            return $data[0]->state_hash;
        }
        return '0x0000000000000000000000000000000000000000000000000000000000000000';
    }

    public function makeHash($payload, $previousHash) {
        $json = $this->canonicalJson($payload);
        return '0x'.hash('sha256', $previousHash.'|'.$json);
    }

    public function append($entityType, $entityId, $action, $payload, $idEvent = null, $idUser = null, $chainKey = 'horse-main') {
        $previousHash = $this->lastHash($chainKey);
        $payload['audit_version'] = 'horse-pwa-v1';
        $payload['previous_hash'] = $previousHash;
        $payload['created_at_utc'] = gmdate('c');
        $stateHash = $this->makeHash($payload, $previousHash);
        $payloadJson = $this->canonicalJson($payload);

        $insert = array(
            '`chain_key`' => $chainKey,
            '`entity_type`' => $entityType,
            '`entity_id`' => (int)$entityId,
            '`id_event`' => $idEvent === null ? 0 : (int)$idEvent,
            '`id_user`' => $idUser === null ? 0 : (int)$idUser,
            '`action`' => $action,
            '`payload_json`' => $payloadJson,
            '`previous_hash`' => $previousHash,
            '`state_hash`' => $stateHash,
            '`blockchain_status`' => 'pending'
        );
        $id = $this->db->insert('gms_audit_chain', $insert, array('%s','%s','%i','%i','%i','%s','%s','%s','%s','%s'));
        if (!$id) {
            throw new Exception('No se pudo registrar auditoria hash');
        }
        return array(
            'id_audit' => (int)$id,
            'previous_hash' => $previousHash,
            'state_hash' => $stateHash,
            'payload' => $payload
        );
    }

    public function pending($limit = 20) {
        $limit = max(1, min(100, (int)$limit));
        $data = $this->db->select(
            'SELECT id_audit, chain_key, entity_type, entity_id, IFNULL(id_event,0) AS id_event, IFNULL(id_user,0) AS id_user, action, payload_json, previous_hash, state_hash, created_at FROM gms_audit_chain WHERE blockchain_status = "pending" ORDER BY id_audit ASC LIMIT '.$limit,
            '',
            ''
        );
        $rows = array();
        for ($i = 0; $i < $data['count']; $i++) {
            $rows[] = $data[$i];
        }
        return $rows;
    }

    public function markTx($idAudit, $txHash, $status = 'submitted', $errorMessage = null) {
        $fields = array(
            '`blockchain_status`' => $status,
            '`blockchain_tx_hash`' => $txHash,
            '`submitted_at`' => date('Y-m-d H:i:s')
        );
        $formats = array('%s','%s','%s');
        if ($status === 'confirmed') {
            $fields['`confirmed_at`'] = date('Y-m-d H:i:s');
            $formats[] = '%s';
        }
        if ($errorMessage !== null) {
            $fields['`error_message`'] = $errorMessage;
            $formats[] = '%s';
        }
        return $this->db->update('gms_audit_chain', $fields, $formats, array('`id_audit`' => (int)$idAudit), array('%i'));
    }
}
?>
