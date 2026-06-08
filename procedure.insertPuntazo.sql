CREATE DEFINER=`root`@`localhost` PROCEDURE `insertPuntazo`(IN `num` INT(0) UNSIGNED, IN `i` INT(0) UNSIGNED)
    MODIFIES SQL DATA
    COMMENT 'rellenar 1 millon de registros'
WHILE i < num DO
    INSERT INTO `tb_puntazo` (`id`) VALUES (NULL);
    SET i = i + 1;
END WHILE