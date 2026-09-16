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
$langinsstatopat = $_SESSION['lingua'];
$paginainsstatopat = "insstatopatr.inc";
$linguainsstatopat = ($langinsstatopat . $paginainsstatopat);
include($linguainsstatopat);

// 🔒 Solo admin
if ($user != 'admin') {
  header('Location: ./index2.php');
  exit;
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// 🔹 Recupero ultimo ID
$Qultimoid = mysqli_query($connect, "SELECT MAX(id) AS max_id FROM tb_stato_patrimoniale");
$ultimoid = mysqli_fetch_assoc($Qultimoid)['max_id'] ?? 0;

// 🔹 Funzione di aggiornamento
// Calcolo automatico valori Cassa e Banca
function aggiornaValore($connect, $campoEntrata, $campoUscita, $idRiga) {
  $sumEntrata = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM($campoEntrata) FROM tb_primanota"))[0];
  $sumUscita  = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM($campoUscita) FROM tb_primanota"))[0];
  $valore = $sumEntrata - $sumUscita;
  mysqli_query($connect, "UPDATE tb_stato_patrimoniale SET valore = '$valore' WHERE id = '$idRiga'");
}

// ID da ESCLUDERE dai totali (attività e passività)
$ids_esclusi = [1, 2, 4, 12, 18, 30, 36, 49, 53, 57, 58, 59, 60, 64, 67, 68, 72, 73, 86]; // <-- metti qui gli ID che NON vuoi sommare
$ids_sql = implode(',', $ids_esclusi);

aggiornaValore($connect, 'entrata', 'uscita', 38); // Cassa
aggiornaValore($connect, 'entratab', 'uscitab', 36); // Banca

// Calcolo Totali

$entrata = mysqli_fetch_row(mysqli_query(
  $connect,
  "SELECT SUM(valore)
   FROM tb_stato_patrimoniale
   WHERE costoricavo = 'attivita'
   AND id NOT IN ($ids_sql)"
))[0];

$uscita = mysqli_fetch_row(mysqli_query(
  $connect,
  "SELECT SUM(valore)
   FROM tb_stato_patrimoniale
   WHERE costoricavo = 'passivita'
   AND id NOT IN ($ids_sql)"
))[0];
//$entrata = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo = 'attivita'"))[0];
//$uscita  = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo = 'passivita'"))[0];
$avanzo  = $entrata - $uscita;
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolostatopatr; ?></h2>
    <p class="card-subtitle"><?php echo $Lnota1; ?></p>

    <div style="text-align:center; margin-bottom:10px;">
      <a href="./stampa_statopat.php" target="_blank" class="link-btn"><?php echo $Lstampastatopat; ?></a>
 <!--     <a href="./Azzera.php?Tabella=tb_stato_patrimoniale&Modulo=Stato Patrimoniale" class="link-btn"><?php echo $Lazzerastatopat; ?></a> -->
    </div>

    <!-- 🔹 Sezione Cancella -->
   <h3 style="text-align:center;"><?php echo $Lcancella; ?></h3>
    <p style="text-align:center;"><small><?php echo $Lnotacancella; ?></small></p>
<div class="sinx-form">
      <form action="./conf_canc.php?Tabella=tb_stato_patrimoniale&Riferimento=id" method="POST" class="sinx-form" style="max-width:400px;margin:auto;">
        <label><?php echo $Lnumero; ?> *</label>
        <select name="id_mod" required>
          <option value="">--</option>
          <?php
          for ($a = 1; $a <= $ultimoid; $a++) {
            $rs = mysqli_query($connect, "SELECT id FROM tb_stato_patrimoniale WHERE id = $a LIMIT 1");
            if ($row = mysqli_fetch_assoc($rs)) {
              echo "<option>{$row['id']}</option>";
            }
          }
          ?>
        </select>
        <input type="submit" value="- Cancella -" class="btn-delete">
      </form>
    </div>

    <hr class="divider">


    <!-- 🔹 Sezione Modifica -->
    <h3 style="text-align:center;"><?php echo $Lmodifica; ?></h3>
    <p style="text-align:center;"><small><?php echo $Lnotamodifica; ?></small></p>
    <div class="sinx-form">

      <form action="./conf_mod_ceconomico.php?Tabella=tb_stato_patrimoniale" method="POST" class="sinx-form" style="max-width:500px;margin:auto;">
        <label><?php echo $Lnumero; ?></label>
        <select name="id_mod" required>
          <option value="">--</option>
          <?php
          for ($a = 1; $a <= $ultimoid; $a++) {
            $rs = mysqli_query($connect, "SELECT id FROM tb_stato_patrimoniale WHERE id = $a LIMIT 1");
            if ($row = mysqli_fetch_assoc($rs)) {
              echo "<option>{$row['id']}</option>";
            }
          }
          ?>
        </select>

        <label><?php echo $Lcampo; ?></label>
        <select name="campo" required>
          <option value="descrizione"><?php echo $Ldescrizione; ?></option>
          <option value="valore"><?php echo $Lvalore; ?></option>
        </select>

        <label><?php echo $Lnuovorecord; ?></label>
        <input name="record" type="text" required>
        <button type="submit" class="btn-edit"><?php echo $Lmodifica; ?></button>
      </form>
    </div>

    <hr class="divider">

    <!-- 🔹 Stato Patrimoniale -->
     <div class="appointments appointments-4col" style="margin-top:20px;">


      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <!-- Attività -->
        <div>
          <h4 style="text-align:center; color:#233592;"><?php echo $Lattivita; ?></h4>
          <?php
          $q = mysqli_query($connect, "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo = 'attivita' ORDER BY id");
          while ($r = mysqli_fetch_assoc($q)) {
            echo "<div class='appointment-item'><span>{$r['id']}</span><span>{$r['descrizione']}</span><span class='val-entrata' style='text-align:right;'>{$r['valore']} €</span></div>";
          }
          ?>
        </div>

        <!-- Passività -->
        <div>
          <h4 style="text-align:center; color:#233592;"><?php echo $Lpassivita; ?></h4>
          <?php
          $p = mysqli_query($connect, "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo = 'passivita' ORDER BY id");
          while ($r = mysqli_fetch_assoc($p)) {
            echo "<div class='appointment-item'><span>{$r['id']}</span><span>{$r['descrizione']}</span><span class='val-uscita' style='text-align:right;'>{$r['valore']} €</span></div>";
          }
          ?>
        </div>
      </div>
    </div>

    <hr class="divider">

     <!-- Totali -->
    <div class="card" style="background:#f8f8f8;">
      <table style="width:100%; text-align:center;">
        <tr>
          <td><b><?php echo $Ltotaleattivi; ?></b></td>
          <td class="val-entrata"><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
          <td><b><?php echo $Ltotalepassivi; ?></b></td>
          <td class="val-uscita"><?php echo number_format($uscita, 2, ',', '.'); ?> €</td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <td><b><?php echo $Lavanzogestione; ?></b></td>
          <td><b><?php echo number_format($avanzo, 2, ',', '.'); ?> €</b></td>
        </tr>
        <tr>
          <td><b><?php echo $Ltotpareggio; ?></b></td>
          <td><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
          <td><b><?php echo $Ltotpareggio; ?></b></td>
          <td><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
        </tr>
      </table>
    </div>

  </div>
</div>

        <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./InsStatoPatrimoniale.php" method="GET">
        <input type="submit" value="Ritorna allo Statop Patrimoniale" class="btn-edit">
      </form>
    </div>
<hr>
<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
