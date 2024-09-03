<?php
session_start();
include "../../DB_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData = $_POST;
    $formData['created_date'] = date("Y-m-d");
    $formData['last_modified'] = date("Y-m-d");
    $formData['helper_name'] = $_SESSION['user_id'];

    $sessionNumbers = isset($formData['session_number']) ? $formData['session_number'] : [];
    $sessionDates = isset($formData['session_date']) ? $formData['session_date'] : [];
    $sessionHours = isset($formData['session_hour']) ? $formData['session_hour'] : [];
    $sessionNotes = isset($formData['notes']) ? $formData['notes'] : [];
    $assistant_lawyer = isset($formData['assistant_lawyer']) ? $formData['assistant_lawyer'] : [];
    $session_date_hjri = isset($formData['session_date_hjri']) ? $formData['session_date_hjri'] : [];

    unset($formData['session_number'], $formData['session_date'], $formData['session_hour'], $formData['session_date_hjri'], $formData['notes'],$formData['assistant_lawyer']);

    if (!empty($_FILES['id_picture']['tmp_name'])) {
        $pictureTmpName = $_FILES['id_picture']['tmp_name'];
        $pictureExtension = pathinfo($_FILES['id_picture']['name'], PATHINFO_EXTENSION);
        $pictureName = uniqid('id_picture', true) . '_' . bin2hex(random_bytes(8)) . '.' . $pictureExtension;
        $picturePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $pictureName;

        if (move_uploaded_file($pictureTmpName, $picturePath)) {
            $formData['id_picture'] = $pictureName;
        } else {
            echo "Error: Failed to move uploaded file.";
            exit();
        }
    } else {
        $formData['id_picture'] = '';
    }

    try {
        $conn->beginTransaction();

        if (isset($_POST['plaintiff']) && is_array($_POST['plaintiff'])) {
            $formData['plaintiff'] = implode(',', $_POST['plaintiff']);
        } else {
            $formData['plaintiff'] = '';
        }

        if (isset($_POST['defendant']) && is_array($_POST['defendant'])) {
            $formData['defendant'] = implode(',', $_POST['defendant']);
        } else {
            $formData['defendant'] = '';
        }

        $sql = "INSERT INTO cases (" . implode(", ", array_keys($formData)) . ") VALUES (" . implode(", ", array_fill(0, count($formData), "?")) . ")";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($formData));

        $caseId = $conn->lastInsertId();

        if (!empty($sessionNumbers) && !empty($sessionDates) && !empty($sessionHours)) {
            $sessionSql = "INSERT INTO sessions (case_id, session_number, session_date, session_hour, session_date_hjri, notes, assistant_lawyer) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sessionStmt = $conn->prepare($sessionSql);

            for ($i = 0; $i < count($sessionNumbers); $i++) {
                $sessionStmt->execute([$caseId, $sessionNumbers[$i], $sessionDates[$i], $sessionHours[$i], $session_date_hjri[$i], $sessionNotes[$i], $assistant_lawyer[$i]]);
            }
        }

        $conn->commit();

        echo "New record created successfully";
        unset($_SESSION['client_name']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: Unable to insert record. " . $e->getMessage();
    }
} else {
    header("Location: cases.php");
    exit();
}
?>
