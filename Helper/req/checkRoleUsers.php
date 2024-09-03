<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
include "../../DB_connection.php";
include '../permissions_script.php';
if ($pages['roles']['delete'] == 0) {
    header("Location: ../home.php");
    exit();
}

if (isset($_GET['id'])) {
    $roleId = (int)$_GET['id'];

    // SQL query to count users associated with the role
    $sql = "SELECT 
                COALESCE(a.count, 0) + COALESCE(l.count, 0) + COALESCE(h.count, 0) + COALESCE(c.count, 0) + COALESCE(m.count, 0) AS user_count
            FROM powers p
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM admin WHERE role_id = ? GROUP BY role_id
            ) a ON p.power_id = a.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM lawyer WHERE role_id = ? GROUP BY role_id
            ) l ON p.power_id = l.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM helpers WHERE role_id = ? GROUP BY role_id
            ) h ON p.power_id = h.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM clients WHERE username IS NOT NULL AND username != '' AND role_id = ? GROUP BY role_id
            ) c ON p.power_id = c.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM managers_office WHERE role_id = ? GROUP BY role_id
            ) m ON p.power_id = m.role_id
            WHERE p.power_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$roleId, $roleId, $roleId, $roleId, $roleId, $roleId]);
    $user_count = $stmt->fetchColumn();

    echo json_encode(['user_count' => $user_count]);
}
?>
<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>