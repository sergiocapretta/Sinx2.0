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

  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

  $query = "SELECT * FROM tb_primanota ORDER BY str_to_date(data_registr, '%d-%m-%Y')";
  $rs = mysqli_query($connect, $query)
    or die("<b>Errore:</b> Impossibile eseguire la query.");
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Registro Prima Nota</title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

  <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa registro</button>
  </div>

  <h2>Registro Prima Nota Semplice</h2>
  <hr>

  <table class="pn-table">
    <thead>
      <tr>
        <th>Data registr.</th>
        <th>Descrizione</th>
        <th colspan="2">Cassa</th>
        <th colspan="2">Banca</th>
      </tr>
      <tr class="subheader">
        <th></th>
        <th></th>
        <th>Entrata</th>
        <th>Uscita</th>
        <th>Entrata</th>
        <th>Uscita</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_array($rs)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['data_registr']); ?></td>
        <td><?php echo htmlspecialchars($row['descrizione']); ?></td>
        <td class="val"><?php echo $row['entrata']; ?></td>
        <td class="val"><?php echo $row['uscita']; ?></td>
        <td class="val"><?php echo $row['entratab']; ?></td>
        <td class="val"><?php echo $row['uscitab']; ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <?php
  // Totali
  $entrata_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entrata) FROM tb_primanota"))[0];
  $uscita_tot  = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscita) FROM tb_primanota"))[0];
  $entratab_tot = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entratab) FROM tb_primanota"))[0];
  $uscitab_tot  = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscitab) FROM tb_primanota"))[0];
  ?>

  <h3>Totali</h3>
  <table class="totals-table">
    <tr>
      <td><b>Entrata Cassa:</b></td>
      <td class="val"><?php echo number_format($entrata_tot, 2, ',', '.'); ?> €</td>
    </tr>
    <tr>
      <td><b>Uscita Cassa:</b></td>
      <td class="val"><?php echo number_format($uscita_tot, 2, ',', '.'); ?> €</td>
    </tr>
    <tr class="highlight">
      <td><b>Guadagno/Perdita Cassa:</b></td>
      <td class="val"><b><?php echo number_format($entrata_tot - $uscita_tot, 2, ',', '.'); ?> €</b></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
      <td><b>Entrata Banca:</b></td>
      <td class="val"><?php echo number_format($entratab_tot, 2, ',', '.'); ?> €</td>
    </tr>
    <tr>
      <td><b>Uscita Banca:</b></td>
      <td class="val"><?php echo number_format($uscitab_tot, 2, ',', '.'); ?> €</td>
    </tr>
    <tr class="highlight">
      <td><b>Guadagno/Perdita Banca:</b></td>
      <td class="val"><b><?php echo number_format($entratab_tot - $uscitab_tot, 2, ',', '.'); ?> €</b></td>
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



