<?php
require_once 'auth.php';


if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo "Access forbidden!";
    exit;
}

require_once 'DB_connection.php';
include "send_email_sessions.php";
include "send_whatsup_sessions.php";
include "send_dues_reminder.php";
include "send_dues.php";
echo "\nDone!";
?>
