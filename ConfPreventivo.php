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

if (!$user) {
  header('Location: ./InsPreventivo.php');
  exit;
}

include('./Intestazione.php');
include('./dati_db.inc');

$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
  or die("cannot connect DB");

// 🧩 Ricezione ID socio
$id_assoc = intval($_POST['nomeass']);
$modulo = $_POST['modulo'];
$data = $_POST['data'];
$presenti = $_POST['presenti'];
$verbale = $_POST['Verbale'];

// 🧩 Query anagrafe per ID
$query = "SELECT nome, cognome, indirizzo, provincia, cap, tel, email, citta
          FROM tb_anagrafe
          WHERE id_anagrafe = $id_assoc";
$rs = mysqli_query($connect, $query) or die("Errore nella query anagrafe");
$row = mysqli_fetch_assoc($rs);

$nome = $row['nome'];
$cognome = $row['cognome'];
$nome_completo = "$nome $cognome";
$via = $row['indirizzo'];
$citta = $row['citta'];
$cap = $row['cap'];
$tel = $row['tel'];
$email = $row['email'];

// 🧩 Dati associazione
$rss = mysqli_query($connect, "SELECT * FROM tb_anagrafe_associaz");
$riga = mysqli_fetch_assoc($rss);

$luogo = $riga['citta'];
$indsede = $riga['indirizzo'];
$numsede = $riga['numero'];
$associazione = $riga['nome'];

$salvaDownload = isset($_POST['salvaDownload']) ? true : false;
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Modulo - <?php echo ucfirst($modulo); ?></title>
  <link rel="stylesheet" href="style_stampa.css">
</head>
<body>

<div class="no-print" style="text-align:center; margin-bottom:20px;">
  <button class="print-btn" onclick="window.print()">🖨️ Stampa modulo</button>

  <!-- Nuovo pulsante per invio email -->
  <form action="Comp_email.php" method="POST" style="display:inline;">
    <input type="hidden" name="subject" value="<?php echo ucfirst($modulo); ?>">
    <input type="hidden" name="formcontent" id="emailContent">
    <button type="submit" class="print-btn" style="background:#4caf50;">📧 Invia via email</button>
  </form>
</div>

<script>
  // Inserisce automaticamente nel campo nascosto il corpo HTML del modulo
  document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.contenuto-modulo').innerHTML;
    document.getElementById('emailContent').value = content;
  });
</script>

<hr>

<div class="contenuto-modulo">
<?php
switch ($modulo) {

  /* === Preventivo lavori === */
  case 'Preventivo':
    echo "
    <h2>Offerta del $data</h2>
    <p align='right'><i>Alla cortese attenzione</i></br><b>$nome_completo</b></br>Via $via</br>$cap $citta</p>
    &emsp;
    <p align='left'><b>Oggetto: <i>$presenti</i></b></p>
    &emsp;
    <p align='left'>$verbale</p>
</br>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";

    if ($salvaDownload) {
    // Percorso cartella
    $dir = __DIR__ . "/Download/";

    // Se non esiste, la creo
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    // Nome file: Preventivo_<id_assoc>_<data>.html
    $safeDate = str_replace("-", "", $data);
    $filename = "Preventivo_" . $id_assoc . "_" . $safeDate . ".html";

    $filepath = $dir . $filename;

    // Contenuto identico al modulo mostrato
    $contenuto = "
    <html>
    <head><meta charset='utf-8'><title>Preventivo</title></head>
    <body>
    <h2>Offerta del $data</h2>
    <p align='right'><i>Alla cortese attenzione</i></br><b>$nome_completo</b></br>Via $via</br>$cap $citta</p>
    <p><b>Oggetto: <i>$presenti</i></b></p>
    <p>$verbale</p>
    <p>$luogo, $data</p>
    </body>
    </html>";

    // Salvataggio file
    file_put_contents($filepath, $contenuto);

    echo "<div class='no-print' style='text-align:center; margin-top:20px;'>
            <p style='color:green;'><b>✔ Copia salvata in:</b> /Download/$filename</p>
          </div>";
}
    break;

  default:
    echo "<p><i>Modulo non riconosciuto o non ancora gestito.</i></p>";
    break;
}
?>
</div>

<div class="footer">
  <hr>
  <p>Documento generato da <b>Sinx</b> – Gestionale per Associazioni No-Profit</p>
  <p>Data stampa: <?php echo date("d/m/Y"); ?></p>
</div>

</body>
</html>

<?php mysqli_close($connect); ?>
