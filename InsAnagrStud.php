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
$langanagrstud = $_SESSION['lingua'];
$paginaanagrstud = "insanagrstud.inc";
$linguaanagrstud = ($langanagrstud . $paginaanagrstud);
include($linguaanagrstud);

if ($user == 'admin') {

  include('./top.inc');
  include('./menu.inc');

  include('./dati_db.inc');
  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");
?>
  <!DOCTYPE html>
  <html lang="it">
  <head>
    <meta charset="UTF-8">
    <title><?php echo $Lpresentazioneanagrstud; ?></title>
    <link rel="stylesheet" href="/style.css">
  </head>
  <body>

    <div class="content-section">
      <div class="card">
        <h2 class="card-title"><?php echo $Lpresentazioneanagrstud; ?></h2>

        <div class="form-buttons" style="justify-content: center; flex-wrap: wrap; gap: 10px;">
          <form action="./stampa_lista_tessere.php" method="POST" target="_blank">
            <button class="btn-add" name="ordine" type="submit" value="ntessera"><?php echo $Ltessere; ?></button>
          </form>

          <form action="./Scheda_regioni.php" method="POST">
            <button class="btn-edit" name="Regioni" type="submit" value="regioni">Regioni</button>
          </form>

          <form action="./Scheda_province.php" method="POST">
            <button class="btn-edit" name="Province" type="submit" value="Province">Province</button>
          </form>

          <form action="./Scheda_comuni.php" method="POST">
            <button class="btn-edit" name="Comuni" type="submit" value="comuni">Comuni</button>
          </form>
        </div>

        <hr class="divider">

        <form action="./conf_dati_stud.php" method="POST" enctype="multipart/form-data" class="sinx-form">
          <label for="ntessera"><span style="color:red;"><?php echo $Lntessera; ?></span></label>
          <input name="ntessera" id="ntessera" type="text" required>
          <small><i><?php echo $Listntessera; ?></i></small>

          <!-- Inclusione dati comuni -->
          <?php include('./DatiComuni.inc'); ?>

          <label for="classe"><span style="color:red;"><?php echo $Lfunzione; ?>*</span></label>
          <select name="classe" id="classe" required>
            <option value="" selected><?php echo $Lfunzione; ?></option>
            <?php
            $query = "SELECT classe FROM tb_classe";
            $rs = mysqli_query($connect, $query) or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
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
  </body>
  </html>
<?php
include('./menusx.inc');
echo $Lhelpanagrstud;
include('./botton.inc');
} else {
header('Location: ./Redirect_no_enter.php');
}
?>
