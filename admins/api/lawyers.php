<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include_once "../../DB_connection.php";

    try {
        $admin_id = $_SESSION['user_id'];

        // جلب المكاتب المرتبطة بالآدمن
        $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
        $stmt_offices = $conn->prepare($sql_offices);
        $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt_offices->execute();
        $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($offices)) {
            $office_ids = implode(',', $offices);

            // جلب المحامين المرتبطين بمكاتب الآدمن
            $sql = "SELECT lawyer_id, lawyer_name FROM lawyer WHERE office_id IN ($office_ids)";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // جلب جميع النتائج
            $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($lawyers)) {
                echo json_encode(["error" => "No lawyers found"]);
            } else {
                echo json_encode($lawyers);
            }
        } else {
            echo json_encode(["error" => "No offices found for this admin"]);
        }

    } catch(PDOException $e) {
        echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    }

} else {
    header("Location: ../../logout.php");
    exit;
}
?>
