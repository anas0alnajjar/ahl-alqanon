<?php
session_start();
if (isset($_SESSION['admin_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    $stmt = $conn->prepare("SELECT profiles.*, offices.office_name
                            FROM profiles 
                            LEFT JOIN offices ON profiles.office_id = offices.office_id 
                            ORDER BY profiles.id DESC");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
