<?php
session_start();
if (empty($_SESSION['utente'])) { header('Location: ./index.php'); exit; }
if ($_SESSION['utente'] !== 'admin') { http_response_code(403); exit('Accesso riservato all’amministratore.'); }
require_once __DIR__ . '/ai_provider.php';
$settings = sinx_ai_load_settings();
$savedProvider = $settings['provider'];
if (empty($_SESSION['sinx_ai_csrf'])) $_SESSION['sinx_ai_csrf'] = bin2hex(openssl_random_pseudo_bytes(24));
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf']) ? $_POST['csrf'] : '';
    if (!hash_equals($_SESSION['sinx_ai_csrf'], $token)) { http_response_code(400); exit('Richiesta non valida.'); }
    $provider = isset($_POST['provider']) ? $_POST['provider'] : '';
    if (!in_array($provider, array('openai', 'anthropic', 'gemini', 'qwen'), true)) { $message = 'Fornitore non valido.'; }
    elseif ($provider !== $savedProvider && empty($_POST['api_key'])) { $message = 'Quando cambi fornitore, inserisci la relativa chiave API.'; }
    else {
        $key = trim(isset($_POST['api_key']) ? $_POST['api_key'] : '');
        $settings = array(
            'provider' => $provider,
            'model' => trim(isset($_POST['model']) ? $_POST['model'] : ''),
            'api_key' => $key !== '' ? $key : $settings['api_key'],
            'endpoint' => trim(isset($_POST['endpoint']) ? $_POST['endpoint'] : ''),
            'categories' => array_values(array_intersect(
                isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] : array(),
                array('anagrafica', 'contabilita', 'progetti', 'calendario')
            ))
        );
        if (!$settings['model'] || !$settings['api_key']) $message = 'Inserisci modello e chiave API.';
        elseif ($provider === 'qwen' && !$settings['endpoint']) $message = 'Per Qwen inserisci l’endpoint della regione della tua API key.';
        else {
            $content = "<?php\n// Sinx AI settings. This file is written by ai_config.php.\nreturn " . var_export($settings, true) . ";\n";
            $path = __DIR__ . '/.sinx_ai_settings.php';
            $tmp = $path . '.tmp';
            if (@file_put_contents($tmp, $content, LOCK_EX) !== false && @rename($tmp, $path)) {
                @chmod($path, 0600);
                $message = 'Configurazione salvata.';
            } else { @unlink($tmp); $message = 'Impossibile salvare la configurazione: verifica i permessi della cartella.'; }
        }
    }
}
$categories = array('anagrafica' => 'Anagrafica (dati non identificativi)', 'contabilita' => 'Contabilità', 'progetti' => 'Progetti', 'calendario' => 'Appuntamenti');
include __DIR__ . '/top.inc';
?>
<section class="content-section"><div class="card">
<h2 class="card-title">Configurazione assistente AI</h2>
<p class="card-subtitle">La chiave resta sul server. Per rispondere, Sinx invia al fornitore selezionato la domanda e i risultati della sola query autorizzata; verifica le condizioni privacy del fornitore.</p>
<?php if ($message) echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>'; ?>
<form method="post" class="sinx-form">
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['sinx_ai_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
<label>Fornitore</label><select name="provider">
<?php foreach (array('openai' => 'OpenAI', 'anthropic' => 'Anthropic', 'gemini' => 'Google Gemini', 'qwen' => 'Qwen (Alibaba Cloud Model Studio)') as $id => $label) echo '<option value="' . $id . '"' . ($settings['provider'] === $id ? ' selected' : '') . '>' . $label . '</option>'; ?>
</select>
<label>Endpoint API (necessario per Qwen)</label><input type="url" name="endpoint" placeholder="https://dashscope.aliyuncs.com/compatible-mode/v1" value="<?php echo htmlspecialchars(isset($settings['endpoint']) ? $settings['endpoint'] : '', ENT_QUOTES, 'UTF-8'); ?>">
<label>Modello (nome API)</label><input type="text" name="model" required value="<?php echo htmlspecialchars($settings['model'], ENT_QUOTES, 'UTF-8'); ?>">
<label>Chiave API<?php echo $settings['api_key'] ? ' (vuoto = mantieni quella salvata)' : ''; ?></label><input type="password" name="api_key" autocomplete="new-password" <?php echo $settings['api_key'] ? '' : 'required'; ?> value="">
<fieldset><legend>Categorie consultabili</legend>
<?php foreach ($categories as $id => $label) { $checked = in_array($id, $settings['categories'], true) ? ' checked' : ''; echo '<label><input type="checkbox" name="categories[]" value="' . $id . '"' . $checked . '> ' . $label . '</label><br>'; } ?>
</fieldset>
<p><small>Non abilitare dati identificativi o informazioni particolari. Le richieste e le risposte non vengono salvate da Sinx.</small></p>
<button type="submit" class="btn-edit">Salva configurazione</button>
</form></div></section>
<?php include __DIR__ . '/menusx.inc'; include __DIR__ . '/botton.inc'; ?>
