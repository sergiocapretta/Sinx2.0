<?php
session_start();

$user = $_SESSION['utente'];
if ($user !== 'admin') {
  header('Location: ./index.php');
  exit;
}

include('dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Impossibile connettersi al database");
mysqli_set_charset($connect, "utf8mb4");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {

    $file = $_FILES['sql_file']['tmp_name'];
    $sql_content = '';

    if (is_uploaded_file($file)) {
        // Leggi l'intero contenuto del file SQL.
        // I problemi di memoria sono ridotti in quanto gestiti dal database
        // e non dal ciclo PHP riga per riga.
        $sql_content = file_get_contents($file);

        if ($sql_content === FALSE) {
            $msg = "<div class='card-subtitle' style='color:red;'><b>❌ Errore:</b> impossibile leggere il contenuto del file SQL.</div>";
        } else {
            $errorCount = 0;
            $queryCount = 0;

            // Uso mysqli_multi_query per eseguire tutte le istruzioni
            if (mysqli_multi_query($connect, $sql_content)) {
                // Cicla per ogni set di risultati (query)
                do {
                    if ($result = mysqli_store_result($connect)) {
                        mysqli_free_result($result);
                        $queryCount++;
                    } elseif (mysqli_field_count($connect) == 0) {
                        // Query senza risultato set (CREATE, INSERT, UPDATE, DELETE)
                        $queryCount++;
                    } else {
                        // Questo gestisce gli errori che multi_query non intercetta subito
                        if (mysqli_error($connect)) {
                            $errorCount++;
                            // Non stampiamo l'errore qui per evitare di intasare la pagina
                        }
                    }
                } while (mysqli_more_results($connect) && mysqli_next_result($connect));

                // Controllo finale se ci sono errori non intercettati
                if ($errorCount === 0) {
                     $msg = "<div class='card-subtitle' style='color:green;'><b>✅ Ripristino completato!</b><br>$queryCount query eseguite con successo.</div>";
                } else {
                    $msg = "<div class='card-subtitle' style='color:orange;'><b>⚠️ Ripristino completato con errori.</b><br>$queryCount query elaborate, $errorCount errori rilevati. Controlla il log degli errori del database.</div>";
                }

            } else {
                // Errore nella prima query o nella sintassi generale
                $msg = "<div class='card-subtitle' style='color:red;'><b>❌ Errore critico SQL:</b> " . htmlspecialchars(mysqli_error($connect)) . "</div>";
            }
        }

    } else {
        $msg = "<div class='card-subtitle' style='color:red;'><b>❌ Errore:</b> File non valido o non caricato correttamente.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Ripristino Database</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="content-section">
  <div class="card">
    <h2 class="card-title">🔄 Ripristino Database Sinx</h2>
    <p class="card-subtitle">Carica un file di backup (.sql) per ripristinare il database.</p>

    <form action="" method="POST" enctype="multipart/form-data" class="sinx-form">
      <label><b>Seleziona file SQL:</b></label>
      <input type="file" name="sql_file" accept=".sql" required>

      <div class="form-buttons">
        <input type="submit" value="Ripristina Database" class="btn-add">
        <a href="Backup_database.php" class="btn-edit">📦 Torna al Backup</a>
        <a href="./index2.php" class="btn-delete">⬅️ Torna al Pannello</a>
      </div>
    </form>

    <?php if (!empty($msg)) echo $msg; ?>

    <hr class="divider">

    <div class="card-subtitle" style="text-align:center;">
      <small><i>Operazione disponibile solo per l’amministratore del sistema.</i></small>
    </div>
  </div>
</section>

</body>
</html>

<?php
mysqli_close($connect);
?>
