<?php
/*Sinx for Association - Gestionale per Associazioni no-profit
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
*/

session_start();

$user = $_SESSION['utente'];
$langtipoass = $_SESSION['lingua'];
$paginatipoass = "nmateria.inc";
$linguatipoass = ($langtipoass . $paginatipoass);
include($linguatipoass);

// Gestione permessi
if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled'; $limite = '';
} elseif ($user == 'associato') {
  $limit = 'disabled'; $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolotipoass; ?></h2>

    <!-- Pulsante gestione -->
    <form action="./nmateria_exp.php" method="POST" style="text-align:center; margin-bottom: 20px;">
      <button class="btn-edit" name="stampa" type="submit" <?php echo $limit . ' ' . $limite; ?>>
        Cancella / Modifica
      </button>
    </form>

    <hr class="divider">

    <p class="card-subtitle"><?php echo $Lnuovotipoass; ?></p>

    <!-- Inserimento nuovo tipo di associazione -->
    <form action="./conf_nmateria.php?Tabella=tb_materia&Colonna=materia&Pagina=nmateria" method="POST" class="sinx-form">
      <label><?php echo $Lnuovotipoass . ' ' . $Lsociotipoass; ?>:</label>
      <input type="text" name="nrecord" placeholder="Inserisci nuovo tipo..." required>

      <div class="form-buttons">
        <input type="submit" value="Invia" class="btn-add" <?php echo $limit . ' ' . $limite; ?>>
      </div>
    </form>

    <hr class="divider">

    <h3 class="card-title"><?php echo $Ltitoloelencotipoass; ?></h3>

    <?php
    // Popolamento tabella elenco
    $Query_nome = "SELECT * FROM tb_materia ORDER BY materia";
    $rs = mysqli_query($connect, $Query_nome)
      or die("<b>Errore:</b> Impossibile eseguire la query");

    echo '<div class="appointments appointments-4col" style="margin-top:20px;">';
    echo '<div class="appointments-header">';
    echo '<span><b>ID</b></span>';
    echo '<span><b>' . $Ltipotipoass . ' ' . $Lsociotipoass . '</b></span>';
    echo '</div>';

    while ($row = mysqli_fetch_array($rs)) {
      echo '<div class="appointment-item">';
      echo '<span>' . htmlspecialchars($row['id_materia']) . '</span>';
      echo '<span>' . htmlspecialchars($row['materia']) . '</span>';
      echo '</div>';
    }

    echo '</div>';
    ?>

    <hr class="divider">

    <div style="text-align:center;">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" style="width:32px; vertical-align:middle;">
      <small><i><?php echo $Lhelptipoass; ?></i></small>
    </div>
  </div>
</div>

<?php
include('./menusx.inc');
include('./botton.inc');
?>

