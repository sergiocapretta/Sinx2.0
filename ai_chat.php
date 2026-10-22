<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['utente'])) { http_response_code(401); echo json_encode(array('error' => 'Sessione scaduta.')); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(array('error' => 'Metodo non consentito.')); exit; }
require_once __DIR__ . '/ai_provider.php';

function sinx_ai_json_error($code, $message) { http_response_code($code); echo json_encode(array('error' => $message)); exit; }
function sinx_ai_sensitive_name($name) {
    return preg_match('/(^|_)(nome|cognome|email|mail|telefono|cellulare|indirizzo|cap|codice_fiscale|codfisc|cf|iban|pswd|password|datan|nascita|pec|privacy)(_|$)/i', $name) === 1;
}
function sinx_ai_validate_sql($sql, $allowedTables) {
    $sql = trim($sql);
    $sql = preg_replace('/^```(?:sql)?\s*|\s*```$/i', '', $sql);
    if (strlen($sql) > 5000 || strpos($sql, ';') !== false || strpos($sql, '--') !== false || strpos($sql, '/*') !== false || strpos($sql, '#') !== false) return false;
    if (!preg_match('/^SELECT\b/i', $sql) || preg_match('/\b(UNION|INSERT|UPDATE|DELETE|REPLACE|DROP|ALTER|CREATE|TRUNCATE|GRANT|REVOKE|INTO|OUTFILE|DUMPFILE|PROCEDURE|FOR\s+UPDATE|LOCK|SLEEP|BENCHMARK|LOAD_FILE|INFORMATION_SCHEMA|MYSQL\.)\b/i', $sql)) return false;
    if (preg_match('/\(\s*SELECT\b/i', $sql) || preg_match_all('/\bSELECT\b/i', $sql) !== 1) return false;
    if (preg_match('/\b([a-z_][a-z0-9_]*)\s*\(/i', $sql, $functions)) {
        preg_match_all('/\b([a-z_][a-z0-9_]*)\s*\(/i', $sql, $allFunctions);
        $safe = array('count','sum','avg','min','max','round','coalesce','ifnull','year','month','day','date','datediff');
        foreach ($allFunctions[1] as $fn) if (!in_array(strtolower($fn), $safe, true)) return false;
    }
    if (preg_match('/\bSELECT\s+(?:[a-z0-9_]+\.)?\*(?:\s|,|$)/i', $sql)) return false;
    if (preg_match('/\b([a-z0-9_]+)\b/i', $sql) && preg_match('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', $sql) !== 1) return false;
    preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $tables);
    if (empty($tables[1])) return false;
    foreach ($tables[1] as $table) if (!in_array(strtolower($table), $allowedTables, true)) return false;
    // Comma joins can introduce tables without a JOIN token; disallow them.
    if (preg_match('/\bFROM\b([^)]*)/i', $sql, $from) && strpos($from[1], ',') !== false) return false;
    if (preg_match('/\b(nome|cognome|email|mail|telefono|cellulare|indirizzo|cap|codice_fiscale|codfisc|\bcf\b|iban|pswd|password|datan|nascita|pec|privacy)\b/i', $sql)) return false;
    if (!preg_match('/\bLIMIT\s+[0-9]+\s*$/i', $sql)) $sql .= ' LIMIT 100';
    if (preg_match('/\bLIMIT\s+([0-9]+)/i', $sql, $limit) && (int)$limit[1] > 100) return false;
    return $sql;
}

$question = trim(isset($_POST['question']) ? $_POST['question'] : '');
if ($question === '' || strlen($question) > 1000) sinx_ai_json_error(400, 'Scrivi una domanda (massimo 1000 caratteri).');
$settings = sinx_ai_load_settings();
$tableMap = array(
    'anagrafica' => array('tb_anagrafe', 'tb_classe', 'tb_materia'),
    'contabilita' => array('tb_primanota', 'tb_fatture', 'tb_ricevute', 'tb_conto_economico', 'tb_stato_patrimoniale', 'tb_tot_fatture'),
    'progetti' => array('tb_progetto'),
    'calendario' => array('appuntamenti')
);
$allowedTables = array();
foreach ($settings['categories'] as $category) if (isset($tableMap[$category])) $allowedTables = array_merge($allowedTables, $tableMap[$category]);
$allowedTables = array_values(array_unique($allowedTables));
if (!$allowedTables || !$settings['api_key'] || !$settings['model']) sinx_ai_json_error(503, 'Assistente AI non configurato o nessuna categoria abilitata.');

// Reuse Sinx's existing database settings. The AI query still runs in a
// read-only transaction and passes the SELECT/table/function allowlist below.
include __DIR__ . '/dati_db.inc';
$portAi = isset($port) ? (int)$port : 3306;
$db = @mysqli_connect($host, $username, $password, $db_name, $portAi);
if (!$db) sinx_ai_json_error(503, 'Connessione al database AI non disponibile.');
mysqli_set_charset($db, 'utf8mb4');
$schema = array();
foreach ($allowedTables as $table) {
    $result = mysqli_query($db, 'SHOW COLUMNS FROM `' . $table . '`');
    if (!$result) continue;
    while ($column = mysqli_fetch_assoc($result)) if (!sinx_ai_sensitive_name($column['Field'])) $schema[$table][] = $column['Field'];
}
if (!$schema) sinx_ai_json_error(503, 'Non trovo tabelle consultabili per le categorie abilitate.');

try {
    $prompt = "Sei l'assistente informativo di Sinx. Genera una sola query MySQL SELECT di sola lettura che risponda alla domanda. Non generare istruzioni o commenti: restituisci solo SQL. Usa esclusivamente le tabelle e colonne elencate. Non usare SELECT *, subquery, UNION, commenti, funzioni diverse da COUNT, SUM, AVG, MIN, MAX, ROUND, COALESCE, IFNULL, YEAR, MONTH, DAY, DATE, DATEDIFF. Non selezionare né filtrare dati identificativi. Metti LIMIT 100. Schema consultabile: " . json_encode($schema) . "\nDomanda: " . $question;
    $sql = sinx_ai_call($settings, array(array('role' => 'user', 'content' => $prompt)));
    $safeSql = sinx_ai_validate_sql($sql, array_map('strtolower', $allowedTables));
    if ($safeSql === false) sinx_ai_json_error(422, 'La richiesta richiede dati o una query non consentiti. Riformula la domanda in modo più generale.');
    if (!preg_match('/\b(COUNT|SUM|AVG)\s*\(/i', $safeSql)) sinx_ai_json_error(422, 'Per proteggere i dati personali, l’assistente risponde solo con risultati aggregati.');
    if (!mysqli_query($db, 'START TRANSACTION READ ONLY')) sinx_ai_json_error(503, 'Il database non supporta una transazione in sola lettura per l’assistente.');
    $result = mysqli_query($db, $safeSql);
    if (!$result) { mysqli_rollback($db); sinx_ai_json_error(422, 'Non riesco a leggere i dati per questa domanda.'); }
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $key => $value) if (sinx_ai_sensitive_name($key)) unset($row[$key]);
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
    sinx_ai_json_error(502, 'Il fornitore AI non è al momento raggiungibile. Verifica la configurazione.');
}
mysqli_close($db);
?>
