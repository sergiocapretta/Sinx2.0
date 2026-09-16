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

$user = $_SESSION['utente'];
if ($user == 'admin') {
  $limit = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
}

include('./top.inc');
include('./menu.inc');

$iniz = $_POST['iniziale'];
$associato = $_POST['associato'];

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

$Query_nome = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
$rs = mysqli_query($connect, $Query_nome)
  or die("Errore nella query $Query_nome: " . mysqli_error($connect));
$row = mysqli_fetch_array($rs);
$idprec = $row['id_anagrafe'];
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title">Scheda Associato</h2>

    <div class="form-buttons" style="justify-content:center;">
      <form target="_blank" action="./stampa_scheda_ass.php" method="POST">
        <input type="hidden" name="associato" value="<?php echo $idprec; ?>">
        <input type="submit" value="- Stampa Scheda -" class="btn-add" <?php echo $limit; ?>>
      </form>
    </div>
  </div>
</div>

<hr class="divider">

<?php
include('./modifica_cancella.inc');

$Query_nome = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
$rs = mysqli_query($connect, $Query_nome)
  or die("Errore nella query $Query_nome: " . mysqli_error($connect));

while ($row = mysqli_fetch_array($rs)) :
?>

<div class="content-section">
  <div class="card">
    <h3 class="card-title"><?php echo htmlspecialchars($row['nome'] . ' ' . $row['cognome']); ?></h3>

    <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px;">
      <img src="./Immagini/Utenti/<?php echo $row['immagine']; ?>" class="profile-photo" alt="Foto profilo">
      <form method="post" action="./conf_mod_ass_attivo.php">
        <label><b>ID:</b></label>
        <input type="text" name="id_associato" value="<?php echo $row['id_anagrafe']; ?>" readonly>
        <br>
        <label><b>Socio attivo:</b></label>
        <input type="submit" name="attivo" value="<?php echo $row['associato']; ?>" <?php echo $limit; ?>>
        <br><small><i>(clicca per cambiare stato)</i></small>
      </form>
    </div>

    <div class="sinx-form">
      <label>Tessera</label>
      <input type="text" value="<?php echo htmlspecialchars($row['ntessera']); ?>" readonly>

      <label>Indirizzo</label>
      <input type="text" value="<?php echo htmlspecialchars($row['indirizzo']); ?>" readonly>

      <label>Comune</label>
      <input type="text" value="<?php echo htmlspecialchars($row['cap']); ?>" readonly>

      <label>Regione</label>
      <input type="text" value="<?php echo htmlspecialchars($row['citta']); ?>" readonly>

      <label>Provincia</label>
      <input type="text" value="<?php echo htmlspecialchars($row['provincia']); ?>" readonly>

      <label>Telefono</label>
      <input type="text" value="<?php echo htmlspecialchars($row['tel']); ?>" readonly>

      <label>Telefono 2</label>
      <input type="text" value="<?php echo htmlspecialchars($row['tel2']); ?>" readonly>

      <label>Email</label>
      <input type="text" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>

      <label>Tipo Socio</label>
      <input type="text" value="<?php echo htmlspecialchars($row['materia']); ?>" readonly>

      <label>Codice Fiscale</label>
      <input type="text" value="<?php echo htmlspecialchars($row['nomerif']); ?>" readonly>

      <label>Data Nascita</label>
      <input type="text" value="<?php echo htmlspecialchars($row['datan']); ?>" readonly>

      <label>Carica Amministrativa</label>
      <input type="text" value="<?php echo htmlspecialchars($row['classe']); ?>" readonly>

      <label>Mansione</label>
      <input type="text" value="<?php echo htmlspecialchars($row['mansione']); ?>" readonly>

      <label>Note</label>
      <textarea rows="3" readonly><?php echo htmlspecialchars($row['note']); ?></textarea>
    </div>
  </div>
</div>

<hr class="divider">

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

<!-- FATTURE -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title">Fatture Emesse</h3>
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>Numero</span><span>Data</span><span>Totale</span><span> </span>
      </div>
      <?php
      $Query = "SELECT * FROM tb_tot_fatture WHERE nome = '" . mysqli_real_escape_string($connect, $row['nome']) . "'";
      $rsf = mysqli_query($connect, $Query)
        or die("Errore nella query $Query: " . mysqli_error($connect));

      while ($roww = mysqli_fetch_array($rsf)) :
      ?>
      <div class="appointment-item">
        <span><?php echo $roww['id_tot_fatture']; ?></span>
        <span><?php echo $roww['data']; ?></span>
        <span class="val-entrata"><b><?php echo $roww['tot_fattura']; ?> €</b></span>
        <span></span>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php
endwhile;
mysqli_close($connect);
include('./menusx.inc');
?>

<hr>
<div style="text-align:center;">
  <img src="./Immagini/suggerimento.png" alt="Suggerimento">
  <small><i>Scorrendo in fondo, è possibile visualizzare le ricevute e le fatture emesse all’associato.</i></small>
</div>

<?php include('./botton.inc'); ?>
