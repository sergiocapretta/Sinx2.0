<?php
require_once __DIR__ . '/demo_config.inc';

/*
 * Non concedo l'accesso se PHP non ha caricato il controllo globale.
 * Questo controllo si può verificare anche lasciando la demo disattivata.
 */
if (!defined('SINX_DEMO_GUARD_LOADED') || !SINX_DEMO_GUARD_LOADED) {
    http_response_code(503);
    exit('Protezione demo non attiva. L’accesso è temporaneamente disabilitato.');
}

if (empty($sinx_demo_enabled)) {
    http_response_code(404);
    exit('La demo è protetta correttamente, ma al momento è disattivata.');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
session_regenerate_id(true);

$_SESSION['utente'] = 'admin';
$_SESSION['nome'] = 'Visitatore demo';
$_SESSION['lingua'] = './lang/ita/';
$_SESSION['sinx_demo'] = true;

header('Location: ./index2.php');
exit;
