<?php
/*======================================================================+
 File name   : conf_logo_associaz.php
 Begin       : 2013-10-10
 Last Update : 2013-10-10

 Description : logo association

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
$upload_dir = "./Immagini";
if(@is_uploaded_file($_FILES["immagine"]["tmp_name"])) {

    $file_name = $_FILES["immagine"]["name"];
    $temp_path = $_FILES["immagine"]["tmp_name"];
    $target_path = "$upload_dir/$file_name";

    // 1. Sposto il file temporaneo nella directory finale
    if(@move_uploaded_file($temp_path, $target_path)) {

        // 2. Rinomina del logo dell'Associazione (Il controllo deve usare '===')
        // La funzione rename ritorna TRUE in caso di successo, e FALSE in caso di errore.
        $ret = rename($target_path, "$upload_dir/logo.png");

        if ($ret === FALSE) {
            // Fallimento nella rinomina
            echo "Problemi con la rinomina del file: Verifica che il file esista e che i permessi siano corretti.";
            // Potresti anche provare a eliminare il file caricato se la rinomina fallisce
            // @unlink($target_path);
            redirect('./gest_files.php',2);
            die(); // Uscita per evitare l'esecuzione successiva
        } else {
            // Successo completo
            echo "Immagine caricata con successo";
            redirect('./dati_Associaz.php',2);
        }

    } else {
        // Fallimento nello spostamento (probabilmente problema di permessi della cartella Immagini)
        die("Impossibile spostare il file, controlla l'esistenza o i permessi (777) della directory dove fare l'upload.");
    }

} else {
    // Fallimento nell'upload iniziale (ad esempio, file troppo grande, errore di rete, ecc.)
    echo "Problemi nell'upload del file immagine. Codice errore: " . $_FILES["immagine"]["error"];
    redirect('./gest_files.php' ,2);
    die ("");
}
// Rimosso il redirect finale, ora è nel blocco 'else' del rename
?>
