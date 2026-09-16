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
$user = $_SESSION['utente'] ?? null;

if (!$user) {
  header("Location: ./Redirect_no_enter.php");
  exit;
}

// Impostazione dei permessi
$limit = '';
$limite = '';
switch ($user) {
  case 'admin':
    $limit = $limite = '';
    break;
  case 'limitato':
  case 'operatore':
    $limit = 'disabled';
    $limite = '';
    break;
  case 'associato':
    $limit = 'disabled';
    $limite = '';
    break;
}

include('./top.inc');
include('./menu.inc');

// Lingua dinamica
$langricerca = $_SESSION['lingua'];
$paginaricerca = "schedaregioni.inc";
$linguaricerca = $langricerca . $paginaricerca;
include($linguaricerca);

// Connessione DB
include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Errore di connessione al database: " . mysqli_connect_error());
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Lidmodifica . " / " . $Lnome; ?></h2>
    <p class="card-subtitle"><?php echo $LgestioneRegioni ?? "Gestione regioni italiane"; ?></p>

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>ID</span>
        <span>Regione</span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM regioni ORDER BY id_reg ASC";
      $rs = mysqli_query($connect, $Query_nome)
        or die("Errore nella query: " . mysqli_error($connect));

      while ($row = mysqli_fetch_assoc($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id_reg']}</span>
                <span>{$row['nome_regione']}</span>
              </div>";
      }
      ?>
    </div>

    <hr class="divider">

    <!-- 🔹 Form di modifica/inserimento -->
    <form action="./conf_regioni.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label for="id"><b><?php echo $Lidmodifica; ?></b></label>
      <input type="text" name="id" id="id" size="5" required <?php echo $limit; ?>>

      <label for="nome"><b><?php echo $Lnome; ?></b></label>
      <input type="text" name="nome" id="nome" size="25" required <?php echo $limit; ?>>

      <div class="form-buttons">
        <button type="submit" class="btn-edit" <?php echo $limit . $limite; ?>>
          <?php echo $Lmodifica ?? 'Modifica'; ?>
        </button>
      </div>
    </form>

    <hr class="divider">

    <!-- 🔹 Navigazione tra schede -->
    <div class="form-buttons">
      <form action="./Scheda_regioni.php" method="POST">
        <button name="Regioni" value="regioni" class="btn-add">Regioni</button>
      </form>

      <form action="./Scheda_province.php" method="POST">
        <button name="Province" value="province" class="btn-add">Province</button>
      </form>

      <form action="./Scheda_comuni.php" method="POST">
        <button name="Comuni" value="comuni" class="btn-add">Comuni</button>
      </form>
    </div>
  </div>
</div>
<center>
<?php
mysqli_close($connect);
include('./menusx.inc');
echo $Lsuggerimento;
include('./botton.inc');

?>
</center>
