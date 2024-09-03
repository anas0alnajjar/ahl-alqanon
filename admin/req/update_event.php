<?php
include "../../DB_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_start_date = $_POST['event_start_date'];
    $event_end_date = $_POST['event_end_date'];
    $client_id = isset($_POST['client_name']) ? $_POST['client_name'] : null;
    $lawyer_id = isset($_POST['lawer_name']) ? $_POST['lawer_name'] : null;


    try {
        $sql = "UPDATE events 
                SET event_name = :event_name, 
                    event_start_date = :event_start_date, 
                    event_end_date = :event_end_date, 
                    lawyer_id = :lawyer_id, 
                    client_id = :client_id
                WHERE event_id = :event_id"; // تم إزالة الفاصلة الزائدة هنا
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_name', $event_name, PDO::PARAM_STR);
        $stmt->bindParam(':event_start_date', $event_start_date, PDO::PARAM_STR);
        $stmt->bindParam(':event_end_date', $event_end_date, PDO::PARAM_STR);
        $stmt->bindParam(':lawyer_id', $lawyer_id, PDO::PARAM_INT);
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update event']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
