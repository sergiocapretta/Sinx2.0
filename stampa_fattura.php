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

  $fattura = $_POST['id'];
  if ($fattura == "") {
    echo "<center><b>Numero fattura non selezionato</b><br>Inserisci un numero di fattura</center>";
    redirect('./InsFattura.php', 2);
    die("");
  }

  $dataoggi = date('d/m/Y');

  include('./dati_db.inc');
  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

  $Query_nome = "SELECT nome FROM tb_fatture WHERE id_fatt = $fattura LIMIT 1";
  $rs = mysqli_query($connect, $Query_nome) or die(mysqli_error($connect));
  $row = mysqli_fetch_array($rs);
  $nomecl = $row['nome'];

  $query_cliente = "SELECT nome, indirizzo, cap, citta, provincia, nomerif
                    FROM tb_anagrafe WHERE nome = '$nomecl'";
  $comando = mysqli_query($connect, $query_cliente) or die(mysqli_error($connect));
  $array = mysqli_fetch_array($comando);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Fattura n. <?php echo $fattura; ?></title>
  <link rel="stylesheet" href="style_stampa.css">

</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom:10px;">
  <button class="print-btn" onclick="window.print()">🖨️ Stampa fattura</button>
</div>

<div class="fattura-header">
  <h2>Fattura n. <?php echo $fattura; ?></h2>
  <p>Data: <strong><?php echo $dataoggi; ?></strong></p>
  <hr>
</div>

<div class="cliente-box">
  <h3>Dati Cliente</h3>
  <p><strong><?php echo $array['nome']; ?></strong></p>
  <p><?php echo $array['indirizzo']; ?></p>
  <p><?php echo $array['cap'] . " " . $array['citta'] . " (" . $array['provincia'] . ")"; ?></p>
  <p><small>Cod. Fiscale / P.IVA:</small> <?php echo $array['nomerif']; ?></p>
</div>

<h3 style="text-align:center;">Dettaglio Fattura</h3>
<table class="dettagli-fattura">
  <thead>
    <tr>
      <th>Q.tà</th>
      <th>Descrizione</th>
      <th>Prezzo Unitario</th>
      <th>IVA</th>
      <th>Totale</th>
    </tr>
  </thead>
  <tbody>
<?php
  $Query_tab = "SELECT quantita, descr, euro, iva, modpaga, totale FROM tb_fatture WHERE id_fatt = '$fattura'";
  $ris = mysqli_query($connect, $Query_tab) or die(mysqli_error($connect));

  $modpagamento = "";
  $perciva = 0;

  while ($riga = mysqli_fetch_array($ris)) {
    $modpagamento = $riga["modpaga"];
    $perciva = $riga['iva'];
    echo "<tr>
            <td align='center'>{$riga['quantita']}</td>
            <td>{$riga['descr']}</td>
            <td align='right'>" . number_format($riga['euro'], 2, ',', '.') . " €</td>
            <td align='center'>{$riga['iva']}%</td>
            <td align='right'><strong>" . number_format($riga['totale'], 2, ',', '.') . " €</strong></td>
          </tr>";
  }

  $query = "SELECT SUM(totale) as totale_numero FROM tb_fatture WHERE id_fatt = $fattura";
  $result = mysqli_query($connect, $query);
  $row = mysqli_fetch_array($result);
  $totale = $row['totale_numero'];
  $totNoIva = $totale / (1 + ($perciva / 100));
  $totIva = $totNoIva * ($perciva / 100);
?>
  </tbody>
</table>

<h3 style="text-align:center;">Riepilogo Totali</h3>
<table class="totali">
  <tr><td>Imponibile:</td><td><?php echo number_format($totNoIva, 2, ',', '.'); ?> €</td></tr>
  <tr><td>IVA (<?php echo $perciva; ?>%):</td><td><?php echo number_format($totIva, 2, ',', '.'); ?> €</td></tr>
  <tr><td><strong>Totale Fattura:</strong></td><td><strong><?php echo number_format($totale, 2, ',', '.'); ?> €</strong></td></tr>
</table>

<div class="modalita">
  <strong>Modalità di pagamento:</strong><br>
  <?php echo htmlspecialchars($modpagamento); ?>
</div>

<div class="footer">
  <hr>
  <p>Data stampa: <?php echo date('d/m/Y H:i'); ?></p>
  <p>Software gestionale Sinx — www.sinx.it</p>
</div>

</body>
</html>

<?php
  mysqli_close($connect);
} else {
  header('Location: ./index.php');
}
?>
