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
$langutente = $_SESSION['lingua'];
$paginautente = "insutente.inc";
$linguautente = ($langutente . $paginautente);
include($linguautente);

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
    <h2 class="card-title"><?php echo $Ltitoloutente; ?></h2>
    <p class="card-subtitle"><?php echo $Linsnuovoutente; ?><br><small><?php echo $Lsuggnuovoutente; ?></small></p>

    <!-- Pulsante gestione utenti -->
    <form action="./InsUtente_exp.php" method="GET" style="text-align:center; margin-bottom:15px;">
      <button type="submit" class="btn-edit">Cancella | Modifica</button>
    </form>

    <!-- Form inserimento nuovo utente -->
    <form action="./conf_dati_utente.php" method="POST" class="sinx-form">
    <h3>Nuovo utente</h3>
      <label><?php echo $Lnuovoutente; ?> *</label>
      <input type="text" name="nomeut" placeholder="Nome Utente" required>

      <label><?php echo $Llivelloutente; ?> *</label>
      <select name="livello" required>
        <option value=""><?php echo $Lsuggcampoutente; ?></option>
        <option value="admin"><?php echo $Lamministrutente; ?></option>
        <option value="operatore"><?php echo $Loperatoreutente; ?></option>
        <option value="associato"><?php echo $Loperatorelimitato; ?></option>
        <option value="limitato"><?php echo $Llimitatoutente; ?></option>
      </select>

      <label><?php echo $Lpasswdutente; ?> *</label>
      <input type="password" name="passwd" required>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add">
      </div>
    </form>

    <hr class="divider">

    <!-- Elenco utenti -->
    <h3 class="card-title"><?php echo $LlistaUtenti ?? 'Elenco Utenti'; ?></h3>
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>ID</span>
        <span><?php echo $Lutenteutente; ?></span>
        <span><?php echo $Llivelloutente; ?></span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM utenti ORDER BY id";
      $rs = mysqli_query($connect, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query.");

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id']}</span>
                <span>{$row['utente']}</span>
                <span>{$row['nome']}</span>
              </div>";
      }
      ?>
    </div>

    <hr class="divider">

    <!-- Note personali -->
    <?php
    $nutente = $user;
    $Query_note = "SELECT * FROM utenti WHERE utente = '$nutente'";
    $rs = mysqli_query($connect, $Query_note) or die("<b>Errore:</b> Impossibile eseguire la query delle note.");
    $riga = mysqli_fetch_assoc($rs);
    ?>
    <h3 class="card-title">Note personali</h3>
    <p class="card-subtitle">Compilazione e lista delle note</br><i>Queste note le puoi vedere solamente tu</i></p>

    <form action="./conf_note_utenti.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <input type="text" name="nome" value="<?php echo $riga['id']; ?>" readonly>
      <textarea name="formcontent" rows="10" class="note-area"><?php echo htmlspecialchars($riga['note']); ?></textarea>

      <div class="form-buttons">
        <input type="submit" value="- Registra Nota -" class="btn-add">
      </div>
    </form>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<img src="./Immagini/suggerimento.png" alt="Suggerimento">
<small><i><?php echo $Lhelputente; ?></i></small>
<hr>

<?php
include('./botton.inc');


// =============== NON ADMIN ===============
} elseif ($user != 'admin') {

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

$user = $_SESSION['utente'];
$nutente = $_SESSION['nome'];
$Query_nome = "SELECT * FROM utenti WHERE utente = '$nutente'";
$rs = mysqli_query($connect, $Query_nome) or die("Errore query");
$riga = mysqli_fetch_assoc($rs);
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $nutente; ?></h2>

    <form action="./conf_mod_utenti.php" method="POST" class="sinx-form">
      <label>ID</label>
      <input type="text" name="id_mod" value="<?php echo $riga['id']; ?>" readonly>

      <label><?php echo $Lcampoutente; ?></label>
      <select name="campo" required>
        <option value=""><?php echo $Lsuggcampoutente; ?></option>
        <option value="utente"><?php echo $Lnomeutente; ?></option>
        <option value="pswd">Password</option>
      </select>

      <label><?php echo $Lnuovorecordutente; ?></label>
      <input type="text" name="record" required>

      <div class="form-buttons">
        <input type="submit" value="Modifica" class="btn-edit">
      </div>
    </form>

    <hr class="divider">

    <h3 class="card-title">Note personali</h3>
    <p class="card-subtitle">Compilazione e lista delle note </br><i>Queste note le puoi vedere solamente tu</i></p>

    <form action="./conf_note_utenti.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <input type="text" name="nome" value="<?php echo $riga['id']; ?>" readonly>
      <textarea name="formcontent" rows="10" class="note-area"><?php echo htmlspecialchars($riga['note']); ?></textarea>

      <div class="form-buttons">
        <input type="submit" value="- Registra Nota -" class="btn-add">
      </div>
    </form>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<img src="./Immagini/suggerimento.png" alt="Suggerimento">
<small><i><?php echo $Lhelputentesingolo; ?></i></small>
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
