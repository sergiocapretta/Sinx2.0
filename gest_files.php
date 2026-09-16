<?php
/*======================================================================+
 File name   : gest_files.php
 Begin       : 2012-07-08
 Last Update : 2012-07-08

 Description : Image and files upload

 Author: Sergio Capretta

 (c) Copyright:
               Sergio Capretta
             
               ITALY
               www.sinx.it
               info@sinx.it

Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 by Sergio Capretta

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
$paginagestfiles = "gestfiles.inc";
$linguagestfiles = ($langgestfiles . $paginagestfiles);
include($linguagestfiles);

if ($user == 'admin') {

  include('./top.inc');
  include('./menu.inc');
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitologestfiles; ?></h2>

    <!-- === CARICA IMMAGINI === -->
    <form action="./conf_immagine.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label><?php echo $Lcaricaimmagine; ?></label>
      <input type="hidden" name="MAX_FILE_SIZE" value="30000">
      <input name="immagine" type="file" accept="image/*" onchange="previewImage(this, 'preview1')">
      <img id="preview1" class="profile-photo" style="display:none; margin-top:10px;" alt="Anteprima Immagine">

      <p><small><i><?php echo $Lsugg1caricaimmagine; ?></i></small></p>
      <p><small><i><?php echo $Lsugg2caricaimmagine; ?></i></small></p>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add">
      </div>
    </form>

    <hr class="divider">

    <!-- === CARICA FILES === -->
    <h3 class="card-title"><?php echo $Ltitolocaricamoduli; ?></h3>

    <form action="./conf_files.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label><?php echo $Lcaricafile; ?></label>
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
      <input name="immagine" type="file" accept="application/pdf,application/text,application/odt">

      <p><small><i><?php echo $Lsugg1caricafiles; ?></i></small></p>
      <p><small><i><?php echo $Lsugg2caricafiles; ?></i></small></p>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add">
      </div>
    </form>

    <hr class="divider">

    <!-- === CARICA LOGO ASSOCIAZIONE === -->
    <h3 class="card-title"><?php echo $Lcaricamentologo; ?></h3>

    <form action="./conf_logo_associaz.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label><?php echo $Lcaricalogo; ?></label>
      <input type="hidden" name="MAX_FILE_SIZE" value="30000">
      <input name="immagine" type="file" accept="image/*" onchange="previewImage(this, 'previewLogo')">
      <img id="previewLogo" class="profile-photo" style="display:none; margin-top:10px;" alt="Anteprima Logo">

      <p><small><i><?php echo $Lsugg1caricalogo; ?></i></small></p>
      <p><small><i><?php echo $Lsugg2caricalogo; ?></i></small></p>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add">
      </div>
    </form>
  </div>
</div>

<!-- RITORNA AI FILES -->
<div class="form-buttons" style="margin-top:30px;">
  <input type="button" value="⬅ Torna alla Gestione Files" class="btn-edit"
         onclick="top.location.href='./Files.php'">
</div>

<div class="content-section">
  <div class="card" style="text-align:center;">
    <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="40" class="bordo">
    <p><small><i><?php echo $Lhelpgestimmagini; ?></i></small></p>
  </div>
</div>

<!-- === SCRIPT PER ANTEPRIMA IMMAGINI === -->
<script>
function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
}
</script>

<?php
  include('./menusx.inc');
  include('./botton.inc');

} else {
  // Funzione redirect per utenti non autorizzati
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

  echo "<center>$Llivello1gestimmagini<b>$user</b> <br>$Llivello2gestimmagini<b>Admin</b></center>";
  redirect('./index2.php', 3);
}
?>
