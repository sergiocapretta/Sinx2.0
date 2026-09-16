<?php
/*======================================================================+
 File name   : stampa_statopat.php
 Begin       : 2012-09-21
 Last Update : 2012-09-21

 Description : Page format for printing listen

 Author: Sergio Capretta

 (c) Copyright:
               Sergio Capretta
             
               ITALY
               www.sinx.it
               info@sinx.it

Sinx for Association - Gestionale per Associazioni no-profit
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
=========================================================================+*/

session_start();

$user = $_SESSION['utente'];

if ($user) {

  include('./Intestazione.php');
  include('./dati_db.inc');

  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

  // Query attività e passività
  $query_att = "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo = 'attivita' ORDER BY id";
  $query_pas = "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo = 'passivita' ORDER BY id";

  $res_att = mysqli_query($connect, $query_att) or die(mysqli_error($connect));
  $res_pas = mysqli_query($connect, $query_pas) or die(mysqli_error($connect));

  // Totali
  $tot_att = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo = 'attivita'"))[0];
  $tot_pas = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo = 'passivita'"))[0];
  $avanzo = $tot_att - $tot_pas;
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Stato Patrimoniale</title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

  <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa Stato Patrimoniale</button>
  </div>

  <h2>Stato Patrimoniale</h2>
  <hr>

  <table class="ce-table">
    <thead>
      <tr>
        <th colspan="3">Attività</th>
        <th colspan="3">Passività</th>
      </tr>
      <tr>
        <th>ID</th><th>Descrizione</th><th class="val">Importo (€)</th>
        <th>ID</th><th>Descrizione</th><th class="val">Importo (€)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Calcolo righe parallele
      $righe = max(mysqli_num_rows($res_att), mysqli_num_rows($res_pas));
      for ($i = 0; $i < $righe; $i++) {
        $att = mysqli_fetch_assoc($res_att);
        $pas = mysqli_fetch_assoc($res_pas);
        echo "<tr>";
        echo "<td>" . ($att['id'] ?? '') . "</td>";
        echo "<td>" . ($att['descrizione'] ?? '') . "</td>";
        echo "<td class='val'>" . (isset($att['valore']) ? number_format($att['valore'], 2, ',', '.') : '') . "</td>";
        echo "<td>" . ($pas['id'] ?? '') . "</td>";
        echo "<td>" . ($pas['descrizione'] ?? '') . "</td>";
        echo "<td class='val'>" . (isset($pas['valore']) ? number_format($pas['valore'], 2, ',', '.') : '') . "</td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>

  <table class="totals-table">
    <tr>
      <td><b>Totale Attività</b></td>
      <td class="val"><?php echo number_format($tot_att, 2, ',', '.'); ?> €</td>
      <td style="width:10%;"></td>
      <td><b>Totale Passività</b></td>
      <td class="val"><?php echo number_format($tot_pas, 2, ',', '.'); ?> €</td>
    </tr>
    <tr class="highlight">
      <td></td><td></td><td></td>
      <td><b>Avanzo di Gestione</b></td>
      <td class="val"><b><?php echo number_format($avanzo, 2, ',', '.'); ?> €</b></td>
    </tr>
  </table>

  <div class="footer">
    <hr>
    <p>Documento generato da Sinx - Gestionale per Associazioni No-Profit</p>
    <p>Data di stampa: <?php echo date("d/m/Y"); ?></p>
  </div>

</body>
</html>
<?php
  mysqli_close($connect);
} else {
  header('Location: ./index.php');
}
?>


