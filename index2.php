<?php
session_start();

/*======================================================================+
 File name   : index2.php
 Begin       : 2010-08-04
 Last Update : 2022-05-22

 Description : The sinx's first page

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
=========================================================================+
*/


$user = $_SESSION['utente'];
$nutente = $_SESSION['nome'];
$langu = $_SESSION['lingua'];
$paginaindex2 = "index2.inc";
$linguaindex2 = ($langu.$paginaindex2);
include($linguaindex2);

if ($user) {
include('./top.inc');
include('./menu.inc');

	include ('./dati_db.inc');
	$link=mysqli_connect("$host", "$username", "$password","$db_name")or die(mysqli_connect_error("Non posso connettermi al database"));

//Funzione compleanno
$compleanno = date('j-n');
$compleanno2 = date('d-m');
$oggi = date("d-m-Y");
$query = "SELECT nome,cognome
	FROM tb_anagrafe
	WHERE datan REGEXP '^$compleanno2' OR datan REGEXP '^$compleanno'
	ORDER BY id_anagrafe";


$rs=@mysqli_query($link, $query) or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
while($row=mysqli_fetch_array($rs,MYSQLI_ASSOC))
{
  if($row)
  {
    echo "<br><h3><center><font color='red'>Oggi <i>".$row['nome']."&nbsp".$row['cognome']."</i> compie gli anni</font></center></h3>";
    }
}

?>
<h2><?php echo $Lbenvenuto.chr(32).$nutente; ?></h2>

      <div style="margin-left: 20px;"><small><?php echo $Lfrase; ?></center></div></small>

<?php
// --- BLOCCO NOTE ---
$Query_nome = "SELECT * FROM tb_note";
$rs = mysqli_query($link, $Query_nome) or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
$row = mysqli_fetch_array($rs, MYSQLI_ASSOC);

echo <<<HTML
<section class="content-section">
  <div class="card">
    <h3 class="card-title">$Lptitolonote</h3>
    <p class="card-subtitle">$LlinkDaHome <a href="./InsNotepad.php" class="link-btn">Blocco Note</a></p>
    <p><b>$Lcompnote</b></p>

    <form action="./conf_note.php" method="POST" enctype="multipart/form-data">
      <textarea name="formcontent" rows="12" readonly class="note-area">{$row['testo']}</textarea>
    </form>
  </div>
</section>

<hr class="divider">
HTML;

// --- APPUNTAMENTI ---
$oggi = date("d-m-Y");
echo <<<HTML
<section class="content-section">
  <div class="card">
    <h3 class="card-title"> Appuntamenti per il $oggi</h3>
    <div class="appointments">
      <div class="appointments-header">
        <span><b>Titolo</b></span>
        <span><b>Descrizione</b></span>
      </div>

HTML;

// Popolo la tabella appuntamenti del giorno
$Query = "SELECT * FROM appuntamenti WHERE str_data='$oggi'";
$rs = mysqli_query($connect, $Query) or die(mysqli_error($connect));

while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
    echo <<<ROW
      <div class="appointment-item">
        <span>{$row['titolo']}</span>
        <span>{$row['testo']}</span>
      </div>
ROW;
}

echo <<<HTML
    </div>
  </div>
</section>

<hr class="divider">
<!-- SCRIPT SOSTEGNO -->
  <div><small><center> $Ldonazione </center></small></br></div>
      <center><script async
  src="https://js.stripe.com/v3/buy-button.js">
</script>

<stripe-buy-button
  buy-button-id="buy_btn_1NffVFLKMcvTs3yVET94mIoQ"
  publishable-key="pk_live_4Zm3b1O9tH7DagjYQBq6wItl"
>
</stripe-buy-button></center>

<center> $echo $Lhelpindex2 </center>
HTML;

include('./menusx.inc');
include('./botton.inc');

@mysqli_close($link);
} else {
header('Location: ./index.php');
}

?>
