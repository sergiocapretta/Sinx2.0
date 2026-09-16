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
$langrendiconto = $_SESSION['lingua'];
$paginarendiconto = "rendiconto.inc";
$linguarendiconto = ($langrendiconto . $paginarendiconto);
include($linguarendiconto);

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
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolorendiconto; ?></h2>
    <p class="card-subtitle"><?php echo $Lrendicontoanno; ?></p>

    <div class="form-buttons" style="margin-top: 20px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
      <form action="./InsBilancioEconomicoOdv.php" method="post">
        <input type="submit" class="btn-add" value="Bilancio ODV" <?php echo $limit; ?>>
      </form>
      <form action="./InsBilancioEconomicoAps.php" method="post">
        <input type="submit" class="btn-edit" value="Bilancio APS" <?php echo $limit; ?>>
      </form>
    </div>

    <div class="card-description" style="margin-top: 30px; text-align: justify; column-count: 2; column-gap: 20px;">
      <small>
        <i><?php echo $Lnuovoschema; ?></i><br><br>
        <?php echo $Ldescrrendiconto; ?>
      </small>
    </div>
  </div>
</div>

<div class="content-section">
  <div class="card" style="text-align: center;">
    <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="60" style="margin-bottom: 10px;">
    <p><small><i><?php echo $Lhelprendiconto; ?></i></small></p>
  </div>
</div>

<?php
include('./menusx.inc');
include('./botton.inc');
?>

