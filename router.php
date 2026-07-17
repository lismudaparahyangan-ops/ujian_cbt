<?php
// Custom router for PHP built-in server so CodeIgniter routes work correctly on Railway.
// Static files are served directly if they exist; all other requests go to index.php.
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . ($url['path'] ?? '');
    if ($file !== __FILE__ && is_file($file)) {
        return false;
    }
}

require_once __DIR__ . '/index.php';
