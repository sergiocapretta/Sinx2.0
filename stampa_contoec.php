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

if ($user) {

    include('./Intestazione.php');
    include('./dati_db.inc');
    $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port) or die("cannot connect DB");

    // Calcoli preliminari
    $query = "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo = 'ricavi'";
    $entrata = mysqli_fetch_row(mysqli_query($connect, $query))[0] ?? 0;

    $query = "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo = 'oneri'";
    $uscita = mysqli_fetch_row(mysqli_query($connect, $query))[0] ?? 0;

    $avanzo = $entrata - $uscita;
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Conto Economico</title>
<link rel="stylesheet" href="style_stampa.css">
</head>
<body>

<div class="no-print" style="text-align:center;">
  <button class="print-btn" onclick="window.print()">🖨️ Stampa Conto Economico</button>
</div>

<div class="page">
  <h2>Conto Economico</h2>
  <hr>

  <table class="ce-table">
    <thead>
      <tr>
        <th colspan="3">Proventi e Ricavi</th>
        <th colspan="3">Costi e Oneri</th>
      </tr>
      <tr>
        <th>ID</th><th>Descrizione</th><th>Importo (€)</th>
        <th>ID</th><th>Descrizione</th><th>Importo (€)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Recupero ricavi e oneri
      $ricavi = mysqli_query($connect, "SELECT * FROM tb_conto_economico WHERE costoricavo = 'ricavi' ORDER BY id");
      $oneri = mysqli_query($connect, "SELECT * FROM tb_conto_economico WHERE costoricavo = 'oneri' ORDER BY id");

      // Creo un array con righe parallele
      $righe = max(mysqli_num_rows($ricavi), mysqli_num_rows($oneri));

      for ($i = 0; $i < $righe; $i++) {
          $r1 = mysqli_fetch_array($ricavi);
          $r2 = mysqli_fetch_array($oneri);

          echo "<tr>";
          echo "<td>" . ($r1['id'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($r1['descrizione'] ?? '') . "</td>";
          echo "<td class='val'>" . ($r1['valore'] ?? '') . "</td>";
          echo "<td>" . ($r2['id'] ?? '') . "</td>";
          echo "<td>" . htmlspecialchars($r2['descrizione'] ?? '') . "</td>";
          echo "<td class='val'>" . ($r2['valore'] ?? '') . "</td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>

  <hr>

  <table class="totals-table">
    <tr>
      <td><strong>Totale Proventi:</strong></td>
      <td class="val"><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
      <td></td>
      <td><strong>Totale Spese:</strong></td>
      <td class="val"><?php echo number_format($uscita, 2, ',', '.'); ?> €</td>
    </tr>
    <tr>
      <td></td><td></td><td></td>
      <td><strong>Avanzo di Gestione:</strong></td>
      <td class="val"><strong><?php echo number_format($avanzo, 2, ',', '.'); ?> €</strong></td>
    </tr>
    <tr>
      <td><small>Totale a pareggio</small></td>
      <td class="val"><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
      <td></td>
      <td><small>Totale a pareggio</small></td>
      <td class="val"><?php echo number_format($entrata, 2, ',', '.'); ?> €</td>
    </tr>
  </table>

  <div class="footer">
    <hr>
    <small>Generato da Sinx Gestionale — <?php echo date('d/m/Y H:i'); ?></small>
  </div>
</div>

</body>
</html>

<?php
    mysqli_close($connect);
} else {
    header('Location: ./index.php');
}
?>



