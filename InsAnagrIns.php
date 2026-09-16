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
$langanagrins = $_SESSION['lingua'];
$paginaanagrins = "insanagrins.inc";
$linguaanagrins = ($langanagrins . $paginaanagrins);
include($linguaanagrins);

if ($user == 'admin') {

  include('./top.inc');
  include('./menu.inc');

  include('./dati_db.inc');
  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");
?>

  <div class="content-section">
    <div class="card">
      <h2 class="card-title"><?php echo $Lpresentazioneangrins; ?></h2>
      <p class="card-subtitle"><?php echo $Lsuggerimento; ?></p>

      <hr class="divider">

      <!-- 🔹 Sezione link rapidi -->
      <div class="form-buttons">
        <form action="./stampa_lista_tessere.php" method="POST" target="_blank">
          <button name="ordine" type="submit" value="ntessera" class="btn-add">
            <?php echo $Ltessere; ?>
          </button>
        </form>

        <form action="./Scheda_regioni.php" method="POST">
          <button name="Regioni" type="submit" value="regioni" class="btn-edit">
            Regioni
          </button>
        </form>

        <form action="./Scheda_province.php" method="POST">
          <button name="Province" type="submit" value="Province" class="btn-edit">
            Province
          </button>
        </form>

        <form action="./Scheda_comuni.php" method="POST">
          <button name="Comuni" type="submit" value="comuni" class="btn-edit">
            Comuni
          </button>
        </form>
      </div>

      <hr class="divider">

      <!-- 🔹 Form inserimento associato -->
      <form action="./conf_dati_ins.php" method="POST" enctype="multipart/form-data" class="sinx-form">

        <label for="ntessera" style="color:red;"><?php echo $Lntessera; ?> *</label>
        <input name="ntessera" id="ntessera" type="text" required>
        <small><i><?php echo $Listntessera; ?></i></small>

        <?php include('./DatiComuni.inc'); ?>

        <label for="materia" style="color:red;"><?php echo $Ltipoassociato; ?> *</label>
        <select name="materia" id="materia" required>
          <option value="" selected><?php echo $Ltipoassociato; ?></option>
          <?php
          $query = "SELECT materia FROM tb_materia";
          $rs = mysqli_query($connect, $query)
            or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
          while ($row = mysqli_fetch_row($rs)) {
            echo "<option>" . htmlspecialchars($row[0]) . "</option>";
          }
          mysqli_close($connect);
          ?>
        </select>

        <div class="form-buttons">
          <button type="submit" class="btn-add"><?php echo $Linvia; ?></button>
        </div>
      </form>
    </div>
  </div>

<?php
include('./menusx.inc');
echo $Lhelpanagrins;
include('./botton.inc');
} else {
header('Location: ./Redirect_no_enter.php');
}
?>
