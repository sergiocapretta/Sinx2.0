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
$langcontoec = $_SESSION['lingua'];
$paginacontoec = "inscontoec.inc";
$linguacontoec = ($langcontoec . $paginacontoec);
include($linguacontoec);

if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'limitato') {
  $limit = 'disabled'; $limite = 'disabled';
} elseif ($user == 'associato' || $user == 'operatore') {
  $limit = 'disabled'; $limite = '';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");
?>

<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Ltitolocontoec; ?></h3>
    <p class="card-subtitle"><?php echo $Lnota1; ?></p>

    <form action="./InsContoEconomico_exp.php" method="GET" class="form-buttons" style="justify-content:center;">
      <button name="stampa" type="submit" class="btn-edit" <?php echo $limite; ?>>
        Stampa | Cancella | Modifica
      </button>
    </form>

    <hr class="divider">

    <h4 class="card-subtitle"><?php echo $Lnuovavocecontoec; ?></h4>

    <form action="./conf_dati_contoec.php" method="POST" class="sinx-form">
      <label><?php echo $Loperazione; ?> *</label>
      <input name="operazione" type="text" required placeholder="<?php echo $Linscausale; ?>" <?php echo $limite; ?>>

      <label><?php echo $Lvalore; ?> *</label>
      <input name="valore" type="text" required placeholder="<?php echo $Lnota4; ?>" <?php echo $limite; ?>>

      <label><?php echo $Ltipovoce; ?></label>
      <div style="display:flex; gap:20px; align-items:center;">
        <label><input type="radio" name="contoec" value="ricavi" checked> <?php echo $Lproventiric; ?></label>
        <label><input type="radio" name="contoec" value="oneri"> <?php echo $Lcostioneri; ?></label>
      </div>

      <div class="form-buttons">
        <input value="Invia" type="submit" class="btn-add" <?php echo $limite; ?>>
      </div>
    </form>

    <hr class="divider">

    <h3 class="card-title"><?php echo $Ltitolocontoec; ?></h3>



    <div class="column" style="display:flex; gap:20px; justify-content:space-between;">
      <div style="width:48%;">
        <?php
        $Query_ricavi = "SELECT * FROM tb_conto_economico WHERE costoricavo = 'ricavi' ORDER BY id";
        $rs = mysqli_query($connect, $Query_ricavi) or die("<b>Errore:</b> Impossibile eseguire la query");

        echo "<div class='appointments'>";
        echo "<div class='appointments-header'><span>ID</span><span>$Lproventiric</span><span>$Limporto</span></div>";
        while ($row = mysqli_fetch_array($rs)) {
          echo "<div class='appointment-item'>
                  <span>{$row['id']}</span>
                  <span>{$row['descrizione']}</span>
                  <span class='val-entrata'>{$row['valore']} &euro;</span>
                </div>";
        }
        echo "</div>";
        ?>
      </div>

      <div style="width:48%;">
        <?php
        $Query_oneri = "SELECT * FROM tb_conto_economico WHERE costoricavo = 'oneri' ORDER BY id";
        $ros = mysqli_query($connect, $Query_oneri) or die("<b>Errore:</b> Impossibile eseguire la query");

        echo "<div class='appointments'>";
        echo "<div class='appointments-header'><span>ID</span><span>$Lcostioneri</span><span>$Limporto</span></div>";
        while ($riga = mysqli_fetch_array($ros)) {
          echo "<div class='appointment-item'>
                  <span>{$riga['id']}</span>
                  <span>{$riga['descrizione']}</span>
                  <span class='val-uscita'>{$riga['valore']} &euro;</span>
                </div>";
        }
        echo "</div>";
        ?>
      </div>
    </div>

    <?php
    // Calcolo somme
    $result = mysqli_query($connect, "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo='ricavi'");
    $entrata = mysqli_fetch_row($result);

    $result = mysqli_query($connect, "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo='oneri'");
    $uscita = mysqli_fetch_row($result);
    ?>

    <hr class="divider">

    <div class="card-subtitle" style="text-align:center;">
      <table align="center" width="80%">
        <tr>
          <td><b><?php echo $Ltotproventi; ?></b></td>
          <td align="right" class="val-entrata"><?php echo number_format($entrata[0], 2); ?> &euro;</td>
        </tr>
        <tr>
          <td><b><?php echo $Ltotspese; ?></b></td>
          <td align="right" class="val-uscita"><?php echo number_format($uscita[0], 2); ?> &euro;</td>
        </tr>
        <tr>
          <td><b><?php echo $Lavanzogestione; ?></b></td>
          <td align="right">
            <b><?php echo number_format($entrata[0] - $uscita[0], 2); ?> &euro;</b>
          </td>
        </tr>
        <tr>
          <td><b><?php echo $Ltotpareggio; ?></b></td>
          <td align="right">
            <b><?php echo number_format(($entrata[0] - $uscita[0]) + $uscita[0], 2); ?> &euro;</b>
          </td>
        </tr>
      </table>
    </div>

    <hr class="divider">

    <div style="text-align:center;">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="24">
      <small><i><?php echo $Lhelpcontoec; ?></i></small>
    </div>

  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
