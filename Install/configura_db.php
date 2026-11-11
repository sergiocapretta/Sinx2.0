<?php
session_start();\nif (file_exists(__DIR__ . '/installazione_completata.lock')) { http_response_code(403); exit('Installazione già completata.'); }

function errore_installazione($messaggio) {
    http_response_code(400);
    $messaggio = htmlspecialchars($messaggio, ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html lang="it"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Configurazione Sinx</title><link rel="stylesheet" href="../style.css">';
    echo '<style>body{font-family:Arial,sans-serif;background:#f3f5f8;margin:0;padding:30px 15px;color:#263445}.box{max-width:650px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 5px 24px #0001}.errore{background:#fff0f0;border-left:4px solid #c0392b;padding:14px;margin:18px 0;line-height:1.5}a{color:#1769aa}</style>';
    echo '</head><body><main class="box"><h1>Configurazione database</h1><div class="errore">'.$messaggio.'</div><p>Correggi il problema e riprova.</p><p><a href="./index.php">Torna all\'installazione</a></p></main></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./index.php');
    exit;
}

if (empty($_SESSION['sinx_install_csrf']) ||
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['sinx_install_csrf'], $_POST['csrf_token'])) {
    errore_installazione('Sessione non valida o scaduta. Riapri la pagina di installazione e riprova.');
}

$host = trim($_POST['host'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$db_name = trim($_POST['db_name'] ?? '');

if ($host === '' || $username === '' || $db_name === '') {
    errore_installazione('Compila tutti i campi obbligatori. La password può essere lasciata vuota solo se il provider non ne prevede una.');
}

$root = dirname(__DIR__);
$config_file = $root . '/dati_db.inc';

if (!is_file($config_file) || !is_writable($config_file)) {
    errore_installazione('Il file dati_db.inc non è scrivibile da PHP. Verifica i permessi del solo file tramite il pannello hosting (normalmente 644 se il proprietario coincide con l’utente PHP). Non è necessario assegnare permessi 777.');
}

// Preparo le cartelle utilizzate da Sinx senza ricorrere a permessi 777.
$cartelle = array('Immagini', 'Log', 'tmp');
foreach ($cartelle as $nome_cartella) {
    $percorso = $root . '/' . $nome_cartella;
    if (!is_dir($percorso) && !@mkdir($percorso, 0755, true) && !is_dir($percorso)) {
        errore_installazione('Non è stato possibile creare la cartella ' . $nome_cartella . '. Verifica i permessi della cartella principale.');
    }
    if (!is_writable($percorso)) {
        errore_installazione('La cartella ' . $nome_cartella . ' esiste ma non è scrivibile da PHP. Correggi proprietario o permessi dal pannello hosting; evita 777.');
    }
}

// Verifico le credenziali e l'accesso al database già creato dal provider.
mysqli_report(MYSQLI_REPORT_OFF);
$connessione = @new mysqli($host, $username, $password, $db_name);
if ($connessione->connect_errno) {
    errore_installazione('Connessione al database non riuscita: ' . $connessione->connect_error);
}
if (!$connessione->set_charset('utf8mb4')) {
    $connessione->close();
    errore_installazione('Connessione riuscita, ma non è stato possibile impostare il charset utf8mb4.');
}
$connessione->close();

// Scrivo il file di configurazione solo dopo aver verificato connessione e cartelle.
$contenuto = "<?php\n";
$contenuto .= "/* Configurazione database generata dall'installer di Sinx. */\n";
$contenuto .= '$host = ' . var_export($host, true) . ";\n";
$contenuto .= '$username = ' . var_export($username, true) . ";\n";
$contenuto .= '$password = ' . var_export($password, true) . ";\n";
$contenuto .= '$db_name = ' . var_export($db_name, true) . ";\n";
$contenuto .= '$sinx_installato = false;' . "\n";
$contenuto .= "?>\n";

if (file_put_contents($config_file, $contenuto, LOCK_EX) === false) {
    errore_installazione('Non è stato possibile scrivere dati_db.inc. Verifica i permessi del file.');
}

unset($_SESSION['sinx_install_csrf']);
header('Location: ./index.php?db_configurato=1');
exit;
