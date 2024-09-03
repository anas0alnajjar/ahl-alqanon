<?php
include "../../DB_connection.php";

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    try {
        $sql = "SELECT * FROM events WHERE event_id = :event_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'event' => $event]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Event not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No event ID provided']);
}
?>
