<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include "../../DB_connection.php";
    include '../permissions_script.php';
    if ($pages['profiles']['read'] == 0) {
        header("Location: ../home.php");
        exit();
    }

    $admin_id = $_SESSION['user_id'];

    // جلب office_ids المرتبطة بالآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        $stmt = $conn->prepare("SELECT profiles.*, offices.office_name
                                FROM profiles 
                                LEFT JOIN offices ON profiles.office_id = offices.office_id 
                                WHERE profiles.office_id IN ($office_ids)
                                ORDER BY profiles.id DESC");
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
