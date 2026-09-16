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

$user = $_SESSION['utente'];
$langinsprimanota = $_SESSION['lingua'];
$paginainsprimanota = "insprimanota.inc";
$linguainsprimanota = ($langinsprimanota.$paginainsprimanota);
include($linguainsprimanota);

// 🔹 Controllo permessi
if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'operatore') {
  $limit = 'disabled'; $limite = '';
} elseif ($user == 'associato' || $user == 'limitato') {
  function redirect($url, $tempo = 0) {
    if (!headers_sent()) header("Refresh: $tempo; URL=$url");
    else echo "<meta http-equiv='refresh' content='$tempo;url=$url'>";
  }
  echo "<center>Il tuo utente ha un livello <b>$user</b><br>Area riservata solo all'utente <b>Admin</b></center>";
  redirect('./index2.php', 3);
  exit;
}

include('./top.inc');
include('./menu.inc');

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port) or die("cannot connect DB");

// Recupero ID massimi
function getMaxId($connect, $table, $field = 'id') {
  $res = mysqli_query($connect, "SELECT MAX($field) AS maxid FROM $table");
  $row = mysqli_fetch_assoc($res);
  return $row['maxid'] ?? 0;
}
$ultimoid = getMaxId($connect, 'tb_primanota', 'id_primanota');
$ultimoidce = getMaxId($connect, 'tb_conto_economico');
$ultimoidsp = getMaxId($connect, 'tb_stato_patrimoniale');
?>

<!-- 🔹 TITOLO PRINCIPALE -->
<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitoloprimanota; ?></h2>
    <p class="card-subtitle"><?php echo $Lnota; ?></p>
    <div class="text-center">
    <center>
      <a class="link-btn" href="./stampa_pnota.php" target="_blank"><?php echo $Lstampa; ?></a> |
    </center>
    </div>
  </div>
</div>

<hr class="divider">

<!-- 🔹 SEZIONE CANCELLAZIONE -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lcancella; ?></h3>
    <p class="card-subtitle"><?php echo $Linserisciid; ?></p>

    <form action="./conf_canc_pnota.php" method="POST" class="sinx-form">
      <label>ID Prima Nota *</label>
      <select name="id_canc" required>
        <option value="">--</option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $q = mysqli_query($connect, "SELECT id_primanota FROM tb_primanota WHERE id_primanota=$a LIMIT 1");
          if ($row = mysqli_fetch_row($q)) echo "<option>{$row[0]}</option>";
        }
        ?>
      </select>

      <label>Voce Conto Economico *</label>
      <select name="nomecontoec" required>
        <option value="">--</option>
        <?php
        for ($a = 1; $a <= $ultimoidce; $a++) {
          $q = mysqli_query($connect, "SELECT descrizione FROM tb_conto_economico WHERE id=$a LIMIT 1");
          if ($row = mysqli_fetch_row($q)) echo "<option>{$row[0]}</option>";
        }
        ?>
      </select>

      <div class="form-buttons">
        <button type="submit" class="btn-delete" <?php echo $limit; ?>>Cancella</button>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 SEZIONE MODIFICA -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lmodifica; ?></h3>
    <p class="card-subtitle"><?php echo $Linserisciidmodifica; ?></p>

    <form action="./conf_mod_pnota.php" method="POST" class="sinx-form">
      <label><?php echo $Lnumero; ?> *</label>
      <select name="id_mod" required>
        <option value="">--</option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $q = mysqli_query($connect, "SELECT id_primanota FROM tb_primanota WHERE id_primanota=$a LIMIT 1");
          if ($row = mysqli_fetch_row($q)) echo "<option>{$row[0]}</option>";
        }
        ?>
      </select>

      <label><?php echo $Lcampo; ?> *</label>
      <select name="campo" required>
        <option value="">--</option>
        <option value="data_registr"><?php echo $Ldata; ?></option>
        <option value="descrizione"><?php echo $Ldescrizione; ?></option>
        <option value="entrata"><?php echo $Lentratacassa; ?></option>
        <option value="uscita"><?php echo $Luscitacassa; ?></option>
        <option value="entratab"><?php echo $Lentratabanca; ?></option>
        <option value="uscitab"><?php echo $Luscitabanca; ?></option>
      </select>

      <label><?php echo $Lnuovorecord; ?> *</label>
      <input type="text" name="record" required>

      <div class="form-buttons">
        <button type="submit" class="btn-edit" <?php echo $limit; ?>>Modifica</button>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 TABELLA PRIMA NOTA -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lprimanota; ?></h3>

    <div class="appointments appointments-5colpn">
      <div class="appointments-header">
        <span><b><?php echo $Lnumero; ?></b></span>
        <span><b><?php echo $Ldata; ?></b></span>
        <span><b><?php echo $Ldescrizione; ?></b></span>
        <span><b><?php echo $Lcassa; ?></b></span>
        <span><b><?php echo $Lbanca; ?></b></span>
      </div>

      <?php
      $rs = mysqli_query($connect, "SELECT * FROM tb_primanota ORDER BY id_primanota");
      while ($row = mysqli_fetch_assoc($rs)) :
      ?>
        <div class="appointment-item">
          <span><?php echo $row['id_primanota']; ?></span>
          <span><?php echo $row['data_registr']; ?></span>
          <span><?php echo htmlspecialchars($row['descrizione']); ?></span>
          <span>
            <?php
            if (!empty($row['entrata']) && $row['entrata'] > 0)
              echo "<span class='val-entrata'>{$row['entrata']}</span><br>";
            if (!empty($row['uscita']) && $row['uscita'] > 0)
              echo "<span class='val-uscita'>{$row['uscita']}</span>";
            ?>
          </span>
          <span>
            <?php
            if (!empty($row['entratab']) && $row['entratab'] > 0)
              echo "<span class='val-entrata'>{$row['entratab']}</span><br>";
            if (!empty($row['uscitab']) && $row['uscitab'] > 0)
              echo "<span class='val-uscita'>{$row['uscitab']}</span>";
            ?>
          </span>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<hr class="divider">

<!-- 🔹 TABELLA TOTALI -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Ltotali; ?></h3>

    <?php
    // Totali CASSA
    $entrata_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entrata) FROM tb_primanota"))[0];
    $uscita_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscita) FROM tb_primanota"))[0];

    // Totali BANCA
    $entratab_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entratab) FROM tb_primanota"))[0];
    $uscitab_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscitab) FROM tb_primanota"))[0];
    ?>

    <table width="100%">
      <tr><td><b><?php echo $Lcassa; ?></b></td><td align="right"><?php echo $entrata_tot - $uscita_tot; ?></td></tr>
      <tr><td><b><?php echo $Lbanca; ?></b></td><td align="right"><?php echo $entratab_tot - $uscitab_tot; ?></td></tr>
    </table>
  </div>
</div>

    <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./InsPrimanota.php" method="GET">
        <input type="submit" value="Ritorna alla Prima Nota" class="btn-edit">
      </form>
    </div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<center>
<img src="./Immagini/suggerimento.png" style="vertical-align:middle;">
<small><i><?php echo $Lhelpprimanota; ?></i></small>
</center>
<?php include('./botton.inc'); ?>
