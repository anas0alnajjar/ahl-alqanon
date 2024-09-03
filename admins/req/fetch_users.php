<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include "../../DB_connection.php";

    $admin_id = $_SESSION['user_id'];

    // الحصول على جميع office_ids الخاصة بالآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        $stmt = $conn->prepare("
            SELECT l.username, l.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, l.lawyer_id AS id, 'محامي' AS source 
            FROM lawyer l
            LEFT JOIN powers p ON l.role_id = p.power_id
            WHERE l.office_id IN ($office_ids)
            
            UNION
            
            SELECT h.username, h.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, h.id, 'إداري' AS source 
            FROM helpers h
            LEFT JOIN powers p ON h.role_id = p.power_id
            WHERE h.office_id IN ($office_ids)
            
            UNION
            
            SELECT c.username, c.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, c.client_id AS id, 'موكل' AS source 
            FROM clients c
            LEFT JOIN powers p ON c.role_id = p.power_id
            WHERE c.office_id IN ($office_ids) AND c.username IS NOT NULL AND c.username != ''
            
            UNION
            
            SELECT m.username, m.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, m.id, 'مدير مكتب' AS source 
            FROM managers_office m
            LEFT JOIN powers p ON m.role_id = p.power_id
            WHERE m.office_id IN ($office_ids)
        ");
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} else {
    header("Location: ../../logout.php");
    exit();
}
?>
