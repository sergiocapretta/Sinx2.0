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

  $associato = $_POST['associato'];
  $Query_nome = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
  $rs = mysqli_query($connect, $Query_nome)
    or die("Errore nella query $Query_nome: " . mysqli_error($connect));

  while ($row = mysqli_fetch_array($rs)) {
    ?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Scheda Associato - <?php echo htmlspecialchars($row['nome'] . " " . $row['cognome']); ?></title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

  <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa scheda</button>
  </div>

  <h2>Scheda Associato</h2>
  <hr>

  <div class="photo">
    <?php if (!empty($row['immagine'])): ?>
      <img src="./Immagini/Utenti/<?php echo htmlspecialchars($row['immagine']); ?>" alt="Foto associato">
    <?php else: ?>
      <img src="./Immagini/Utenti/default.png" alt="Foto non disponibile">
    <?php endif; ?>
  </div>

  <div class="cliente-box">
    <h3><?php echo htmlspecialchars($row['nome'] . " " . $row['cognome']); ?></h3>
    <p><b>ID:</b> <?php echo $row['id_anagrafe']; ?></p>
    <p><b>Indirizzo:</b> <?php echo htmlspecialchars($row['indirizzo']); ?></p>
    <p><b>CAP:</b> <?php echo htmlspecialchars($row['cap']); ?></p>
    <p><b>Città:</b> <?php echo htmlspecialchars($row['citta']); ?> (<?php echo htmlspecialchars($row['provincia']); ?>)</p>
    <p><b>Telefono:</b> <?php echo htmlspecialchars($row['tel']); ?><?php if ($row['tel2']) echo " / " . htmlspecialchars($row['tel2']); ?></p>
    <p><b>Email:</b> <?php echo htmlspecialchars($row['email']); ?></p>
    <p><b>Codice Fiscale:</b> <?php echo htmlspecialchars($row['nomerif']); ?></p>
    <p><b>Data di nascita:</b> <?php echo htmlspecialchars($row['datan']); ?></p>
    <p><b>Tipo Socio:</b> <?php echo htmlspecialchars($row['materia']); ?></p>
    <p><b>Funzione:</b> <?php echo htmlspecialchars($row['classe']); ?></p>
    <p><b>Mansione:</b> <?php echo htmlspecialchars($row['mansione']); ?></p>
    <p><b>Socio Attivo:</b> <?php echo htmlspecialchars($row['associato']); ?></p>
    <p><b>Note:</b> <?php echo nl2br(htmlspecialchars($row['note'])); ?></p>
  </div>

  <h3>Ricevute Emesse</h3>
  <table class="receipts-table">
    <tr>
      <th>Numero</th>
      <th>Data</th>
      <th>Importo (€)</th>
      <th>Descrizione</th>
    </tr>

    <?php
    $Query = "SELECT * FROM tb_ricevute WHERE nome = '$row[nome]'";
    $rb = mysqli_query($connect, $Query)
      or die("Errore nella query $Query: " . mysqli_error($connect));

    while ($rec = mysqli_fetch_array($rb)) {
      echo "<tr>
              <td>{$rec['id_ric']}</td>
              <td>{$rec['data']}</td>
              <td style='text-align:right;'>{$rec['euro']}</td>
              <td>{$rec['descr']}</td>
            </tr>";
    }
    ?>
  </table>

  <div class="footer">
    <hr>
    <p>Documento generato da Sinx - Gestionale per Associazioni No-Profit</p>
    <p>Data di stampa: <?php echo date("d/m/Y"); ?></p>
  </div>

</body>
</html>
<?php
  } // fine while
  mysqli_close($connect);
} else {
  header('Location: ./index.php');
}
?>
