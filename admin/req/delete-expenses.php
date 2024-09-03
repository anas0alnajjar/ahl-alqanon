<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $source = $_POST['source'];
            $success = false;

            switch ($source) {
                case 'sessions':
                    $sql = "DELETE FROM `expenses` WHERE id = :id";
                    break;
                case 'genral':
                    $sql = "DELETE FROM overhead_costs WHERE id = :id";
                    break;
                default:
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false]);
                    exit;
            }

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $success = true;
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
        }
    } else {
        header("Location: ../genral_expenses.php");
        exit;
    }
} else {
    header("Location: ../genral_expenses.php");
    exit;
}
?>
