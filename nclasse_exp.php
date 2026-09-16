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
$langfunzioni = $_SESSION['lingua'];
$paginafunzioni = "nclasse.inc";
$linguafunzioni = ($langfunzioni . $paginafunzioni);
include($linguafunzioni);

if ($user == 'admin') {
  $limit = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
}

include('./top.inc');
include('./menu.inc');

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");
?>

<link rel="stylesheet" href="/style.css">

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolofunzione; ?></h2>
    <p class="card-subtitle"><?php echo $Lcancfunzione; ?></p>

    <!-- 🔹 Cancellazione funzione -->
    <form action="./conf_canc.php?Tabella=tb_classe&Riferimento=id_classe" method="POST" class="sinx-form">
      <label><?php echo $Listrcancfunzione; ?></label>
      <input type="number" name="id_mod" placeholder="ID funzione" required>
      <button type="submit" class="btn-delete" <?php echo $limit; ?>>
        <?php echo $Lcancella ?? '- Cancella -'; ?>
      </button>
    </form>

    <hr class="divider">

    <!-- 🔹 Modifica funzione -->
    <h3 class="card-title"><?php echo $Lmodfunzione; ?></h3>
    <p class="card-subtitle"><?php echo $Listrmodfunzione; ?></p>

    <form action="./conf_mod.php?Tabella=tb_classe&Voce=classe&Riferimento=id_classe" method="POST" class="sinx-form">
      <label>ID:</label>
      <input type="number" name="id_mod" placeholder="ID funzione">

      <label><?php echo $Lnuovorecfunzione; ?>:</label>
      <input type="text" name="record" placeholder="Nuovo nome funzione">

      <button type="submit" class="btn-edit" <?php echo $limit; ?>>
        <?php echo $Lmodifica ?? 'Modifica'; ?>
      </button>
    </form>

    <hr class="divider">

    <!-- 🔹 Elenco funzioni -->
    <h3 class="card-title"><?php echo $Lelencofunzione; ?></h3>
    <p class="card-subtitle"><?php echo $Ldescrelencofunzione; ?></p>

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><b>ID</b></span>
        <span><b><?php echo $Lfunzione; ?></b></span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM tb_classe ORDER BY classe";
      $rs = mysqli_query($connect, $Query_nome)
        or die("<b>Errore:</b> Impossibile eseguire la query");

      while ($row = mysqli_fetch_array($rs)) {
        echo "<div class='appointment-item'>";
        echo "<span>{$row['id_classe']}</span>";
        echo "<span>{$row['classe']}</span>";
        echo "</div>";
      }
      ?>
    </div>

    <hr class="divider">

    <!-- 🔹 Sezione aiuto -->
    <div class="text-center">
      <img src="/Immagini/suggerimento.png" alt="Suggerimento" style="width:40px; vertical-align:middle;">
      <small><i><?php echo $Lhelpfunzioni; ?></i></small>
    </div>

    <hr class="divider">

    <!-- 🔹 Pulsante Torna indietro -->
    <div class="text-center">
      <a href="./nclasse.php" class="btn-add">← Torna indietro</a>
    </div>
  </div>
</div>

<?php include('./botton.inc'); ?>
