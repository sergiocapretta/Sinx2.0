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

if (!isset($_SESSION['utente']) || $_SESSION['utente'] !== 'admin') {
    header('Location: index2.php');
    exit;
}

include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name)
    or die("Errore DB");

mysqli_set_charset($connect, 'utf8mb4');

include('./top.inc');
include('./menu.inc');
?>

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Cancella singolo evento</h3>

    <form action="./conf_canc_cal.php" method="post">
      <select name="id" required style="width:100%; padding:8px;">
        <option value="">-- seleziona evento --</option>

<?php
$query = "
SELECT id, titolo, str_data, ora
FROM appuntamenti
ORDER BY
  STR_TO_DATE(str_data, '%e-%c-%Y') DESC,
  ora DESC
";

$rs = mysqli_query($connect, $query) or die(mysqli_error($connect));

while ($row = mysqli_fetch_assoc($rs)) {
    $ora = ($row['ora'] !== '00:00:00') ? substr($row['ora'], 0, 5) : '';
    echo "<option value='{$row['id']}'>
        {$row['str_data']} {$ora} – {$row['titolo']}
    </option>";
}
?>

      </select>
      <br><br>
    <center>  <button type="submit" class="btn-edit">
        Cancella evento selezionato
      </button></center>
    </form>
  </div>
</section>

<hr style="width:80%;">

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Cancella tutto il calendario</h3>

    <form action="./conf_canc_all_cal.php" method="post"
          onsubmit="return confirm('ATTENZIONE: cancellare TUTTI gli eventi?');">
      <center><button type="submit" class="btn-edit" >
        Cancella tutto
      </button></center>
    </form>
  </div>
</section>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
