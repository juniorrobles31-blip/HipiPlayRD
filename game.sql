-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2016 at 07:27 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `games`
--
CREATE DATABASE IF NOT EXISTS `games` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `games`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `Prueba balance`$$
CREATE  PROCEDURE `Prueba balance`()
    SQL SECURITY INVOKER
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE p_id_user INT DEFAULT 0;
    DECLARE p_trans INT DEFAULT 0;
    DECLARE p_balance INT DEFAULT 0;
    DECLARE p_user INT DEFAULT 0;
    DECLARE p_total INT DEFAULT 0;
    DECLARE cur1 CURSOR FOR SELECT `id_user` FROM `gms_user`;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
     
    OPEN cur1;
    read_loop: LOOP
      FETCH cur1 INTO p_id_user;
        IF done THEN
          LEAVE read_loop;
        END IF;
            SET p_trans     = 0;
            SET p_balance   = 0;
            SET p_user      = 0;
         
            SELECT IFNULL(SUM(`amount`),0) INTO p_trans FROM `gms_transaction`  WHERE `id_user` = p_id_user  AND DATE(`entry_date`) = CURDATE() - INTERVAL 1 DAY;
             
            SELECT IFNULL(`balance`,0) INTO p_balance  FROM `gms_balance`  WHERE `id_user` = p_id_user AND `date` =  CURDATE()- INTERVAL 2 DAY;
             
            SELECT IFNULL(`id_user`,0) INTO p_user  FROM `gms_balance` WHERE `id_user` = p_id_user AND `date` =  CURDATE()- INTERVAL 2 DAY;
            SET p_total = p_balance + p_trans;
            CASE
                WHEN p_user = 0 THEN           
                    INSERT INTO `gms_balance`( `id_user`, `balance`, `date`) VALUES (p_id_user, p_total, CURDATE() - INTERVAL 1 DAY);
            ELSE
                UPDATE `gms_balance` SET `balance` = p_total,`date`=CURDATE() - INTERVAL 1 DAY WHERE `id_user` = p_id_user;
            END CASE;
 
    END LOOP;
 
    CLOSE cur1;
     
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gms_balance`
--

DROP TABLE IF EXISTS `gms_balance`;
CREATE TABLE IF NOT EXISTS `gms_balance` (
  `id_balance` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(30) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id_balance`),
  UNIQUE KEY `gms_balance_uq1` (`id_user`,`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `gms_balance`
--

INSERT INTO `gms_balance` (`id_balance`, `id_user`, `balance`, `date`) VALUES
(5, 1, '975.00', '2016-10-01'),
(6, 3, '1000.00', '2016-10-01');

-- --------------------------------------------------------

--
-- Table structure for table `gms_config`
--

DROP TABLE IF EXISTS `gms_config`;
CREATE TABLE IF NOT EXISTS `gms_config` (
  `id_config` tinyint(1) NOT NULL AUTO_INCREMENT,
  `nm_play` bigint(30) NOT NULL COMMENT 'numero de juga del puntazo',
  `lowest_puntazo` tinyint(1) NOT NULL,
  `highest_puntazo` int(11) NOT NULL,
  `percentage_super_cumulative` decimal(4,2) NOT NULL,
  `super_cumulative` decimal(10,2) NOT NULL,
  `lowest_amount` decimal(10,2) NOT NULL,
  `count_winner` smallint(4) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gms_config`
--

INSERT INTO `gms_config` (`id_config`, `nm_play`, `lowest_puntazo`, `highest_puntazo`, `percentage_super_cumulative`, `super_cumulative`, `lowest_amount`, `count_winner`, `price`) VALUES
(1, 2, 1, 10000, '0.02', '16.00', '10000.00', 100, '100.00');

-- --------------------------------------------------------

--
-- Table structure for table `gms_game`
--

DROP TABLE IF EXISTS `gms_game`;
CREATE TABLE IF NOT EXISTS `gms_game` (
  `id_game` tinyint(2) NOT NULL AUTO_INCREMENT,
  `cd_game` varchar(15) NOT NULL,
  `game` varchar(20) NOT NULL,
  `desc_game` varchar(100) NOT NULL,
  PRIMARY KEY (`id_game`),
  UNIQUE KEY `game` (`game`),
  UNIQUE KEY `cd_game` (`cd_game`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='juegos disponibles' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `gms_game`
--

INSERT INTO `gms_game` (`id_game`, `cd_game`, `game`, `desc_game`) VALUES
(0, 'recarga', 'Recargas', 'Recargas de Balance'),
(1, 'dice.1', 'Dado directo', 'un solo dado con una apuesta. Dado Unico'),
(2, 'dice.3', 'Dado Tripleta', 'Tres dados con una apuesta. Dado Triple'),
(3, 'horse', 'Caballos', 'Caballos con una apuestas'),
(4, 'roulette', 'Ruleta', 'Ruleta con una apuesta'),
(5, 'dice.2', 'Super dado', 'Un dado con tres apuestas.'),
(6, 'puntazo', 'puntazo millonario', 'Loteria semanal');

-- --------------------------------------------------------

--
-- Table structure for table `gms_numbers`
--

DROP TABLE IF EXISTS `gms_numbers`;
CREATE TABLE IF NOT EXISTS `gms_numbers` (
  `number` tinyint(1) NOT NULL,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='numeros que solo pueden insertar';

--
-- Dumping data for table `gms_numbers`
--

INSERT INTO `gms_numbers` (`number`) VALUES
(0),
(1),
(2),
(3),
(4),
(5),
(6);

-- --------------------------------------------------------

--
-- Table structure for table `gms_timer`
--

DROP TABLE IF EXISTS `gms_timer`;
CREATE TABLE IF NOT EXISTS `gms_timer` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `ciclo` tinyint(1) unsigned NOT NULL DEFAULT '10',
  `play_time` datetime NOT NULL,
  `last_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gms_timer`
--

INSERT INTO `gms_timer` (`id_config`, `ciclo`, `play_time`, `last_time`) VALUES
(1, 1, '2016-10-11 23:00:05', NULL),
(2, 255, '2016-11-10 00:00:00', '2016-09-21 03:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `gms_tp_values`
--

DROP TABLE IF EXISTS `gms_tp_values`;
CREATE TABLE IF NOT EXISTS `gms_tp_values` (
  `id_tp_values` int(11) NOT NULL AUTO_INCREMENT,
  `ds_tp_values` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tp_values`),
  UNIQUE KEY `unique_one` (`ds_tp_values`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='tipo de valores. ejemplo: Genero' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `gms_tp_values`
--

INSERT INTO `gms_tp_values` (`id_tp_values`, `ds_tp_values`) VALUES
(3, 'Beneficios'),
(8, 'Formas de pago'),
(1, 'Genero'),
(4, 'Recargas'),
(2, 'Tipos de cuenta'),
(5, 'Transacciones bancarias'),
(7, 'Transacciones juegos');

-- --------------------------------------------------------

--
-- Table structure for table `gms_transaction`
--

DROP TABLE IF EXISTS `gms_transaction`;
CREATE TABLE IF NOT EXISTS `gms_transaction` (
  `id_trans` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(30) NOT NULL,
  `id_game` tinyint(2) NOT NULL,
  `id_values` int(11) NOT NULL,
  `nm_play` bigint(30) unsigned NOT NULL,
  `nm_one` tinyint(2) NOT NULL DEFAULT '0',
  `nm_two` tinyint(2) NOT NULL DEFAULT '0',
  `nm_three` tinyint(2) NOT NULL DEFAULT '0',
  `nm_puntazo` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL,
  `pay` bigint(30) unsigned DEFAULT NULL,
  `pay_sponsor` int(1) DEFAULT '0' COMMENT '0 - no pagado 1 -pagado',
  `entry_date` datetime NOT NULL,
  PRIMARY KEY (`id_trans`),
  UNIQUE KEY `gms_transaction_uq1` (`id_values`,`pay`) COMMENT 'only one pay per play',
  KEY `id_game` (`id_game`),
  KEY `numero_uno` (`nm_one`),
  KEY `numero_dos` (`nm_two`),
  KEY `numero_tres` (`nm_three`),
  KEY `gms_transaction_IDX1` (`nm_one`,`nm_play`,`id_game`),
  KEY `gms_transaction_IDX2` (`nm_two`,`nm_play`,`id_game`),
  KEY `gms_transaction_IDX3` (`nm_three`,`nm_play`,`id_game`),
  KEY `gms_transaction_IDX4` (`id_game`,`id_user`,`nm_play`),
  KEY `gms_transaction_IDX5` (`id_user`,`id_game`),
  KEY `id_values` (`id_values`),
  KEY `pay` (`pay`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `gms_transaction`
--

INSERT INTO `gms_transaction` (`id_trans`, `id_user`, `id_game`, `id_values`, `nm_play`, `nm_one`, `nm_two`, `nm_three`, `nm_puntazo`, `amount`, `pay`, `pay_sponsor`, `entry_date`) VALUES
(1, 1, 1, 13, 2, 2, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(3, 1, 1, 13, 2, 1, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(5, 3, 1, 13, 2, 3, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(6, 3, 1, 13, 2, 4, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(7, 3, 1, 13, 2, 5, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(8, 3, 1, 13, 2, 6, 0, 0, 0, '-25.00', NULL, 0, '2016-09-02 06:18:10'),
(15, 3, 4, 13, 1, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-09-02 06:18:10'),
(16, 3, 4, 13, 1, 2, 0, 0, 0, '-10.00', NULL, 0, '2016-09-02 06:18:10'),
(18, 3, 4, 14, 1, 0, 0, 0, 0, '19.00', 16, 0, '2016-09-07 21:43:49'),
(20, 3, 1, 14, 2, 0, 0, 0, 0, '125.00', 6, 0, '2016-09-07 21:54:45'),
(21, 3, 5, 13, 2, 1, 2, 3, 0, '-10.00', NULL, 0, '2016-09-02 06:18:10'),
(22, 3, 5, 13, 2, 4, 5, 6, 0, '-10.00', NULL, 0, '2016-09-02 06:18:10'),
(24, 3, 5, 14, 2, 0, 0, 0, 0, '19.00', 22, 0, '2016-09-07 22:24:41'),
(25, 3, 1, 13, 5, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-09-30 01:16:50'),
(30, 3, 1, 13, 5, 2, 0, 0, 0, '-10.00', NULL, 0, '2016-10-01 15:45:28'),
(31, 3, 4, 13, 3, 2, 0, 0, 0, '-10.00', NULL, 0, '2016-10-01 15:48:21'),
(32, 3, 4, 13, 3, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-10-01 15:50:39'),
(33, 1, 0, 9, 0, 0, 0, 0, 0, '100.00', NULL, 0, '2016-10-02 17:47:56'),
(34, 1, 1, 13, 5, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-10-02 17:48:03'),
(35, 3, 6, 13, 1, 0, 0, 0, 8220, '-100.00', NULL, 0, '2016-10-02 18:04:06'),
(36, 3, 6, 13, 1, 0, 0, 0, 1158, '-100.00', NULL, 0, '2016-10-02 18:04:25'),
(37, 1, 5, 13, 5, 1, 2, 3, 0, '-10.00', NULL, 0, '2016-10-02 18:11:25'),
(38, 3, 6, 13, 1, 0, 0, 0, 7004, '-100.00', NULL, 0, '2016-10-02 18:11:27'),
(39, 1, 2, 13, 14, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-10-02 18:11:32'),
(40, 1, 4, 13, 3, 1, 0, 0, 0, '-10.00', NULL, 0, '2016-10-02 18:11:43'),
(41, 1, 3, 13, 2, 1, 2, 3, 0, '-10.00', NULL, 0, '2016-10-02 18:11:52'),
(42, 1, 6, 13, 1, 0, 0, 0, 9708, '-100.00', NULL, 0, '2016-10-02 18:15:35'),
(43, 1, 6, 13, 2, 0, 0, 0, 383, '-100.00', NULL, 0, '2016-10-02 18:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `gms_user`
--

DROP TABLE IF EXISTS `gms_user`;
CREATE TABLE IF NOT EXISTS `gms_user` (
  `id_user` bigint(30) NOT NULL AUTO_INCREMENT,
  `id_zzvm` bigint(30) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sponsor` bigint(30) DEFAULT NULL,
  `entry_date` date NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gms_user`
--

INSERT INTO `gms_user` (`id_user`, `id_zzvm`, `alias`, `last_login`, `sponsor`, `entry_date`) VALUES
(1, 7, 'pedro', '2016-07-21 04:00:00', 0, '2016-09-01'),
(3, 1, 'root', '2016-07-21 04:00:00', 0, '2016-09-22');

-- --------------------------------------------------------

--
-- Table structure for table `gms_values`
--

DROP TABLE IF EXISTS `gms_values`;
CREATE TABLE IF NOT EXISTS `gms_values` (
  `id_values` int(11) NOT NULL AUTO_INCREMENT,
  `id_tp_values` int(11) NOT NULL,
  `cd_values` varchar(3) NOT NULL COMMENT 'codigos relacionados con el sistema',
  `ds_values` varchar(70) NOT NULL,
  PRIMARY KEY (`id_values`),
  UNIQUE KEY `UNIQUE_VALUE` (`id_tp_values`,`ds_values`),
  KEY `id_tp_values` (`id_tp_values`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='valores de los tipos. Ejemplo: Genero Masculino y Femenino' AUTO_INCREMENT=27 ;

--
-- Dumping data for table `gms_values`
--

INSERT INTO `gms_values` (`id_values`, `id_tp_values`, `cd_values`, `ds_values`) VALUES
(1, 2, '', 'Cuenta ahorro'),
(2, 2, '', 'Cuenta corriente'),
(3, 3, '', 'Beneficio general'),
(4, 3, '', 'Beneficio al corte'),
(5, 1, '', 'Femenino'),
(6, 1, '', 'Masculino'),
(7, 4, '', 'Recarga realizada'),
(8, 4, '', 'Recarga reversada'),
(9, 5, 'DB', 'Debito balance'),
(10, 5, 'AB', 'Acredito balance'),
(11, 3, '', 'Beneficio recarga'),
(12, 3, '', 'Beneficio apartado'),
(13, 7, '', 'Apuesta realizada'),
(14, 7, '', 'Apuesta ganada'),
(15, 7, '', 'Apuesta reversada'),
(16, 3, '', 'Impuesto Art. 12, Ley 288-04/0.15% por transferencias bancarias'),
(17, 3, '', 'Reverso Impuesto Art. 12, Ley 288-04/0.15% por transferencias bancaria'),
(18, 3, '', 'Reverso beneficio recarga'),
(19, 5, '', 'Transferencias a terceros'),
(20, 5, '', 'Transferencias de balance'),
(21, 8, '', 'Transferencia bancaria'),
(22, 8, '', 'Retiro en establecimiento'),
(23, 7, '', 'Beneficio Punto de Venta'),
(24, 7, '', 'Beneficio por Servicio'),
(25, 7, '', 'Beneficio Promotor'),
(26, 3, '', 'Prueba');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_dice.1`
--

DROP TABLE IF EXISTS `gms_won_dice.1`;
CREATE TABLE IF NOT EXISTS `gms_won_dice.1` (
  `id_won` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `nm_one` tinyint(1) NOT NULL,
  `entry_date` datetime NOT NULL,
  PRIMARY KEY (`id_won`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `numero` (`nm_one`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `gms_won_dice.1`
--

INSERT INTO `gms_won_dice.1` (`id_won`, `nm_one`, `entry_date`) VALUES
(1, 4, '2016-09-07 21:54:34'),
(2, 4, '2016-09-07 21:54:45'),
(3, 4, '2016-09-07 22:41:09'),
(4, 2, '2016-09-17 22:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_dice.2`
--

DROP TABLE IF EXISTS `gms_won_dice.2`;
CREATE TABLE IF NOT EXISTS `gms_won_dice.2` (
  `id_won` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `nm_one` tinyint(1) NOT NULL,
  `entry_date` datetime NOT NULL,
  PRIMARY KEY (`id_won`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `numero` (`nm_one`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `gms_won_dice.2`
--

INSERT INTO `gms_won_dice.2` (`id_won`, `nm_one`, `entry_date`) VALUES
(1, 4, '2016-09-07 22:23:24'),
(2, 4, '2016-09-07 22:24:41'),
(3, 3, '2016-09-07 22:41:09'),
(4, 1, '2016-09-17 22:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_dice.3`
--

DROP TABLE IF EXISTS `gms_won_dice.3`;
CREATE TABLE IF NOT EXISTS `gms_won_dice.3` (
  `id_won` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `nm_one` tinyint(1) NOT NULL,
  `nm_two` tinyint(1) NOT NULL,
  `nm_three` tinyint(1) NOT NULL,
  `entry_date` datetime NOT NULL,
  PRIMARY KEY (`id_won`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `numero_uno` (`nm_one`),
  KEY `numero_dos` (`nm_two`),
  KEY `numero_tres` (`nm_three`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `gms_won_dice.3`
--

INSERT INTO `gms_won_dice.3` (`id_won`, `nm_one`, `nm_two`, `nm_three`, `entry_date`) VALUES
(12, 6, 4, 1, '2016-09-07 22:41:09'),
(13, 3, 6, 6, '2016-09-17 22:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_horse`
--

DROP TABLE IF EXISTS `gms_won_horse`;
CREATE TABLE IF NOT EXISTS `gms_won_horse` (
  `id_won` bigint(30) NOT NULL AUTO_INCREMENT,
  `nm_one` tinyint(1) NOT NULL,
  `nm_two` tinyint(1) NOT NULL,
  `nm_three` tinyint(1) NOT NULL,
  `nm_four` tinyint(1) NOT NULL,
  `nm_five` tinyint(1) NOT NULL,
  `nm_six` tinyint(1) NOT NULL,
  `entry_date` datetime NOT NULL,
  PRIMARY KEY (`id_won`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `nm_one` (`nm_one`),
  KEY `nm_two` (`nm_two`),
  KEY `nm_three` (`nm_three`),
  KEY `nm_four` (`nm_four`),
  KEY `nm_five` (`nm_five`),
  KEY `nm_six` (`nm_six`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gms_won_horse`
--

INSERT INTO `gms_won_horse` (`id_won`, `nm_one`, `nm_two`, `nm_three`, `nm_four`, `nm_five`, `nm_six`, `entry_date`) VALUES
(1, 1, 3, 4, 2, 6, 5, '2016-09-17 22:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_puntazo`
--

DROP TABLE IF EXISTS `gms_won_puntazo`;
CREATE TABLE IF NOT EXISTS `gms_won_puntazo` (
  `id_puntazo` bigint(30) NOT NULL AUTO_INCREMENT,
  `nm_play` bigint(30) NOT NULL,
  `number` int(11) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_puntazo`),
  UNIQUE KEY `gms_won_putnazo_uq1` (`nm_play`,`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;

--
-- Dumping data for table `gms_won_puntazo`
--

INSERT INTO `gms_won_puntazo` (`id_puntazo`, `nm_play`, `number`, `entry_date`) VALUES
(1, 1, 200, '2016-10-02 23:26:11'),
(2, 1, 201, '2016-10-02 23:26:11'),
(3, 1, 202, '2016-10-02 23:26:11'),
(4, 1, 203, '2016-10-02 23:26:11'),
(5, 1, 204, '2016-10-02 23:26:11'),
(6, 1, 205, '2016-10-02 23:26:11'),
(7, 1, 206, '2016-10-02 23:26:11'),
(8, 1, 207, '2016-10-02 23:26:11'),
(9, 1, 208, '2016-10-02 23:26:11'),
(10, 1, 209, '2016-10-02 23:26:11'),
(11, 1, 210, '2016-10-02 23:26:11'),
(12, 1, 211, '2016-10-02 23:26:11'),
(13, 1, 212, '2016-10-02 23:26:11'),
(14, 1, 213, '2016-10-02 23:26:11'),
(15, 1, 214, '2016-10-02 23:26:11'),
(16, 1, 215, '2016-10-02 23:26:11'),
(17, 1, 216, '2016-10-02 23:26:11'),
(18, 1, 217, '2016-10-02 23:26:11'),
(19, 1, 218, '2016-10-02 23:26:11'),
(20, 1, 219, '2016-10-02 23:26:11'),
(21, 1, 220, '2016-10-02 23:26:11'),
(22, 1, 221, '2016-10-02 23:26:11'),
(23, 1, 222, '2016-10-02 23:26:11'),
(24, 1, 223, '2016-10-02 23:26:11'),
(25, 1, 224, '2016-10-02 23:26:11'),
(26, 1, 225, '2016-10-02 23:26:11'),
(27, 1, 226, '2016-10-02 23:26:11'),
(28, 1, 227, '2016-10-02 23:26:11'),
(29, 1, 228, '2016-10-02 23:26:11'),
(30, 1, 229, '2016-10-02 23:26:11'),
(31, 1, 230, '2016-10-02 23:26:11'),
(32, 1, 231, '2016-10-02 23:26:11'),
(33, 1, 232, '2016-10-02 23:26:11'),
(34, 1, 233, '2016-10-02 23:26:11'),
(35, 1, 234, '2016-10-02 23:26:11'),
(36, 1, 235, '2016-10-02 23:26:11'),
(37, 1, 236, '2016-10-02 23:26:11'),
(38, 1, 237, '2016-10-02 23:26:11'),
(39, 1, 238, '2016-10-02 23:26:11'),
(40, 1, 239, '2016-10-02 23:26:11'),
(41, 1, 240, '2016-10-02 23:26:11'),
(42, 1, 241, '2016-10-02 23:26:11'),
(43, 1, 242, '2016-10-02 23:26:11'),
(44, 1, 243, '2016-10-02 23:26:11'),
(45, 1, 244, '2016-10-02 23:26:11'),
(46, 1, 245, '2016-10-02 23:26:11'),
(47, 1, 246, '2016-10-02 23:26:11'),
(48, 1, 247, '2016-10-02 23:26:11'),
(49, 1, 248, '2016-10-02 23:26:11'),
(50, 1, 249, '2016-10-02 23:26:11'),
(51, 1, 250, '2016-10-02 23:26:11'),
(52, 1, 251, '2016-10-02 23:26:11'),
(53, 1, 252, '2016-10-02 23:26:11'),
(54, 1, 253, '2016-10-02 23:26:11'),
(55, 1, 254, '2016-10-02 23:26:11'),
(56, 1, 255, '2016-10-02 23:26:11'),
(57, 1, 256, '2016-10-02 23:26:11'),
(58, 1, 257, '2016-10-02 23:26:11'),
(59, 1, 258, '2016-10-02 23:26:11'),
(60, 1, 259, '2016-10-02 23:26:11'),
(61, 1, 260, '2016-10-02 23:26:11'),
(62, 1, 261, '2016-10-02 23:26:11'),
(63, 1, 262, '2016-10-02 23:26:11'),
(64, 1, 263, '2016-10-02 23:26:11'),
(65, 1, 264, '2016-10-02 23:26:11'),
(66, 1, 265, '2016-10-02 23:26:11'),
(67, 1, 266, '2016-10-02 23:26:11'),
(68, 1, 267, '2016-10-02 23:26:11'),
(69, 1, 268, '2016-10-02 23:26:11'),
(70, 1, 269, '2016-10-02 23:26:11'),
(71, 1, 270, '2016-10-02 23:26:11'),
(72, 1, 271, '2016-10-02 23:26:11'),
(73, 1, 272, '2016-10-02 23:26:11'),
(74, 1, 273, '2016-10-02 23:26:11'),
(75, 1, 274, '2016-10-02 23:26:11'),
(76, 1, 275, '2016-10-02 23:26:11'),
(77, 1, 276, '2016-10-02 23:26:11'),
(78, 1, 277, '2016-10-02 23:26:11'),
(79, 1, 278, '2016-10-02 23:26:11'),
(80, 1, 279, '2016-10-02 23:26:11'),
(81, 1, 280, '2016-10-02 23:26:11'),
(82, 1, 281, '2016-10-02 23:26:11'),
(83, 1, 282, '2016-10-02 23:26:11'),
(84, 1, 283, '2016-10-02 23:26:11'),
(85, 1, 284, '2016-10-02 23:26:11'),
(86, 1, 285, '2016-10-02 23:26:11'),
(87, 1, 286, '2016-10-02 23:26:11'),
(88, 1, 287, '2016-10-02 23:26:11'),
(89, 1, 288, '2016-10-02 23:26:11'),
(90, 1, 289, '2016-10-02 23:26:11'),
(91, 1, 290, '2016-10-02 23:26:11'),
(92, 1, 291, '2016-10-02 23:26:11'),
(93, 1, 292, '2016-10-02 23:26:11'),
(94, 1, 293, '2016-10-02 23:26:11'),
(95, 1, 294, '2016-10-02 23:26:11'),
(96, 1, 295, '2016-10-02 23:26:11'),
(97, 1, 296, '2016-10-02 23:26:11'),
(98, 1, 297, '2016-10-02 23:26:11'),
(99, 1, 298, '2016-10-02 23:26:11'),
(100, 1, 299, '2016-10-02 23:26:11'),
(101, 1, 300, '2016-10-02 23:26:11');

-- --------------------------------------------------------

--
-- Table structure for table `gms_won_roulette`
--

DROP TABLE IF EXISTS `gms_won_roulette`;
CREATE TABLE IF NOT EXISTS `gms_won_roulette` (
  `id_won` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `nm_one` tinyint(2) NOT NULL,
  `entry_date` datetime NOT NULL,
  `place` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_won`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `numero` (`nm_one`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gms_won_roulette`
--

INSERT INTO `gms_won_roulette` (`id_won`, `nm_one`, `entry_date`, `place`) VALUES
(1, 1, '2016-09-07 21:49:06', 0),
(2, 2, '2016-09-17 22:59:07', 10);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gms_balance`
--
ALTER TABLE `gms_balance`
  ADD CONSTRAINT `gms_balance_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `gms_user` (`id_user`);

--
-- Constraints for table `gms_transaction`
--
ALTER TABLE `gms_transaction`
  ADD CONSTRAINT `gms_transaction_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `gms_game` (`id_game`),
  ADD CONSTRAINT `gms_transaction_ibfk_3` FOREIGN KEY (`nm_one`) REFERENCES `gms_numbers` (`number`),
  ADD CONSTRAINT `gms_transaction_ibfk_4` FOREIGN KEY (`nm_two`) REFERENCES `gms_numbers` (`number`),
  ADD CONSTRAINT `gms_transaction_ibfk_5` FOREIGN KEY (`nm_three`) REFERENCES `gms_numbers` (`number`),
  ADD CONSTRAINT `gms_transaction_ibfk_6` FOREIGN KEY (`id_user`) REFERENCES `gms_user` (`id_user`),
  ADD CONSTRAINT `gms_transaction_ibfk_7` FOREIGN KEY (`id_values`) REFERENCES `gms_values` (`id_values`),
  ADD CONSTRAINT `gms_transaction_ibfk_8` FOREIGN KEY (`pay`) REFERENCES `gms_transaction` (`id_trans`);

--
-- Constraints for table `gms_won_dice.1`
--
ALTER TABLE `gms_won_dice.1`
  ADD CONSTRAINT `gms_won_dice.1_ibfk_1` FOREIGN KEY (`nm_one`) REFERENCES `gms_numbers` (`number`);

--
-- Constraints for table `gms_won_dice.2`
--
ALTER TABLE `gms_won_dice.2`
  ADD CONSTRAINT `gms_won_dice.2_ibfk_1` FOREIGN KEY (`nm_one`) REFERENCES `gms_numbers` (`number`);

--
-- Constraints for table `gms_won_dice.3`
--
ALTER TABLE `gms_won_dice.3`
  ADD CONSTRAINT `gms_won_dice.3_ibfk_1` FOREIGN KEY (`nm_one`) REFERENCES `gms_numbers` (`number`),
  ADD CONSTRAINT `gms_won_dice.3_ibfk_2` FOREIGN KEY (`nm_two`) REFERENCES `gms_numbers` (`number`),
  ADD CONSTRAINT `gms_won_dice.3_ibfk_3` FOREIGN KEY (`nm_three`) REFERENCES `gms_numbers` (`number`);

--
-- Constraints for table `gms_won_roulette`
--
ALTER TABLE `gms_won_roulette`
  ADD CONSTRAINT `gms_won_roulette_ibfk_1` FOREIGN KEY (`nm_one`) REFERENCES `gms_numbers` (`number`);

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS  `Create Balance`$$
CREATE  EVENT `Create Balance` ON SCHEDULE EVERY 1 DAY STARTS '2016-09-02 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE p_id_user INT DEFAULT 0;
    DECLARE p_trans INT DEFAULT 0;
    DECLARE p_balance INT DEFAULT 0;
    DECLARE p_user INT DEFAULT 0;
    DECLARE p_total INT DEFAULT 0;
    DECLARE cur1 CURSOR FOR SELECT `id_user` FROM `gms_user`;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
     
    OPEN cur1;
    read_loop: LOOP
      FETCH cur1 INTO p_id_user;
        IF done THEN
          LEAVE read_loop;
        END IF;
            SET p_trans     = 0;
            SET p_balance   = 0;
            SET p_user      = 0;
         
            SELECT IFNULL(SUM(`amount`),0) INTO p_trans FROM `gms_transaction`  WHERE `id_user` = p_id_user  AND DATE(`entry_date`) = CURDATE() - INTERVAL 1 DAY;
             
            SELECT IFNULL(`balance`,0) INTO p_balance  FROM `gms_balance`  WHERE `id_user` = p_id_user AND `date` =  CURDATE()- INTERVAL 2 DAY;
             
            SELECT IFNULL(`id_user`,0) INTO p_user  FROM `gms_balance` WHERE `id_user` = p_id_user AND `date` =  CURDATE()- INTERVAL 2 DAY;
            SET p_total = p_balance + p_trans;
            CASE
                WHEN p_user = 0 THEN           
                    INSERT INTO `gms_balance`( `id_user`, `balance`, `date`) VALUES (p_id_user, p_total, CURDATE() - INTERVAL 1 DAY);
            ELSE
                UPDATE `gms_balance` SET `balance` = p_total,`date`=CURDATE() - INTERVAL 1 DAY WHERE `id_user` = p_id_user;
            END CASE;
 
    END LOOP;
 
    CLOSE cur1;
     
END$$

DELIMITER ;
SET FOREIGN_KEY_CHECKS=1;
