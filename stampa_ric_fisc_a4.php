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

  $nnumeroric = $_POST['numero'];
  $numeroric = htmlspecialchars($nnumeroric, ENT_NOQUOTES, "UTF-8");

  // Funzione redirect
  function redirect($url, $tempo = FALSE) {
    if (!headers_sent() && $tempo == FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo == FALSE) $tempo = 0;
      echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
    }
  }

  include('./dati_db.inc');
  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

  if ($numeroric == "") {
    echo "<center><b>Il campo Numero è obbligatorio</b></center>";
    redirect('./InsRicFisc.php', 2);
    die();
  }

  $Query = "SELECT * FROM tb_ricevute WHERE id_ric = '$numeroric'";
  $rs = mysqli_query($connect, $Query)
    or die(mysqli_error($connect));

  $row = mysqli_fetch_array($rs);

  if (!$row) {
    echo "<center><b>Nessuna ricevuta trovata con numero $numeroric</b></center>";
    die();
  }
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Ricevuta Fiscale n. <?php echo $row['id_ric']; ?></title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

  <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa ricevuta</button>
  </div>

  <h2>Ricevuta Fiscale</h2>
  <hr>

  <table class="ricevuta-table">
    <tr>
      <th>Numero Ricevuta</th>
      <td><?php echo $row['id_ric']; ?></td>
    </tr>
    <tr>
      <th>Data</th>
      <td><?php echo $row['data']; ?></td>
    </tr>
    <tr>
      <th>Ricevuto da</th>
      <td><?php echo htmlspecialchars($row['nome']); ?></td>
    </tr>
    <tr>
      <th>Importo (€)</th>
      <td><?php echo number_format($row['euro'], 2, ',', '.'); ?></td>
    </tr>
    <tr>
      <th>Causale</th>
      <td><?php echo htmlspecialchars($row['descr']); ?></td>
    </tr>
  </table>

  <div class="firma-box">
    <p>Firma ___________________________________________</p>
  </div>

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
