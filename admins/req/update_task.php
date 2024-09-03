<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {
include '../../DB_connection.php';
include '../permissions_script.php';
if ($pages['notifications']['write'] == 0) {
    header("Location: ../home.php");
    exit();
}

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$response = array('error' => '', 'success' => '');

// التحقق من الطلب
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];
    $lawyer_id = $_POST['lawyer_id'];
    $client_id = isset($_POST['client_id']) ? $_POST['client_id'] : null;
    $helper_id = isset($_POST['helper_id']) ? $_POST['helper_id'] : null;
    $priority = isset($_POST['priority']) ? $_POST['priority'] : null;
    $task_title = isset($_POST['task_title']) ? $_POST['task_title'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $task_attach = '';

    // التعامل مع المرفقات
    if (isset($_FILES['task_attach']) && $_FILES['task_attach']['error'] == UPLOAD_ERR_OK) {
        // جلب الملف القديم
        $stmt = $conn->prepare("SELECT task_attach FROM todos WHERE id = ?");
        $stmt->execute([$task_id]);
        $old_task = $stmt->fetch(PDO::FETCH_ASSOC);
        $old_file = $old_task['task_attach'];

        // حذف الملف القديم إذا كان موجودًا
        if ($old_file && file_exists('../../uploads/' . $old_file)) {
            unlink('../../uploads/' . $old_file);
        }

        // رفع الملف الجديد
        $uploadDir = '../../uploads/';
        $fileName = time() . '_' . basename($_FILES['task_attach']['name']); // إضافة توقيع زمني
        $uploadFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['task_attach']['tmp_name'], $uploadFilePath)) {
            $task_attach = $fileName;
        } else {
            $response['error'] = 'Failed to upload new file';
        }
    } else {
        // إذا لم يتم رفع ملف جديد، نحتفظ بالملف القديم
        $stmt = $conn->prepare("SELECT task_attach FROM todos WHERE id = ?");
        $stmt->execute([$task_id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        $task_attach = $task['task_attach'];
    }

    if (empty($response['error'])) {
        $stmt = $conn->prepare("UPDATE todos SET lawyer_id = ?, client_id = ?, helper_id = ?, priority = ?, task_title = ?, title = ?, task_attach = ? WHERE id = ?");
        $result = $stmt->execute([$lawyer_id, $client_id, $helper_id, $priority, $task_title, $title, $task_attach, $task_id]);

        if ($result) {
            $response['success'] = 'Task updated successfully';
        } else {
            $response['error'] = 'Failed to update task';
        }
    }
} else {
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
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