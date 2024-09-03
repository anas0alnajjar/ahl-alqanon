<?php
session_start();

$secret_key = '0940005979';

$passed_key = null;
if (php_sapi_name() === 'cli') {
    // إذا كان التشغيل من سطر الأوامر
    if (isset($argv[1])) {
        parse_str($argv[1], $args);
        $passed_key = $args['key'] ?? null;
    }
} else {
    // إذا كان التشغيل من خلال متصفح
    $passed_key = $_GET['key'] ?? null;
}

if ($passed_key !== $secret_key) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
} else {
    $_SESSION['authenticated'] = true;
    echo "Access granted!";
}
?>
