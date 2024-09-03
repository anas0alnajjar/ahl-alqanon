<?php
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {

        require '../../DB_connection.php'; // الاتصال بقاعدة البيانات

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role_name = $_POST['role_name'];
            $office_id = $_POST['office_id'];
            $default_role = isset($_POST['default_role']) ? 1 : 0;
            $default_role_client = isset($_POST['default_role_client']) ? 1 : 0;
            $default_role_lawyer = isset($_POST['default_role_lawyer']) ? 1 : 0;
            $default_role_manager = isset($_POST['default_role_manager']) ? 1 : 0;
            $default_role_helper = isset($_POST['default_role_helper']) ? 1 : 0;
            $permissions = $_POST['permissions'];
            $role_id = $_POST['role_id'];
            $lawyer_ids = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : [];

            try {
                // التحقق من وجود دور افتراضي آخر قبل بدء المعاملة
                if ($default_role == 1) {
                    $check_sql = "SELECT role FROM powers WHERE default_role = 1 AND power_id != ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->execute([$role_id]);
                    $existing_default_role = $check_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existing_default_role) {
                        echo json_encode(['status' => 'error', 'message' => 'يوجد بالفعل دور افتراضي باسم: ' . $existing_default_role['role']]);
                        exit;
                    }
                }

                // ابدأ معاملة
                $conn->beginTransaction();

                // تحويل مصفوفة lawyer_id إلى سلسلة نصية مفصولة بفواصل
                $lawyer_ids_string = implode(',', $lawyer_ids);
        
                // تحديث الدور
                $update_role_sql = "UPDATE powers SET role = ?, office_id = ?, default_role = ?, lawyer_id = ?, default_role_helper = ?, default_role_client = ?, default_role_lawyer = ?, default_role_manager = ? WHERE power_id = ?";
                $stmt = $conn->prepare($update_role_sql);
                $stmt->execute([$role_name, $office_id, $default_role, $lawyer_ids_string, $default_role_helper, $default_role_client, $default_role_lawyer, $default_role_manager,  $role_id]);

                // حذف الصلاحيات الحالية
                $delete_permissions_sql = "DELETE FROM page_permissions WHERE role_id = ?";
                $stmt = $conn->prepare($delete_permissions_sql);
                $stmt->execute([$role_id]);
        
                // إدراج صلاحيات الصفحات الجديدة
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
        
                echo json_encode(['status' => 'success', 'message' => 'تم تحديث الدور بنجاح']);
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
