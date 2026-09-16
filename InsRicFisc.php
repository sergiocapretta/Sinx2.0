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

if (file_exists($linguainsricfisc)) {
  include($linguainsricfisc);
}

// 🔹 Controllo livello utente
if ($user == 'admin') {
  $limit = '';
  $limite = '';
} elseif ($user == 'operatore') {
  $limit = 'disabled';
  $limite = '';
} elseif (in_array($user, ['associato', 'limitato'])) {

  // Funzione redirect
  function redirect($url, $tempo = false) {
    if (!headers_sent()) {
      if ($tempo) header("Refresh:$tempo; url=$url");
      else header("Location:$url");
      exit;
    } else {
      echo "<meta http-equiv='refresh' content='" . ($tempo ?: 0) . ";url=$url'>";
    }
  }

  echo "<div style='text-align:center; padding:20px;'>
          Il tuo utente ha un livello <b>$user</b>.<br>
          Area permessa solo all’utente <b>Admin</b>.
        </div>";
  redirect('./index2.php', 3);
  exit;
}

include('./top.inc');
include('./menu.inc');

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Impossibile connettersi al database.");

// 🔹 Recupero ultimo ID ricevuta
$Query = "SELECT MAX(id_ric) AS ultimo_id FROM tb_ricevute";
$Qultimoid = mysqli_query($connect, $Query);
$Tultimoid = mysqli_fetch_assoc($Qultimoid);
$ultimoid = $Tultimoid['ultimo_id'] ?? 0;
?>

<!-- 🔹 COLLEGAMENTO CSS -->
<link rel="stylesheet" href="/style.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitoloric; ?></h2>

    <form action="./InsRicFisc_exp.php" method="POST">
      <center>
        <button type="submit" class="btn-add" <?php echo $limit . ' ' . $limite; ?>>
          <i class="material-icons">print</i> <?php echo $Lstampcancmod; ?>
        </button>
      </center>
    </form>

    <hr class="divider">
    <h3 class="card-title"><?php echo $Lnuova; ?></h3>

    <!-- 🔹 Form inserimento ricevuta fiscale -->
    <form action="./conf_Ric_Fiscale.php" method="POST" class="sinx-form">
      <label><?php echo $Ldata; ?></label>
      <input type="text" name="data" value="<?php echo date('d-m-Y'); ?>">

      <label><?php echo $Lnumric; ?>:</label>
      <input type="text" value="<?php echo $ultimoid + 1; ?>" readonly>

      <label><?php echo $Lvocecontoec; ?>*</label>
      <select name="contoec" required>
        <option value=""><?php echo $Lcausale; ?></option>
        <?php
        $query = "SELECT descrizione FROM tb_conto_economico WHERE costoricavo='ricavi'";
        $rs = mysqli_query($connect, $query) or die("Errore nella query della Combo Conto Economico");
        while ($row = mysqli_fetch_row($rs)) {
          echo "<option>" . htmlspecialchars($row[0]) . "</option>";
        }
        ?>
      </select>
      <small><i><?php echo $Linserirecausale; ?></i></small>

      <label><?php echo $Lricevutoda; ?>*</label>
      <select name="insnome" required>
        <option value=""><?php echo $Lnome; ?></option>
        <?php
        $query = "SELECT cognome, nome FROM tb_anagrafe ORDER BY cognome";
        $rs = mysqli_query($connect, $query) or die("Errore nella query della Combo Anagrafe");
        while ($row = mysqli_fetch_row($rs)) {
          echo "<option>" . htmlspecialchars($row[0]) . " " . htmlspecialchars($row[1]) . "</option>";
        }
        ?>
      </select>

      <label><?php echo $Leuro; ?>*</label>
      <input type="number" name="euro" min="1" step="any" required>

      <fieldset>
        <legend><?php echo $Lmodalita . $Lcausale; ?></legend>
        <label><input type="radio" name="causale" value="preimpostata" checked> <?php echo $Lpreimpostata; ?></label>
        <label><input type="radio" name="causale" value="libera"> <?php echo $Llibera; ?></label>
      </fieldset>

      <label><?php echo $Lcausale . $Llibera; ?></label>
      <input type="text" name="descrizione">

      <label><?php echo $Lcausale . $Lpreimpostata; ?></label>
      <select name="causalepre">
        <option value="Tesseramento <?php echo date('Y'); ?> con avviso">
          <?php echo $Ltesseramentocscadenza; ?>
        </option>
        <option value="Tesseramento <?php echo date('Y'); ?> senza avviso">
          <?php echo $Ltesseramentonoscadenza; ?>
        </option>
      </select>

      <div class="form-buttons">
        <button name="invio" type="submit" class="btn-add" <?php echo $limit . ' ' . $limite; ?>>
          <i class="material-icons">send</i> <?php echo $Linvia; ?>
        </button>
      </div>
    </form>

    <hr class="divider">
    <h3 class="card-title"><?php echo $Lelencoricevute; ?></h3>

    <div class="appointments appointments-5col">
      <div class="appointments-header">
        <span><?php echo $Lnumric; ?></span>
        <span><?php echo $Ldata; ?></span>
        <span><?php echo $Lnome; ?></span>
        <span class="text-right"><?php echo $Leuro; ?></span>
        <span class="text-right"><?php echo $Ldescrizione; ?></span>
      </div>

      <?php
      $Query = "SELECT * FROM tb_ricevute ORDER BY id_ric";
      $rs = mysqli_query($connect, $Query) or die(mysqli_error($connect));

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id_ric']}</span>
                <span>{$row['data']}</span>
                <span>{$row['nome']}</span>
                <span class='text-right val-entrata'>{$row['euro']}</span>
                <span class='text-right'>{$row['descr']}</span>
              </div>";
      }
      ?>
    </div>

    <hr class="divider">
    <div class="text-center">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" style="width:40px;">
      <p><i><?php echo $Lhelpric; ?></i></p>
    </div>
  </div>
</div>

<?php
mysqli_close($connect);
include('./botton.inc');
?>
