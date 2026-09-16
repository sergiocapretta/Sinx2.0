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
$langinsnote = $_SESSION['lingua'];
$paginainsnote = "insnote.inc";
$linguainsnote = ($langinsnote.$paginainsnote);
include($linguainsnote);

// Permessi in base all’utente
$limit = ($user === 'limitato' || $user === 'associato') ? 'disabled' : '';

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$link = mysqli_connect($host, $username, $password, $db_name)
    or die(mysqli_connect_error("Non posso connettermi al database"));

// Recupero la nota
$Query_nome = "SELECT * FROM tb_note LIMIT 1";
$rs = mysqli_query($link, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
$row = mysqli_fetch_array($rs, MYSQLI_ASSOC);
?>

<!-- Contenitore principale -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Lptitolonote; ?></h3>
    <p class="card-subtitle"><?php echo $Lcompnote; ?></p>

    <form action="./conf_note.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label for="nome"><?php echo $Ldescrizione; ?></label>
      <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($row['dest']); ?>" <?php echo $limit; ?>>

      <label for="formcontent"><?php echo $Ldescrizionenote; ?></label>
      <textarea name="formcontent" id="formcontent" rows="15" class="note-area" <?php echo $limit; ?>>
<?php echo htmlspecialchars($row['testo']); ?>
      </textarea>

      <div class="form-buttons">
        <button type="submit" name="registra"  class="btn-add" <?php echo $limit; ?>> - Registra Nota -</button>
      </div>
    </form>
  </div>
</div>

<?php
include('./menusx.inc');
?><hr><img src='./Immagini/suggerimento.png'><small><i><?php echo $Lhelpnote;
include('./botton.inc');

@mysqli_close($link);

?>
