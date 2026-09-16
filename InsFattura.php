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
$langinsfattura = $_SESSION['lingua'];
$paginainsfattura = "insfattura.inc";
$linguainsfattura = ($langinsfattura . $paginainsfattura);
include($linguainsfattura);

// 🔹 Controllo livello utente
if ($user == 'admin') {
    $limit = '';
    $limite = '';
} else if ($user == 'operatore') {
    $limit = 'disabled';
    $limite = '';
} else if ($user == 'associato' || $user == 'limitato') {

    // Funzione redirect
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

    echo "<center>Il tuo utente ha un livello <b>$user</b><br>Area permessa solo all'utente <b>Admin</b></center>";
    redirect('./index2.php', 3);
    exit;
}

// 🔹 Include elementi comuni
include('./top.inc');
include('./menu.inc');

// 🔹 Connessione DB
include('./dati_db.inc');
$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port) or die("cannot connect DB");

// 🔹 Recupero ultimo id
$Query = "SELECT MAX(id_tot_fatture) FROM tb_tot_fatture";
$Qultimoid = mysqli_query($connect, $Query);
$ultimoid = 0;
while ($Tultimoid = mysqli_fetch_array($Qultimoid)) {
    $ultimoid = $Tultimoid['MAX(id_tot_fatture)'];
}
?>

<!-- 🔹 Titolo -->
<center><h3><?php echo $Lptitolofattura; ?></h3></center>

<!-- 🔹 SEZIONE STAMPA FATTURA -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lstampa; ?></h3>

    <form action='./stampa_fattura.php' method='POST' target="_blank">
      <div class="sinx-form">
        <label for="fattura"><?php echo $Lnumerofattura; ?> *</label>
        <select name="id" id="fattura">
          <option value="" selected="selected"></option>
          <?php
          for ($a = 1; $a <= $ultimoid; $a++) {
              $query = "SELECT id_tot_fatture FROM tb_tot_fatture WHERE id_tot_fatture = $a ORDER BY tot_fattura DESC, id_tot_fatture LIMIT 1";
              $rs = mysqli_query($connect, $query);
              while ($row = mysqli_fetch_row($rs)) {
                  echo "<option>" . $row[0] . "</option>";
              }
          }
          ?>
        </select>

        <div class="form-buttons">
          <button type="submit" class="btn-add" <?php echo($limit); echo($limite); ?>>
            <?php echo $L_stampafattura; ?>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 SEZIONE REGISTRA FATTURA -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lregistrafattura; ?></h3>

    <form action='./incasso_fattura.php' method='POST'>
      <div class="sinx-form">
        <label><?php echo $Lvocecontoec; ?> *</label>
        <select name="contoec">
          <option value="" selected="selected"><?php echo $Lcausale; ?></option>
          <?php
          $query = "SELECT descrizione FROM tb_conto_economico";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) {
              echo "<option>" . $row[0] . "</option>";
          }
          ?>
        </select>
        <small><i><?php echo $Linscausale; ?></i></small>

        <label><?php echo $Lnumerofattura; ?> *</label>
        <select name="id">
          <option value="" selected="selected"></option>
          <?php
          for ($a = 1; $a <= $ultimoid; $a++) {
              $query = "SELECT id_tot_fatture FROM tb_tot_fatture WHERE id_tot_fatture = $a ORDER BY tot_fattura DESC, id_tot_fatture LIMIT 1";
              $rs = mysqli_query($connect, $query);
              while ($row = mysqli_fetch_row($rs)) {
                  echo "<option>" . $row[0] . "</option>";
              }
          }
          ?>
        </select>

        <fieldset>
          <legend><small><?php echo $Lincassoin; ?></small></legend>
          <label><input type="radio" name="incasso" value="cassa" checked> <?php echo $Lcassa; ?></label>
          <label><input type="radio" name="incasso" value="banca"> <?php echo $Lbanca; ?></label>
        </fieldset>

        <div class="form-buttons">
          <button type="submit" class="btn-add" <?php echo($limit); echo($limite); ?>>
            - Registra incasso -
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 NUOVA FATTURA -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lcompfattura; ?></h3>
    <small><center><?php echo $Lnota1; ?></center></small>

    <form action='./conf_dati_fattura.php' method='POST'>
      <div class="sinx-form">
        <label><?php echo $Lnumerofattura; ?>:</label>
        <input type="text" name="fattnum" value="<?php echo ($ultimoid + 1); ?>">

        <label><?php echo $Lnomecliente; ?> *</label>
        <select name="nome">
          <option value="" selected="selected"></option>
          <?php
          $query = "SELECT nome FROM tb_anagrafe ORDER BY nome";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) {
              echo "<option>" . $row[0] . "</option>";
          }
          ?>
        </select>

        <label><?php echo $Ldata; ?>:</label>
        <input type="text" name="data" value="<?php echo date('d-m-Y'); ?>">

        <label><?php echo $Ldescrizione; ?></label>
        <input type="text" name="Descr">

        <label><?php echo $Lquantita; ?></label>
        <input type="text" name="Qta">

        <label><?php echo $Lprezzoun; ?></label>
        <input type="text" name="prezzoun" placeholder="<?php echo $Lhelpdecimali; ?>">

        <label><?php echo $Liva; ?></label>
        <input type="text" name="iva" placeholder="<?php echo $LhelpIva; ?>">

        <label><?php echo $Lmodpaga; ?></label>
        <input type="text" name="modpaga" placeholder="<?php echo $LhelpModPaga; ?>">
        <small><i><?php echo $Lnota2; ?></i></small>

        <div class="form-buttons">
          <button type="submit" class="btn-add" <?php echo($limit); echo($limite); ?>>
            - Registra Fattura -
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 VISUALIZZA FATTURE -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lvisualizzafatt; ?></h3>

    <form action='./visual_fattura.php' method='POST'>
      <div class="sinx-form">
        <label><?php echo $Lnumerofattura; ?> *</label>
        <select name="id">
          <option value="" selected="selected"></option>
          <?php
          for ($a = 1; $a <= $ultimoid; $a++) {
              $query = "SELECT id_tot_fatture FROM tb_tot_fatture WHERE id_tot_fatture = $a ORDER BY tot_fattura DESC, id_tot_fatture LIMIT 1";
              $rs = mysqli_query($connect, $query);
              while ($row = mysqli_fetch_row($rs)) {
                  echo "<option>" . $row[0] . "</option>";
              }
          }
          ?>
        </select>

        <div class="form-buttons">
          <button type="submit" class="btn-edit" <?php echo($limit); echo($limite); ?>>
            - Visualizza Fattura -
          </button>
        </div>
      </div>
    </form>
    <small><center><?php echo $Lnota3; ?></center></small>
  </div>
</div>

<hr class="divider">

<!-- 🔹 IMPORTA FATTURA ELETTRONICA XML -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title">📥 Importa Fattura Elettronica (XML)</h3>
    <small>
      <center>
        Carica una fattura elettronica in formato XML (FatturaPA) per inserirla automaticamente nel gestionale.
      </center>
    </small>

    <form action="import_fattura_xml.php" method="POST" enctype="multipart/form-data">
      <div class="sinx-form">

      <!-- Selezione cliente -->
        <label><?php echo $Lnomecliente; ?> *</label>
        <select name="nome_cliente" required>
          <option value="" selected="selected"></option>
          <?php
          $query = "SELECT nome FROM tb_anagrafe ORDER BY nome";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>" . htmlspecialchars($row[0]) . "</option>";
          }
          ?>
        </select>
        <small><i>Cliente a cui associare la fattura importata</i></small>

        <label>File fattura XML *</label>
        <input type="file" name="fattura_xml" accept=".xml" required>

        <small>
          <i>
            Verranno importate automaticamente intestazione, righe e totale fattura.
          </i>
        </small>

        <div class="form-buttons">
          <button type="submit" class="btn-add">
            📄 Importa fattura XML
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- 🔹 ELENCO FATTURE -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lelencofatture; ?></h3>

    <table class="appointments appointments-4col" align='center' cellpadding='4' cellspacing='0' width='100%'>
      <tr class="appointments-header">
        <th><?php echo $Lnumerofattura; ?></th>
        <th><?php echo $Ldata; ?></th>
        <th><?php echo $Lnomecliente; ?></th>
        <th><?php echo $Ltotivaincl; ?></th>
      </tr>

      <?php
      for ($a = 1; $a <= $ultimoid; $a++) {
          $Query = "SELECT id_tot_fatture, tot_fattura, nome, data
                    FROM tb_tot_fatture
                    WHERE id_tot_fatture = $a
                    ORDER BY tot_fattura DESC, id_tot_fatture
                    LIMIT 1";
          $rs = mysqli_query($connect, $Query);
          while ($row = mysqli_fetch_array($rs)) {
              echo "
              <tr class='appointment-item'>
                <td align='center'><small>{$row['id_tot_fatture']}</small></td>
                <td align='center'><small>{$row['data']}</small></td>
                <td align='center'><small>{$row['nome']}</small></td>
                <td align='center'><small>{$row['tot_fattura']}</small></td>
              </tr>";
          }
      }
      ?>
    </table>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<center>
<img src='./Immagini/suggerimento.png' alt="Suggerimento">
<small><i><?php echo $Lhelpfatture; ?></i></small>
</center>
<hr>
<?php include('./botton.inc'); ?>
