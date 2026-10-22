<?php
/* Sinx AI provider adapter. Requires PHP 7+ and OpenSSL HTTPS stream support. */
function sinx_ai_defaults() {
    return array('provider' => 'openai', 'model' => '', 'api_key' => '', 'endpoint' => '', 'categories' => array());
}

function sinx_ai_load_settings() {
    $file = __DIR__ . '/.sinx_ai_settings.php';
    if (!is_file($file)) return sinx_ai_defaults();
    $settings = include $file;
    if (!is_array($settings)) return sinx_ai_defaults();
    return array_merge(sinx_ai_defaults(), $settings);
}

function sinx_ai_call($settings, $messages) {
    $provider = isset($settings['provider']) ? $settings['provider'] : '';
    $model = trim(isset($settings['model']) ? $settings['model'] : '');
    $key = trim(isset($settings['api_key']) ? $settings['api_key'] : '');
    if (!$key || !$model) throw new Exception('Configurazione AI incompleta.');

    if ($provider === 'openai') {
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = array('Authorization: Bearer ' . $key);
        $body = array('model' => $model, 'messages' => $messages, 'temperature' => 0);
    } elseif ($provider === 'qwen') {
        $endpoint = rtrim(isset($settings['endpoint']) ? $settings['endpoint'] : '', '/');
        $parts = parse_url($endpoint);
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $validHost = $host === 'dashscope.aliyuncs.com' || preg_match('/^(?:[a-z0-9-]+\.)+maas\.aliyuncs\.com$/', $host);
        if (!$validHost || !isset($parts['scheme']) || $parts['scheme'] !== 'https' || (isset($parts['path']) && $parts['path'] !== '/compatible-mode/v1') || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || isset($parts['port'])) {
            throw new Exception('Endpoint Qwen non valido.');
        }
        $url = $endpoint . '/chat/completions';
        $headers = array('Authorization: Bearer ' . $key);
        $body = array('model' => $model, 'messages' => $messages, 'temperature' => 0);
    } elseif ($provider === 'anthropic') {
        $url = 'https://api.anthropic.com/v1/messages';
        $headers = array('x-api-key: ' . $key, 'anthropic-version: 2023-06-01');
        $system = '';
        $contents = array();
        foreach ($messages as $message) {
            if ($message['role'] === 'system') $system .= $message['content'] . "\n";
            else $contents[] = array('role' => $message['role'] === 'assistant' ? 'assistant' : 'user', 'content' => $message['content']);
        }
        $body = array('model' => $model, 'max_tokens' => 900, 'system' => $system, 'messages' => $contents);
    } elseif ($provider === 'gemini') {
        // Accept either a bare model ID or the API's "models/{id}" form.
        $modelId = preg_replace('/^models\//', '', $model);
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($modelId) . ':generateContent';
        // Send the key in the documented header instead of the URL.
        $headers = array('x-goog-api-key: ' . $key);
        $contents = array();
        $systemInstruction = '';
        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemInstruction .= ($systemInstruction === '' ? '' : "\n") . $message['content'];
                continue;
            }
            $role = $message['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = array('role' => $role, 'parts' => array(array('text' => $message['content'])));
        }
        $body = array('contents' => $contents, 'generationConfig' => array('temperature' => 0, 'maxOutputTokens' => 900));
        if ($systemInstruction !== '') {
            $body['systemInstruction'] = array('parts' => array(array('text' => $systemInstruction)));
        }
    } else {
        throw new Exception('Fornitore AI non supportato.');
    }

    $headers[] = 'Content-Type: application/json';
    $context = stream_context_create(array('http' => array(
        'method' => 'POST', 'header' => implode("\r\n", $headers),
        'content' => json_encode($body), 'timeout' => 35, 'ignore_errors' => true,
        'follow_location' => 0
    ), 'ssl' => array('verify_peer' => true, 'verify_peer_name' => true)));
    // Retry transient network and service errors with exponential backoff.
    $maxAttempts = 3;
    $data = null;
    $httpStatus = 0;
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            if ($attempt < $maxAttempts) {
                usleep((int)(pow(2, $attempt - 1) * 1000000) + mt_rand(0, 250000));
                continue;
            }
            throw new Exception('Connessione al fornitore AI non riuscita.');
        }

        $data = json_decode($response, true);
        if (!is_array($data)) throw new Exception('Risposta non valida dal fornitore AI.');
        $httpStatus = 0;
        if (isset($http_response_header[0]) && preg_match('/\s([0-9]{3})\s/', $http_response_header[0], $statusMatch)) {
            $httpStatus = (int)$statusMatch[1];
        }

        $isTransient = $httpStatus === 408 || $httpStatus === 429 || ($httpStatus >= 500 && $httpStatus <= 599);
        if ($isTransient && $attempt < $maxAttempts) {
            usleep((int)(pow(2, $attempt - 1) * 1000000) + mt_rand(0, 250000));
            continue;
        }
        break;
    }

    if ($httpStatus >= 400) {
        $providerError = isset($data['error']['message']) ? $data['error']['message'] : 'Risposta di errore senza dettagli.';
        throw new Exception($provider . ' API HTTP ' . $httpStatus . ': ' . $providerError, $httpStatus);
    }

    if ($provider === 'openai' || $provider === 'qwen') $answer = isset($data['choices'][0]['message']['content']) ? $data['choices'][0]['message']['content'] : '';
    elseif ($provider === 'anthropic') $answer = isset($data['content'][0]['text']) ? $data['content'][0]['text'] : '';
    else $answer = isset($data['candidates'][0]['content']['parts'][0]['text']) ? $data['candidates'][0]['content']['parts'][0]['text'] : '';
    if (!$answer) throw new Exception('Il fornitore AI non ha restituito una risposta utilizzabile.');
    return $answer;
}
?>
