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
$langstampasoci = $_SESSION['lingua'];
$paginastampasoci = "stampasoci.inc";
$linguastampasoci = ($langstampasoci . $paginastampasoci);
include($linguastampasoci);

if ($user) {

    include('./Intestazione.php');
    include('./dati_db.inc');

    $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
        or die("cannot connect DB");

    $ordine = $_POST['ordine'] ?? 'cognome';

    echo '<!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Stampa Soci</title>
        <link rel="stylesheet" href="./style_stampa.css" media="all">
    </head>
    <body>';

    echo "<h2>$Ltitolosoci</h2>
          <hr>";

    // Query soci attivi
    $Query_nome = "SELECT * FROM tb_anagrafe
                   WHERE tipologia != 'Extra'
                   AND associato = 'si'
                   ORDER BY $ordine";

    $rs = mysqli_query($connect, $Query_nome)
        or die('<b>Errore:</b> ' . mysqli_error($connect));

    // Tabella soci
    echo <<<HTML

     <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa scheda</button>
  </div>

    <table class="soci-table">
        <thead>
            <tr>
                <th>$Lid</th>
                <th>$Lnome</th>
                <th>$Lcognome</th>
                <th>$Lindirizzo</th>
                <th>$Lcitta</th>
                <th>$Lprovincia</th>
                <th>$Lcodfisc</th>
                <th>$Ltipo</th>
                <th>$Lfunzione</th>
                <th>$Lemail</th>
            </tr>
        </thead>
        <tbody>
HTML;

    while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
        echo "<tr>
                <td>{$row['ntessera']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['cognome']}</td>
                <td>{$row['indirizzo']}</td>
                <td>{$row['cap']} {$row['citta']}</td>
                <td>{$row['provincia']}</td>
                <td>{$row['nomerif']}</td>
                <td>{$row['materia']}</td>
                <td>{$row['classe']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }

    echo "</tbody></table>";

    echo '<div class="footer">
            <p>Stampa generata automaticamente dal gestionale SINX</p>
          </div>';

    echo '</body></html>';

    mysqli_close($connect);

} else {
    header('Location: ./index.php');
}
?>



