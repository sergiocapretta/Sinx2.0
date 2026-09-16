<?php
/*
========================================================================+
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
    =====================================================================+
*/
session_start();

$user = $_SESSION['utente'] ?? '';

switch ($user) {
  case 'admin':
    $limit = '';
    $limite = '';
    break;
  case 'limitato':
  case 'operatore':
    $limit = 'disabled';
    $limite = '';
    break;
  case 'associato':
    $limit = 'disabled';
    $limite = 'disabled';
    break;
  default:
    $limit = $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port) or die("Impossibile connettersi al DB");

// Ultimo ID
$result = mysqli_query($connect, "SELECT MAX(id) AS ultimo FROM appuntamenti");
$row = mysqli_fetch_assoc($result);
$ultimoid = $row['ultimo'] ?? 0;

// Codice data da GET
$cod = $_GET['cod'] ?? '';
?>

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Appuntamenti del giorno</h3>
    <div class="appointments-5col">
      <div class="appointments-header">
        <span>ID</span>
        <span>Data</span>
        <span>Titolo</span>
        <span>Ora</span>
        <span>Testo</span>
      </div>

      <?php
      $Query = "SELECT * FROM appuntamenti WHERE str_data = '" . mysqli_real_escape_string($connect, $cod) . "'";
      $rs = mysqli_query($connect, $Query);

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "
        <div class='appointment-item'>
          <span>{$row['id']}</span>
          <span>{$row['str_data']}</span>
          <span>{$row['titolo']}</span>
          <span>{$row['ora']}</span>
          <span>{$row['testo']}</span>
        </div>";
      }
      ?>

<hr class="divider">

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Cancella appuntamento</h3>
    <form action="./conf_canc_cal.php" method="POST" class="sinx-form">
      <label for="id">ID appuntamento:</label>
      <select name="id" id="id">
        <option value="">— Seleziona ID —</option>
        <?php
        for ($a = 1; $a <= $ultimoid; $a++) {
          $query = "SELECT id FROM appuntamenti WHERE id = $a LIMIT 1";
          $rs = mysqli_query($connect, $query);
          if ($row = mysqli_fetch_row($rs)) {
            echo "<option value='{$row[0]}'>{$row[0]}</option>";
          }
        }
        ?>
      </select>

      <label for="data">Data:</label>
      <input type="text" name="data" id="data" value="<?php echo htmlspecialchars($cod); ?>" readonly>

      <button type="submit" <?php echo $limite; ?> class="btn-delete">🗑 Cancella</button>
    </form>
  </div>
</section>

<hr class="divider">

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Modifica / Nuovo appuntamento</h3>
    <form action="./conf_mod_cal.php" method="POST" class="sinx-form">
      <label for="idmod">ID (solo per modifica):</label>
      <select name="id" id="idmod">
        <option value="">— Seleziona ID —</option>
        <?php
        for ($a = 1; $a <= 1000; $a++) {
          $query = "SELECT id FROM appuntamenti WHERE id = $a LIMIT 1";
          $rs = mysqli_query($connect, $query);
          if ($row = mysqli_fetch_row($rs)) {
            echo "<option value='{$row[0]}'>{$row[0]}</option>";
          }
        }
        ?>
      </select>

      <label for="data2">Data:</label>
      <input type="text" name="data" id="data2" value="<?php echo htmlspecialchars($cod); ?>">

      <label for="ora">Ora:</label>
<input type="time" name="ora" id="ora" step="60" <?php echo $limit; ?>>

      <label for="campo">Titolo:</label>
      <input type="text" name="campo" id="campo" placeholder="Inserisci il titolo" <?php echo $limit; ?>>

      <label for="record">Testo:</label>
      <textarea name="record" id="record" rows="4" placeholder="Inserisci la descrizione" <?php echo $limit; ?>></textarea>

      <div class="form-buttons">
        <button type="submit" name="modifica" class="btn-edit" <?php echo $limit . ' ' . $limite; ?>>✏️ Modifica</button>
        <button type="submit" name="nuovo" class="btn-add" <?php echo $limit . ' ' . $limite; ?>>➕ Nuovo</button>
      </div>
    </form>
  </div>
</section>




    </div>
  </div>
</section>
<?php
mysqli_close($connect);
include('./menusx.inc');
?><hr><img src='./Immagini/suggerimento.png'><small><i>Se devi aggiungere un'altro appuntamento, inserisci titolo e descrizione e premi 'Nuovo'.<br>Se devi modificare un appuntamento, devi inserire anche l'id dell'appuntamneto da modificare, lo modifichi e premi 'modifica'
<hr></i></small><?php
include('./botton.inc');
?>
