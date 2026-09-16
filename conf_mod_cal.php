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
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
$user = $_SESSION['utente'];
if ($user) {

    include('./dati_db.inc');
    $connect = mysqli_connect($host, $username, $password, $db_name)
        or die("cannot connect DB");

    // Recupero e sanificazione input
    $id = $_POST['id'] ?? '';
    $data = $_POST['data'] ?? '';
    $campo = $_POST['campo'] ?? '';
    $record = $_POST['record'] ?? '';

    // 🔒 Pulizia e protezione contro caratteri speciali
    $campo = trim(mysqli_real_escape_string($connect, $campo));
    $record = trim(mysqli_real_escape_string($connect, $record));
    $data = trim(mysqli_real_escape_string($connect, $data));
    $ora = mysqli_real_escape_string($connect, $_POST['ora'] ?? '00:00:00');
    $id = intval($id); // forza numerico per sicurezza

    // Funzione redirect
    function redirect($url, $tempo = FALSE)
    {
        if (!headers_sent() && $tempo == FALSE) {
            header('Location:' . $url);
        } elseif (!headers_sent() && $tempo != FALSE) {
            header('Refresh:' . $tempo . ';' . $url);
        } else {
            if ($tempo == FALSE) $tempo = 0;
            echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
        }
    }

    // Controllo campi obbligatori
    if ($campo == "") {
        echo "<center><b>Il campo Titolo è obbligatorio</b></center>";
        redirect('./Calendario2.php', 2);
        exit;
    }
    if ($record == "") {
        echo "<center><b>Il campo Testo è obbligatorio</b></center>";
        redirect('./Calendario2.php', 2);
        exit;
    }

    // --- NUOVO APPUNTAMENTO ---
    if (isset($_POST['nuovo'])) {
        $sql = "INSERT INTO appuntamenti (titolo, testo, str_data, ora) VALUES ('$campo', '$record', '$data', '$ora')";
        $result = mysqli_query($connect, $sql);
    }

    // --- MODIFICA APPUNTAMENTO ---
    if (isset($_POST['modifica'])) {
        if ($id == 0) {
            echo "<center><b>Il campo ID è obbligatorio per la modifica</b></center>";
            redirect('./Calendario2.php', 2);
            exit;
        }

        $sql = "UPDATE appuntamenti SET titolo = '$campo', testo = '$record', str_data = '$data', ora = '$ora' WHERE id = '$id'";
        $result = mysqli_query($connect, $sql);
    }

    if (!$result) {
        // Mostra errore SQL dettagliato in fase di debug (poi puoi toglierlo)
        error_log("ERRORE SQL: " . mysqli_error($connect));
        header('Location: ./errore.php?rif=Calendario2');
    } else {
        header('Location: ./conferma.php?rif=Calendario2');
    }

    mysqli_close($connect);
} else {
    header('Location: ./index.php');
}
?>
