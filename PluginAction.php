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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./Plugin.php');
    exit;
}

require_once __DIR__ . '/plugins/functions.php';
$catalog = include __DIR__ . '/plugins/catalog.php';

$pluginId = isset($_POST['plugin_id']) ? (string) $_POST['plugin_id'] : '';
$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
$token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';

if (!sinxPluginsVerifyCsrf($token) || !isset($catalog[$pluginId])) {
    header('Location: ./Plugin.php?notice=error');
    exit;
}

include __DIR__ . '/dati_db.inc';
$db = mysqli_connect($host, $username, $password, $db_name);
if (!$db) {
    header('Location: ./Plugin.php?notice=error');
    exit;
}

if (!sinxPluginsEnsureTable($db)) {
    mysqli_close($db);
    header('Location: ./Plugin.php?notice=error');
    exit;
}

if ($action === 'save') {
    $url = trim((string) ($_POST['plugin_url'] ?? ''));
    $roles = sinxPluginsNormalizeRoles($_POST['visible_roles'] ?? []);
    $definition = $catalog[$pluginId];

    if (!sinxPluginsValidateUrl($url) || $roles === '') {
        mysqli_close($db);
        header('Location: ./PluginSetup.php?id=' . rawurlencode($pluginId) . '&error=1');
        exit;
    }

    $plugin = [
        'plugin_id' => $pluginId,
        'name' => $definition['name'],
        'description' => $definition['description'],
        'menu_label' => $definition['menu_label'],
        'menu_icon' => $definition['menu_icon'],
        'url' => $url,
        'version' => $definition['version'],
        'visible_roles' => $roles,
    ];

    $ok = sinxPluginsSave($db, $plugin);
    mysqli_close($db);
    if ($ok) {
        header('Location: ./index2.php?plugin_notice=installed');
    } else {
        header('Location: ./Plugin.php?notice=error');
    }
    exit;
}

if ($action === 'remove') {
    $ok = sinxPluginsRemove($db, $pluginId);
    mysqli_close($db);
    header('Location: ./Plugin.php?notice=' . ($ok ? 'removed' : 'error'));
    exit;
}

mysqli_close($db);
header('Location: ./Plugin.php?notice=error');
exit;
