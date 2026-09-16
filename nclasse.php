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
  $limit = ''; $limite = '';
} else if ($user == 'limitato') {
  $limit = 'disabled'; $limite = '';
} else if ($user == 'operatore') {
  $limit = 'disabled'; $limite = '';
} else if ($user == 'associato') {
  $limit = 'disabled'; $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');

include('./dati_db.inc');
$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
  or die("cannot connect DB");
?>

<!-- CONTAINER PRINCIPALE -->
<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitolofunzione; ?></h2>

    <!-- Pulsante per cancellazione/modifica -->
    <form action="./nclasse_exp.php" method="post" class="sinx-form">
      <button type="submit" name="stampa" class="btn-edit" <?php echo($limit); echo($limite); ?>>
        <i class="material-icons">edit</i> Cancella | Modifica
      </button>
    </form>

    <hr class="divider">

    <!-- Inserimento nuova funzione -->
    <h3 class="card-subtitle"><?php echo $Lnuovofunzione; ?></h3>

    <form action="./conf_nmateria.php?Tabella=tb_classe&Colonna=classe&Pagina=nclasse"
          method="POST"
          class="sinx-form">

      <label for="nrecord"><?php echo $Lnuovafunzione; ?>:</label>
      <input type="text" name="nrecord" id="nrecord" placeholder="Inserisci nuova funzione">

      <div class="form-buttons">
        <button type="submit" class="btn-add" <?php echo($limit); echo($limite); ?>>
          <i class="material-icons">send</i> Invia
        </button>
      </div>
    </form>

    <hr class="divider">

    <!-- Elenco funzioni -->
    <h3 class="card-subtitle"><?php echo $Lelencofunzione; ?></h3>
    <p class="text-center"><small><?php echo $Ldescrelencofunzione; ?></small></p>

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><b>ID <?php echo $Lfunzione; ?></b></span>
        <span><b><?php echo $Lfunzione; ?></b></span>
      </div>

      <?php
      $Query_nome = "SELECT * FROM tb_classe ORDER BY classe";
      $rs = mysqli_query($connect, $Query_nome)
        or die("<b>Errore:</b> Impossibile eseguire la query.");

      while ($row = mysqli_fetch_array($rs)) {
        echo "<div class='appointment-item'>
                <span>{$row['id_classe']}</span>
                <span>{$row['classe']}</span>
              </div>";
      }
      ?>
    </div>

    <hr class="divider">

    <!-- Suggerimento -->
    <div class="text-center">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" style="width:40px;vertical-align:middle;">
      <small><i><?php echo $Lhelpfunzioni; ?></i></small>
    </div>
  </div>
</div>

<?php
include('./menusx.inc');
include('./botton.inc');
?>
