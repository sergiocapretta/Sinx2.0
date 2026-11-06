<?php
/*======================================================================+
 File name   : conf_dati_stud.php
 Begin       : 2010-08-04
 Last Update : 2012-07-08
 Description : confirm data
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
if ($user) {
	$ntessera = $_POST['ntessera'];
	$nnome = $_POST['nome'];
	$ncognome = $_POST['cognome'];
	$nindirizzo = $_POST['indirizzo'];
	$cap = $_POST['comuni'];
	$ncitta = $_POST['regioni'];
	$nprovincia = $_POST['provincie'];
	$ntel = $_POST['tel'];
	$ntel2 = $_POST['tel2'];
	$ndatangg = $_POST['datangg'];
	$ndatanmm = $_POST['datanmm'];
	$ndatanaaaa = $_POST['datanaaaa'];
	$materia = $_POST['materia'];
	$nnomerif = $_POST['nomerif']; //Codice fiscale
	$denominazione = trim($_POST['denominazione'] ?? '');
	$tipo_soggetto = $_POST['tipo_soggetto'] ?? 'persona_fisica';
	$partita_iva = trim($_POST['partita_iva'] ?? '');
	$PEC = trim($_POST['PEC'] ?? '');
	$codice_destinatario = strtoupper(trim($_POST['codice_destinatario'] ?? ''));
	$nazione = strtoupper(trim($_POST['nazione'] ?? 'IT'));
	$tipi_validi = array('persona_fisica', 'soggetto_privato', 'pubblica_amministrazione');
	if (!in_array($tipo_soggetto, $tipi_validi, true)) { $tipo_soggetto = 'persona_fisica'; }
	if ($codice_destinatario !== '') {
		$lunghezza_codice = ($tipo_soggetto === 'pubblica_amministrazione') ? 6 : 7;
		if (!preg_match('/^[A-Z0-9]{' . $lunghezza_codice . '}$/', $codice_destinatario)) { die('Codice destinatario non valido per il tipo di soggetto selezionato.'); }
	}
	$email = $_POST['email'];
	$nnote = $_POST['note'];
	
$ndatan = ($ndatangg."-".$ndatanmm."-".$ndatanaaaa);
//escape html
$nome = htmlspecialchars($nnome, ENT_NOQUOTES, "UTF-8");
$tessera = htmlspecialchars($ntessera, ENT_NOQUOTES, "UTF-8");
$cognome = htmlspecialchars($ncognome, ENT_NOQUOTES, "UTF-8");
$indirizzo = htmlspecialchars($nindirizzo, ENT_NOQUOTES, "UTF-8");
$citta = htmlspecialchars($ncitta, ENT_NOQUOTES, "UTF-8");
$provincia = htmlspecialchars($nprovincia, ENT_NOQUOTES, "UTF-8");
$tel = htmlspecialchars($ntel, ENT_NOQUOTES, "UTF-8");
$tel2 = htmlspecialchars($ntel2, ENT_NOQUOTES, "UTF-8");
$datan = htmlspecialchars($ndatan, ENT_NOQUOTES, "UTF-8");
$nomerif = htmlspecialchars($nnomerif, ENT_NOQUOTES, "UTF-8");
$note = htmlspecialchars($nnote, ENT_NOQUOTES, "UTF-8");
//Se L'associato è attivo
$checkbox = isset($_POST["check"]);
if ($checkbox) {
$associato = "si";
} else { $associato = "no";}
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

		
// *** GESTIONE DELL'IMMAGINE ***
$upload_dir = "./Immagini/Utenti";
if(@is_uploaded_file($_FILES["immagine"]["tmp_name"])) {
$file_name = $_FILES["immagine"]["name"];
@move_uploaded_file($_FILES["immagine"]["tmp_name"], "$upload_dir/$file_name")
or die("Impossibile spostare il file, controlla l'esistenza o i permessi della directory dove fare l'upload.");
} else {
$file_name = "personal.gif";
@move_uploaded_file($_FILES["immagine"]["tmp_name"], "$upload_dir/$file_name");
}
// *** FINE MODULO GESTIONE IMMAGINE ***
	include ('./dati_db.inc');
	$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port ) or die("cannot connect DB");
$tb_anagrafe = 'tb_anagrafe';
	if ($nome){ 
		$sql = "INSERT INTO $tb_anagrafe
            (ntessera,nome,cognome,indirizzo,cap,citta,provincia,tel,tel2,datan,classe,nomerif,materia,mansione,email,tipologia,note,immagine,associato,denominazione,tipo_soggetto,partita_iva,PEC,codice_destinatario,nazione)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($connect, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssssss",
            $tessera,$nome,$cognome,$indirizzo,$cap,$citta,$provincia,$tel,$tel2,$datan,
            $classeVuota,$nomerif,$materia,$mansioneVuota,$email,$tipologiaIns,$note,$file_name,
            $associato,$denominazione,$tipo_soggetto,$partita_iva,$PEC,$codice_destinatario,$nazione
        );
        $classeVuota = '';
        $mansioneVuota = '';
        $tipologiaIns = 'Ins';
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
		header('location: ./conferma.php?rif=InsAnagrIns'); //Vado alla pagina di conferma
	}else{ 
		header('location: ./errore.php?rif=InsAnagrIns'); //Vado alla pagina di errore
		}
mysqli_close($connect);
} else {
header("Location: ./index.php");
} ?>
