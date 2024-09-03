<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
        include "../../DB_connection.php";

        $case_id = $_POST['case_id'] ?? null;

        if ($case_id) {
            if ($case_id === "ALL") {
                $query = "
                SELECT s.session_date, COUNT(s.sessions_id) AS count, c.case_title
                FROM sessions s
                JOIN cases c ON s.case_id = c.case_id
                WHERE c.client_id = :client_id OR FIND_IN_SET(:client_id, c.plaintiff)
                GROUP BY s.session_date, c.case_title
                ORDER BY s.session_date";
            } else {
                $query = "
                SELECT s.session_date, COUNT(s.sessions_id) AS count, c.case_title
                FROM sessions s
                JOIN cases c ON s.case_id = c.case_id
                WHERE c.case_id = :case_id
                GROUP BY s.session_date, c.case_title
                ORDER BY s.session_date";
            }

            $stmt = $conn->prepare($query);
            if ($case_id === "ALL") {
                $stmt->bindParam(':client_id', $_SESSION['user_id'], PDO::PARAM_INT);
            } else {
                $stmt->bindParam(':case_id', $case_id, PDO::PARAM_INT);
            }
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['sessions' => $sessions]);
        } else {
            echo json_encode(['error' => 'Case ID not set']);
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
