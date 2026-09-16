<?php
/*======================================================================+
 File name   : conf_immagine.php
 Begin       : 2012-07-08
 Last Update : 2025-11-07
 Description : upload e rinomina immagine logo per installazione
 Author      : Sergio Capretta
=========================================================================+*/

/**
 * Redirect HTTP o via meta refresh
 */
function redirect(string $url, int $tempo = 0): void {
    if (!headers_sent()) {
        if ($tempo > 0) {
            header("Refresh: {$tempo}; url={$url}");
        } else {
            header("Location: {$url}");
        }
        exit;
    }
    echo "<meta http-equiv=\"refresh\" content=\"{$tempo}; url={$url}\">";
    exit;
}

// === GESTIONE UPLOAD IMMAGINE ===
$upload_dir = "../Immagini";
$input_name = "immagine";

if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
    echo "Errore durante l'upload del file.";
    redirect('./Install2.php', 2);
}

$tmp_file = $_FILES[$input_name]['tmp_name'];
$file_name = basename($_FILES[$input_name]['name']);
$target_path = "{$upload_dir}/{$file_name}";

// Controllo base tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $tmp_file);
finfo_close($finfo);
$allowed = ['image/png', 'image/jpeg', 'image/gif'];

if (!in_array($mime, $allowed, true)) {
    echo "Tipo di file non valido ({$mime}). Sono accettate solo immagini PNG, JPG, GIF.";
    redirect('./Install2.php', 3);
}

// Creazione dir se non esiste
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Spostamento file
if (!move_uploaded_file($tmp_file, $target_path)) {
    die("Impossibile spostare il file. Controlla i permessi della directory di upload.");
}

// Rinomina in logo.png
$new_name = "{$upload_dir}/logo.png";
if (!rename($target_path, $new_name)) {
    echo "Problema con la rinomina del file.";
    redirect('./Install2.php', 2);
}

echo "Immagine caricata e rinominata con successo.";
redirect('./Install3.php', 2);
?>
