<?php
/*Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 by Sergio Capretta

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
*/

session_start();

if (!isset($_SESSION['utente']) || $_SESSION['utente'] != 'admin') {
  header('Location: ./index.php');
  exit;
}

if (isset($_POST['file'])) {
  $file = urldecode($_POST['file']);

  // Sicurezza: consentiamo l’eliminazione solo in specifiche cartelle
  $allowedDirs = ['Immagini/Utenti', 'Download'];
  $safe = false;

  foreach ($allowedDirs as $dir) {
    if (strpos(realpath($file), realpath($dir)) === 0) {
      $safe = true;
      break;
    }
  }

  if ($safe && file_exists($file)) {
    if (unlink($file)) {
      $msg = "File eliminato correttamente.";
    } else {
      $msg = "Errore: impossibile eliminare il file.";
    }
  } else {
    $msg = "Operazione non consentita o file inesistente.";
  }

  // Ritorna alla pagina principale
  header("Location: ./Files.php?msg=" . urlencode($msg));
  exit;
}
?>
