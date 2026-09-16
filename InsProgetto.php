<?php
/*
 * Sinx for Association - Gestionale per Associazioni no-profit
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
*/

session_start();

$user = $_SESSION['utente'];
$langprogetto = $_SESSION['lingua'];
$paginaprogetto = "insprogetto.inc";
$linguaprogetto = ($langprogetto . $paginaprogetto);
include($linguaprogetto);

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// =============== ADMIN ===============
if ($user == 'admin') {
?>
<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $LtitoloProgetto; ?></h2>
    <p class="card-subtitle"><?php echo $Linsnuovoprogetto; ?><br><small><?php echo $Lsuggnuovoprogetto; ?></small></p>

    <!-- Pulsante gestione utenti -->
    <form action="./InsProgetto_exp.php" method="GET" style="text-align:center; margin-bottom:15px;">
      <button type="submit" class="btn-edit">Cancella | Modifica</button>
    </form>

    <!-- Form inserimento nuovo utente -->
    <form action="./conf_dati_progetto.php" method="POST" class="sinx-form">
    <h3>Nuovo Progetto o Attività</h3>
      <label><?php echo $Lnuovoprogetto; ?> *</label>
      <input type="text" name="nomeprog" placeholder="Nome progetto" required>

      <label><?php echo $Ldescrprogetto; ?> *</label>
      <input type="text" name="descprog" required>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add">
      </div>
    </form>

    <hr class="divider">

    <!-- Elenco progetti -->
    <h3 class="card-title"><?php echo $LlistaProgetti; ?></h3>
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><?php echo $LId; ?></span>
        <span><?php echo $LProgetto; ?></span>
        <span><?php echo $LDescrizione; ?></span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM tb_progetto ORDER BY id";
      $rs = mysqli_query($connect, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query.");

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id_progetto']}</span>
                <span>{$row['nome']}</span>
                <span>{$row['descr']}</span>
              </div>";
      }
      ?>
    </div>

    <hr class="divider">


    </form>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<img src="./Immagini/suggerimento.png" alt="Suggerimento">
<small><i><?php echo $Lhelpprogetto; ?></i></small>
<hr>

<?php
include('./botton.inc');


// =============== NON ADMIN ===============
} elseif ($user != 'admin') {
  ?>
   <h3 class="card-title"><?php echo $LlistaProgetti; ?></h3>
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><?php echo $LId; ?></span>
        <span><?php echo $LProgetto; ?></span>
        <span><?php echo $LDescrizione; ?></span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM tb_progetto ORDER BY id";
      $rs = mysqli_query($connect, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query.");

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id_progetto']}</span>
                <span>{$row['nome']}</span>
                <span>{$row['descr']}</span>
              </div>";
      }

mysqli_close($connect);
include('./menusx.inc');
?>
    </div>
<hr>
<img src="./Immagini/suggerimento.png" alt="Suggerimento">
<small><i><?php echo $Lhelpprogettonoadmin; ?></i></small>
<hr>

<?php
include('./botton.inc');


// =============== NESSUN ACCESSO ===============
} else {
  function redirect($url, $tempo = FALSE) {
    if (!headers_sent() && !$tempo) {
      header('Location:' . $url);
    } elseif (!headers_sent()) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      echo "<meta http-equiv='refresh' content='{$tempo};{$url}'>";
    }
  }

  echo "<center>$Lsugg1utente<b>$user</b><br>$Lsugg2utente<b>Admin</b></center>";
  redirect('./index2.php', 3);
}
?>
