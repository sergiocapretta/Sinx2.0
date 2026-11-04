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
?>
    <!-- Collega il CSS di stampa -->
    <link rel="stylesheet" href="style_stampa.css" media="all">

      <div class="no-print" style="text-align:center;">
    <button class="print-btn" onclick="window.print()">🖨️ Stampa</button>
  </div>

    <h2>Lista Tessere Registrate</h2>
    <hr>

<?php
    $ordine = $_POST['ordine'] ?? 'nome';

    include('./dati_db.inc');
    $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
        or die("cannot connect DB");

    $Query_nome = "SELECT * FROM tb_anagrafe WHERE tipologia != 'Extra' ORDER BY $ordine";
    $rs = mysqli_query($connect, $Query_nome)
        or die("Errore nella query $Query_nome: " . mysqli_error($connect));
?>

    <table class="soci-table">
        <thead>
            <tr>
                <th><?php echo $Lid; ?></th>
                <th><?php echo $Lnome; ?></th>
                <th><?php echo $Lcognome; ?></th>
                <th><?php echo $Lindirizzo; ?></th>
                <th><?php echo $Lcitta; ?></th>
                <th><?php echo $Lprovincia; ?></th>
                <th><?php echo $Lcodfisc; ?></th>
                <th><?php echo $Ltipo; ?></th>
                <th><?php echo $Lfunzione; ?></th>
                <th><?php echo $Lemail; ?></th>
                <th>Attivo</th>
            </tr>
        </thead>
        <tbody>
<?php
    while ($row = mysqli_fetch_array($rs)) {
        echo "<tr>
                <td style='text-align:center;'>" . htmlspecialchars($row['ntessera']) . "</td>
                <td>" . htmlspecialchars($row['nome']) . "</td>
                <td>" . htmlspecialchars($row['cognome']) . "</td>
                <td>" . htmlspecialchars($row['indirizzo']) . "</td>
                <td>" . htmlspecialchars($row['cap']) . " " . htmlspecialchars($row['citta']) . "</td>
                <td>" . htmlspecialchars($row['provincia']) . "</td>
                <td>" . htmlspecialchars($row['nomerif']) . "</td>
                <td>" . htmlspecialchars($row['materia']) . "</td>
                <td>" . htmlspecialchars($row['classe']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td style='text-align:center;'>" . htmlspecialchars($row['associato']) . "</td>
            </tr>";
    }
?>
        </tbody>
    </table>

    <div class="footer">
        <hr>
        <p>Stampa generata automaticamente dal gestionale Sinx - <?php echo date('d/m/Y H:i'); ?></p>
    </div>

<?php
    mysqli_close($connect);
} else {
    header('Location: ./index.php');
    exit;
}
?>



