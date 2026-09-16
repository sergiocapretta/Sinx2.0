<?php
/*
======================================================================+
Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 - 2025 by Sergio Capretta

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
=========================================================================+
*/

session_start();

$user = $_SESSION['utente'];
if ($user !== 'admin') {
  header('Location: ./Redirect_no_enter.php');
  exit;
}

include('dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Impossibile connettersi al database");
mysqli_set_charset($connect, "utf8mb4");

// === CARTELLA BACKUP ===
$backupDir = __DIR__ . '/backups';
if (!file_exists($backupDir)) {
  mkdir($backupDir, 0775, true);
}

// === FUNZIONE DUMP TABELLA ===
function datadump($table, $connect)
{
  $out = "";
  $out .= "# Dump of `$table`\n";
  $out .= "# Date: " . date("Y-m-d H:i:s") . "\n\n";

  // Aggiungiamo l'istruzione DROP TABLE per prevenire problemi di ID duplicati
  $out .= "DROP TABLE IF EXISTS `$table`;\n\n"; // <-- NUOVA RIGA CRITICA!

  // --- Struttura tabella ---
  $resCreate = mysqli_query($connect, "SHOW CREATE TABLE `$table`");
  if ($resCreate && ($rowCreate = mysqli_fetch_assoc($resCreate))) {
    $createTable = $rowCreate['Create Table'];

    // Correzione Charset/Collation mantenuta (importante per l'encoding)
    $createTable = preg_replace(
        '/CHARSET=\w+( COLLATE=\w+)?/',
        'CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        $createTable
    );

    $out .= $createTable . ";\n\n";
  }

  // --- Dati tabella ---
  $res = mysqli_query($connect, "SELECT * FROM `$table`");
  if (!$res) return $out . "\n\n";

  $fields = mysqli_fetch_fields($res);
  $columns = array_map(fn($f) => "`{$f->name}`", $fields);
  $colsList = implode(", ", $columns);

  static $setNamesDone = false;
  if (!$setNamesDone) {
    $out = "SET NAMES utf8mb4;\n\n" . $out;
    $setNamesDone = true;
  }

  while ($row = mysqli_fetch_assoc($res)) {
    $vals = [];
    foreach ($row as $v) {
      if (is_null($v)) {
        $vals[] = "NULL";
      } else {
        // Correzione precedente mantenuta: usiamo SOLO mysqli_real_escape_string
        $v = mysqli_real_escape_string($connect, $v);
        $vals[] = "'" . $v . "'";
      }
    }
    $out .= "INSERT INTO `$table` ($colsList) VALUES(" . implode(", ", $vals) . ");\n";
  }

  return $out . "\n\n";
}

// === ELIMINAZIONE BACKUP ===
if (isset($_GET['delete'])) {
  $fileToDelete = basename($_GET['delete']);
  $path = $backupDir . '/' . $fileToDelete;
  if (is_file($path)) {
    unlink($path);
  }
  header("Location: backup_database.php");
  exit;
}

// === DOWNLOAD BACKUP SALVATO ===
if (isset($_GET['download'])) {
  $fileToDownload = basename($_GET['download']);
  $path = $backupDir . '/' . $fileToDownload;
  if (is_file($path)) {
    header("Content-Type: application/sql");
    header("Content-Disposition: attachment; filename=\"$fileToDownload\"");
    readfile($path);
    exit;
  }
}

// === ESECUZIONE BACKUP ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup'])) {
  $result_tabelle = mysqli_query($connect, "SHOW TABLES");
  $file_name = "Sinx_Backup_" . date('Y-m-d_H-i') . ".sql";
  $backupContent = "-- Backup Database Sinx\n-- Data: " . date("Y-m-d H:i:s") . "\n\n";

  while ($r = mysqli_fetch_row($result_tabelle)) {
    $backupContent .= datadump($r[0], $connect);
  }

  // Se è selezionata l'opzione "salva su server"
  if (isset($_POST['save_server'])) {
    file_put_contents($backupDir . '/' . $file_name, $backupContent);
  }

  // Download immediato
  header("Content-Type: application/sql; charset=utf-8");
  header("Content-Disposition: attachment; filename=\"$file_name\"");
  echo $backupContent;
  mysqli_close($connect);
  exit;
}

// === LISTA BACKUP SALVATI ===
$backupFiles = array_diff(scandir($backupDir, SCANDIR_SORT_DESCENDING), ['.', '..']);

include('./top.inc');
include('./menu.inc');
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Backup Database Sinx</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="content-section">
  <div class="card">
    <h2 class="card-title">💾 Backup del Database Sinx</h2>
    <p class="card-subtitle">
      Crea una copia completa del database in formato SQL per il salvataggio o la migrazione.
    </p>

    <form action="" method="POST" class="sinx-form">
      <label><input type="checkbox" name="save_server" checked> Salva anche sul server</label>
      <p><b>Attenzione:</b> il file generato conterrà tutti i dati presenti nel gestionale.</p>

      <div class="form-buttons">
        <input type="submit" name="backup" value="📥 Esegui Backup" class="btn-add">
        </div>
         <div class="form-buttons">
        <a href="restore_db.php" class="btn-edit">🔄 Ripristina Database</a>
         </div>
         <div class="form-buttons">
        <a href="./index2.php" class="btn-delete">⬅️ Torna al Pannello</a>
      </div>
    </form>

    <hr class="divider">

    <h3 class="card-title">📂 Backup salvati sul server</h3>
    <div class="appointments appointments-5colbac">
<div class="appointments-header">
  <span>#</span>
  <span>Data file</span>
  <span>Nome file</span>
  <span>Dimensione</span>
  <span>Azioni</span>
</div>

      <?php
      if (empty($backupFiles)) {
        echo "<div class='appointment-item'><span>-</span><span colspan='3'>Nessun backup presente.</span></div>";
      } else {
        $i = 1;
        foreach ($backupFiles as $file) {
          $filePath = $backupDir . '/' . $file;
          $date = date("d/m/Y H:i", filemtime($filePath));
$size = filesize($filePath);
$sizeKB = round($size / 1024, 2) . " KB";

echo "<div class='appointment-item'>
        <span>$i</span>
        <span>$date</span>
        <span>$file</span>
        <span>$sizeKB</span>
        <span>
          <a href='?download=$file' class='btn-mini'>⬇️ Scarica</a>
          <a href='?delete=$file' class='btn-mini' onclick='return confirm(\"Eliminare definitivamente $file?\")'>🗑️ Elimina</a>
        </span>
      </div>";
          $i++;
        }
      }
      ?>
    </div>

    <hr class="divider">
    <div class="card-subtitle" style="text-align:center;">
      <small><i>Operazione riservata all’amministratore del sistema.</i></small>
    </div>
  </div>
</section>

<?php
include('./menusx.inc');
include('./botton.inc');
mysqli_close($connect);
?>
</body>
</html>
