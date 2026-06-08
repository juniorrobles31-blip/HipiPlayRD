-- ============================================================
-- MVP Juego de Caballos PWA + Blockchain Ready
-- Autor: ChatGPT
-- Uso: importar DESPUES de los SQL originales del proyecto juega123.
-- ============================================================

-- El codigo legacy usa id_user_reg en inserciones. Si tu tabla no lo tiene, se agrega.
SET @has_id_user_reg := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'gms_transaction'
    AND COLUMN_NAME = 'id_user_reg'
);
SET @sql := IF(@has_id_user_reg = 0,
  'ALTER TABLE gms_transaction ADD COLUMN id_user_reg BIGINT(30) NULL AFTER id_trans',
  'SELECT "id_user_reg already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS gms_horse_events (
  id_event BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  event_code VARCHAR(64) NOT NULL,
  nm_play BIGINT UNSIGNED NOT NULL,
  status ENUM('open','closed','revealed','settled','cancelled') NOT NULL DEFAULT 'open',
  commit_hash CHAR(66) NOT NULL,
  server_seed_hash CHAR(66) NOT NULL,
  server_seed_secret VARCHAR(128) NOT NULL,
  result_one TINYINT UNSIGNED NULL,
  result_two TINYINT UNSIGNED NULL,
  result_three TINYINT UNSIGNED NULL,
  result_four TINYINT UNSIGNED NULL,
  result_five TINYINT UNSIGNED NULL,
  result_six TINYINT UNSIGNED NULL,
  result_hash CHAR(66) NULL,
  source VARCHAR(32) NOT NULL DEFAULT 'server_prng',
  blockchain_status ENUM('pending','submitted','confirmed','error') NOT NULL DEFAULT 'pending',
  blockchain_tx_hash VARCHAR(128) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  closed_at DATETIME NULL,
  revealed_at DATETIME NULL,
  settled_at DATETIME NULL,
  PRIMARY KEY (id_event),
  UNIQUE KEY uq_horse_event_code (event_code),
  UNIQUE KEY uq_horse_nm_play (nm_play),
  KEY idx_horse_status (status),
  KEY idx_horse_blockchain_status (blockchain_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS gms_demo_wallet (
  id_wallet BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user BIGINT(30) NOT NULL,
  balance DECIMAL(10,2) NOT NULL DEFAULT 5000.00,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_wallet),
  UNIQUE KEY uq_demo_wallet_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS gms_demo_bets (
  id_demo_bet BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_event BIGINT UNSIGNED NOT NULL,
  id_user BIGINT(30) NOT NULL,
  client_uuid VARCHAR(80) NULL,
  amount DECIMAL(10,2) NOT NULL,
  nm_one TINYINT NOT NULL DEFAULT 0,
  nm_two TINYINT NOT NULL DEFAULT 0,
  nm_three TINYINT NOT NULL DEFAULT 0,
  winner TINYINT NULL,
  payout DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('pending','synced','settled','cancelled') NOT NULL DEFAULT 'synced',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  settled_at DATETIME NULL,
  PRIMARY KEY (id_demo_bet),
  UNIQUE KEY uq_demo_client_uuid (client_uuid),
  KEY idx_demo_event (id_event),
  KEY idx_demo_user (id_user),
  CONSTRAINT fk_demo_bets_event FOREIGN KEY (id_event) REFERENCES gms_horse_events(id_event)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS gms_audit_chain (
  id_audit BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chain_key VARCHAR(64) NOT NULL DEFAULT 'horse-main',
  entity_type VARCHAR(64) NOT NULL,
  entity_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  id_event BIGINT UNSIGNED NULL,
  id_user BIGINT(30) NULL,
  action VARCHAR(64) NOT NULL,
  payload_json TEXT NOT NULL,
  previous_hash CHAR(66) NOT NULL,
  state_hash CHAR(66) NOT NULL,
  blockchain_status ENUM('pending','submitted','confirmed','error') NOT NULL DEFAULT 'pending',
  blockchain_tx_hash VARCHAR(128) NULL,
  error_message TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  submitted_at DATETIME NULL,
  confirmed_at DATETIME NULL,
  PRIMARY KEY (id_audit),
  UNIQUE KEY uq_audit_state_hash (state_hash),
  KEY idx_audit_pending (blockchain_status, id_audit),
  KEY idx_audit_event (id_event),
  KEY idx_audit_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS gms_referral_links (
  id_referral BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  inviter_user_id BIGINT(30) NOT NULL,
  token VARCHAR(128) NOT NULL,
  invite_url TEXT NOT NULL,
  status ENUM('active','used','expired','revoked') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  used_at DATETIME NULL,
  PRIMARY KEY (id_referral),
  UNIQUE KEY uq_referral_token (token),
  KEY idx_referral_inviter (inviter_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS gms_referral_claims (
  id_claim BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  referral_token VARCHAR(128) NOT NULL,
  inviter_user_id BIGINT(30) NOT NULL,
  invited_user_id BIGINT(30) NULL,
  invited_phone VARCHAR(30) NULL,
  ip_address VARCHAR(64) NULL,
  user_agent TEXT NULL,
  status ENUM('opened','registered','rewarded','cancelled') NOT NULL DEFAULT 'opened',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  registered_at DATETIME NULL,
  rewarded_at DATETIME NULL,
  PRIMARY KEY (id_claim),
  KEY idx_claim_token (referral_token),
  KEY idx_claim_inviter (inviter_user_id),
  KEY idx_claim_invited (invited_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
