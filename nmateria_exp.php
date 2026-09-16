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

if ($user == 'admin') {
  $limit = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port) or die("cannot connect DB");
?>

<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo $Ltitolotipoass; ?></h3>

    <!-- Sezione Cancellazione -->
    <p class="card-subtitle"><?php echo $Lcanctipoass; ?></p>
    <p><small><?php echo $Lsuggcanctipoass; ?></small></p>

    <form action="./conf_canc.php?Tabella=tb_materia&Riferimento=id_materia" method="POST" class="sinx-form">
      <label>ID:</label>
      <input type="text" name="id_mod" placeholder="ID da cancellare">
      <div class="form-buttons">
        <input type="submit" value="- Cancella -" class="btn-delete" <?php echo $limit; ?>>
      </div>
    </form>

    <hr class="divider">

    <!-- Sezione Modifica -->
    <p class="card-subtitle"><?php echo $Lmodtipoass; ?></p>
    <p><small><?php echo $Lsuggmodtipoass; ?></small></p>

    <form action="./conf_mod.php?Tabella=tb_materia&Voce=materia&Riferimento=id_materia" method="POST" class="sinx-form">
      <label>ID:</label>
      <input type="text" name="id_mod" placeholder="ID da modificare">

      <label><?php echo $Lnuovorecordtipoass; ?>:</label>
      <input type="text" name="record" placeholder="Nuovo valore...">

      <div class="form-buttons">
        <input type="submit" value="Modifica" class="btn-edit" <?php echo $limit; ?>>
      </div>
    </form>

    <hr class="divider">

    <!-- Sezione Elenco -->
    <h3 class="card-title"><?php echo $Ltitoloelencotipoass; ?></h3>
    <p class="card-subtitle"><?php echo $Lelencotipoass; ?></p>

    <?php
    $Query_nome = "SELECT * FROM tb_materia ORDER BY materia";
    $rs = mysqli_query($connect, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query");

    echo '<div class="appointments appointments-4col">';
    echo '<div class="appointments-header">';
    echo '<span><b>ID</b></span><span><b>' . $Ltipotipoass . '</b></span>';
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
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="24">
      <small><i><?php echo $Lhelptipoass; ?></i></small>
    </div>

  </div>
</div>

<?php
include('./menusx.inc');
include('./botton.inc');
?>
