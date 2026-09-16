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
$langrubrica = $_SESSION['lingua'];
$paginarubrica = "rubrica.inc";
$linguarubrica = ($langrubrica.$paginarubrica);
include($linguarubrica);

if ($user) {
  include('./top.inc');
  include('./menu.inc');

  include('./dati_db.inc');
  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");


  // 🔹 Gestione della ricerca
  $ricerca = isset($_POST['ricerca']) ? trim($_POST['ricerca']) : "";
?>

<!-- ===== RUBRICA SINX ===== -->
<div class="content-section">
  <div class="card">
    <h2 class="card-title">
      <i class="material-icons">contacts</i> <?php echo $Lpresentazionerubrica; ?>
    </h2>

<!-- 🔹 Barra di ricerca -->
    <form method="POST" class="sinx-form" style="margin-bottom: 20px;">
      <input type="search" name="ricerca" placeholder="<?php echo $Lricercanome ?? 'Cerca per nome o cognome...'; ?>" value="<?php echo htmlspecialchars($ricerca); ?>">
      <div class="form-buttons">
        <button type="submit" class="btn-add">
          <i class="material-icons" style="vertical-align: middle;">search</i> <?php echo $Lcerca ?? 'Cerca'; ?>
        </button>
        <?php if ($ricerca != ""): ?>
          <a href="Rubrica.php" class="btn-delete" style="text-align:center; text-decoration:none; padding:10px 16px;">
            <i class="material-icons" style="vertical-align: middle;">close</i> <?php echo $Lpulisci ?? 'Pulisci'; ?>
          </a>
        <?php endif; ?>
      </div>
    </form>

    <hr class="divider">

    <!-- Intestazione colonne -->
    <div class="appointments appointments-5col">
      <div class="appointments-header">
        <span><b><?php echo $Lnome; ?></b></span>
        <span><b><?php echo $Lcognome; ?></b></span>
        <span><b><?php echo $Lindirizzo; ?></b></span>
        <span><b><?php echo $Lcitta; ?></b></span>
        <span><b><?php echo $Lprovincia; ?></b></span>
        <span><b><?php echo $Lemail; ?></b></span>
        <span><b><?php echo $Ltelefono; ?></b></span>
        <span><b><?php echo $Ltelefono; ?> 2</b></span>
        <span><b><?php echo $Ldatanasc; ?></b></span>
      </div>

      <?php

      // 🔹 Se l’utente ha fatto una ricerca
      if ($ricerca != "") {
        $ricerca_sql = mysqli_real_escape_string($connect, $ricerca);
        $query = "
          SELECT * FROM tb_anagrafe
          WHERE nome LIKE '%$ricerca_sql%'
             OR cognome LIKE '%$ricerca_sql%'
          ORDER BY cognome, nome
        ";
        $rs = mysqli_query($connect, $query) or die("<b>Errore query ricerca.</b>");

        if (mysqli_num_rows($rs) > 0) {
          echo "<h3 class='card-subtitle'>Risultati per: <i>" . htmlspecialchars($ricerca) . "</i></h3>";
          while ($row = mysqli_fetch_assoc($rs)) {
            echo "<div class='appointment-item'>
                    <span>{$row['nome']}</span>
                    <span>{$row['cognome']}</span>
                    <span>{$row['indirizzo']}</span>
                    <span>{$row['cap']} {$row['citta']}</span>
                    <span>{$row['provincia']}</span>
                    <span>{$row['email']}</span>
                    <span>{$row['tel']}</span>
                    <span>{$row['tel2']}</span>
                    <span>{$row['datan']}</span>
                  </div>";
          }
        } else {
          echo "<p style='text-align:center; color:#666;'>Nessun risultato trovato per '<b>" . htmlspecialchars($ricerca) . "</b>'</p>";
        }

 // 🔹 Altrimenti mostra la rubrica completa per lettera
      } else {
      $alfabeto = range('a', 'z');
      foreach ($alfabeto as $lettera) {
        $Query_nome = "SELECT * FROM tb_anagrafe
                       WHERE cognome REGEXP '^$lettera'
                       ORDER BY cognome";
        $rs = mysqli_query($connect, $Query_nome)
          or die("<b>Errore:</b> Impossibile eseguire la query.");

        // Titolo della sezione (lettera iniziale)
        echo "<h3 class='card-subtitle' style='background-color:#fffdb9; padding:6px; border-radius:6px;'>– " . strtoupper($lettera) . " –</h3>";

        while ($row = mysqli_fetch_assoc($rs)) {
          echo "<div class='appointment-item'>
                  <span>{$row['nome']}</span>
                  <span>{$row['cognome']}</span>
                  <span>{$row['indirizzo']}</span>
                  <span>{$row['cap']} {$row['citta']}</span>
                  <span>{$row['provincia']}</span>
                  <span>{$row['email']}</span>
                  <span>{$row['tel']}</span>
                  <span>{$row['tel2']}</span>
                  <span>{$row['datan']}</span>
                </div>";
		}
	  }
        }

      ?>
    </div>
  </div>
</div>
<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');

header('Location: ./index.php');
}
?>
