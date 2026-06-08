-- ============================================================
-- Modulo Pool de Invitacion minuto a minuto + Gift Wallet
-- Uso: importar DESPUES de 01_caballos_blockchain_ready.sql
-- ============================================================

CREATE TABLE IF NOT EXISTS `gms_referral_links` (
  `id_referral_link` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inviter_user_id` BIGINT(30) NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `invite_url` TEXT NOT NULL,
  `status` ENUM('active','expired','revoked') NOT NULL DEFAULT 'active',
  `total_opens` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_purchases` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_purchase_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id_referral_link`),
  UNIQUE KEY `uq_referral_token` (`token`),
  KEY `idx_referral_inviter` (`inviter_user_id`),
  KEY `idx_referral_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_referral_visits` (
  `id_visit` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(128) NOT NULL,
  `inviter_user_id` BIGINT(30) NOT NULL,
  `invited_user_id` BIGINT(30) NULL,
  `session_key` VARCHAR(128) NULL,
  `ip_address` VARCHAR(64) NULL,
  `user_agent` TEXT NULL,
  `status` ENUM('opened','registered','purchased','cancelled') NOT NULL DEFAULT 'opened',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registered_at` DATETIME NULL,
  `purchased_at` DATETIME NULL,
  PRIMARY KEY (`id_visit`),
  KEY `idx_ref_visit_token` (`token`),
  KEY `idx_ref_visit_inviter` (`inviter_user_id`),
  KEY `idx_ref_visit_invited` (`invited_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_referral_purchases` (
  `id_purchase` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(128) NOT NULL,
  `inviter_user_id` BIGINT(30) NOT NULL,
  `invited_user_id` BIGINT(30) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `purchase_reference` VARCHAR(128) NOT NULL,
  `source` VARCHAR(50) NOT NULL DEFAULT 'manual',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_purchase`),
  UNIQUE KEY `uq_purchase_reference` (`purchase_reference`),
  KEY `idx_ref_purchase_token` (`token`),
  KEY `idx_ref_purchase_inviter` (`inviter_user_id`),
  KEY `idx_ref_purchase_invited` (`invited_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_invitation_pool_config` (
  `id_config` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `pool_percentage` DECIMAL(5,2) NOT NULL DEFAULT 5.00 COMMENT 'Porcentaje de ganancias del minuto: 1-10',
  `min_pool_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `min_purchase_amount` DECIMAL(12,2) NOT NULL DEFAULT 1.00,
  `round_seconds` SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  `tie_rule` ENUM('first_to_score','split') NOT NULL DEFAULT 'first_to_score',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `gms_invitation_pool_config` (`id_config`, `pool_percentage`, `min_pool_amount`, `min_purchase_amount`, `round_seconds`, `tie_rule`, `active`)
VALUES (1, 5.00, 0.00, 1.00, 60, 'first_to_score', 1)
ON DUPLICATE KEY UPDATE `id_config` = `id_config`;

CREATE TABLE IF NOT EXISTS `gms_invitation_pool_counters` (
  `id_user` BIGINT(30) NOT NULL,
  `score_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Contador competitivo; solo se reinicia al ganador',
  `total_purchases` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_purchase_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `first_score_at` DATETIME NULL COMMENT 'Para desempate: gana quien llego primero al score',
  `last_purchase_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id_user`),
  KEY `idx_pool_counter_score` (`score_count`, `first_score_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_invitation_pool_rounds` (
  `id_round` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `round_code` VARCHAR(32) NOT NULL,
  `started_at` DATETIME NOT NULL,
  `scheduled_close_at` DATETIME NOT NULL,
  `closed_at` DATETIME NULL,
  `gross_profit` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `pool_percentage` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
  `pool_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('open','closed','no_winner','cancelled') NOT NULL DEFAULT 'open',
  `winner_user_id` BIGINT(30) NULL,
  `winner_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `tie_rule` VARCHAR(30) NOT NULL DEFAULT 'first_to_score',
  `id_audit` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_round`),
  UNIQUE KEY `uq_pool_round_code` (`round_code`),
  KEY `idx_pool_round_status` (`status`, `scheduled_close_at`),
  KEY `idx_pool_round_winner` (`winner_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_invitation_pool_entries` (
  `id_entry` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_round` BIGINT UNSIGNED NOT NULL,
  `id_user` BIGINT(30) NOT NULL,
  `score_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `ranking_position` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_winner` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_entry`),
  KEY `idx_pool_entry_round` (`id_round`),
  KEY `idx_pool_entry_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_gift_wallet` (
  `id_user` BIGINT(30) NOT NULL,
  `locked_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `released_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `wagering_required` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Debe apostar este monto para liberar',
  `wagering_progress` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gms_gift_ledger` (
  `id_gift_ledger` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` BIGINT(30) NOT NULL,
  `id_round` BIGINT UNSIGNED NULL,
  `type` ENUM('credit_locked','wager_progress','release_to_balance','manual_adjustment') NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `wagering_required_delta` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `wagering_progress_delta` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('locked','released','processed') NOT NULL DEFAULT 'processed',
  `reference_table` VARCHAR(80) NULL,
  `reference_id` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gift_ledger`),
  KEY `idx_gift_user` (`id_user`),
  KEY `idx_gift_round` (`id_round`),
  KEY `idx_gift_reference` (`reference_table`, `reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
