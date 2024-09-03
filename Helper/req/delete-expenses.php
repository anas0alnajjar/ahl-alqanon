<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {
        include "../../DB_connection.php";
        include '../permissions_script.php';

        if ($pages['expenses']['delete'] == 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $source = $_POST['source'];
            $success = false;

            switch ($source) {
                case 'sessions':
                    $sql = "DELETE FROM `expenses` WHERE id = :id";
                    break;
                case 'general':
                    $sql = "DELETE FROM overhead_costs WHERE id = :id";
                    break;
                default:
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Invalid source']);
                    exit;
            }

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            try {
                if ($stmt->execute()) {
                    $success = true;
                }
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
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
