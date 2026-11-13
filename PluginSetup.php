<?php
session_start();
if (empty($_SESSION['utente'])) {
    header('Location: ./index.php');
    exit;
}
if ($_SESSION['utente'] !== 'admin') {
    header('Location: ./index2.php');
    exit;
}

include_once __DIR__ . '/plugins/functions.php';
$catalog = include __DIR__ . '/plugins/catalog.php';
$pluginId = isset($_GET['id']) ? (string) $_GET['id'] : '';
if (!isset($catalog[$pluginId])) {
    header('Location: ./Plugin.php');
    exit;
}
$definition = $catalog[$pluginId];

include __DIR__ . '/top.inc';
if (!sinxPluginsEnsureTable($connect)) {
    echo '<div class="plugin-error">Impossibile predisporre il registro dei plugin. Verificare i permessi del database.</div>';
    include './botton.inc';
    exit;
}

$installed = sinxPluginsGetOne($connect, $pluginId);
$currentUrl = $installed ? $installed['url'] : '';
$currentRoles = $installed ? array_filter(array_map('trim', explode(',', $installed['visible_roles']))) : sinxPluginsAllowedRoles();
$csrf = sinxPluginsCsrfToken();
?>
<main class="plugins-page">
  <section class="plugin-form-card">
    <h1><?php echo $installed ? 'Configura' : 'Installa'; ?> <?php echo htmlspecialchars($definition['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><?php echo htmlspecialchars($definition['description'], ENT_QUOTES, 'UTF-8'); ?></p>

    <?php if (isset($_GET['error'])): ?>
      <div class="plugin-error">L'indirizzo inserito non è valido, non è stato possibile salvare la configurazione oppure non è stato selezionato almeno un profilo. Verificare i dati e riprovare.</div>
    <?php endif; ?>

    <div class="plugin-warning">
      Questa procedura collega Sinx a un'applicazione esterna già installata sul server. Non copia né installa i file di Reunion e non crea il suo database: registra l'indirizzo e aggiunge la voce al menu di Sinx.
    </div>

    <form method="post" action="./PluginAction.php">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="plugin_id" value="<?php echo htmlspecialchars($pluginId, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="action" value="save">

      <label for="plugin_url">Indirizzo completo di Reunion *</label>
      <input type="url" id="plugin_url" name="plugin_url" maxlength="500" required
             placeholder="https://www.esempio.it/reunion/"
             value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8'); ?>">
      <p class="plugin-help">Inserire l'indirizzo completo della pagina iniziale di Reunion, comprensivo di https:// e del percorso corretto.</p>

      <label>Utenti Sinx che potranno visualizzare il collegamento</label>
      <div class="plugin-roles">
        <?php foreach (sinxPluginsAllowedRoles() as $role): ?>
          <label><input type="checkbox" name="visible_roles[]" value="<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>" <?php echo in_array($role, $currentRoles, true) ? 'checked' : ''; ?>> <?php echo htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8'); ?></label>
        <?php endforeach; ?>
      </div>
      <p class="plugin-help">Questa scelta controlla la visibilità della voce nel menu di Sinx. Le autorizzazioni interne a Reunion restano gestite da Reunion.</p>

      <div class="plugin-actions">
        <button class="plugin-button" type="submit"><?php echo $installed ? 'Salva configurazione' : 'Attiva plugin'; ?></button>
        <a class="plugin-button secondary" href="./Plugin.php">Annulla</a>
      </div>
    </form>

    <?php if ($installed): ?>
      <form method="post" action="./PluginAction.php" onsubmit="return confirm('Rimuovere il collegamento di Reunion dal menu di Sinx? L’applicazione esterna e i suoi dati non verranno cancellati.');">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="plugin_id" value="<?php echo htmlspecialchars($pluginId, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="remove">
        <div class="plugin-actions">
          <button class="plugin-button danger" type="submit">Rimuovi da Sinx</button>
        </div>
      </form>
    <?php endif; ?>
  </section>
</main>
<?php
include './botton.inc';
@mysqli_close($connect);
?>
