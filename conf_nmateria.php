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

if (!isset($_SESSION['utente'])) {
  header('Location: ./index.php');
  exit;
}

$user = $_SESSION['utente'];
$nmateria = trim($_POST['nrecord'] ?? '');
$materia = htmlspecialchars($nmateria, ENT_QUOTES, "UTF-8");

// --- Funzione redirect ---
function redirect($url, $tempo = 0) {
  if (!headers_sent()) {
    if ($tempo > 0) {
      header("Refresh: $tempo; URL=$url");
    } else {
      header("Location: $url");
    }
  } else {
    echo "<meta http-equiv='refresh' content='$tempo;url=$url'>";
  }
  exit;
}

// --- Connessione DB ---
include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Impossibile connettersi al database.");

// --- Recupero parametri da GET ---
$tb_materia = $_GET['Tabella'] ?? '';
$colonna = $_GET['Colonna'] ?? '';
$pagina = $_GET['Pagina'] ?? 'index';

// --- Validazione input ---
if (empty($materia)) {
  echo "<div class='sinx-form' style='text-align:center; margin-top:50px;'>
          <h2 style='color:red;'>Inserimento non valido</h2>
          <p>Verifica di aver compilato correttamente tutti i campi.</p>
        </div>";
  redirect("$pagina.php", 2);
}

// --- Inserimento dati ---
if (!empty($materia) && !empty($tb_materia) && !empty($colonna)) {
  $stmt = mysqli_prepare($connect, "INSERT INTO `$tb_materia` (`$colonna`) VALUES (?)");
  mysqli_stmt_bind_param($stmt, 's', $materia);
  $result = mysqli_stmt_execute($stmt);

  if ($result) {
    header("Location: ./conferma.php?rif=$pagina");
  } else {
    echo "<div class='sinx-form' style='text-align:center; margin-top:50px;'>
            <h2 style='color:red;'>Errore durante l'inserimento</h2>
            <p>" . mysqli_error($connect) . "</p>
          </div>";
    redirect("./errore.php?rif=$pagina", 2);
  }

  mysqli_stmt_close($stmt);
}

mysqli_close($connect);
?>
