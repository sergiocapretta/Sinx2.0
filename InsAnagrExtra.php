<?php
/*
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
*/

session_start();

$user = $_SESSION['utente'];
$langanagrextra = $_SESSION['lingua'];
$paginaanagrextra = "insanagrextra.inc";
$linguaanagrextra = ($langanagrextra . $paginaanagrextra);
include($linguaanagrextra);

if ($user == 'admin') {
  $limit = '';
  $limite = '';
} else if ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
  $limite = '';
} else if ($user == 'associato') {
  $limit = 'disabled';
  $limite = 'disabled';
}

if ($user == 'admin') {
  include('./top.inc');
  include('./menu.inc');
  ?>

  <link rel="stylesheet" href="/style.css">
  <div class="card" style="max-width: 900px; margin: 40px auto;">

    <h2 class="card-title"><?php echo $Lpresentazioneextra; ?></h2>
    <p class="card-subtitle"><?php echo $Lnotaextra; ?></p>

    <!-- 🔹 PULSANTI RAPIDI -->
    <div class="form-buttons" style="justify-content: center; flex-wrap: wrap; gap: 10px;">
      <form action="./stampa_extra.php" method="POST" target="_blank">
        <button name="ordine" value="ntessera" class="btn-edit"><?php echo $Ltessere; ?></button>
      </form>

      <form action="./Scheda_regioni.php" method="POST">
        <button name="Regioni" value="regioni" class="btn-add">Regioni</button>
      </form>

      <form action="./Scheda_province.php" method="POST">
        <button name="Province" value="Province" class="btn-add">Province</button>
      </form>

      <form action="./Scheda_comuni.php" method="POST">
        <button name="Comuni" value="comuni" class="btn-add">Comuni</button>
      </form>
    </div>

    <hr class="divider">

    <!-- 🔹 FORM PRINCIPALE -->
    <form action="./conf_dati_extra.php" method="POST" enctype="multipart/form-data" class="sinx-form">

      <label for="ntessera"><b><?php echo $Lntessera; ?></b></label>
      <input type="text" name="ntessera" id="ntessera" required <?php echo $limit; ?> placeholder="Numero tessera">
      <small><i><?php echo $Listntessera; ?></i></small>

      <?php include('./DatiComuni.inc'); ?>

      <label for="mansione"><?php echo $Lmansione; ?></label>
      <input type="text" name="mansione" id="mansione" required placeholder="Inserisci la mansione">

      <div class="form-buttons" style="justify-content:center;">
        <button type="submit" class="btn-add" <?php echo $limit; ?>><?php echo $Linvia; ?></button>
      </div>
    </form>
  </div>
<?php
include('./menusx.inc');
echo $Lhelpextra;
include('./botton.inc');
} else {
header('Location: ./Redirect_no_enter.php');
}
?>
