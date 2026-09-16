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
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
  session_start();

$user = $_SESSION['utente'];

$langcalendario = $_SESSION['lingua'];
$paginacalendario = "calendario2.inc";
$linguacalendario = ($langcalendario.$paginacalendario);
include($linguacalendario);

	include ('./dati_db.inc');
	$connect = mysqli_connect("$host", "$username", "$password", "$db_name") or die("cannot connect DB");

include('./top.inc');
include('./menu.inc');


if ($user == 'admin') {
  $limit=''; $limite='';
} else if ($user == 'limitato') {
  $limit='disabled'; $limite='';
} else if ($user == 'operatore') {
  $limit='disabled'; $limite='';
} else if ($user == 'associato') {
  $limit='disabled'; $limite='';
}

function normalizza_data($data) {
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $data, $m)) {
        $giorno = str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $mese   = str_pad($m[2], 2, '0', STR_PAD_LEFT);
        $anno   = $m[3];
        return "$giorno-$mese-$anno";
    }
    return date('d-m-Y'); // fallback
}
?>

     <center><h2><?php echo $Ltitolocal2 ?></h2></center>
<center><small><?php echo $Lsugg1cal2 ?></small></center>
<br><br>
        <table align='center' border='0' width='80%'>
          <tbody>
<?php

// --- APPUNTAMENTI ---
$oggi = date("d-m-Y"); // es: 01-01-2026
echo <<<HTML

<hr style="width: 80%; height: 2px;">

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Gli appuntamenti di oggi $oggi</h3>
    <div class="appointments-3col">
      <div class="appointments-header">
        <span><b>Ora</b></span>
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
        <span>{$row['ora']}</span>
        <span>{$row['titolo']}</span>
        <span>{$row['testo']}</span>
      </div>
ROW;
}

echo <<<HTML
    </div>
  </div>
</section>
<center>
HTML;



//Condizione se non ci sono appuntamenti
$Query = "SELECT str_data FROM appuntamenti WHERE str_data='$oggi' ORDER BY ora ASC;";
$rs=mysqli_query($connect, $Query)
or die('' . mysqli_error());

$riga=mysqli_fetch_array($rs);
if ($riga['str_data'] != $oggi) {
echo $Lnoeventical2;
}

?>
</center>


<?php

// --- SEZIONE APPUNTAMENTI MENSILI ---

// Calcolo mese e anno correnti o navigati
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calcola mese precedente e successivo
$prev_month = $month - 1;
$prev_year  = $year;
$next_month = $month + 1;
$next_year  = $year;

if ($prev_month == 0) { $prev_month = 12; $prev_year--; }
if ($next_month == 13) { $next_month = 1; $next_year++; }

// Nome del mese in italiano
setlocale(LC_TIME, 'it_IT.UTF-8');
$month_name = strftime('%B %Y', mktime(0, 0, 0, $month, 1, $year));

// Intervallo date per query
$start_date = sprintf('1-%d-%d', $month, $year);
$end_date   = date('t', strtotime("$year-$month-01")) . "-$month-$year";

// Query appuntamenti per mese
$Query = "SELECT * FROM appuntamenti
WHERE STR_TO_DATE(str_data, '%e-%c-%Y') BETWEEN
STR_TO_DATE('$start_date', '%e-%c-%Y') AND STR_TO_DATE('$end_date', '%e-%c-%Y')
ORDER BY STR_TO_DATE(str_data, '%e-%c-%Y'), ora ASC";

$rs = mysqli_query($connect, $Query) or die(mysqli_error($connect));

echo <<<HTML
<hr style="width: 80%; height: 2px;">

<section class="content-section">
  <div class="card">
    <div class="calendar-header">
      <a class="nav-btn" href="?month=$prev_month&year=$prev_year">⟨--</a>
      <span class="month-title">$month_name</span>
      <a class="nav-btn" href="?month=$next_month&year=$next_year">--⟩</a>
    </div>

    <h3 class="card-title">Appuntamenti del mese</h3>

    <div class="appointments">
      <div class="appointments-header">
        <span><b>Data</b></span>
        <span><b>Titolo</b></span>
      </div>
HTML;

if (mysqli_num_rows($rs) > 0) {
  while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
    echo <<<ROW
      <div class="appointment-item">
        <span>{$row['str_data']} {$row['ora']}</span>
        <span>{$row['titolo']}</span>
      </div>
ROW;
  }
} else {
  echo "<div class='appointment-item'><span colspan='2'><i>Nessun appuntamento per questo mese.</i></span></div>";
}

echo <<<HTML
    </div>
  </div>
</section>
HTML;

?>
 <form action='./Canc_Calendario.php'>
 <center><button <?php echo($limit);?><?php echo($limite);?> name="cancella" type="submit" class="btn-edit">
   Cancella eventi calendario
 </button></center>
 </form>

<hr style="width: 80%;">

<section class="content-section">
  <div class="card">
    <h3 class="card-title">Importa calendario Google (.ics)</h3>

    <form action="importa_calendario.php" method="post" enctype="multipart/form-data">
      <input type="file" name="ics_file" accept=".ics" required>
      <br><br>
      <button type="submit" class="btn-edit">
        Importa eventi
      </button>
    </form>
  </div>
</section>

<?php
mysqli_close($connect);
include('./menusx.inc');
?><hr><center><img src='./Immagini/suggerimento.png'><small><i><?php echo $Lsugg2cal2 ?><hr></i></small></center>
<?php
include('./botton.inc');

?>
