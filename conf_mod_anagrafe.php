<?php
session_start();

$user = $_SESSION['utente'] ?? '';
if ($user !== 'admin') {
    die('Non hai i permessi per modificare questa anagrafica.');
}

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die('Connessione al database non riuscita.');

$id = isset($_POST['id_anagrafe']) ? (int)$_POST['id_anagrafe'] : 0;
if ($id <= 0) {
    die('Identificativo anagrafico non valido.');
}

$campi = array(
    'ntessera', 'nome', 'cognome', 'indirizzo', 'cap', 'citta', 'provincia',
    'tel', 'tel2', 'email', 'materia', 'nomerif', 'denominazione',
    'tipo_soggetto', 'partita_iva', 'PEC', 'codice_destinatario', 'nazione',
    'datan', 'classe', 'mansione', 'note'
);

$valori = array();
foreach ($campi as $campo) {
    $valori[$campo] = trim($_POST[$campo] ?? '');
}

$tipi_validi = array('persona_fisica', 'soggetto_privato', 'pubblica_amministrazione');
if (!in_array($valori['tipo_soggetto'], $tipi_validi, true)) {
    die('Tipo soggetto fiscale non valido.');
}

$valori['codice_destinatario'] = strtoupper($valori['codice_destinatario']);
if ($valori['codice_destinatario'] !== '') {
    $lunghezza = ($valori['tipo_soggetto'] === 'pubblica_amministrazione') ? 6 : 7;
    if (!preg_match('/^[A-Z0-9]{' . $lunghezza . '}$/', $valori['codice_destinatario'])) {
        die('Il codice destinatario non rispetta il formato previsto per il tipo di soggetto.');
    }
}

$valori['nazione'] = strtoupper($valori['nazione']);
if ($valori['nazione'] !== '' && !preg_match('/^[A-Z]{2}$/', $valori['nazione'])) {
    die('La nazione deve essere indicata con il codice ISO di due lettere.');
}

$set = array();
foreach ($campi as $campo) {
    $set[] = "`" . $campo . "` = ?";
}
$sql = "UPDATE tb_anagrafe SET " . implode(', ', $set) . " WHERE id_anagrafe = ?";
$stmt = mysqli_prepare($connect, $sql);
if (!$stmt) {
    die('Errore nella preparazione del salvataggio: ' . mysqli_error($connect));
}

$types = str_repeat('s', count($campi)) . 'i';
$params = array($types);
foreach ($campi as $campo) {
    $params[] = &$valori[$campo];
}
$params[] = &$id;
call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $params));

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($connect);

if (!$ok) {
    die('Errore durante il salvataggio dei dati anagrafici.');
}

$iniziale = trim($_POST['iniziale'] ?? '');
$url = './Scheda_associato_exp.php?associato=' . $id;
if ($iniziale !== '') {
    $url .= '&iniziale=' . rawurlencode($iniziale);
}
header('Location: ' . $url, true, 303);
exit;
?>
