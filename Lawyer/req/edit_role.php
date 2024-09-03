<?php
session_start();

// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../errors.txt');

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {

        require '../../DB_connection.php'; // الاتصال بقاعدة البيانات
        include '../permissions_script.php';
        if ($pages['roles']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['role_name'], $_POST['office_id'], $_POST['permissions'], $_POST['role_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'بيانات غير كاملة']);
                exit();
            }

            $role_name = $_POST['role_name'];
            $office_id = $_POST['office_id'];
            $permissions = $_POST['permissions'];
            $role_id = $_POST['role_id'];
            
            // error_log("-----------------Starting Role Update-----------------");
            // error_log("Received Data: role_name=$role_name, office_id=$office_id, role_id=$role_id, permissions=" . print_r($permissions, true));
        
            try {
                $conn->beginTransaction();
        
                $update_role_sql = "UPDATE powers SET `role` = ?, office_id = ? WHERE power_id = ?";
                $stmt = $conn->prepare($update_role_sql);
                $stmt->execute([$role_name, $office_id, $role_id]);
                
                if ($stmt->rowCount() > 0) {
                    // error_log("Role updated successfully.");
                } else {
                    // error_log("Role update failed.");
                }

                $delete_permissions_sql = "DELETE FROM page_permissions WHERE role_id = ? AND page_name IN (" . implode(',', array_fill(0, count($permissions), '?')) . ")";
                $stmt = $conn->prepare($delete_permissions_sql);
                $stmt->execute(array_merge([$role_id], array_keys($permissions)));
                
                if ($stmt->rowCount() > 0) {
                    // error_log("Permissions deleted successfully.");
                } else {
                    // error_log("Permissions deletion failed or no permissions to delete.");
                }
        
                $page_permissions_stmt = $conn->prepare("INSERT INTO page_permissions (id, role_id, page_name, can_read, can_write, can_add, can_delete) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE can_read=VALUES(can_read), can_write=VALUES(can_write), can_add=VALUES(can_add), can_delete=VALUES(can_delete)");
                foreach ($permissions as $page => $perms) {
                    $can_read = isset($perms['read']) ? 1 : 0;
                    $can_write = isset($perms['write']) ? 1 : 0;
                    $can_add = isset($perms['add']) ? 1 : 0;
                    $can_delete = isset($perms['delete']) ? 1 : 0;
                    $permission_id = $perms['permission_id'] ?? null;
                    $page_permissions_stmt->execute([$permission_id, $role_id, $page, $can_read, $can_write, $can_add, $can_delete]);
                    
                    if ($page_permissions_stmt->rowCount() > 0) {
                        // error_log("Permissions for $page inserted successfully.");
                    } else {
                        // error_log("Permissions insertion for $page failed.");
                    }
                }
        
                $conn->commit();
        
                echo json_encode(['status' => 'success', 'message' => 'تمت تحديث الدور بنجاح']);
            } catch (Exception $e) {
                $conn->rollBack();
                // error_log("Transaction failed: " . $e->getMessage());
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
