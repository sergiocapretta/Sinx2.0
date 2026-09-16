<?php
/*
 * Sinx for Association - Gestionale per Associazioni no-profit
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
*/

//session_start();


if ($user) {

include('./dati_db.inc');
    $connect = mysqli_connect($host, $username, $password, $db_name) or die("Impossibile connettersi al DB");

    function Calendar($m, $y, $connect)
    {
        $user = $_SESSION['utente'];
        $langmese = $_SESSION['lingua'];
        $linguamese = ($langmese . "mese.inc");
        if (file_exists($linguamese)) include($linguamese);

        // Mese e anno attuali
        if (empty($_GET['d'])) {
            $m = date('n');
            $y = date('Y');
        } else {
            $m = (int)strftime("%m", (int)$_GET['d']);
            $y = (int)strftime("%Y", (int)$_GET['d']);
        }

        $precedente = mktime(0, 0, 0, $m - 1, 1, $y);
        $successivo = mktime(0, 0, 0, $m + 1, 1, $y);

        $nomi_mesi = [
            $Lgennaio, $Lfebbraio, $Lmarzo, $Laprile, $Lmaggio, $Lgiugno,
            $Lluglio, $Lagosto, $Lsettembre, $Lottobre, $Lnovembre, $Ldicembre
        ];
        $nomi_giorni = [
            $Llunedi, $Lmartedi, $Lmercoledi, $Lgiovedi, $Lvenerdi, $Lsabato, $Ldomenica
        ];

        $cols = 7;
        $days = date("t", mktime(0, 0, 0, $m, 1, $y));
        $lunedi = date("w", mktime(0, 0, 0, $m, 1, $y));
        if ($lunedi == 0) $lunedi = 7;

        echo "<div class='calendar-wrapper'>";
        echo "<div class='calendar-header'>";
        echo "<a href='?d={$precedente}' class='nav'>&lt;</a>";
        echo "<span class='month-title'>{$nomi_mesi[$m - 1]} $y</span>";
        echo "<a href='?d={$successivo}' class='nav'>&gt;</a>";
        echo "</div>";

        echo "<div class='calendar-grid'>";
        foreach ($nomi_giorni as $v) {
            echo "<div class='day-name'>$v</div>";
        }

        $oggi = date("d-m-Y");

        for ($j = 1; $j < $days + $lunedi; $j++) {
            if ($j < $lunedi) {
                echo "<div class='empty'></div>";
            } else {
                $day = $j - ($lunedi - 1);

                $dataObj = DateTime::createFromFormat('Y-n-j', "$y-$m-$day");
                $data = $dataObj->format('d-m-Y');

                $Query = "SELECT * FROM appuntamenti WHERE str_data = '$data'";
                $rs = mysqli_query($connect, $Query) or die(mysqli_error($connect));
                $evento = (mysqli_num_rows($rs) > 0);

                if ($data == $oggi) {
                    echo "<div class='day today'>$day</div>";
                } elseif ($evento) {
                    echo "<div class='day event'><a href='DettCal.php?cod=$data&cat=$day'>$day</a></div>";
                } else {
                    echo "<div class='day normal'><a href='Calendario.php?cod=$data&cat=$day'>$day</a></div>";
                }
            }
        }

        echo "</div>";
        echo "</div>";
    }

    // Richiamo la funzione
    Calendar(date("m"), date("Y"), $connect);
}
?>
