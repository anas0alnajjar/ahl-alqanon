<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    $stmt = $conn->prepare("SELECT a.username, a.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, a.admin_id AS id, 'آدمن' AS source 
                            FROM admin a
                            LEFT JOIN powers p ON a.role_id = p.power_id
                            
                            UNION
                            
                            SELECT l.username, l.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, l.lawyer_id AS id, 'محامي' AS source 
                            FROM lawyer l
                            LEFT JOIN powers p ON l.role_id = p.power_id
                            
                            UNION
                            
                            SELECT h.username, h.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, h.id, 'إداري' AS source 
                            FROM helpers h
                            LEFT JOIN powers p ON h.role_id = p.power_id
                            
                            UNION
                            
                            SELECT c.username, c.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, c.client_id AS id, 'موكل' AS source 
                            FROM clients c
                            LEFT JOIN powers p ON c.role_id = p.power_id
                            WHERE c.username IS NOT NULL AND c.username != ''
                            
                            UNION
                            
                            SELECT m.username, m.role_id, COALESCE(p.role, 'لم يتم تحديد رول له بعد') AS role, m.id, 'مدير مكتب' AS source 
                            FROM managers_office m
                            LEFT JOIN powers p ON m.role_id = p.power_id;");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
