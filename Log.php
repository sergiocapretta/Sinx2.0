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

session_start();

$user = $_SESSION['utente'];
$langlog = $_SESSION['lingua'];
$paginalog = "log.inc";
$lingualog = ($langlog . $paginalog);
include($lingualog);

// ✅ Accesso riservato all'admin
if ($user != 'admin') {
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

    echo "<center>$Lhelp1log<b>$user</b> <br>$Lhelp2log<b>Admin</b></center>";
    redirect('./index2.php', 3);
    exit;
}

include('./top.inc');
include('./menu.inc');

// ✅ Percorso del file di log
$logfile = './log/logSinx.txt';

// ✅ Funzione per cancellare i log più vecchi di 30 giorni
if (file_exists($logfile)) {
    $lines = file($logfile, FILE_IGNORE_NEW_LINES);
    $today = time();

    $blocks = [];
    $currentBlock = [];

    foreach ($lines as $line) {
        // Ogni blocco parte con "------"
        if (trim($line) === '------') {
            if (!empty($currentBlock)) {
                $blocks[] = $currentBlock;
            }
            $currentBlock = [];
        }

        $currentBlock[] = $line;
    }

    // Aggiunge l'ultimo blocco
    if (!empty($currentBlock)) {
        $blocks[] = $currentBlock;
    }

    $retained = [];

    foreach ($blocks as $block) {

        // Cerca la data nel blocco
        $dateFound = false;
        foreach ($block as $line) {
            if (preg_match('/(\d{2})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})/', $line, $m)) {

                $dateFound = true;

                // Converte anno YY → 20YY
                $year = intval($m[3]);
                $year = ($year <= 30) ? 2000 + $year : 1900 + $year;

                $datestr = sprintf('%04d-%02d-%02d %02d:%02d:%02d',
                    $year, $m[2], $m[1], $m[4], $m[5], $m[6]
                );

                $date = strtotime($datestr);

                // Se il blocco è più vecchio di 30 giorni → NON mantenerlo
                if (($today - $date) > 30 * 24 * 60 * 60) {
                    continue 2; // salta tutto il blocco
                }
            }
        }

        // Mantieni blocchi senza data (per sicurezza)
        if (!$dateFound || true) {
            foreach ($block as $line) {
                $retained[] = $line;
            }
        }
    }

    // Salva i blocchi rimanenti nel file
    file_put_contents($logfile, implode("\n", $retained) . "\n");

    $logContent = implode("\n", $retained);

} else {
    $logContent = "⚠️ Nessun file di log trovato.";
}
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitololog; ?></h2>
    <p class="card-subtitle"><?php echo $Lcanclog; ?> (i log più vecchi di 30 giorni vengono cancellati automaticamente)</p>

    <div style="text-align:right;">
      <a href="./CancLog.php" class="link-btn">🗑️ Cancella tutti i log</a>
    </div>

    <hr class="divider">

    <div style="background:#f3f3f3; border-radius:8px; padding:15px; font-family:monospace; font-size:14px; white-space:pre-wrap; max-height:600px; overflow-y:auto;">
      <?php echo $logContent; ?>
    </div>

    <hr class="divider">

    <div style="display:flex; align-items:center; gap:10px;">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" width="32">
      <small><i><?php echo $Lsugg1log; ?></i></small>
    </div>
  </div>
</div>

<?php include('./botton.inc'); ?>
