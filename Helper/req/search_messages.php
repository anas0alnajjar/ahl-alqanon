<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    if (isset($_GET['query'])) {
        $query = $_GET['query'];
        $sql = "SELECT * FROM message WHERE sender_full_name LIKE ? OR message LIKE ? ORDER BY message_id DESC";
        $stmt = $conn->prepare($sql);
        $searchQuery = "%" . $query . "%";
        $stmt->execute([$searchQuery, $searchQuery]);

        if ($stmt->rowCount() > 0) {
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($messages);
        } else {
            echo json_encode([]);
        }
    }
} else {
    echo json_encode([]);
}
?>
