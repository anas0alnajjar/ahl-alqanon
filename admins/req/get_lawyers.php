<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include_once "../../DB_connection.php";

    $user_id = $_SESSION['user_id'];

    // جلب مكاتب الآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $user_id);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        // تعديل الاستعلام لجلب المحامين المرتبطين بالمكاتب التي يديرها الآدمن
        $query = "SELECT lawyer_id, lawyer_name 
                  FROM lawyer 
                  WHERE office_id IN ($office_ids)";
        $statement = $conn->prepare($query);
        $statement->execute();
        $lawyers = $statement->fetchAll(PDO::FETCH_ASSOC);

        $response = array(
            "lawyers" => $lawyers
        );

        echo json_encode($response);
    } else {
        // إذا لم يكن للآدمن أي مكاتب
        $response = array(
            "lawyers" => []
        );
        echo json_encode($response);
    }

} else {
    header("Location: ../logout.php");
    exit;
}
?>
