<?php
include "../DB_connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM offices WHERE office_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $office = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'id' => $office['office_id'],
        'office_name' => $office['office_name'],
        'admin_id' => $office['admin_id'],
        'stop' => $office['stop'],
        'stop_date' => $office['stop_date'],
        'header_image' => $office['header_image'],
        'footer_text' => $office['footer_text'],
        'default_office' => $office['default_office']
    ]);
}
?>
