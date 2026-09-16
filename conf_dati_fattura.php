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
if (!$user) {
    header('Location: ./index.php');
    exit;
}

// 🔹 Recupero dati POST in modo sicuro
$data = trim($_POST['data']);
if (empty($data)) {
    $data = date('d-m-Y');
}

$descrizione = trim($_POST['Descr']);
$quantita = trim($_POST['Qta']);
$prezzoun = trim($_POST['prezzoun']);
$numfattura = trim($_POST['fattnum']);
$nome = trim($_POST['nome']);
$iva = trim($_POST['iva']);
$modpagam = trim($_POST['modpaga']);

// 🔹 Funzione redirect
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

// 🔹 Validazione campi obbligatori
$campi_obbligatori = [
    'Descrizione' => $descrizione,
    'Quantità' => $quantita,
    'Prezzo unitario' => $prezzoun,
    'Numero fattura' => $numfattura,
    'Nome cliente' => $nome,
    'IVA' => $iva
];

foreach ($campi_obbligatori as $campo => $valore) {
    if ($valore === '') {
        echo "<center><b>Il campo $campo è obbligatorio</b></center>";
        redirect('./InsFattura.php', 2);
        exit;
    }
}

// 🔹 Controlli di tipo numerico
if (!is_numeric($quantita) || $quantita <= 0) {
    echo "<center><b>Quantità non valida</b></center>";
    redirect('./InsFattura.php', 2);
    exit;
}

if (!is_numeric($prezzoun) || $prezzoun <= 0) {
    echo "<center><b>Prezzo unitario non valido</b></center>";
    redirect('./InsFattura.php', 2);
    exit;
}

if (!is_numeric($iva) || $iva < 0) {
    echo "<center><b>Aliquota IVA non valida</b></center>";
    redirect('./InsFattura.php', 2);
    exit;
}

// 🔹 Sanificazione
$descrizione = htmlspecialchars($descrizione, ENT_NOQUOTES, "UTF-8");
$modpagam = htmlspecialchars($modpagam, ENT_NOQUOTES, "UTF-8");
$nome = htmlspecialchars($nome, ENT_NOQUOTES, "UTF-8");

// 🔹 Calcolo totale fattura
$totfattura = $quantita * ($prezzoun + ($prezzoun * ($iva / 100)));
$totfattura = round($totfattura, 2);

// 🔹 Connessione DB
include('./dati_db.inc');
$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

$tb_fattura = 'tb_fatture';
$tb_tot_fattura = 'tb_tot_fatture';

// 🔹 Inserimento riga fattura
$sql = "INSERT INTO $tb_fattura (id_fatt, nome, data, euro, quantita, descr, iva, modpaga, totale)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "issddsdss",
    $numfattura,
    $nome,
    $data,
    $prezzoun,
    $quantita,
    $descrizione,
    $iva,
    $modpagam,
    $totfattura
);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    die("<b>Errore DB:</b> " . mysqli_error($connect));
}

// 🔹 Calcolo totale complessivo della fattura
$query = "SELECT SUM(totale) as totale_numero FROM tb_fatture WHERE id_fatt = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "i", $numfattura);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totale);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// 🔹 Aggiorna tabella totali
if ($totale > 0) {
    $sql_tot = "INSERT INTO $tb_tot_fattura (id_tot_fatture, tot_fattura, nome, data)
                VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connect, $sql_tot);
    mysqli_stmt_bind_param($stmt, "idss", $numfattura, $totale, $nome, $data);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// 🔹 Redirect finale
mysqli_close($connect);
header('Location: ./conferma.php?rif=InsFattura');
exit;
?>
