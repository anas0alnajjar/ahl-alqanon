<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {
include "../../DB_connection.php";

if (isset($_POST['client_id'])) {
    $client_id = $_POST['client_id'];
    $helper_id = $_SESSION['user_id'];

    $query = "
    SELECT s.session_date, COUNT(s.sessions_id) AS count, c.case_title
    FROM sessions s
    JOIN cases c ON s.case_id = c.case_id
    WHERE c.client_id = :client_id AND FIND_IN_SET(:helper_id, c.helper_name)
    GROUP BY s.session_date, c.case_title
    ORDER BY s.session_date";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->bindParam(':helper_id', $helper_id, PDO::PARAM_INT);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sessions' => $sessions]);
} else {
    // echo json_encode(['error' => 'Client ID or Helper ID not set']);
}
} else {
    header("Location: ../../index.php");
    exit;
} 
} else {
header("Location: ../../index.php");
exit;
} 
?>
