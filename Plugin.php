<?php
session_start();
if (empty($_SESSION['utente'])) {
    header('Location: ./index.php');
    exit;
}

include_once __DIR__ . '/plugins/functions.php';
$catalog = include __DIR__ . '/plugins/catalog.php';

include __DIR__ . '/top.inc';

if (!sinxPluginsEnsureTable($connect)) {
    echo '<div class="plugin-error">Non è stato possibile predisporre il registro dei plugin. Verificare i permessi del database.</div>';
    include './botton.inc';
    exit;
}

$installed = sinxPluginsGetAll($connect);
$isAdmin = sinxPluginsIsAdmin();
$notice = isset($_GET['notice']) ? (string) $_GET['notice'] : '';
?>
<link rel="stylesheet" href="./plugins/plugins.css">
<main class="plugins-page">
  <div class="plugins-heading">
    <h1>Plugin disponibili</h1>
    <span class="plugin-status"><?php echo count($installed); ?> installati</span>
  </div>
  <p class="plugins-intro">Estendi le funzionalità di Sinx con applicazioni aggiuntive, autonome e facoltative. I plugin installati compariranno nel menu dedicato.</p>

  <?php if ($notice === 'installed'): ?>
    <div class="plugin-notice">Plugin configurato correttamente. La nuova voce è ora disponibile nel menu <strong>Plugin</strong>.</div>
  <?php elseif ($notice === 'removed'): ?>
    <div class="plugin-notice">Il collegamento al plugin è stato rimosso da Sinx. L'applicazione esterna e i suoi dati non sono stati modificati.</div>
  <?php elseif ($notice === 'error'): ?>
    <div class="plugin-error">Non è stato possibile completare l'operazione. Verificare i dati e riprovare.</div>
  <?php endif; ?>

  <div class="plugins-grid">
  <?php foreach ($catalog as $pluginId => $definition):
      $isInstalled = isset($installed[$pluginId]) && (int) $installed[$pluginId]['enabled'] === 1;
      $logo = htmlspecialchars($definition['logo'], ENT_QUOTES, 'UTF-8');
      $name = htmlspecialchars($definition['name'], ENT_QUOTES, 'UTF-8');
      $description = htmlspecialchars($definition['description'], ENT_QUOTES, 'UTF-8');
      $version = htmlspecialchars($definition['version'], ENT_QUOTES, 'UTF-8');
      $setup = htmlspecialchars($definition['setup_page'], ENT_QUOTES, 'UTF-8');
  ?>
    <article class="plugin-card">
      <div class="plugin-card-top">
        <img class="plugin-logo" src="<?php echo $logo; ?>" alt="Logo <?php echo $name; ?>">
        <div>
          <h2><?php echo $name; ?></h2>
          <div class="plugin-version">Versione <?php echo $version; ?></div>
        </div>
      </div>
      <p class="plugin-description"><?php echo $description; ?></p>
      <div class="plugin-actions">
        <?php if ($isInstalled): ?>
          <span class="plugin-status installed">Installato</span>
          <?php if ($isAdmin): ?>
            <a class="plugin-button secondary" href="<?php echo $setup; ?>">Configura</a>
          <?php endif; ?>
        <?php else: ?>
          <span class="plugin-status">Non installato</span>
          <?php if ($isAdmin): ?>
            <a class="plugin-button" href="<?php echo $setup; ?>">Installa</a>
          <?php else: ?>
            <span class="plugin-help">Installazione riservata all'amministratore</span>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </article>
  <?php endforeach; ?>
  </div>
</main>
<?php
include './botton.inc';
@mysqli_close($connect);
?>
