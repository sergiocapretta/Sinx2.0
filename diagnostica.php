<?php
session_start();

include('./dati_db.inc');

$connect = @mysqli_connect($host, $username, $password, $db_name, $port);

function mysql_tables_report($connect)
{
    $out = [];

    if (!$connect) {
        return ["TABLE CHECK: DB not connected"];
    }

    $res = mysqli_query($connect, "SHOW TABLES");

    if (!$res) {
        return ["TABLE CHECK ERROR: " . mysqli_error($connect)];
    }

    $out[] = "DATABASE TABLES:";

    while ($row = mysqli_fetch_array($res)) {

        $table = $row[0];

        // sicurezza base nome tabella
        $table_safe = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $count_res = @mysqli_query($connect, "SELECT COUNT(*) AS tot FROM `$table_safe`");

        if ($count_res) {
            $count = mysqli_fetch_assoc($count_res)['tot'];
            $out[] = "OK $table_safe ($count record)";
        } else {
            $out[] = "OK $table_safe (COUNT ERROR)";
        }
    }

    return $out;
}

function mysql_advanced_check($connect)
{
    $out = [];

    if (!$connect) {
        return ["MYSQL ADVANCED: DB not connected"];
    }

    // Versione server
    $out[] = "MYSQL VERSION: " . mysqli_get_server_info($connect);

    // Engine check
    $res = mysqli_query($connect, "SHOW ENGINES");
    if ($res) {
        $out[] = "ENGINES:";
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['Support'] === 'YES' || $row['Support'] === 'DEFAULT') {
                $out[] = "OK " . $row['Engine'];
            }
        }
    }

    // Threads / process list
    $res = mysqli_query($connect, "SHOW PROCESSLIST");
    if ($res) {
        $count = mysqli_num_rows($res);
        $out[] = "ACTIVE PROCESSES: $count";
    }

    // Test query performance base
    $start = microtime(true);
    mysqli_query($connect, "SELECT 1");
    $time = microtime(true) - $start;

    $out[] = "SIMPLE QUERY TIME: " . round($time * 1000, 4) . " ms";

    return $out;
}

function db_write_test($connect)
{
    $out = [];

    if (!$connect) {
        return ["DB WRITE TEST: no connection"];
    }

    mysqli_begin_transaction($connect);

    $test_table = "sinx_diag_test";

    // crea tabella temporanea se non esiste
    mysqli_query($connect, "
        CREATE TABLE IF NOT EXISTS $test_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test VARCHAR(50)
        )
    ");

    // insert test
    $ok = mysqli_query($connect, "
        INSERT INTO $test_table (test)
        VALUES ('diagnostic_test')
    ");

    if ($ok) {
        $out[] = "DB INSERT TEST: OK";
    } else {
        $out[] = "DB INSERT TEST ERROR: " . mysqli_error($connect);
    }

    // rollback totale
    mysqli_rollback($connect);

    $out[] = "ROLLBACK: executed (no data modified)";

    return $out;
}

function run_checks($connect)
{
    $report = [];

    $report[] = "\nMYSQL ADVANCED:";
    $report = array_merge($report, mysql_advanced_check($connect));

    $report[] = "\nDB WRITE TEST:";
    $report = array_merge($report, db_write_test($connect));

    // PHP
    $report[] = "PHP VERSION: " . phpversion();

    // MySQL
    if ($connect) {
        $report[] = "DB CONNECTION: OK";
        $report[] = "MYSQL SERVER: " . mysqli_get_server_info($connect);
        $report[] = "CHARSET: " . mysqli_character_set_name($connect);
    } else {
        $report[] = "DB CONNECTION: ERROR - " . mysqli_connect_error();
    }

    // Extensions
    $mods = ['mysqli','mbstring','xml','simplexml','zip','gd','openssl'];

    $report[] = "\nEXTENSIONS:";
    foreach ($mods as $m) {
        $report[] = extension_loaded($m) ? "OK $m" : "MISSING $m";
    }

    // Mail
    $report[] = "\nMAIL FUNCTION: " . (function_exists('mail') ? 'OK' : 'MISSING');

    // Session
    $report[] = "\nSESSION STATUS: " . session_status();

    // Upload
    $report[] = "\nPHP LIMITS:";
    $report[] = "upload_max_filesize: " . ini_get('upload_max_filesize');
    $report[] = "post_max_size: " . ini_get('post_max_size');
    $report[] = "memory_limit: " . ini_get('memory_limit');
    $report[] = "max_execution_time: " . ini_get('max_execution_time');

    // Cartelle
    $folders = ['./Download','./backup','./tmp'];

    $report[] = "\nFOLDERS:";
    foreach ($folders as $f) {
        if (file_exists($f)) {
            $report[] = is_writable($f) ? "OK writable $f" : "NOT WRITABLE $f";
        } else {
            $report[] = "MISSING $f";
        }
    }

    // Tabelle base
$report[] = "\nDATABASE TABLES:";
$report = array_merge($report, mysql_tables_report($connect));

    $report[] = "\nEND DIAGNOSTIC";

    return $report;
}

$report = run_checks($connect);

/* =========================
   DOWNLOAD MODE
========================= */
if (isset($_GET['download'])) {

    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="sinx_diagnostica.txt"');

    echo implode("\n", $report);
    exit;
}

include('./top.inc');
include('./menu.inc');
?>

<div class="content-section">
  <div class="card">

    <h2 class="card-title">Diagnostica Sistema SINX</h2>

    <form method="get">
      <button type="submit" name="download" value="1" class="btn-edit">
        ⬇ Scarica report diagnostica
      </button>
    </form>

    <hr>

    <pre style="background:#111;color:#0f0;padding:15px;overflow:auto;">
<?php echo htmlspecialchars(implode("\n", $report)); ?>
    </pre>

  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
