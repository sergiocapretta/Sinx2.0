<?php
session_start();

$root = dirname(__DIR__);
$lock_file = __DIR__ . '/installazione_completata.lock';
if (file_exists($lock_file)) {
    http_response_code(403);
    echo '<!doctype html><html lang="it"><meta charset="utf-8"><title>Sinx già installato</title><body style="font-family:Arial,sans-serif;background:#f3f5f8;padding:30px"><main style="max-width:620px;margin:40px auto;background:white;padding:30px;border-radius:12px"><h1>Sinx risulta già installato</h1><p>La procedura di installazione è stata completata. Per sicurezza non è possibile avviarla nuovamente.</p><p>Se devi reinstallare Sinx, rimuovi manualmente il file di blocco solo dopo aver verificato di voler eseguire una nuova installazione.</p><p><a href="../index.php">Vai al login</a></p></main></body></html>';
    exit;
}

if (isset($_GET['db_configurato']) && $_GET['db_configurato'] === '1') {
    header('Location: ./install_database.php');
    exit;
}

if (empty($_SESSION['sinx_install_csrf'])) {
    $_SESSION['sinx_install_csrf'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Installazione Sinx 2.0</title>
<link rel="stylesheet" href="../style.css">
<style>
*{box-sizing:border-box}body{margin:0;padding:28px 16px;background:#f3f5f8;color:#263445;font-family:Arial,sans-serif}
.install-wrap{max-width:760px;margin:30px auto}.install-card{background:#fff;border-radius:14px;padding:34px;box-shadow:0 8px 30px rgba(25,45,70,.08)}
.brand{font-size:28px;font-weight:700;margin-bottom:6px}.muted{color:#657487;line-height:1.55}.steps{display:flex;gap:8px;margin:24px 0}.step{flex:1;border-radius:8px;background:#e8edf3;padding:10px;text-align:center;font-size:13px;color:#5c6877}.step.active{background:#1f669e;color:#fff}
label{display:block;font-weight:600;margin:17px 0 7px}input{display:block;width:100%;padding:12px 13px;border:1px solid #cbd4df;border-radius:7px;font-size:16px;background:#fff}
button{margin-top:24px;padding:13px 22px;border:0;border-radius:7px;background:#1769aa;color:#fff;font-size:16px;font-weight:600;cursor:pointer}button:hover{background:#125687}
.notice{background:#eef6fc;border-left:4px solid #2581bd;padding:13px 16px;border-radius:5px;line-height:1.5;margin:20px 0}.foot{margin-top:22px;color:#718096;font-size:12px}
@media(max-width:560px){.install-card{padding:22px}.step{font-size:11px;padding:8px 3px}}
</style>
</head>
<body>
<main class="install-wrap">
<section class="install-card">
<div class="brand">Sinx 2.0</div>
<p class="muted">Procedura guidata di installazione</p>
<div class="steps"><div class="step active">1. Database</div><div class="step">2. Preparazione</div><div class="step">3. Amministratore</div><div class="step">4. Logo</div><div class="step">5. Associazione</div></div>
<h1>Collegamento al database</h1>
<p class="muted">Inserisci i dati del database MySQL che hai già creato dal pannello del tuo hosting. Sinx non crea un nuovo database: verifica la connessione e configura automaticamente il file necessario.</p>
<div class="notice"><strong>Prima di iniziare</strong><br>Assicurati che il database esista e che l'utente indicato abbia i privilegi per creare e modificare tabelle.</div>
<form action="./configura_db.php" method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['sinx_install_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
<label for="host">Host database *</label>
<input id="host" name="host" type="text" placeholder="es. localhost" required autocomplete="off">
<label for="db_name">Nome database *</label>
<input id="db_name" name="db_name" type="text" required autocomplete="off">
<label for="username">Utente database *</label>
<input id="username" name="username" type="text" required autocomplete="username">
<label for="password">Password database</label>
<input id="password" name="password" type="password" autocomplete="new-password">
<button type="submit">Verifica e continua</button>
</form>
<p class="foot">I dati vengono verificati sul server e salvati nel file dati_db.inc. Non vengono mostrati nuovamente nella pagina.</p>
</section>
</main>
</body>
</html>
