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
$langgestfiles = $_SESSION['lingua'];
$paginagestfiles = "insanagrfile.inc";
$linguagestfiles = ($langgestfiles . $paginagestfiles);
include($linguagestfiles);

if ($user == 'admin') {

    include('./top.inc');
    include('./menu.inc');
?>
<!-- ✅ FORM MODERNO UPLOAD CSV -->
<link rel="stylesheet" href="/style.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><i class="material-icons">upload_file</i> <?php echo $LTitoloimportacsv; ?></h2>
    <p class="card-subtitle"><?php echo $Lsugg1caricafiles . " " . $Lsugg2caricafiles; ?></p>

    <form action="./conf_importacsv.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000">

      <label for="filecsv"><?php echo $Lcaricafile; ?></label>
      <input type="file" name="filecsv" id="filecsv" required>

      <div class="form-buttons">
        <button type="submit" class="btn-add">⬆️ Upload</button>
      </div>
    </form>

    <hr class="divider">

    <div class="note">
      <img src="/Immagini/suggerimento.png" alt="Suggerimento" style="vertical-align:middle; width:24px;">
      <small><i><?php echo $LNota; ?></i></small>
    </div>
  </div>
</div>

<?php
    include('./menusx.inc');
    include('./botton.inc');
} else {
    header('Location: ./Redirect_no_enter.php');
}
?>
