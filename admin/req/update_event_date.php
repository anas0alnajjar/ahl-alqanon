<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    require '../../DB_connection.php';

    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $type = isset($_POST['type']) ? $_POST['type'] : null;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $hijri_date = isset($_POST['hijri_date']) ? $_POST['hijri_date'] : null;

    // طباعة القيم المدخلة للتأكد منها
    error_log("ID: $id, Type: $type, Start Date: $start_date, End Date: $end_date, Hijri Date: $hijri_date");

    if ($id && $type && $start_date && $hijri_date) {
        try {
            if ($type == 'session') {
                $sql = "UPDATE sessions SET session_date = :start_date, session_date_hjri = :hijri_date WHERE sessions_id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':hijri_date', $hijri_date);
                $stmt->bindParam(':id', $id);
            } elseif ($type == 'event') {
                $sql = "UPDATE events SET event_start_date = :start_date, event_end_date = :end_date WHERE event_id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
                $stmt->bindParam(':id', $id);
            }

            $stmt->execute();

            $response = array('status' => 'success');
            echo json_encode($response);
        } catch (Exception $e) {
            $response = array('status' => 'error', 'message' => $e->getMessage());
            echo json_encode($response);
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Invalid input data.');
        echo json_encode($response);
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
