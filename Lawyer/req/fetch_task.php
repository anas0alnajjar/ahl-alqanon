<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
    include '../../DB_connection.php';
    include '../permissions_script.php';
        if ($pages['notifications']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $stmt = $conn->prepare("SELECT * FROM todos WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        echo json_encode($task);
    } else {
        echo json_encode(['error' => 'Task not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
<?php
    } else {
        header("Location: ../../login.php");
        exit;
    } 
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>