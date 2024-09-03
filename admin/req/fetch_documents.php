<?php
session_start();
if (isset($_SESSION['admin_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    $stmt = $conn->prepare("SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                            FROM documents 
                            LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                            LEFT JOIN clients ON documents.client_id = clients.client_id 
                            ORDER BY documents.document_id DESC");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
