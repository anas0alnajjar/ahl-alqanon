<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {
    include "../../DB_connection.php";
    include '../permissions_script.php';
        if ($pages['inbox']['delete'] == 0) {
            header("Location: ../home.php");
            exit();
        }

    if (isset($_POST['message_id'])) {
        $message_id = $_POST['message_id'];

        $sql = "DELETE FROM `message` WHERE message_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$message_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'unauthorized']);
}
?>
