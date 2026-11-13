<?php
/**
 * Funzioni comuni del sistema plugin di Sinx.
 * I plugin esterni restano applicazioni autonome: Sinx memorizza
 * soltanto i metadati e l'indirizzo di accesso.
 */

function sinxPluginsEnsureTable($db)
{
    $sql = "CREATE TABLE IF NOT EXISTS sinx_plugins (
        plugin_id VARCHAR(64) NOT NULL,
        name VARCHAR(120) NOT NULL,
        description VARCHAR(500) NOT NULL,
        menu_label VARCHAR(120) NOT NULL,
        menu_icon VARCHAR(64) NOT NULL DEFAULT 'extension',
        url VARCHAR(500) NOT NULL,
        version VARCHAR(30) NOT NULL DEFAULT '',
        visible_roles VARCHAR(255) NOT NULL DEFAULT 'admin,operatore,associato,limitato',
        enabled TINYINT(1) NOT NULL DEFAULT 1,
        installed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (plugin_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    try {
        return mysqli_query($db, $sql) === true;
    } catch (Throwable $e) {
        return false;
    }
}

function sinxPluginsGetAll($db)
{
    try {
        $result = mysqli_query($db, "SELECT * FROM sinx_plugins ORDER BY name");
        if (!$result) {
            return [];
        }

        $plugins = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $plugins[$row['plugin_id']] = $row;
        }
        mysqli_free_result($result);
        return $plugins;
    } catch (Throwable $e) {
        return [];
    }
}

function sinxPluginsGetOne($db, $pluginId)
{
    try {
        $stmt = mysqli_prepare($db, "SELECT * FROM sinx_plugins WHERE plugin_id = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, "s", $pluginId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $plugin = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $plugin ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function sinxPluginsSave($db, array $plugin)
{
    $sql = "INSERT INTO sinx_plugins
        (plugin_id, name, description, menu_label, menu_icon, url, version, visible_roles, enabled)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        menu_label = VALUES(menu_label),
        menu_icon = VALUES(menu_icon),
        url = VALUES(url),
        version = VALUES(version),
        visible_roles = VALUES(visible_roles),
        enabled = 1";

    try {
        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssss",
            $plugin['plugin_id'],
            $plugin['name'],
            $plugin['description'],
            $plugin['menu_label'],
            $plugin['menu_icon'],
            $plugin['url'],
            $plugin['version'],
            $plugin['visible_roles']
        );

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    } catch (Throwable $e) {
        return false;
    }
}

function sinxPluginsRemove($db, $pluginId)
{
    try {
        $stmt = mysqli_prepare($db, "DELETE FROM sinx_plugins WHERE plugin_id = ?");
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, "s", $pluginId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    } catch (Throwable $e) {
        return false;
    }
}

function sinxPluginsValidateUrl($url)
{
    if (!is_string($url) || $url === '' || strlen($url) > 500) {
        return false;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $parts = parse_url($url);
    if (!$parts || empty($parts['host']) || empty($parts['scheme'])) {
        return false;
    }

    if (!in_array(strtolower($parts['scheme']), ['https', 'http'], true)) {
        return false;
    }

    // Non accettiamo URL con credenziali incorporate.
    if (isset($parts['user']) || isset($parts['pass'])) {
        return false;
    }

    return true;
}

function sinxPluginsAllowedRoles()
{
    return ['admin', 'operatore', 'associato', 'limitato'];
}

function sinxPluginsNormalizeRoles($roles)
{
    if (!is_array($roles)) {
        return '';
    }

    $allowed = sinxPluginsAllowedRoles();
    $selected = [];
    foreach ($roles as $role) {
        if (in_array($role, $allowed, true) && !in_array($role, $selected, true)) {
            $selected[] = $role;
        }
    }

    return implode(',', $selected);
}

function sinxPluginsCsrfToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['sinx_plugins_csrf'])) {
        $_SESSION['sinx_plugins_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['sinx_plugins_csrf'];
}

function sinxPluginsVerifyCsrf($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['sinx_plugins_csrf'])
        && is_string($token)
        && hash_equals($_SESSION['sinx_plugins_csrf'], $token);
}

function sinxPluginsIsAdmin()
{
    return isset($_SESSION['utente']) && $_SESSION['utente'] === 'admin';
}

function sinxPluginsGetForMenu($db, $role)
{
    $plugins = sinxPluginsGetAll($db);
    $visible = [];

    foreach ($plugins as $plugin) {
        if ((int) $plugin['enabled'] !== 1) {
            continue;
        }

        $roles = array_filter(array_map('trim', explode(',', (string) $plugin['visible_roles'])));
        if ($role === 'admin' || in_array($role, $roles, true)) {
            $visible[] = $plugin;
        }
    }

    return $visible;
}
