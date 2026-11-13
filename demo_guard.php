<?php
/*
 * Protezione globale della copia demo.
 *
 * Configurare PHP con auto_prepend_file che punti a questo file.
 * In questo modo il controllo viene eseguito PRIMA di ogni script PHP,
 * inclusi gli endpoint che non passano da top.inc o dal normale menu.
 */
require_once __DIR__ . '/demo_config.inc';

if (!defined('SINX_DEMO_ENABLED')) {
    define('SINX_DEMO_ENABLED', !empty($sinx_demo_enabled));
}
if (!defined('SINX_DEMO_GUARD_LOADED')) {
    define('SINX_DEMO_GUARD_LOADED', true);
}

if (!SINX_DEMO_ENABLED) {
    return;
}

// La demo contiene dati consultabili: evito cache del browser e indicizzazione.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow');

$sinx_demo_script = basename(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '');

if (!function_exists('sinx_demo_block_action')) {
    function sinx_demo_block_action($status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html lang="it"><head><meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Modalità demo - Sinx</title>';
        echo '<style>body{font-family:Arial,sans-serif;background:#f3f5f8;color:#263445;margin:0;padding:30px 15px}.box{max-width:650px;margin:8vh auto;background:#fff;padding:28px;border-radius:12px;box-shadow:0 5px 24px #0001;text-align:center}h1{color:#233592}.note{background:#eef3ff;padding:14px;border-radius:8px;line-height:1.5}a{color:#233592}</style>';
        echo '</head><body><main class="box"><h1>Modalità demo di Sinx</h1>';
        echo '<p class="note">Questa operazione è disponibile solo nella versione completa di Sinx. ';
        echo 'Nella demo i moduli possono essere compilati, ma i dati non vengono registrati.</p>';
        echo '<p><strong>Nessun dato è stato salvato o modificato.</strong></p>';
        echo '<p><a href="./index2.php">Torna alla demo</a></p></main></body></html>';
        exit;
    }
}

/*
 * Pagine da non eseguire in demo: eseguono test di scrittura, operazioni
 * amministrative, backup/ripristino o modifiche a file di configurazione.
 */
$sinx_demo_blocked_pages = array(
    'Backup_database.php',
    'restore_db.php',
    'diagnostica.php',
    'ai_config.php',
    'CancLog.php',
    'cancella_anno_sociale.php',
    'delete_file.php',
    'import_fattura_xml.php',
    'importa_calendario.php',
    'incasso_fattura.php',
    'email.php'
);

// Tutti gli endpoint con prefisso conf/Conf sono handler di conferma
// o modifica: non devono poter essere richiamati direttamente in demo.
if (in_array($sinx_demo_script, $sinx_demo_blocked_pages, true) ||
    preg_match('/^(conf_|Conf)/', $sinx_demo_script)) {
    sinx_demo_block_action(403);
}

/*
 * POST ammessi solo quando servono a consultare o filtrare dati.
 * Tutti gli altri POST vengono fermati prima di eseguire il file PHP
 * destinatario: niente INSERT/UPDATE/DELETE, upload, email o scritture file.
 */
$sinx_demo_readonly_post_pages = array(
    'Scheda_associato.php',
    'ricerca_nome_stud.php',
    'ricerca_classe_stud.php',
    'ricerca_tipo_ass.php',
    'ricerca_ricevute.php',
    'Rubrica.php',
    'Scheda_province.php',
    'Scheda_comuni.php'
);

$sinx_demo_method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

if (!in_array($sinx_demo_method, array('GET', 'HEAD', 'OPTIONS', 'POST'), true)) {
    sinx_demo_block_action(405);
}

if ($sinx_demo_method === 'POST' &&
    !in_array($sinx_demo_script, $sinx_demo_readonly_post_pages, true)) {
    sinx_demo_block_action();
}
