<?php
session_start();
include "../../DB_connection.php";

// Check if the form data is received properly
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize an array to hold the form data
    $formData = $_POST;
    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Prepare an SQL statement with placeholders for inserting into cases
        $sql = "INSERT INTO clients (" . implode(", ", array_keys($formData)) . ") VALUES (" . implode(", ", array_fill(0, count($formData), "?")) . ")";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($formData));

        // Get the ID of the newly created client
        $clientId = $conn->lastInsertId();
        $_SESSION['client_name'] = $clientId;
        // Commit the transaction
        $conn->commit();

        // Return the client ID as JSON
        echo json_encode(['clientId' => $clientId]);
        
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $conn->rollBack();
        echo json_encode(['error' => "Unable to insert record. " . $e->getMessage()]);
    }
} else {
    header("Location: cases.php");
    exit();
}
?>
