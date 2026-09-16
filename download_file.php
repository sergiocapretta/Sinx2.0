<?php
session_start();

if ($_SESSION['utente'] !== 'admin') {
  die('Accesso negato');
}

if (!isset($_GET['file'])) {
  die('File non specificato');
}

$file = urldecode($_GET['file']);

// sicurezza: consenti solo Download/
$baseDir = realpath('./Download');
$realFile = realpath($file);

if (!$realFile || strpos($realFile, $baseDir) !== 0 || !file_exists($realFile)) {
  die('File non valido');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($realFile) . '"');
header('Content-Length: ' . filesize($realFile));
header('Pragma: public');
header('Cache-Control: must-revalidate');

readfile($realFile);
exit;
