<?php

$secret_key = '123';

$passed_key = null;
if (isset($argv[1])) {
    parse_str($argv[1], $args);
    $passed_key = $args['key'] ?? null;
}

if ($passed_key !== $secret_key) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
}


echo "Done!";
?>
