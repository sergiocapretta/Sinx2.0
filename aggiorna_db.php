<?php
/**
 * Script di aggiornamento tabella appuntamenti
 * Da vecchia struttura a nuova struttura
 * ⚠️ ESEGUIRE UNA SOLA VOLTA
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name)
    or die("Errore connessione DB");

mysqli_set_charset($connect, 'utf8mb4');

echo "<pre>";

// --------------------------------------------------
// 1️⃣ Aggiunta colonna ORA (se non esiste)
// --------------------------------------------------
$checkOra = mysqli_query($connect, "SHOW COLUMNS FROM appuntamenti LIKE 'ora'");
if (mysqli_num_rows($checkOra) == 0) {
    mysqli_query($connect, "
        ALTER TABLE appuntamenti
        ADD ora TIME NOT NULL DEFAULT '00:00:00'
    ");
    echo "✔ Colonna 'ora' aggiunta\n";
} else {
    echo "ℹ Colonna 'ora' già presente\n";
}

// --------------------------------------------------
// 2️⃣ Modifica struttura campi
// --------------------------------------------------
mysqli_query($connect, "
    ALTER TABLE appuntamenti
    MODIFY titolo VARCHAR(500)
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_general_ci
        NULL,
    MODIFY testo TEXT
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_general_ci
        NULL,
    MODIFY str_data VARCHAR(10)
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_general_ci
        NULL
");

echo "✔ Campi aggiornati (charset / NULL)\n";

// --------------------------------------------------
// 3️⃣ Conversione charset tabella
// --------------------------------------------------
mysqli_query($connect, "
    ALTER TABLE appuntamenti
    CONVERT TO CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci
");

echo "✔ Charset tabella convertito a utf8mb4\n";

// --------------------------------------------------
// 4️⃣ Normalizzazione date (g-m-Y → dd-mm-yyyy)
// --------------------------------------------------
$rs = mysqli_query($connect, "SELECT id, str_data FROM appuntamenti");

while ($row = mysqli_fetch_assoc($rs)) {
    $raw = $row['str_data'];

    if (!$raw) continue;

    $dt =
        DateTime::createFromFormat('d-m-Y', $raw) ?:
        DateTime::createFromFormat('j-n-Y', $raw) ?:
        DateTime::createFromFormat('Y-m-d', $raw);

    if ($dt) {
        $new = $dt->format('d-m-Y');
        mysqli_query($connect, "
            UPDATE appuntamenti
            SET str_data = '$new'
            WHERE id = {$row['id']}
        ");
    }
}

echo "✔ Date normalizzate in formato dd-mm-yyyy\n";

// --------------------------------------------------
echo "\n🎉 Aggiornamento completato con successo\n";
echo "👉 Puoi ora ELIMINARE aggiorna_db.php\n";

echo "</pre>";

mysqli_close($connect);
