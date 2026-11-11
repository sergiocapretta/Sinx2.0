<?php
session_start();

if (file_exists(__DIR__ . '/installazione_completata.lock')) {
    http_response_code(403);
    exit('Installazione già completata.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./index.php');
    exit;
}

$nome = trim($_POST['nomeut'] ?? '');
$password_admin = (string)($_POST['passwd'] ?? '');
$livello = 'admin';

if ($nome === '' || $password_admin === '') {
    exit('Nome utente e password sono obbligatori. <a href="./install_database.php">Torna indietro</a>');
}
if (strlen($nome) > 25) {
    exit('Il nome utente non può superare 25 caratteri.');
}
if (strlen($password_admin) < 8) {
    exit('La password deve contenere almeno 8 caratteri. <a href="./install_database.php">Torna indietro</a>');
}

require_once('../dati_db.inc');
mysqli_report(MYSQLI_REPORT_OFF);
$link = @new mysqli($host, $username, $password, $db_name);
if ($link->connect_errno) {
    exit('Connessione al database non riuscita. Verifica i dati di configurazione.');
}

$stmt = $link->prepare("INSERT INTO utenti (utente, nome, pswd) VALUES (?, ?, MD5(?))");
if (!$stmt) {
    $errore = htmlspecialchars($link->error, ENT_QUOTES, 'UTF-8');
    $link->close();
    exit('Impossibile preparare la creazione dell’utente amministratore: ' . $errore);
}
$stmt->bind_param('sss', $nome, $livello, $password_admin);
if (!$stmt->execute()) {
    $errore = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
    $stmt->close();
    $link->close();
    exit('Creazione dell’amministratore non riuscita: ' . $errore);
}
$stmt->close();
$link->close();

header('Location: ./Install2.php');
exit;
?>