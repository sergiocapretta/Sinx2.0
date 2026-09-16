<?php
/*======================================================================+
 File name   : Scheda_associato.php
 Begin       : 2010-08-04
 Last Update : 2014-01-04

 Description : card associated

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

$user = $_SESSION['utente'] ?? null;

// Gestione permessi
$limite = '';
if ($user === 'limitato' || $user === 'operatore' || $user === 'associato') {
  $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');

$associato = (int)($_POST['associato'] ?? 0);

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// --- Recupero dati associato ---
$query_assoc = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
$rs = mysqli_query($connect, $query_assoc)
  or die("Errore nella query $query_assoc: " . mysqli_error($connect));
$row = mysqli_fetch_assoc($rs);

if (!$row) {
  echo "<p class='text-center'><b>Nessun associato trovato.</b></p>";
  exit;
}

// Gestione immagine
$foto = "./Immagini/Utenti/" . $row['immagine'];
if (!file_exists($foto) || empty($row['immagine'])) {
  $foto = "/ImmTemplate/nofoto.png"; // immagine di fallback
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Scheda Associato</title>
  <link rel="stylesheet" href="/style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<div class="content-section">
  <div class="card">

    <h2 class="card-title"><i class="material-icons">person</i> Scheda Associato</h2>
    <hr class="divider">

    <form method="POST" action="./Scheda_associato_exp.php" class="text-center">
      <h4>
        Stampa | Cancella | Modifica
        <input type="submit" <?php echo $limite; ?> name="associato" value="<?php echo $associato; ?>" class="btn-mini">
      </h4>
    </form>

    <!-- === BLOCCO FOTO + DATI BASE === -->
    <div class="flex items-center justify-center gap-6 my-4">
      <div class="photo-box">
        <img src="<?php echo $foto; ?>" alt="Foto associato" class="profile-photo">
      </div>
      <div class="text-left">
        <h3><?php echo htmlspecialchars($row['nome'] . ' ' . $row['cognome']); ?></h3>
        <p><b>ID:</b> <?php echo $row['id_anagrafe']; ?> | <b>Tessera:</b> <?php echo $row['ntessera']; ?></p>
        <p><b>Email:</b> <?php echo $row['email']; ?></p>
        <p><b>Tipo Socio:</b> <?php echo $row['materia']; ?></p>
      </div>
    </div>

    <hr class="divider">

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>Campo</span>
        <span>Valore</span>
        <span></span>
        <span></span>
      </div>

      <div class="appointment-item">
        <span><b>Indirizzo</b></span>
        <span><?php echo $row['indirizzo']; ?></span>
        <span><b>Comune:</b> <?php echo $row['cap']; ?></span>
        <span><b>Regione:</b> <?php echo $row['citta']; ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Provincia</b></span>
        <span><?php echo $row['provincia']; ?></span>
        <span><b>Telefono:</b> <?php echo $row['tel']; ?></span>
        <span><b>Telefono 2:</b> <?php echo $row['tel2']; ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Codice Fiscale</b></span>
        <span><?php echo $row['nomerif']; ?></span>
        <span><b>Data nascita:</b> <?php echo $row['datan']; ?></span>
        <span><b>Carica Amm.:</b> <?php echo $row['classe']; ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Mansione</b></span>
        <span><?php echo $row['mansione']; ?></span>
        <span><b>Note:</b></span>
        <span><?php echo $row['note']; ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Socio attivo</b></span>
        <span>
          <form method="post" action="./conf_mod_ass_attivo.php">
            <input type="hidden" name="id_associato" value="<?php echo $row['id_anagrafe']; ?>">
            <input type="submit" <?php echo $limite; ?> name="attivo" value="<?php echo $row['associato']; ?>" class="btn-mini">
          </form>
        </span>
        <span colspan="2"><small><i><-- clicca per cambiare</i></small></span>
      </div>
    </div>

    <hr class="divider">

    <?php
    // === RICEVUTE ===
    $nome_completo = $row['cognome'] . ' ' . $row['nome'];
    $query_ricevute = "SELECT * FROM tb_ricevute WHERE nome = '" . mysqli_real_escape_string($connect, $nome_completo) . "'";
    $rb = mysqli_query($connect, $query_ricevute);

    if (mysqli_num_rows($rb) > 0) {
      echo "<h3 class='card-title'><i class='material-icons'>receipt</i> Ricevute Emesse</h3>";
      echo "<div class='appointments appointments-receipts'>";
      echo "<div class='appointments-header'>
              <span>ID</span><span>Data</span><span>Euro</span><span>Descrizione</span><span></span><span></span><span></span>
            </div>";

      while ($ric = mysqli_fetch_assoc($rb)) {
        echo "<div class='receipt-item'>
                <span>{$ric['id_ric']}</span>
                <span>{$ric['data']}</span>
                <span>{$ric['euro']}</span>
                <span>{$ric['descr']}</span>
                <span></span><span></span><span></span>
              </div>";
      }
      echo "</div><hr class='divider'>";
    }

    ?>
    <!-- RICEVUTE -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title">Ricevute Emesse</h3>
    <div class="appointments appointments-4col appointments-receipts">
      <div class="appointments-header">
        <span>Num.</span><span>Data</span><span>Importo</span><span>Descrizione</span>
      </div>
      <?php
      $Query = "SELECT * FROM tb_ricevute WHERE nome = '" . mysqli_real_escape_string($connect, $row['nome']) . "'";
      $rb = mysqli_query($connect, $Query)
        or die("Errore nella query $Query: " . mysqli_error($connect));

      while ($roow = mysqli_fetch_array($rb)) :
      ?>
      <div class="receipt-item">
        <span><?php echo $roow['id_ric']; ?></span>
        <span><?php echo $roow['data']; ?></span>
        <span class="val-entrata"><?php echo $roow['euro']; ?> €</span>
        <span><?php echo htmlspecialchars($roow['descr']); ?></span>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<hr class="divider">
<?php

    // === FATTURE ===
    $query_fatture = "SELECT * FROM tb_tot_fatture WHERE nome = '" . mysqli_real_escape_string($connect, $row['nome']) . "'";
    $rf = mysqli_query($connect, $query_fatture);

    if (mysqli_num_rows($rf) > 0) {
      echo "<h3 class='card-title'><i class='material-icons'>description</i> Fatture Emesse</h3>";
      echo "<table align='center' border='0' cellpadding='4' cellspacing='2' width='80%'>";
      while ($fatt = mysqli_fetch_assoc($rf)) {
        echo "<tr>
                <td><b>Fatt. num:</b> {$fatt['id_tot_fatture']}</td>
                <td><b>Data:</b> {$fatt['data']}</td>
                <td><b>Totale (IVA incl.):</b> {$fatt['tot_fattura']}</td>
              </tr>";
      }
      echo "</table>";
    }


?>
<center>
<div class="text-center" style="margin-top:30px;">
      <a href="./ricerca.php" class="btn-add">← Vai alla ricerca</a>
    </div>
</center>
  </div>
</div>
<?php
mysqli_close($connect);
include('./menusx.inc');
?><hr><img src='./Immagini/suggerimento.png'><small><i>Scorrendo in fondo, &egrave possibile visualizzare le ricevute emesse all'associato
<hr></i></small><?php
include('./botton.inc');

?>
