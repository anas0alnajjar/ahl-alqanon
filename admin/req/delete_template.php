<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    require '../../DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $data = json_decode(file_get_contents('php://input'), true);
    $id = $_POST['id'];

    if ($id) {
        $stmt = $conn->prepare('DELETE FROM templates WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الحذف.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'معرف غير صالح.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح.']);
}
} else {
    header("Location: ../login.php");
    exit;
}
?>
