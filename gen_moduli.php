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
  header('Location: ./CompModuli.php');
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
$data2 = $_POST['data2'];
$data3 = $_POST['data3'];
$presenti = $_POST['presenti'];
$ordine = $_POST['OrdineGiorno'];
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

<div class="cliente-box">
  <h3><?php echo htmlspecialchars($associazione); ?></h3>
  <p><b>Luogo:</b> <?php echo htmlspecialchars($luogo); ?></p>
  <p><b>Data:</b> <?php echo htmlspecialchars($data); ?></p>
</div>

<hr>

<div class="contenuto-modulo">
<?php
switch ($modulo) {

  /* === 1. Richiesta Ammissione Socio === */
  case 'ammissione':
    echo "
    <h2>Richiesta di Ammissione a Socio</h2>
    <p>Il sottoscritto <b>$nome_completo</b>, residente in via <b>$via</b>, <b>$cap</b> – <b>$citta</b>,
    Telefono: <b>$tel</b>, Email: <b>$email</b>,</p>
    <p>chiede di essere ammesso quale socio dell’Associazione <b>$associazione</b>.</p>
    <p>Dichiara di conoscere e accettare lo Statuto e si impegna a rispettare le norme statutarie vigenti e le deliberazioni degli organi sociali.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 2. Ammissione Minore === */
  case 'ammissioneminore':
    echo "
    <h2>Richiesta di Ammissione come Aderente Minorenne</h2>
    <p>Il sottoscritto <b>$nome_completo</b>, residente in via <b>$via</b>, <b>$cap</b> – <b>$citta</b>,
    Telefono: <b>$tel</b>, Email: <b>$email</b>,</p>
    <p>avendo preso visione dello Statuto e dei Regolamenti dell’Associazione, dichiara di condividere la democraticità della struttura e di accettarne le finalità.</p>
    <p><b>CHIEDE</b> di aderire all’Associazione in qualità di aderente minorenne.</p>
    <p><b>Per l’esercente la patria potestà:</b><br>
    presto il consenso all’ammissione del minore <b>$nome_completo</b>.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma del genitore/tutore</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 3. Consenso Privacy === */
  case 'consenso':
    echo "
    <h2>Consenso al Trattamento dei Dati Personali</h2>
    <p>Il sottoscritto <b>$nome_completo</b>, residente in via <b>$via</b>, <b>$cap</b> – <b>$citta</b>,
    Telefono: <b>$tel</b>, Email: <b>$email</b>,</p>
    <p>dichiara di aver ricevuto l’informativa ai sensi dell’art. 13 del D.Lgs. 196/2003 e presta il proprio consenso al trattamento dei dati personali per le finalità indicate.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 4. Verbale Assemblea / Consiglio === */
  case 'consiglio':
    echo "
    <h2>Verbale Assemblea Ordinaria</h2>
    <p>In data <b>$data</b> presso <b>$luogo, $indsede</b> si è tenuta l’assemblea ordinaria
    dell’Associazione <b>$associazione</b> per discutere e deliberare sul seguente ordine del giorno:</p>
    <p><i>$ordine</i></p>
    <p>Presenti: <b>$presenti</b></p>
    <p><i>$verbale</i></p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Il Segretario</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 5. Convocazione Consiglio Direttivo === */
  case 'convocazione':
    $pres = mysqli_query($connect, "SELECT nome, cognome FROM tb_anagrafe WHERE classe='Presidente' LIMIT 1");
    $p = mysqli_fetch_assoc($pres);
    echo "
    <h2>Convocazione del Consiglio Direttivo</h2>
    <p>Egregi Signori,</p>
    <p>a norma dell’art. 2381 cod. civ., si avvisa che il Consiglio Direttivo è convocato per il giorno <b>$data2</b>, presso <b>$luogo</b>, per deliberare sul seguente:</p>
    <p><b>Ordine del Giorno:</b></p>
    <p><i>$ordine</i></p>
    <p>In caso di impossibilità a partecipare, si prega di comunicare l’assenza.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Il Presidente</b></p>
      <p>{$p['nome']} {$p['cognome']}</p>
    </div>";
    break;

  /* === 6. Convocazione Assemblea === */
  case 'convocazioneassemblea':
    $pres = mysqli_query($connect, "SELECT nome, cognome FROM tb_anagrafe WHERE classe='Presidente' LIMIT 1");
    $p = mysqli_fetch_assoc($pres);
    echo "
    <h2>Convocazione Assemblea Ordinaria / Straordinaria</h2>
    <p>A tutti i soci dell’Associazione <b>$associazione</b>.</p>
    <p>Si comunica che l’Assemblea è convocata in prima convocazione il giorno <b>$data2</b> presso <b>$indsede $numsede</b> – <b>$luogo</b>,
    ed eventualmente in seconda convocazione il giorno <b>$data3</b>.</p>
    <p><b>Ordine del Giorno:</b></p>
    <p><i>$ordine</i></p>
    <p>In caso di impossibilità a partecipare, si prega di giustificare l’assenza.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Il Presidente</b></p>
      <p>{$p['nome']} {$p['cognome']}</p>
    </div>";
    break;

  /* === 7. Richiesta Rimborso Spese === */
  case 'rimborso':
    echo "
    <h2>Richiesta Rimborso Spese</h2>
    <p>Il sottoscritto <b>$nome_completo</b>, residente in <b>$via, $cap $citta</b>, Telefono: <b>$tel</b>, Email: <b>$email</b>,</p>
    <p>richiede il rimborso delle spese sostenute in data <b>$data</b> per il seguente motivo:</p>
    <blockquote><i>$verbale</i></blockquote>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 8. Dimissioni === */
  case 'dimissioni':
    echo "
    <h2>Comunicazione di Dimissioni da Socio</h2>
    <p>Il sottoscritto <b>$nome_completo</b>, residente in <b>$via, $cap $citta</b>, Telefono: <b>$tel</b>, Email: <b>$email</b>,</p>
    <p>considerato che <i>$verbale</i>, comunica le proprie irrevocabili dimissioni da socio dell’Associazione.</p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 9. Rapporto Attività === */
  case 'rapporto':
    echo "
    <h2>Rapporto Attività Eseguita</h2>
    <p>I soci <b>$presenti</b> comunicano che in data <b>$data</b> hanno svolto l’attività <b>$ordine</b>.</p>
    <p>Note o specifiche: <i>$verbale</i></p>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
    break;

  /* === 10. Preventivo lavori === */
  case 'preventivo':
    echo "
    <h2>Offerta del $data</h2>
    <p align='right'>Alla cortese attenzione</br>$nome_completo</p>
    &emsp;
    <p align='left'>Oggetto: <i>$presenti</i></p>
    &emsp;
    <p align='left'>$verbale</p>
</br>
    <div class='firma-box'>
      <p>$luogo, $data</p><br>
      <p><b>Firma</b></p>
      <p>__________________</p>
    </div>";
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
