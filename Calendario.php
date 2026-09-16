<?php
/*======================================================================+
Sinx for Association - Gestionale per Associazioni no-profit
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
=========================================================================+*/

  session_start();

$user = $_SESSION['utente'];

function appuntamenti() {

$langcal = $_SESSION['lingua'];
$paginacal = "calendario.inc";
$linguacal = ($langcal.$paginacal);
include($linguacal);

include('./top.inc');
include('./menu.inc');

	include ('./dati_db.inc');
	$connect = mysqli_connect("$host", "$username", "$password", "$db_name" )or die("cannot connect DB");

?>
<center>
     <p align="center"><h2><?php echo $Ltitolocal ?></h2></p>
<small><?php echo $Lnota1calendario ?></small></center>
<br>
      <form action="./Calendario.php" method="post">
        <table align='center' border='0' width='60%'>
          <tbody>
<?php

//Funzione per il redirect
function redirect($url,$tempo = FALSE ){
 if(!headers_sent() && $tempo == FALSE ){
  header('Location:' . $url);
 }elseif(!headers_sent() && $tempo != FALSE ){
  header('Refresh:' . $tempo . ';' . $url);
 }else{
  if($tempo == FALSE ){
    $tempo = 0;
  }
  echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
  }
} 

// inserimento dati
if (isset($_POST['submit']) && $_POST['submit']=="invia")
{
  $titolo = addslashes($_POST['titolo']);
  $testo = addslashes($_POST['testo']);

// Recupero la data dal form
$str_data = $_POST['data'] ?? '';

// Normalizzazione gg-mm-aaaa
if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $str_data, $matches)) {
    $giorno = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
    $mese   = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
    $anno   = $matches[3];
    $str_data = "$giorno-$mese-$anno";
} else {
    // Se non rispetta il formato, imposto default odierna
    $str_data = date('d-m-Y');
}

// Escape per sicurezza prima dell'inserimento
$str_data = mysqli_real_escape_string($connect, $str_data);
  $ora = $_POST['ora'] ?? '00:00';
$ora = mysqli_real_escape_string($connect, $ora) . ':00';

//Controllo campi compilati

		if ($titolo == "")
 		{
   		echo $Lavvertenzacalendario;
   		redirect('./Calendario2.php' ,2);
		// break;
		die ("");
		}

		if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $ora)) {
    $ora = '00:00:00';
}

$sql = "INSERT INTO appuntamenti (titolo, testo, str_data, ora)
        VALUES ('$titolo', '$testo', '$str_data', '$ora')";
  if($result = mysqli_query($connect, $sql) or die (mysqli_error()))
  {
    redirect('./conferma.php?rif=Calendario2' ,0);
  }
}else{
$string = $_GET['cod'];
  ?>
<tr>
  <td><font color="red"><b><?php echo $Ltitolocalendario ?> *:</b></td>
  <td><input name="titolo" type="text"></td><br>
</tr>
<tr>
  <td><?php echo $Ltestocalendario ?>:</td>
  <td><textarea name="testo" cols="130" rows="8"></textarea></td><br>
</tr>
<tr>
  <td><?php echo $Ldatacalendario ?>:</td>
  <td>
    <input name="data" type="text"
           pattern="\d{1,2}-\d{1,2}-\d{4}"
           placeholder="gg-mm-aaaa"
           value="<?php echo htmlspecialchars($string); ?>"
           title="Formato: gg-mm-aaaa"
           required>
  </td><br>
</tr>
<tr>
  <td><?php echo $Loracalendario ?? 'Ora'; ?>:</td>
  <td>
    <input name="ora" type="time" value="00:00">
  </td>
</tr>
<tr><td></td><td><input name="submit" type="submit" value="invia" class="btn-edit" ></td></tr>
</tbody>
</table>
</form>

<?php
}

mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
}

if ($user == 'admin') {
  appuntamenti();
} else if ($user == 'limitato') {
  header('Location: ./Redirect_no_enter.php');
} else if ($user == 'operatore') {
  $limit='';
  appuntamenti();
} else if ($user == 'associato') {
 header('Location: ./Redirect_no_enter.php');
 
}
?>
