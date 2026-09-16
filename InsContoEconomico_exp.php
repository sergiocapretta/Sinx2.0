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

// Solo admin
if ($user != 'admin') {
  header('Location: ./Redirect_no_enter.php');
  exit;
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// Recupero ultimo ID
$Query = "SELECT MAX(id) AS ultimoid FROM tb_conto_economico";
$result = mysqli_query($connect, $Query);
$rowUltimo = mysqli_fetch_assoc($result);
$ultimoid = $rowUltimo['ultimoid'] ?? 0;
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolocontoec; ?></h2>
    <p class="card-subtitle"><?php echo $Lnota1; ?></p>

    <div style="text-align:center; margin-bottom:10px;">
      <a href="./stampa_contoec.php" target="_blank" class="link-btn"><?php echo $Lstampacontoec; ?></a>
 <!--     <a href="./Azzera.php?Tabella=tb_conto_economico&Modulo=Conto Economico" class="link-btn"><?php echo $Lazzeracontoec; ?></a> -->
    </div>

    <!-- Sezione Cancella -->
    <h3 style="text-align:center;"><?php echo $Lcancella; ?></h3>
    <p style="text-align:center;"><small><?php echo $Lnota2; ?></small></p>

    <form action="./conf_canc.php?Tabella=tb_conto_economico&Riferimento=id" method="POST" class="sinx-form" style="max-width:400px;margin:auto;">
      <label><?php echo $Lnumero; ?> *</label>
      <select name="id_mod" required>
        <option value="">--</option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $query = "SELECT id FROM tb_conto_economico WHERE id = $a ORDER BY id LIMIT 1";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>" . $row[0] . "</option>";
          }
        }
        ?>
      </select>
      <input type="submit" value="- Cancella -" class="btn-delete">
    </form>

    <hr class="divider">

    <!-- Sezione Modifica -->
    <h3 style="text-align:center;"><?php echo $Lmodifica; ?></h3>
    <p style="text-align:center;"><small><?php echo $Lnota3; ?></small></p>

    <form action="./conf_mod_ceconomico.php?Tabella=tb_conto_economico" method="POST" class="sinx-form" style="max-width:500px;margin:auto;">
      <label><?php echo $Lnumero; ?> *</label>
      <select name="id_mod" required>
        <option value="">--</option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $query = "SELECT id FROM tb_conto_economico WHERE id = $a ORDER BY id LIMIT 1";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>" . $row[0] . "</option>";
          }
        }
        ?>
      </select>

      <label><?php echo $Lcampo; ?> *</label>
      <select name="campo" required>
        <option value=""></option>
        <option value="descrizione"><?php echo $Ldescrizione; ?></option>
        <option value="valore"><?php echo $Lvalore; ?></option>
      </select>

      <label><?php echo $Lnuovorecord; ?> *</label>
      <input type="text" name="record" required>

      <div class="form-buttons">
        <input type="submit" value="Modifica" class="btn-edit">
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

        <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./InsContoEconomico.php" method="GET">
        <input type="submit" value="Ritorna al Conto Economico" class="btn-edit">
      </form>
    </div>

    <hr class="divider">
    <div style="text-align:center;">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="40"><br>
      <small><i><?php echo $Lhelpcontoec; ?></i></small>
    </div>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
