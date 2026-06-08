<?php
/**
 * Cierra rondas vencidas del Pool de Invitacion.
 * Uso CLI cada minuto:
 * php scripts/close_invitation_pool.php
 */
chdir(dirname(__DIR__));
define('ROOT','./include/');
$_SERVER['DOCUMENT_ROOT'] = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : getcwd();
require_once(ROOT.'class/invitation_pool.php');

try {
    $pool = new InvitationPool();
    $result = $pool->closeDueRounds(20);
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
} catch(Exception $e) {
    fwrite(STDERR, json_encode(array('STATUS'=>'ERROR','INFO'=>$e->getMessage()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL);
    exit(1);
}
