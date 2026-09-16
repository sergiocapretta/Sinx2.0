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

// Gestione permessi
if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'limitato') {
  $limit = 'disabled'; $limite = 'disabled';
} elseif ($user == 'associato' || $user == 'operatore') {
  $limit = 'disabled'; $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

// Connessione DB
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

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

    <form action="./InsStatoPatrimoniale_exp.php" method="POST" class="form-buttons" style="justify-content:center;">
      <button name="stampa" type="submit" class="btn-edit" <?php echo $limite; ?>>
        📄 Stampa | ✏️ Modifica | ❌ Cancella
      </button>
    </form>

    <hr class="divider">

    <h3 class="card-title"><?php echo $Lnuovavoce; ?></h3>

    <!-- Form nuova voce -->
    <form action="./conf_dati_patrim.php" method="POST" class="sinx-form">
      <label><?php echo $Loperazione; ?> *</label>
      <input type="text" name="operazione" required placeholder="<?php echo $Linscausale; ?>">

      <label><?php echo $Lvalore; ?> *</label>
      <input type="number" step="0.01" name="valore" required placeholder="<?php echo $Lnotavalore; ?>">

      <fieldset>
        <legend><?php echo $Ltipovoce; ?></legend>
        <label><input type="radio" name="patrim" value="attivita" checked> <?php echo $Lattivita; ?></label>
        <label><input type="radio" name="patrim" value="passivita"> <?php echo $Lpassivita; ?></label>
      </fieldset>

      <div class="form-buttons">
        <input type="submit" value="<?php echo $Linvia; ?>" class="btn-add" <?php echo $limite; ?>>
      </div>
    </form>

    <hr class="divider">

    <!-- Visualizzazione Attività e Passività -->
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

   <div> <small><i><?php echo $LTotalistatatopatr; ?></i></small> </div>

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

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<div style="text-align:center;">
  <img src="./Immagini/suggerimento.png" alt="Suggerimento" style="vertical-align:middle;">
  <small><i><?php echo $Lhelpatatopatr; ?></i></small>
</div>
<hr>
<?php include('./botton.inc'); ?>
