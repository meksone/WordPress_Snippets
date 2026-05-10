<?php
/**
 * mk-create-admin.php
 * Drop in WordPress root. Activate via https://site.com/mk-create-admin.php?mk-create-admin=<password>
 * Self-deletes after use.
 * Version: 1.0.3
 */

// ============================================================
// CONFIG — edit before deploying
// ============================================================

// Password hash — generate with: php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
define('MK_PASSWORD_HASH', '$2y$10$D8adhm1ha2uV5Fky0q8thO6hFjdZ5tQrewd1DxYnyzHZsEUg55WK2');

// New admin user details
define('MK_USER_LOGIN',    'mk-admin');
define('MK_USER_EMAIL',    'wordpress@meksone.com');
define('MK_USER_PASS',     'changeme2026!');
define('MK_USER_FNAME',    'MK');
define('MK_USER_LNAME',    'Admin');

// ============================================================
// END CONFIG
// ============================================================

// Only respond to correct querystring key
if (!isset($_GET['mk-create-admin'])) {
    http_response_code(404);
    exit;
}

$input = $_GET['mk-create-admin'];

// Hash must be replaced from placeholder
if (strpos(MK_PASSWORD_HASH, 'CHANGEME') !== false) {
    die('ERROR: Password hash not configured.');
}

// Verify password
if (!password_verify($input, MK_PASSWORD_HASH)) {
    http_response_code(403);
    die('Forbidden.');
}

// Auto-detect wp-load.php

$wp_load = find_wp_load(__FILE__);
if (!$wp_load) {
    die('ERROR: Could not locate wp-load.php');
}

// Load WordPress
define('ABSPATH_SEARCH', true);
require_once $wp_load;

// Check WP loaded
if (!function_exists('wp_create_user')) {
    die('ERROR: WordPress failed to load.');
}

$result = create_admin_user();
self_delete(__FILE__);

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Done</title>';
echo '<style>body{font-family:monospace;padding:2em;background:#0a0a0a;color:#0f0}</style></head><body>';
echo '<h2>mk-create-admin</h2>';
echo '<pre>' . esc_html($result) . '</pre>';
echo '<p style="color:#ff0">Script deleted. Remove this page from browser history.</p>';
echo '</body></html>';
exit;

// ============================================================
// FUNCTIONS
// ============================================================

function find_wp_load($start) {
    $dir = dirname($start);
    // Search up to 3 levels up
    for ($i = 0; $i < 4; $i++) {
        $candidate = $dir . '/wp-load.php';
        if (file_exists($candidate) && file_exists($dir . '/wp-config.php')) {
            return $candidate;
        }
        $parent = dirname($dir);
        if ($parent === $dir) break; // filesystem root
        $dir = $parent;
    }
    return false;
}

function create_admin_user() {
    $login = MK_USER_LOGIN;
    $email = MK_USER_EMAIL;
    $pass  = MK_USER_PASS;

    // Check existing
    if (username_exists($login)) {
        return "ERROR: Username '{$login}' already exists.";
    }
    if (email_exists($email)) {
        return "ERROR: Email '{$email}' already in use.";
    }

    $user_id = wp_create_user($login, $pass, $email);

    if (is_wp_error($user_id)) {
        return 'ERROR: ' . $user_id->get_error_message();
    }

    $user = new WP_User($user_id);
    $user->set_role('administrator');

    wp_update_user([
        'ID'         => $user_id,
        'first_name' => MK_USER_FNAME,
        'last_name'  => MK_USER_LNAME,
    ]);

    return "OK: Admin created.\nUsername: {$login}\nEmail: {$email}\nPassword: {$pass}\nUser ID: {$user_id}";
}

function self_delete($file) {
    if (is_writable($file)) {
        unlink($file);
    }
}
