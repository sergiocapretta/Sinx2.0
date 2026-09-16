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

$langnomstud = $_SESSION['lingua'];
$paginanomstud = "ricercanomestud.inc";
$linguanomstud = ($langnomstud . $paginanomstud);
include($linguanomstud);

if ($user) {
  include('./top.inc');
  include('./menu.inc');

  $iniz = $_POST['iniziale'] ?? '';
  $tipologia = $_GET['tipologia'] ?? '';

  include('./dati_db.inc');
  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");

  $Query_nome = "SELECT * FROM tb_anagrafe
                 WHERE tipologia = '$tipologia'
                 AND nome REGEXP '^$iniz'
                 ORDER BY nome";
  $rs = mysqli_query($connect, $Query_nome)
    or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
  ?>

  <div class="content-section">
    <div class="card">
      <h2 class="card-title"><?php echo $Lelencosoci; ?></h2>

      <div class="actions" style="text-align:center; margin-bottom:20px;">
        <a href="InsAnagrExtra.php" class="btn-edit">➕ Nuovo Collaboratore</a></br></br>
        <a href="InsAnagrIns.php" class="btn-edit">➕ Nuovo Associato</a></br></br>
        <a href="InsAnagrStud.php" class="btn-edit">➕ Nuovo Associato Direttivo</a>
      </div>

      <div class="appointments appointments-5colass">
        <div class="appointments-header">
          <span>ID</span>
          <span><?php echo $Lntessera; ?></span>
          <span><?php echo $Lnome; ?></span>
          <span><?php echo $LFunzTipo; ?></span>
          <span><?php echo $LAttivo; ?></span>
        </div>

        <?php while ($row = mysqli_fetch_assoc($rs)): ?>
          <div class="appointment-item">
            <span>
              <form method="post" action="./Scheda_associato.php" style="margin:0;">
                <input type="submit" name="associato" value="<?php echo $row['id_anagrafe']; ?>" class="btn-mini">
              </form>
            </span>
            <span><?php echo htmlspecialchars($row['ntessera']); ?></span>
            <span><?php echo htmlspecialchars($row['nome'] . ' ' . $row['cognome']); ?></span>
            <span><?php echo htmlspecialchars($row['classe'] . ' ' . $row['mansione'] . ' ' . $row['materia']); ?></span>
            <span><?php echo htmlspecialchars($row['associato']); ?></span>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <?php


mysqli_close($connect);
include('./menusx.inc');
?><hr><img src='./Immagini/suggerimento.png'><small><i><?php echo $Lnota; ?><hr></i></small><?php
include('./botton.inc');
} else {
header('Location: ./index.php');
}
?>
