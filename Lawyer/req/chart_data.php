<?php
include "../../DB_connection.php";

if (isset($_POST['client_id'])) {
    $client_id = $_POST['client_id'];

    $query = "
    SELECT s.session_date, COUNT(s.sessions_id) AS count, c.case_title
    FROM sessions s
    JOIN cases c ON s.case_id = c.case_id
    WHERE c.client_id = :client_id
    GROUP BY s.session_date, c.case_title
    ORDER BY s.session_date";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sessions' => $sessions]);
} else {
    echo json_encode(['error' => 'Client ID not set']);
}
?>
