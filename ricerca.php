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
$langricerca = $_SESSION['lingua'];
$paginaricerca = "ricerca.inc";
$linguaricerca = ($langricerca.$paginaricerca);
include($linguaricerca);

if ($user == 'admin') {
  $limit='';
} else if ($user == 'associato') {
  $limit='disabled';
} else if ($user == 'limitato') {
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
  echo "<center>$Lsugg1utente<b>$user</b> <br>$Lsugg2utente<b>Admin</b></center>";
redirect('./Redirect_no_enter.php',0);
}


include('./top.inc');
include('./menu.inc');



$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
  or die("cannot connect DB");
?>

<div class="card" style="max-width:900px; margin:auto;">
  <h2 class="text-center"><?php echo $Ltitoloricerca; ?></h2>

  <!-- 🔹 SEZIONE FONDATORI -->
  <div class="sinx-form"><center>
    <h3><i class="material-icons">perm_identity</i> <?php echo $Lfondatori; ?></h3>
    <form action="./ricerca_nome_stud.php?tipologia=Stud" method="POST">
      <label><?php echo $Lricercanome; ?></label>
      <select name="iniziale" required>
        <option value="">—</option>
        <?php foreach (range('A', 'Z') as $lettera) echo "<option value='".strtolower($lettera)."'>$lettera</option>"; ?>
      </select>
      <button type="submit" class="btn-add"><?php echo $Lricercanome; ?></button>
    </form>

    <form action="./ricerca_classe_stud.php" method="POST">
      <label><?php echo $Lfunzione; ?></label>
      <select name="tipo">
        <option value=""><?php echo $Lfunzione; ?></option>
        <?php
          $query = "SELECT classe FROM tb_classe";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) echo "<option>$row[0]</option>";
        ?>
      </select>
      <button type="submit" class="btn-edit"><?php echo $Lfunzione; ?></button>
    </form>
  </center></div>

  <hr class="divider">

  <!-- 🔹 SEZIONE ASSOCIATI -->
  <div class="sinx-form"><center>
    <h3><i class="material-icons">supervisor_account</i> <?php echo $Lassociati; ?></h3>

    <form action="./ricerca_nome_stud.php?tipologia=Ins" method="POST">
      <label><?php echo $Lricercanome; ?></label>
      <select name="iniziale" required>
        <option value="">—</option>
        <?php foreach (range('A', 'Z') as $lettera) echo "<option value='".strtolower($lettera)."'>$lettera</option>"; ?>
      </select>
      <button type="submit" class="btn-add"><?php echo $Lricercanome; ?></button>
    </form>

    <form action="./ricerca_tipo_ass.php" method="POST">
      <label><?php echo $Ltipo; ?></label>
      <select name="tipo">
        <option value=""><?php echo $Ltipo; ?></option>
        <?php
          $query = "SELECT materia FROM tb_materia";
          $rs = mysqli_query($connect, $query);
          while ($row = mysqli_fetch_row($rs)) echo "<option>$row[0]</option>";
        ?>
      </select>
      <button type="submit" class="btn-edit"><?php echo $Ltipo; ?></button>
    </form>
</center>  </div>

  <hr class="divider">

  <!-- 🔹 SEZIONE CONTATTI EXTRA -->
  <div class="sinx-form"><center>
    <h3><i class="material-icons">portrait</i> <?php echo $Lcontatti; ?></h3>
    <form action="./ricerca_nome_stud.php?tipologia=Extra" method="POST">
      <button type="submit" class="btn-add"><?php echo $Lricercanome; ?></button>
    </form>
 </center> </div>

  <hr class="divider">

  <!-- 🔹 SEZIONE RICERCA RICEVUTE -->
  <div class="sinx-form">
    <h2><center><i class="material-icons">receipt</i> <?php echo $Lricercaricevute; ?></center></h2>
    <small><i><center><?php echo $Lnota2; ?></i></center></small>

    <form action="./ricerca_ricevute.php" method="POST">
      <fieldset>
        <legend><?php echo $Lcamporicerca; ?></legend>
        <label><input type="radio" name="campo" value="nome" checked> <?php echo $Lricercanome; ?></label>
        <label><input type="radio" name="campo" value="descr"> <?php echo $Lricercadescrizione; ?></label>
        <label><input type="radio" name="campo" value="euro"> <?php echo $Lvalore; ?></label>
        <label><input type="radio" name="campo" value="data"> <?php echo $Lricercadata; ?></label>
      </fieldset>

      <input type="search" name="ricevute" placeholder="Cerca..." required>
      <button type="submit" class="btn-add"><?php echo $Lcerca; ?></button>
    </form>
  </div>

  <hr class="divider">

  <div class="text-center">
    <h4><?php echo $Ltitoloesempio; ?></h4>
    <small><?php echo $Lesempioricric; ?></small>
  </div>
</div>


<?php

mysqli_close($connect);
include('./menusx.inc');
echo $Lhelpricerca;
include('./botton.inc');

?>
