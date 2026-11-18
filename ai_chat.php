<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['utente'])) { http_response_code(401); echo json_encode(array('error' => 'Sessione scaduta.')); exit; }
if ($_SESSION['utente'] !== 'admin') { http_response_code(403); echo json_encode(array('error' => 'Assistente AI disponibile solo per l’amministratore.')); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(array('error' => 'Metodo non consentito.')); exit; }
require_once __DIR__ . '/ai_provider.php';

function sinx_ai_json_error($code, $message) { http_response_code($code); echo json_encode(array('error' => $message)); exit; }
function sinx_ai_credential_field($name) {
    return preg_match('/(^|_)(pswd|password|passwd|pwd|salt|token|secret|api_key|apikey|private_key|access_key|refresh_token|auth_token|authorization|credential)(_|$)/i', $name) === 1;
}
function sinx_ai_validate_sql($sql, $allowedTables) {
    $sql = trim($sql);
    $sql = preg_replace('/^[\x60]{3}(?:sql)?\s*|\s*[\x60]{3}$/i', '', $sql);
    $sql = preg_replace('/;\s*$/', '', $sql);
    if (strlen($sql) > 10000 || strpos($sql, ';') !== false || strpos($sql, '--') !== false || strpos($sql, '/*') !== false || strpos($sql, '*/') !== false || strpos($sql, '#') !== false) return false;
    if (strpos($sql, '@') !== false || strpos($sql, ':=') !== false) return false;
    if (!preg_match('/^SELECT\b/i', $sql) || preg_match('/\b(UNION|INSERT|UPDATE|DELETE|REPLACE|DROP|ALTER|CREATE|TRUNCATE|GRANT|REVOKE|INTO|OUTFILE|DUMPFILE|PROCEDURE|ANALYSE|FOR\s+UPDATE|FOR\s+SHARE|LOCK|SLEEP|BENCHMARK|LOAD_FILE|INFORMATION_SCHEMA|PERFORMANCE_SCHEMA|MYSQL\.|SYS\.|STRAIGHT_JOIN)\b/i', $sql)) return false;
    if (preg_match('/\(\s*SELECT\b/i', $sql) || preg_match_all('/\bSELECT\b/i', $sql) !== 1) return false;
    preg_match_all('/\b([a-z_][a-z0-9_]*)\s*\(/i', $sql, $allFunctions);
    $safe = array('count','sum','avg','min','max','round','coalesce','ifnull','nullif','if','year','month','day','date','datediff','timestampdiff','date_format','curdate','current_date','now','concat','concat_ws','lower','upper','trim','ltrim','rtrim','length','char_length','substring','substr','left','right','locate','replace','abs','floor','ceil','cast','convert','group_concat');
    foreach ($allFunctions[1] as $fn) if (!in_array(strtolower($fn), $safe, true)) return false;
    if (preg_match('/\bSELECT\s+(?:[a-z0-9_]+\.)?\*(?:\s|,|$)/i', $sql)) return false;
    if (preg_match('/\bFROM\s+[\x60]?[a-zA-Z0-9_]+[\x60]?(?:\s+(?:AS\s+)?[a-zA-Z_][a-zA-Z0-9_]*)?\s*,/i', $sql)) return false;
    preg_match_all('/\b(?:FROM|JOIN)\s+[\x60]?([a-zA-Z0-9_]+)[\x60]?/i', $sql, $tables);
    if (empty($tables[1])) return false;
    foreach ($tables[1] as $table) if (!in_array(strtolower($table), $allowedTables, true)) return false;
    if (preg_match('/\b(pswd|password|passwd|pwd|salt|token|secret|api_key|apikey|private_key|access_key|refresh_token|auth_token|authorization|credential)\b/i', $sql)) return false;
    if (preg_match('/\bLIMIT\b/i', $sql)) {
        if (!preg_match('/\bLIMIT\s+([0-9]+)\s*$/i', $sql, $limit)) return false;
        if ((int)$limit[1] > 100) return false;
    } else $sql .= ' LIMIT 100';
    return $sql;
}

$question = trim(isset($_POST['question']) ? $_POST['question'] : '');
if ($question === '' || strlen($question) > 1000) sinx_ai_json_error(400, 'Scrivi una domanda (massimo 1000 caratteri).');
$settings = sinx_ai_load_settings();
if (!$settings['api_key'] || !$settings['model']) sinx_ai_json_error(503, 'Assistente AI non configurato.');

// Reuse Sinx's existing database settings. The AI query still runs in a
// read-only transaction and passes the SELECT/table/function allowlist below.
include __DIR__ . '/dati_db.inc';
$portAi = isset($port) ? (int)$port : 3306;
$db = @mysqli_connect($host, $username, $password, $db_name, $portAi);
if (!$db) sinx_ai_json_error(503, 'Connessione al database AI non disponibile.');
mysqli_set_charset($db, 'utf8mb4');
$schema = array();
$allowedTables = array();
$tablesResult = mysqli_query($db, 'SHOW TABLES');
if (!$tablesResult) sinx_ai_json_error(503, 'Non riesco a leggere l’elenco delle tabelle del database.');
$excludedTables = array('utenti', 'tb_utenti', 'users', 'user', 'auth', 'tb_auth', 'login', 'tb_login');
while ($tableRow = mysqli_fetch_row($tablesResult)) {
    $table = $tableRow[0];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) continue;
    if (in_array(strtolower($table), $excludedTables, true)) continue;
    $columnsSql = 'SHOW COLUMNS FROM ' . chr(96) . $table . chr(96);
    $columnsResult = mysqli_query($db, $columnsSql);
    if (!$columnsResult) continue;
    while ($column = mysqli_fetch_assoc($columnsResult)) {
        if (!sinx_ai_credential_field($column['Field'])) $schema[$table][] = $column['Field'];
    }
    if (!empty($schema[$table])) $allowedTables[] = strtolower($table);
    else unset($schema[$table]);
}
if (!$schema) sinx_ai_json_error(503, 'Non trovo tabelle applicative consultabili nel database.');
$allowedTables = array_values(array_unique($allowedTables));

try {
    $prompt = "Sei l'assistente informativo di Sinx. Genera una sola query MySQL SELECT di sola lettura per rispondere alla domanda. Restituisci esclusivamente SQL, senza Markdown e senza punto e virgola finale. Puoi consultare tutte le tabelle e le colonne dello schema fornito, anche dati identificativi come nome e cognome, perché la richiesta proviene dall'amministratore autenticato. Non tentare di leggere credenziali o segreti: le relative tabelle e colonne sono escluse. Usa solo le tabelle e colonne elencate; non usare SELECT *, subquery, UNION, CTE, commenti, variabili SQL, assegnazioni, query multiple o funzioni non necessarie. Sono ammesse le funzioni COUNT, SUM, AVG, MIN, MAX, ROUND, COALESCE, IFNULL, NULLIF, IF, YEAR, MONTH, DAY, DATE, DATEDIFF, TIMESTAMPDIFF, DATE_FORMAT, CURDATE, NOW, CONCAT, CONCAT_WS, LOWER, UPPER, TRIM, LENGTH, CHAR_LENGTH, SUBSTRING, LEFT, RIGHT, LOCATE, REPLACE, ABS, FLOOR, CEIL, CAST, CONVERT e GROUP_CONCAT. Per domande sui nominativi cerca nei campi nome e cognome; se viene chiesto un elenco, restituisci le colonne utili e non solo un conteggio. Metti LIMIT 100. Schema: " . json_encode($schema) . "\nDomanda: " . $question;
    $sql = sinx_ai_call($settings, array(array('role' => 'user', 'content' => $prompt)));
    $safeSql = sinx_ai_validate_sql($sql, array_map('strtolower', $allowedTables));
    if ($safeSql === false) sinx_ai_json_error(422, 'La richiesta richiede dati o una query non consentiti. Riformula la domanda in modo più generale.');
    if (!mysqli_query($db, 'START TRANSACTION READ ONLY')) sinx_ai_json_error(503, 'Il database non supporta una transazione in sola lettura per l’assistente.');
    $result = mysqli_query($db, $safeSql);
    if (!$result) { mysqli_rollback($db); sinx_ai_json_error(422, 'Non riesco a leggere i dati per questa domanda.'); }
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $key => $value) if (sinx_ai_credential_field($key)) unset($row[$key]);
        $rows[] = $row;
        if (count($rows) >= 100) break;
    }
    mysqli_commit($db);
    $answer = sinx_ai_call($settings, array(
        array('role' => 'system', 'content' => 'Rispondi in italiano in modo breve e chiaro usando solo i risultati forniti. Se i risultati sono vuoti o insufficienti, dichiaralo. Non inventare valori e non suggerire modifiche al database.'),
        array('role' => 'user', 'content' => 'Domanda: ' . $question . "\nRisultati: " . json_encode($rows))
    ));
    echo json_encode(array('answer' => $answer));
} catch (Exception $e) {
    // Keep provider diagnostics on the server, without exposing credentials.
    error_log('[Sinx AI] ' . $e->getMessage());
    $providerStatus = (int)$e->getCode();
    if ($providerStatus === 503 || ($providerStatus >= 500 && $providerStatus <= 599)) {
        sinx_ai_json_error(502, 'Il servizio AI è temporaneamente sovraccarico o non disponibile. Riprova tra qualche minuto.');
    }
    if ($providerStatus === 429) {
        sinx_ai_json_error(502, 'Il fornitore AI ha temporaneamente limitato le richieste. Attendi qualche minuto e riprova.');
    }
    sinx_ai_json_error(502, 'Il fornitore AI non è al momento raggiungibile. Verifica la configurazione e riprova.');
}
mysqli_close($db);
?>
