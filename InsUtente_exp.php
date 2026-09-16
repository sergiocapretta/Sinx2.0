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

if ($user == 'admin') {

  include('./top.inc');
  include('./menu.inc');
  include('./dati_db.inc');

  $connect = mysqli_connect($host, $username, $password, $db_name) or die("cannot connect DB");

  // Recupero ultimo id
  $Query = "SELECT MAX(id) AS ultimo_id FROM utenti";
  $Qultimoid = mysqli_query($connect, $Query);
  $Tultimoid = mysqli_fetch_array($Qultimoid);
  $ultimoid = $Tultimoid['ultimo_id'];
  ?>
  <h2 class="card-title"><?php echo $Ltitoloutente; ?></h2>

  <div class="content-section">
    <div class="card">
      <h3 class="card-title"><?php echo $Lcancutente; ?></h3>
      <p class="card-subtitle"><?php echo $Lsuggcancutente; ?></p>

      <!-- === CANCELLAZIONE UTENTE === -->
      <form action="./conf_canc.php?Tabella=utenti&Riferimento=id" method="POST" class="sinx-form">
        <label for="id_mod"><?php echo $LId; ?> *</label>

          <select name="id_mod" id="id_mod" required>
            <option value="" selected>Seleziona ID utente...</option>
            <?php
            for ($a = 1; $a <= $ultimoid; $a++) {
              $query = "SELECT id FROM utenti WHERE id = $a LIMIT 1";
              $rs = mysqli_query($connect, $query);
              while ($row = mysqli_fetch_row($rs)) {
                echo "<option>" . $row[0] . "</option>";
              }
            }
            ?>
          </select>
          <input type="submit" value="- Cancella -" class="btn-delete">

      </form>
	</div>
</div>

      <hr class="divider">

      <!-- === MODIFICA UTENTE === -->
<div class="content-section">
	<div class="card">
      <h3 class="card-title"><?php echo $Lmodutente; ?></h3>
      <p class="card-subtitle"><?php echo $Lsuggmodutente; ?></p>

      <form action="./conf_mod_utenti.php" method="POST" class="sinx-form">
        <label>ID *</label>
        <input type="number" name="id_mod" required placeholder="Inserisci ID da modificare">

        <label><?php echo $Lcampoutente; ?> *</label>
        <select name="campo" required>
          <option value="" selected><?php echo $Lsuggcampoutente; ?></option>
          <option value="utente"><?php echo $Lnomeutente; ?></option>
          <option value="nome"><?php echo $Llivelloutente; ?></option>
          <option value="pswd">Password</option>
        </select>

        <label><?php echo $Lnuovorecordutente; ?> *</label>
        <input type="text" name="record" required placeholder="Nuovo valore">

        <div class="form-buttons">
          <input type="submit" value="Modifica" class="btn-edit">
        </div>
      </form>
	</div>
</div>

      <hr class="divider">

      <!-- === ELENCO UTENTI === -->
<div class="content-section">
  <div class="card">
      <h3 class="card-title">Elenco utenti</h3>

      <?php
      $Query_nome = "SELECT * FROM utenti ORDER BY id";
      $rs = mysqli_query($connect, $Query_nome)
        or die("<b>Errore:</b> Impossibile eseguire la query.");

      echo '<div class="appointments appointments-4col" style="margin-top:20px;">
              <div class="appointments-header">
                <span>ID</span>
                <span>' . $Lutenteutente . '</span>
                <span>' . $Llivelloutente . '</span>
                <span></span>
              </div>';

      while ($row = mysqli_fetch_array($rs)) {
        echo '<div class="appointment-item">
                <span>' . $row['id'] . '</span>
                <span>' . htmlspecialchars($row['utente']) . '</span>
                <span>' . htmlspecialchars($row['nome']) . '</span>
                <span></span>
              </div>';
      }
      echo '</div>';

      mysqli_close($connect);
      ?>

      <div style="text-align:center; margin-top:30px;">
        <img src="./Immagini/suggerimento.png" width="40" alt="help">
        <small><i><?php echo $Lhelputente; ?></i></small>
      </div>
    </div>
  </div>

  <!-- PULSANTE RITORNO GESTIONE UTENTI -->
  <div class="form-buttons" style="margin-top:30px;">
  <a href="./InsUtente.php" class="btn-add" style="text-decoration:none; text-align:center;">
    ← Torna alla gestione utenti
  </a>
</div>

  <?php
  include('./menusx.inc');
  include('./botton.inc');

} else {
  // Se non è admin, redirect
  function redirect($url, $tempo = FALSE)
  {
    if (!headers_sent() && $tempo == FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo == FALSE) $tempo = 0;
      echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
    }
  }

  echo "<center>$Lsugg1utente <b>$user</b><br>$Lsugg2utente <b>Admin</b></center>";
  redirect('./InsUtente.php', 3);
}
?>
