<?php
session_start();

// التحقق من صلاحيات الجلسة
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Lawyer') {
    header("Location: ../../logout.php");
    exit();
}

include "../../DB_connection.php";
include "../get_office.php";

$response = [];
try {
    $user_id = $_SESSION['user_id'];
    $OfficeId = getOfficeId($conn, $user_id);

    if (!empty($OfficeId)) {
        $stmt = $conn->prepare("
            SELECT h.username, h.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, h.id, 'إداري' AS source 
            FROM helpers h
            LEFT JOIN powers p ON h.role_id = p.power_id
            WHERE h.lawyer_id = :lawyer_id
            
            UNION
            
            SELECT c.username, c.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, c.client_id AS id, 'موكل' AS source 
            FROM clients c
            LEFT JOIN powers p ON c.role_id = p.power_id
            WHERE c.lawyer_id = :lawyer_id AND c.username IS NOT NULL AND c.username != ''
        ");
        $stmt->bindParam(':lawyer_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = $results;
    } else {
        $response = [];
    }
} catch (Exception $e) {
    $response = ['error' => 'An error occurred: ' . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
