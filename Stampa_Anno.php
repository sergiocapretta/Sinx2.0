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

if ($user == 'admin') {

  include('./Intestazione.php');
  include('./dati_db.inc');
  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
      or die("cannot connect DB");
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Stampa Contabilità - Sinx</title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

<div class="no-print" style="text-align:center;">
  <button class="print-btn" onclick="window.print()">🖨️ Stampa Contabilità</button>
</div>

<!-- ========================== LIBRO SOCI ========================== -->
<h2>📘 Libro Soci</h2>
<table class="ce-table">
  <thead>
    <tr>
      <th>N. Tessera</th>
      <th>Nome</th>
      <th>Indirizzo</th>
      <th>Città</th>
      <th>Provincia</th>
      <th>Cod. Fiscale</th>
      <th>Tipo</th>
      <th>Funzione</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $Query = "SELECT * FROM tb_anagrafe WHERE tipologia != 'Extra' AND associato = 'si' ORDER BY ntessera";
  $rs = mysqli_query($connect, $Query);
  while ($row = mysqli_fetch_array($rs)) {
    echo "<tr>
            <td>{$row['ntessera']}</td>
            <td>{$row['nome']}</td>
            <td>{$row['indirizzo']}</td>
            <td>{$row['citta']}</td>
            <td>{$row['provincia']}</td>
            <td>{$row['nomerif']}</td>
            <td>{$row['materia']}</td>
            <td>{$row['classe']}</td>
          </tr>";
  }
  ?>
  </tbody>
</table>

<hr>

<!-- ========================== PRIMA NOTA ========================== -->
<h2>💰 Registro Prima Nota</h2>
<table class="pn-table">
  <thead>
    <tr>
      <th>Data</th>
      <th>Descrizione</th>
      <th>Entrata Cassa</th>
      <th>Uscita Cassa</th>
      <th>Entrata Banca</th>
      <th>Uscita Banca</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $rs = mysqli_query($connect, "SELECT * FROM tb_primanota ORDER BY id_primanota");
  while ($row = mysqli_fetch_array($rs)) {
    echo "<tr>
            <td>{$row['data_registr']}</td>
            <td>{$row['descrizione']}</td>
            <td class='val'>{$row['entrata']}</td>
            <td class='val'>{$row['uscita']}</td>
            <td class='val'>{$row['entratab']}</td>
            <td class='val'>{$row['uscitab']}</td>
          </tr>";
  }

  // Totali
  $entrata_cassa = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entrata) FROM tb_primanota"))[0];
  $uscita_cassa = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscita) FROM tb_primanota"))[0];
  $entrata_banca = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(entratab) FROM tb_primanota"))[0];
  $uscita_banca = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(uscitab) FROM tb_primanota"))[0];
  ?>
  </tbody>
</table>

<table class="totals-table">
  <tr><td>Entrata Cassa</td><td class="val"><?= $entrata_cassa ?> €</td></tr>
  <tr><td>Uscita Cassa</td><td class="val"><?= $uscita_cassa ?> €</td></tr>
  <tr class="highlight"><td>Saldo Cassa</td><td class="val"><b><?= $entrata_cassa - $uscita_cassa ?> €</b></td></tr>
  <tr><td>Entrata Banca</td><td class="val"><?= $entrata_banca ?> €</td></tr>
  <tr><td>Uscita Banca</td><td class="val"><?= $uscita_banca ?> €</td></tr>
  <tr class="highlight"><td>Saldo Banca</td><td class="val"><b><?= $entrata_banca - $uscita_banca ?> €</b></td></tr>
</table>

<hr>

<!-- ========================== FATTURE ========================== -->
<h2>📄 Elenco Fatture</h2>
<table class="ce-table">
  <thead>
    <tr>
      <th>Numero</th>
      <th>Data</th>
      <th>Cliente</th>
      <th>Totale (IVA inclusa)</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $rs = mysqli_query($connect, "SELECT id_tot_fatture, tot_fattura, nome, data FROM tb_tot_fatture ORDER BY id_tot_fatture");
  while ($row = mysqli_fetch_array($rs)) {
    echo "<tr>
            <td>{$row['id_tot_fatture']}</td>
            <td>{$row['data']}</td>
            <td>{$row['nome']}</td>
            <td class='val'>{$row['tot_fattura']} €</td>
          </tr>";
  }
  ?>
  </tbody>
</table>

<hr>

<!-- ========================== RICEVUTE ========================== -->
<h2>🧾 Elenco Ricevute</h2>
<table class="ce-table">
  <thead>
    <tr>
      <th>Numero</th>
      <th>Data</th>
      <th>Nome</th>
      <th>Importo (€)</th>
      <th>Descrizione</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $rs = mysqli_query($connect, "SELECT * FROM tb_ricevute ORDER BY id_ric");
  while ($row = mysqli_fetch_array($rs)) {
    echo "<tr>
            <td>{$row['id_ric']}</td>
            <td>{$row['data']}</td>
            <td>{$row['nome']}</td>
            <td class='val'>{$row['euro']}</td>
            <td>{$row['descr']}</td>
          </tr>";
  }
  ?>
  </tbody>
</table>

<hr>

<!-- ========================== CONTO ECONOMICO ========================== -->
<h2>📊 Conto Economico</h2>
<table class="dual-table">
  <thead>
    <tr>
      <th colspan="3">Proventi e Ricavi</th>
      <th colspan="3">Costi e Oneri</th>
    </tr>
    <tr>
      <th>ID</th><th>Descrizione</th><th>Importo</th>
      <th>ID</th><th>Descrizione</th><th>Importo</th>
    </tr>
  </thead>
  <tbody>
  <tr>
    <td colspan="3" valign="top">
    <?php
      $ricavi = mysqli_query($connect, "SELECT * FROM tb_conto_economico WHERE costoricavo='ricavi' ORDER BY id");
      while ($r = mysqli_fetch_array($ricavi)) {
        echo "<div>{$r['id']} - {$r['descrizione']} ({$r['valore']} €)</div>";
      }
    ?>
    </td>
    <td colspan="3" valign="top">
    <?php
      $oneri = mysqli_query($connect, "SELECT * FROM tb_conto_economico WHERE costoricavo='oneri' ORDER BY id");
      while ($r = mysqli_fetch_array($oneri)) {
        echo "<div>{$r['id']} - {$r['descrizione']} ({$r['valore']} €)</div>";
      }
    ?>
    </td>
  </tr>
  </tbody>
</table>

<?php
$entrate = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo='ricavi'"))[0];
$uscite  = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_conto_economico WHERE costoricavo='oneri'"))[0];
?>
<table class="totals-table">
  <tr><td>Totale Ricavi</td><td class="val"><?= $entrate ?> €</td></tr>
  <tr><td>Totale Oneri</td><td class="val"><?= $uscite ?> €</td></tr>
  <tr class="highlight"><td>Avanzo di Gestione</td><td class="val"><b><?= $entrate - $uscite ?> €</b></td></tr>
</table>

<hr>

<!-- ========================== STATO PATRIMONIALE ========================== -->
<h2>🏛️ Stato Patrimoniale</h2>
<table class="dual-table">
  <thead>
    <tr>
      <th colspan="3">Attività</th>
      <th colspan="3">Passività</th>
    </tr>
    <tr>
      <th>ID</th><th>Descrizione</th><th>Importo</th>
      <th>ID</th><th>Descrizione</th><th>Importo</th>
    </tr>
  </thead>
  <tbody>
  <tr>
    <td colspan="3" valign="top">
    <?php
      $attivita = mysqli_query($connect, "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo='attivita' ORDER BY id");
      while ($r = mysqli_fetch_array($attivita)) {
        echo "<div>{$r['id']} - {$r['descrizione']} ({$r['valore']} €)</div>";
      }
    ?>
    </td>
    <td colspan="3" valign="top">
    <?php
      $passivita = mysqli_query($connect, "SELECT * FROM tb_stato_patrimoniale WHERE costoricavo='passivita' ORDER BY id");
      while ($r = mysqli_fetch_array($passivita)) {
        echo "<div>{$r['id']} - {$r['descrizione']} ({$r['valore']} €)</div>";
      }
    ?>
    </td>
  </tr>
  </tbody>
</table>

<?php
$attiv = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo='attivita'"))[0];
$passiv = mysqli_fetch_row(mysqli_query($connect, "SELECT SUM(valore) FROM tb_stato_patrimoniale WHERE costoricavo='passivita'"))[0];
?>
<table class="totals-table">
  <tr><td>Totale Attività</td><td class="val"><?= $attiv ?> €</td></tr>
  <tr><td>Totale Passività</td><td class="val"><?= $passiv ?> €</td></tr>
  <tr class="highlight"><td>Avanzo Patrimoniale</td><td class="val"><b><?= $attiv - $passiv ?> €</b></td></tr>
</table>

<div class="footer">
  <p>Sinx - Sistema Gestionale Associativo<br>Report contabile generato automaticamente</p>
</div>

</body>
</html>

<?php
mysqli_close($connect);
} else {
  header('Location: ./index.php');
}
?>


