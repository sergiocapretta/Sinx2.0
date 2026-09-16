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
$langinsprimanota = $_SESSION['lingua'] ?? 'it_';
$paginainsprimanota = "insprimanota.inc";
$linguainsprimanota = $langinsprimanota . $paginainsprimanota;
include($linguainsprimanota);

if ($user == 'admin') {
  $limit = '';
  $limite = '';
} elseif ($user == 'operatore') {
  $limit = 'disabled';
  $limite = '';
} elseif (in_array($user, ['associato', 'limitato'])) {
  function redirect($url, $tempo = false) {
    if (!headers_sent()) {
      if ($tempo === false) header("Location: $url");
      else header("Refresh: $tempo; URL=$url");
      exit;
    } else {
      echo "<meta http-equiv='refresh' content='" . ($tempo ?: 0) . ";url=$url'>";
      exit;
    }
  }
  echo "<center>Il tuo utente ha un livello <b>$user</b><br>Area permessa solo all'utente <b>Admin</b></center>";
  redirect('./index2.php', 3);
}

include('./top.inc');
include('./menu.inc');

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Errore di connessione al database.");
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Prima Nota</title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>

<body>
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Ltitoloprimanota; ?></h3>
    <p class="card-subtitle"><?php echo $Lnota; ?></p>

    <form action="./InsPrimanota_exp.php" method="post">
      <center>
        <button name="stampa" type="submit" class="btn-add" <?php echo $limite . ' ' . $limit; ?>>
          <i class="material-icons">print</i> <?php echo $LStampaCancellaModifica; ?>
        </button>
      </center>
    </form>

    <hr class="divider">

    <h4 class="card-title"><?php echo $Lnuovomovimento; ?></h4>
    <form action="./conf_dati_pnota.php" method="POST" class="sinx-form">

      <label><?php echo $Ldata; ?>:
        <input name="data" type="text" required value="<?php echo date('d-m-Y'); ?>">
      </label>

      <label><?php echo $Lvocecontoec; ?>:
        <select name="contoec" required>
          <option value=""><?php echo $Lcausale; ?></option>
          <?php
            $query = "SELECT descrizione FROM tb_conto_economico";
            $rs = mysqli_query($connect, $query) or die("Errore query: " . mysqli_error($connect));
            while ($row = mysqli_fetch_row($rs)) {
              echo "<option>" . htmlspecialchars($row[0]) . "</option>";
            }
          ?>
        </select>
      </label>

      <label><?php echo $Loperazione; ?>:
        <input name="operazione" type="text" size="30">
      </label>

      <label><?php echo $Limporto; ?>:
        <input name="valore" type="text" size="10">
        <small><i><?php echo $Lsuggdecimali; ?></i></small>
      </label>

      <fieldset>
        <legend><?php echo $Ltipomovimento; ?></legend>

        <b><?php echo $Lcassa; ?></b><br>
        <input type="radio" name="conto" value="entrata" checked> <?php echo $Lentrata; ?> -
        <input type="radio" name="conto" value="uscita"> <?php echo $Luscita; ?><br><br>

        <b><?php echo $Lbanca; ?></b><br>
        <input type="radio" name="conto" value="entratab"> <?php echo $Lentrata; ?> -
        <input type="radio" name="conto" value="uscitab"> <?php echo $Luscita; ?>
      </fieldset>

      <div class="form-buttons">
        <button name="invio" type="submit"  class="btn-add" <?php echo $limit . ' ' . $limite; ?>>
         <i class="material-icons">send</i> Invia
        </button>
      </div>
    </form>

    <hr class="divider">

    <h3 class="card-title"><?php echo $Lprimanota; ?></h3>

    <?php
    $query = "SELECT * FROM tb_primanota ORDER BY id_primanota DESC";
    $rs = mysqli_query($connect, $query) or die("Errore query: " . mysqli_error($connect));
    ?>

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

    <?php
      // Totali
      $get = fn($col) => mysqli_fetch_row(mysqli_query($connect, "SELECT SUM($col) FROM tb_primanota"))[0] ?? 0;
      $entrata_tot = $get('entrata');
      $uscita_tot = $get('uscita');
      $entratab_tot = $get('entratab');
      $uscitab_tot = $get('uscitab');
    ?>

    <hr class="divider">

    <h4 class="card-title"><?php echo $Lcassa; ?></h4>
    <table width="90%" align="center">
      <tr><td><?php echo $Lentratacassa; ?></td><td align="right"><?php echo $entrata_tot; ?></td></tr>
      <tr><td><?php echo $Luscitacassa; ?></td><td align="right"><?php echo $uscita_tot; ?></td></tr>
      <tr><td><b><?php echo $Lguadperdita; ?></b></td><td align="right"><b><?php echo $entrata_tot - $uscita_tot; ?></b></td></tr>
    </table>

    <h4 class="card-title"><?php echo $Lbanca; ?></h4>
    <table width="90%" align="center">
      <tr><td><?php echo $Lentratabanca; ?></td><td align="right"><?php echo $entratab_tot; ?></td></tr>
      <tr><td><?php echo $Luscitabanca; ?></td><td align="right"><?php echo $uscitab_tot; ?></td></tr>
      <tr><td><b><?php echo $Lguadperdita; ?></b></td><td align="right"><b><?php echo $entratab_tot - $uscitab_tot; ?></b></td></tr>
    </table>

    <hr class="divider">

    <center>
      <img src="/Immagini/suggerimento.png" alt="Suggerimento" width="60"><br>
      <small><i><?php echo $Lhelpprimanota; ?></i></small>
    </center>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
</body>
</html>
