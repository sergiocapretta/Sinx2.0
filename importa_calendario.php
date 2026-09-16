<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['utente']) || $_SESSION['utente'] !== 'admin') {
    header('Location: index2.php');
    exit;
}

include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name)
    or die("Errore DB");

mysqli_set_charset($connect, 'utf8mb4');

if (!isset($_FILES['ics_file']) || $_FILES['ics_file']['error'] !== UPLOAD_ERR_OK) {
    die("File ICS non valido");
}

/* Lettura sicura file */
$raw = file_get_contents($_FILES['ics_file']['tmp_name']);
if ($raw === false) {
    die("Impossibile leggere il file ICS");
}

/* Unfold righe ICS (RFC 5545) */
$raw = preg_replace("/\r\n[ \t]/", "", $raw);
$lines = preg_split("/\r\n|\n|\r/", $raw);

$importati = 0;
$scartati  = 0;
$evento    = [];

foreach ($lines as $line) {

    $line = trim($line);

    if ($line === 'BEGIN:VEVENT') {
        $evento = [];
        continue;
    }

if (strpos($line, 'DTSTART') === 0) {
    if (preg_match('/:(\d{8})(T(\d{6}))?/', $line, $m)) {
        $dt = DateTime::createFromFormat('Ymd', $m[1]);
        $evento['data'] = $dt->format('d-m-Y');
        $evento['ora']  = isset($m[3])
            ? substr($m[3], 0, 2) . ':' . substr($m[3], 2, 2) . ':00'
            : '00:00:00';
    }
    continue;
}

    if (strpos($line, 'SUMMARY:') === 0) {
        $evento['titolo'] = trim(substr($line, 8));
        continue;
    }

    if (strpos($line, 'DESCRIPTION:') === 0) {
        $evento['testo'] = trim(substr($line, 12));
        continue;
    }

if ($line === 'END:VEVENT') {

    if (empty($evento['data']) || empty($evento['titolo'])) {
        $scartati++;
        continue;
    }

    $data   = mysqli_real_escape_string($connect, $evento['data']);
    $titolo = mysqli_real_escape_string($connect, $evento['titolo']);
    $testo  = mysqli_real_escape_string($connect, $evento['testo'] ?? '');
    $ora    = mysqli_real_escape_string(
        $connect,
        $evento['ora'] ?? '00:00:00'
    );

    $check = mysqli_query($connect, "
        SELECT id FROM appuntamenti
        WHERE str_data='$data'
          AND ora='$ora'
          AND titolo='$titolo'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) === 0) {
        mysqli_query($connect, "
            INSERT INTO appuntamenti (titolo, testo, str_data, ora)
            VALUES ('$titolo', '$testo', '$data', '$ora')
        ");
        $importati++;
    } else {
        $scartati++;
    }
}
}

mysqli_close($connect);

header("Location: ./conferma.php?rif=Calendario2");
exit;
