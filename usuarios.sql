-- Usuario externos | clave: ZdysQDjbHBhQpqjD
GRANT USAGE ON *.* TO 'usergames'@'localhost' IDENTIFIED BY PASSWORD '*989AD1F9BCC0CD03043BEB3D285E885CC35AF36F';

GRANT SELECT ON `games`.`tb_won_dice.3` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_user` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_config` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_game` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_won_dice.2` TO 'usergames'@'localhost';

GRANT INSERT ON `games`.`tb_error` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_won_roulette` TO 'usergames'@'localhost';

GRANT SELECT, INSERT ON `games`.`tb_trans` TO 'usergames'@'localhost';

GRANT SELECT ON `games`.`tb_won_dice.1` TO 'usergames'@'localhost';


-- Usuario para ganadores | clave: npLPuTbfCejzBP5Y

GRANT USAGE ON *.* TO 'supgames'@'localhost' IDENTIFIED BY PASSWORD '*18F13ECA4992D017DF58E1C62E380D0861676667';

GRANT INSERT ON `games`.`tb_won_roulette` TO 'supgames'@'localhost';

GRANT INSERT ON `games`.`tb_won_dice.3` TO 'supgames'@'localhost';

GRANT INSERT ON `games`.`tb_won_dice.1` TO 'supgames'@'localhost';

GRANT SELECT ON `games`.`tb_trans` TO 'supgames'@'localhost';

GRANT INSERT ON `games`.`tb_won_dice.2` TO 'supgames'@'localhost';

-- Usuario para Webservice | clave: LwUnv8N2mmY8V7z4

GRANT USAGE ON *.* TO 'wsgames'@'localhost' IDENTIFIED BY PASSWORD '*5C4897742966B88948796BAC8AED0D03E7BC6EC1';

GRANT INSERT ON `games`.`tb_user` TO 'wsgames'@'localhost';

GRANT SELECT (pass_access, id_access, user_access) ON `games`.`tb_access` TO 'wsgames'@'localhost';