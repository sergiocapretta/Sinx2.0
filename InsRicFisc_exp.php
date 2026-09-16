<?php
/*======================================================================+
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
=========================================================================+*/
session_start();
$user = $_SESSION['utente'] ?? '';
$langinsricfisc = $_SESSION['lingua'] ?? '';
$paginainsricfisc = "insricfisc.inc";
$linguainsricfisc = $langinsricfisc . $paginainsricfisc;

include($linguainsricfisc);
include('./top.inc');
include('./menu.inc');

// === Controllo permessi utente ===
if ($user == 'admin') {
  $limit = '';
  $limite = '';
} elseif ($user == 'operatore') {
  $limit = 'disabled';
  $limite = '';
} elseif ($user == 'associato' || $user == 'limitato') {

  // Funzione di redirect compatibile
  function redirect($url, $tempo = FALSE) {
    if (!headers_sent() && $tempo == FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      $tempo = $tempo ?: 0;
      echo "<meta http-equiv=\"refresh\" content=\"$tempo;url=$url\">";
    }
  }

  echo "<div class='content-section'>
          <div class='card'>
            <h3>Accesso negato</h3>
            <p>Il tuo utente ha un livello <b>$user</b><br>
            Area permessa solo all'utente <b>Admin</b></p>
          </div>
        </div>";
  redirect('./index2.php', 3);
  exit;
}

// === Connessione al database ===
include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// Recupero ultimo ID ricevuta
$Query = "SELECT MAX(id_ric) AS ultimoid FROM tb_ricevute";
$Qultimoid = mysqli_query($connect, $Query);
$ultimoid = ($row = mysqli_fetch_assoc($Qultimoid)) ? $row['ultimoid'] : 0;
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?php echo $Ltitoloric ?? 'Gestione Ricevute'; ?></title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitoloric; ?></h2>
    <p class="card-subtitle"><?php echo $Lnotaric; ?></p>

    <h3><?php echo $Lstampa; ?></h3>
    <form action="./stampa_rfisc.php" method="POST" target="_blank" class="sinx-form" value="foglio">
 <!--     <label>
        <input type="radio" name="tipo" value="immagine" checked>
        Stampa vista immagine
      </label>
      <label>
        <input type="radio" name="tipo" value="foglio">
        Stampa vista pagina
      </label>
-->
      <label for="numero"><?php echo $Lnumero; ?>*</label>
      <select name="numero" id="numero" required>
        <option value=""></option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $rs = mysqli_query($connect, "SELECT id_ric FROM tb_ricevute WHERE id_ric = $a LIMIT 1");
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>{$row[0]}</option>";
          }
        }
        ?>
      </select>

      <button type="submit" class="btn-add" <?php echo $limit; ?>>
        <i class="material-icons">print</i> Stampa
      </button>
    </form>
  </div>
</div>

<hr class="divider">

<!-- === CANCELLAZIONE === -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lcancella; ?></h3>
    <p class="card-subtitle"><?php echo $Listrcancella; ?></p>

    <form action="./conf_canc_ric.php" method="POST" class="sinx-form">
      <label for="id_canc"><?php echo $Lnumric; ?>*</label>
      <select name="id_canc" id="id_canc" required>
        <option value=""></option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $rs = mysqli_query($connect, "SELECT id_ric FROM tb_ricevute WHERE id_ric = $a LIMIT 1");
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>{$row[0]}</option>";
          }
        }
        ?>
      </select>

      <label for="contoec"><?php echo $Lvocecontoec; ?>*</label>
      <select name="contoec" id="contoec" required>
        <option value=""><?php echo $Lcausale; ?></option>
        <?php
        $rs = mysqli_query($connect, "SELECT descrizione FROM tb_conto_economico");
        while ($row = mysqli_fetch_row($rs)) {
          echo "<option>{$row[0]}</option>";
        }
        ?>
      </select>

      <button type="submit" class="btn-delete" <?php echo $limit; ?>>
        <i class="material-icons">delete</i> Cancella
      </button>
    </form>
  </div>
</div>

<hr class="divider">

<!-- === MODIFICA === -->
<div class="content-section">
  <div class="card">
  <h3 class="card-title"><?php echo $Lmodifica; ?></h3>
    <p class="card-subtitle"><?php echo $Listrmodifica; ?></p>

    <form action="./conf_mod_ric.php" method="POST" class="sinx-form">
      <label for="id_mod"><?php echo $Lnumric; ?>*</label>
      <select name="id_mod" id="id_mod" required>
        <option value=""></option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $rs = mysqli_query($connect, "SELECT id_ric FROM tb_ricevute WHERE id_ric = $a LIMIT 1");
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>{$row[0]}</option>";
          }
        }
        ?>
      </select>

      <label for="campo"><?php echo $Lcampo; ?>*</label>
      <select name="campo" id="campo" required>
        <option value=""></option>
        <option value="nome"><?php echo $Lnome; ?></option>
        <option value="data"><?php echo $Ldata; ?></option>
        <option value="euro"><?php echo $Lprezzo; ?></option>
        <option value="descr"><?php echo $Ldescrizione; ?></option>
      </select>

      <label for="record"><?php echo $Lnuovorecord; ?>*</label>
      <input type="text" name="record" id="record" required>

      <button type="submit" class="btn-edit" <?php echo $limit; ?>>
        <i class="material-icons">edit</i> Modifica
      </button>
    </form>
  </div>
</div>

<hr class="divider">

<!-- === ELENCO RICEVUTE === -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lelencoricevute; ?></h3>

    <div class="appointments appointments-receipts">
      <div class="appointments-header">
        <span><?php echo $Lnumric; ?></span>
        <span><?php echo $Ldata; ?></span>
        <span><?php echo $Lnome; ?></span>
        <span><?php echo $Leuro; ?></span>
        <span><?php echo $Ldescrizione; ?></span>
      </div>

      <?php
      $Query = "SELECT * FROM tb_ricevute ORDER BY id_ric";
      $rs = mysqli_query($connect, $Query);
      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='receipt-item'>
                <span>{$row['id_ric']}</span>
                <span>{$row['data']}</span>
                <span>{$row['nome']}</span>
                <span>{$row['euro']}</span>
                <span>{$row['descr']}</span>
              </div>";
      }
      ?>
    </div>
  </div>
</div>

    <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./InsRicFisc.php" method="GET">
        <input type="submit" value="Ritorna alle Ricevute" class="btn-edit">
      </form>
    </div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<div class="content-section">
  <img src="./Immagini/suggerimento.png" alt="Suggerimento">
  <small><i><?php echo $Lhelpric; ?></i></small>
</div>

<?php include('./botton.inc'); ?>
</body>
</html>
