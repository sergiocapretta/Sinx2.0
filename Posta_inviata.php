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
if ($user) {

  include('./top.inc');
  include('./menu.inc');
  include('./dati_db.inc');

  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");

  // Recupero le mail salvate
  $Query_nome = "SELECT * FROM tb_email ORDER BY data DESC";
  $rs = mysqli_query($connect, $Query_nome)
    or die("<b>Errore:</b> Impossibile eseguire la query della tabella email");

  ?>
  <div class="content-section">
    <div class="card">
      <h2 class="card-title">📧 Lista di mail salvate</h2>
      <p class="card-subtitle">Tutte le comunicazioni archiviate nel sistema</p>

      <div class="appointments appointments-4col">
        <div class="appointments-header">
          <span><b>id</b></span>
          <span><b>Data</b></span>
          <span><b>Destinatario</b></span>
          <span><b>Messaggio</b></span>
        </div>

        <?php while ($row = mysqli_fetch_assoc($rs)) { ?>
          <div class="appointment-item">
            <span><?php echo htmlspecialchars($row['id_mail']); ?></span>
            <span><?php echo htmlspecialchars($row['data']); ?></span>
            <span><?php echo htmlspecialchars($row['dest']); ?></span>
            <span><?php echo nl2br($row['testo']); ?></span>
          </div>
        <?php } ?>
      </div>

      <div class="form-buttons" style="margin-top: 25px;">
        <a href="./Comp_email.php" class="btn-add">📨 Ritorna alla posta</a>
      </div>
    </div>
  </div>

  <?php
  mysqli_close($connect);
  include('./menusx.inc');
  include('./botton.inc');

} else {
  header('Location: ./index.php');
}
?>
