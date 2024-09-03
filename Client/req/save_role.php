<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {

        require '../../DB_connection.php'; // الاتصال بقاعدة البيانات
        include '../permissions_script.php';
        if ($pages['roles']['add'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role_name = $_POST['role_name'];
            $office_id = $_POST['office_id'];
            $user_id = $_POST['lawyer_id'];
            $permissions = $_POST['permissions'];
        
            try {
                // ابدأ معاملة
                $conn->beginTransaction();
        
                // إدراج الدور الجديد
                $stmt = $conn->prepare("INSERT INTO powers (`role`, office_id, lawyer_id) VALUES (?, ?, ?)");
                $stmt->execute([$role_name, $office_id, $user_id]);
                $role_id = $conn->lastInsertId();
        
                // إدراج صلاحيات الصفحات
                $page_permissions_stmt = $conn->prepare("INSERT INTO page_permissions (role_id, page_name, can_read, can_write, can_add, can_delete) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($permissions as $page => $perms) {
                    $can_read = isset($perms['read']) ? 1 : 0;
                    $can_write = isset($perms['write']) ? 1 : 0;
                    $can_add = isset($perms['add']) ? 1 : 0;
                    $can_delete = isset($perms['delete']) ? 1 : 0;
                    $page_permissions_stmt->execute([$role_id, $page, $can_read, $can_write, $can_add, $can_delete]);
                }
        
                // إنهاء المعاملة
                $conn->commit();
        
                echo json_encode(['status' => 'success', 'message' => 'تمت إضافة الدور بنجاح']);
            } catch (Exception $e) {
                // التراجع عن المعاملة في حالة حدوث خطأ
                $conn->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'خطأ: ' . $e->getMessage()]);
            }
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة']);
    } 
} else {
    echo json_encode(['status' => 'error', 'message' => 'الرجاء تسجيل الدخول أولاً']);
}
?>
