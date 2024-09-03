<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    include "../../DB_connection.php";

    $admin_id = $_SESSION['user_id'];

    // جلب مكاتب الآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        // جلب المستندات المرتبطة بالمحامين أو العملاء المرتبطين بمكاتب الآدمن
        $stmt = $conn->prepare("SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                                FROM documents 
                                LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                                LEFT JOIN clients ON documents.client_id = clients.client_id 
                                WHERE lawyer.office_id IN ($office_ids) OR clients.office_id IN ($office_ids)
                                ORDER BY documents.document_id DESC");
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
