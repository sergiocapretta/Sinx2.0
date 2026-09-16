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
$langNannosoc = $_SESSION['lingua'];
$paginaNannosoc = "Nannosoc.inc";
$linguaNannosoc = ($langNannosoc . $paginaNannosoc);
include($linguaNannosoc);

if ($user == 'admin') {
  include('./top.inc');
  include('./menu.inc');
?>
  <html lang="it">
  <head>
    <meta charset="ISO-8859-1">
    <title><?php echo $LtitoloNannosoc; ?></title>
    <script>
      // Conferma per l'azzeramento contabile
      function confermaAzzera() {
        return confirm("⚠️ ATTENZIONE: questa operazione azzererà tutti i dati contabili dell'anno in corso.\n\nVuoi davvero procedere?");
      }
    </script>
  </head>
  <body>

    <div class="content-section">
      <div class="card" style="text-align:center; max-width:600px; margin:auto;">
        <h2 class="card-title"><?php echo $LtitoloNannosoc; ?></h2>
        <p><strong><?php echo $LattenzioneNannosoc; ?></strong></p>
        <p><?php echo $LavvertenzaNannosoc; ?></p>

        <div class="form-buttons" style="justify-content:center; gap:20px; margin-top:20px; flex-wrap:wrap;">
          <form action="./Stampa_Anno.php" method="post" target="_blank">
            <input type="submit" class="btn-add" value="Archivia / Stampa" title="Creazione file per archivio e stampa">
          </form>

          <form action="./cancella_anno_sociale.php" method="post" onsubmit="return confermaAzzera();">
            <input type="submit" class="btn-delete" value="Azzera contabilità" title="Azzeramenti contabili">
          </form>
        </div>

        <hr class="divider">

        <p>
          <a href="./index2.php" class="link-btn"><strong><?php echo $LannullaesciNannosoc; ?></strong></a>
        </p>
      </div>
    </div>

    <div class="content-section">
      <div class="card" style="text-align:center;">
        <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="60" style="margin-bottom:10px;">
        <p><small><i><?php echo $LmsarchiviaNannosoc; ?></i></small></p>
      </div>
    </div>

  </body>
  </html>

<?php
  include('./menusx.inc');
  include('./botton.inc');
} else {
  // --- Gestione redirect se non admin ---
  function redirect($url, $tempo = FALSE)
  {
    if (!headers_sent() && $tempo == FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo == FALSE) {
        $tempo = 0;
      }
      echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
    }
  }

  echo "<center>$Lmslogin1Nannosoc<b>$user</b> <br>$Lmslogin2Nannosoc<b>$Lmslogin3Nannosoc</b></center>";
  redirect('./index2.php', 3);
}
?>
