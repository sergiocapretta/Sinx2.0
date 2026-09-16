
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<!--======================================================================+
 File name   : top.inc
 Begin       : 2010-08-04
 Last Update : 2012-08-27

 Description : The top of all pages

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
-->
<?php
$nutente = $_SESSION['nome'];
$user = $_SESSION['utente'];
$langtop = $_SESSION['lingua'];
$langmenu = $_SESSION['lingua'];

$paginatop = "topLang.inc";
$linguatop = ($langtop.$paginatop);
include($linguatop);


$paginamenu = "menu.inc";
$linguamenu = ($langmenu.$paginamenu);
include($linguamenu);

//LOG DI SINX
if (isset($_SERVER['HTTP_REFERER'])){
$provenienza = $_SERVER['HTTP_REFERER'];
} else {
$provenienza = "";
};
$data = date("d-m-y"); $ora = date("G:i:s");
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

$testo = "<br>\n\r------\n\r<br>".$data." ".$ora."<br>";
$testo = $testo."\n\rIndirizzo IP: ".$ip."<br>";
$testo = $testo."\n\rProvenienza connessione: ".$provenienza."<br>";
$testo = $testo."\n\rAltre info: ".$browser."<br>";
$testo = $testo."\n\rUtente: ".$nutente."<br>";
$testo = $testo."\n\rLivello Utente: ".$user;

$var=fopen("./log/logSinx.txt","a");
fwrite($var,$testo);
fclose($var);

include ('./dati_db.inc');
$connect = mysqli_connect("$host", "$username", "$password", "$db_name") or die("cannot connect DB");
$Query_nome = "SELECT * FROM tb_anagrafe_associaz";

$rs=mysqli_query($connect, $Query_nome)
or die("<b>Errore:</b> Impossibile eseguire la query della Combo");
$row=mysqli_fetch_array($rs);

// --- METEO BASATO SULL'INDIRIZZO DELL'ASSOCIAZIONE ---
// (NO API KEY, usa Nominatim + Open-Meteo)

$full_address = trim($row['indirizzo']." ".$row['numero'].", ".$row['cap']." ".$row['citta']." ".$row['provincia']." Italy");

$nominatimUrl = "https://nominatim.openstreetmap.org/search?q=" . urlencode($full_address) . "&format=json&limit=1";

// Header obbligatorio per Nominatim
$opts = [
    "http" => [
        "header" => "User-Agent: SinxAssociationApp/1.0\r\n"
    ]
];
$context = stream_context_create($opts);

$geoResponse = @file_get_contents($nominatimUrl, false, $context);

$latitude = $longitude = $city = null;

// Geocodifica
if ($geoResponse !== false) {
    $geoData = json_decode($geoResponse, true);
    if (!empty($geoData[0]['lat']) && !empty($geoData[0]['lon'])) {
        $latitude = $geoData[0]['lat'];
        $longitude = $geoData[0]['lon'];
        $city = $row['citta'];
    }
}

$weather = null;

// Meteo SOLO se abbiamo le coordinate
if ($latitude && $longitude) {
    $meteoUrl = "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current=temperature_2m,weather_code";
    $meteoResponse = @file_get_contents($meteoUrl);

    if ($meteoResponse !== false) {
        $weather = json_decode($meteoResponse, true);
    }
}

// Variabili finali
$weather_icon = "";
$weather_temp = "";
$weather_desc = "";

// Mappa icone Open-Meteo
$weatherMap = [
    0 => ["☀️", "Sereno"],
    1 => ["🌤️", "Prevalentemente sereno"],
    2 => ["⛅", "Parzialmente nuvoloso"],
    3 => ["☁️", "Nuvoloso"],
    45 => ["🌫️", "Nebbia"],
    48 => ["🌫️", "Nebbia con brina"],
    51 => ["🌦️", "Pioviggine leggera"],
    53 => ["🌧️", "Pioviggine"],
    55 => ["🌧️", "Pioviggine intensa"],
    61 => ["🌦️", "Pioggia leggera"],
    63 => ["🌧️", "Pioggia"],
    65 => ["🌧️", "Pioggia intensa"],
    71 => ["🌨️", "Neve leggera"],
    73 => ["❄️", "Neve"],
    75 => ["❄️", "Neve intensa"],
    95 => ["⛈️", "Temporale"],
    96 => ["⛈️", "Temporale con grandine"],
    99 => ["⛈️", "Temporale forte con grandine"]
];

if (!empty($weather["current"])) {
    $code = $weather["current"]["weather_code"];
    $weather_temp = round($weather["current"]["temperature_2m"], 1) . "°C";

    if (isset($weatherMap[$code])) {
        $weather_icon = $weatherMap[$code][0];
        $weather_desc = $weatherMap[$code][1];
    }
}

?>
<head>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
 <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sinx</title>
<link rel="stylesheet" type="text/css" href="style.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type='text/javascript' src='./menu_assets/menu_jquery.js'></script>
<script type='text/javascript' src='./clock/clock.js'></script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>
<!-- HEADER -->
<header class="header-container">
  <div class="header-left">
    <a href="./index2.php">
      <img src="./ImmTemplate/Nuovo_Logo_web.png" alt="Logo" class="header-logo">
    </a>
  </div>

<!-- MENU NUOVO -->
<!-- Pulsante hamburger -->
<div class="menu-toggle" onclick="toggleMenu()">
  <i class="material-icons">menu</i>
</div>
<!-- Menu laterale a scomparsa -->
<nav id="sideMenu" class="side-menu">
  <div id='cssmenu'>
    <ul>

      <li class='has-sub'><a href='#'><span><?php echo $Lassociazione; ?></span></a>
        <ul>
          <li><a href='./dati_Associaz.php'><i class="material-icons">slow_motion_video</i><span><?php echo $Ldatiassociazione; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['sito'])) ? $row['sito'] : "dati_Associaz.php"?>'><i class="material-icons">web_asset</i><span><?php echo $Lsito; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['webmail']))? $row['webmail'] : "dati_Associaz.php"?>'><i class="material-icons">mail_outline</i><span><?php echo $Lmail; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['webPEC']))? $row['webPEC'] : "dati_Associaz.php"?>'><i class="material-icons">email</i><span><?php echo $Lmailpec; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['facebook']))? $row['facebook'] : "dati_Associaz.php" ?>'><i class="material-icons">share</i><span><?php echo $Lfacebook; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['instagram']))? $row['instagram'] : "dati_Associaz.php" ?>'><i class="material-icons">share</i><span><?php echo $Linstagram; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['youtube']))? $row['youtube'] : "dati_Associaz.php" ?>'><i class="material-icons">videocam</i><span><?php echo $Lyoutube; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['twitter']))? $row['twitter'] : "dati_Associaz.php" ?>'><i class="material-icons">share</i><span><?php echo $Ltwitter; ?></span></a></li>
          <li><a target="_blank" href='<?php echo(isset($row['HomeBanking']))? $row['HomeBanking'] : "dati_Associaz.php" ?>'><i class="material-icons">account_balance</i><span><?php echo $LHomeBanking; ?></span></a></li>
        </ul>
      </li>

      <li class='has-sub'><a href='#'><span><?php echo $Lanagrafica; ?></span></a>
        <ul>
          <li><a href='./ricerca_nome_stud.php?tipologia=Stud'><i class="material-icons">perm_identity</i><span><?php echo $Lfondatori; ?></span></a></li>
          <li><a href='./ricerca_nome_stud.php?tipologia=Ins'><i class="material-icons">supervisor_account</i><span><?php echo $Lassociati; ?></span></a></li>
          <li><a href='./ricerca_nome_stud.php?tipologia=Extra'><i class="material-icons">portrait</i><span><?php echo $Laltri; ?></span></a></li>
          <li><a href='./InsAnagrFile.php'><i class="material-icons">folder_open</i><span><?php echo $Limporta; ?></span></a></li>
          <li><a href='./ricerca.php'><i class="material-icons">search</i><span><?php echo $Lcerca; ?></span></a></li>
          <li><a href='./Rubrica.php'><i class="material-icons">chrome_reader_mode</i><span><?php echo $Lrubrica; ?></span></a></li>
          <li class='last'><a href='./pre_stampa_soci.php'><i class="material-icons">list</i><span><?php echo $Llibrosoci; ?></span></a></li>
        </ul>
      </li>

      <li class='has-sub'><a href='#'><span><?php echo $Lcontabilita; ?></span></a>
        <ul>
          <li><a href='./InsPrimanota.php'><i class="material-icons">view_day</i><span><?php echo $Lprimanota; ?></span></a></li>
          <li><a href='./InsPreventivo.php'><i class="material-icons">description</i><span>Preventivi</span></a></li>
          <li><a href='./InsRicFisc.php'><i class="material-icons">web</i><span><?php echo $Lquietanza; ?></span></a></li>
          <li><a href='./InsFattura.php'><i class="material-icons">content_copy</i><span><?php echo $Lfattura; ?></span></a></li>
          <li><a href='./InsContoEconomico.php'><i class="material-icons">view_list</i><span><?php echo $Lcontoec; ?></span></a></li>
          <li><a href='./InsStatoPatrimoniale.php'><i class="material-icons">work</i><span><?php echo $Lstatopat; ?></span></a></li>
          <li><a href='./Rendiconto.php'><i class="material-icons">view_carousel</i><span><?php echo $Lrendiconto; ?></span></a></li>
          <li class='last'><a href='./Nuovo_Anno_soc.php'><i class="material-icons">fiber_new</i><span><?php echo $Lnuovoannosociale; ?></span></a></li>
        </ul>
      </li>

      <li class='has-sub'><a href='#'><span><?php echo $Lgestione; ?></span></a>
        <ul>
          <li><a href='./CompModuli.php'><i class="material-icons">assignment</i><span><?php echo $Lmoduli; ?></span></a></li>
          <li><a href='./Calendario2.php'><i class="material-icons">date_range</i><span><?php echo $Lcalendario; ?></span></a></li>
          <li><a href='./InsNotepad.php'><i class="material-icons">description</i><span><?php echo $Lblocconote; ?></span></a></li>
          <li><a href='./Comp_email.php'><i class="material-icons">contact_mail</i><span>E-Mail</span></a></li>
          <li><a href='./InsUtente.php'><i class="material-icons">account_box</i><span><?php echo $Lutenti; ?></span></a></li>
          <li><a href='./Files.php'><i class="material-icons">assignment_turned_in</i><span><?php echo $Lfileseimmagini; ?></span></a></li>
          <li><a href='./Log.php'><i class="material-icons">assignment</i><span><?php echo $Llog; ?></span></a></li>
          <li><a href='./Backup_database.php'><i class="material-icons">open_in_browser</i><span>Backup</span></a></li>
          <li><a href='./diagnostica.php'><i class="material-icons">assignment</i><span>Diagnostica di sistema</span></a></li>
        </ul>
      </li>

      <li class='has-sub'><a href='#'><span><?php echo $Lspecifiche; ?></span></a>
        <ul>
          <li><a href='./Scheda_regioni.php'><i class="material-icons">receipt</i><span>Regioni Province e Comuni</span></a></li>
          <li><a href='./nclasse.php'><i class="material-icons">create</i><span><?php echo $Lfunzioniassociati; ?></span></a></li>
          <li><a href='./nmateria.php'><i class="material-icons">low_priority</i><span><?php echo $Ltipoassociati; ?></span></a></li>
          <li><a href='./Licenza.php'><i class="material-icons">reorder</i><span><?php echo $Llicenza; ?></span></a></li>
        </ul>
      </li>

      <li><a href='./Manuale.php'><i class="material-icons">import_contacts</i><span><?php echo $Lmanuale; ?></span></a></li>
      <li><a href="#" onclick="openCalculator()"><i class="material-icons">calculate</i><span>Apri Calcolatrice</span></a></li>
      <li><a href='./logout.php'><i class="material-icons">exit_to_app</i><span><?php echo $Luscita; ?></span></a></li>

      <script>
        function openCalculator() {
          window.open('calcolatrice.php', 'Calcolatrice', 'width=400,height=400');
        }
      </script>

    </ul>
  </div>
</nav>

<script>
  function toggleMenu() {
    document.getElementById("sideMenu").classList.toggle("open");
  }

  // Chiude il menu cliccando fuori
  document.addEventListener('click', function(e) {
    const menu = document.getElementById("sideMenu");
    const toggle = document.querySelector(".menu-toggle");
    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
      menu.classList.remove("open");
    }
  });
</script>

  <div class="header-center">
    <h2><?php echo $Lpresentazione ?></h2>
    <sub>
      <?php echo $row['nome'] ?> - <?php echo $row['indirizzo'] ?>, <?php echo $row['numero'] ?>
      - <?php echo $row['cap'] ?> <?php echo $row['citta'] ?> - <?php echo $row['provincia'] ?>
    </sub>
    <br>
    <small>
      <?php echo $Lutente ?><?php echo $nutente ?> -
      <?php echo $Llivello ?><?php echo $user ?>
    </small>
  </div>

  <div class="header-right">
      <div class="weather-box">
      <div class="weather-icon"><?php echo $weather_icon; ?></div>
      <div class="weather-temp"><?php echo $weather_temp; ?></div>
      <div class="weather-desc"><?php echo $weather_desc; ?></div>
      <div class="weather-city"><?php echo $city; ?></div>
    </div>
<hr style="width:10%">
    <div><small>
      <?php echo $Ldata; echo (date("d-m-y")); ?>
      <body onload="startClock()">
    </small>
    </div>
    <div id="clock"></div>
  </div>
    <small><a class="logout-link" href="./logout.php" title="Logout">Logout
      <i class="material-icons">exit_to_app</i>
    </a></small>
</header>
