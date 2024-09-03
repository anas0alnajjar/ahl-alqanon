<?php
$permissions_json = '';
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Lawyer') {
        $lawyer_id = $_SESSION['user_id'];

        // جلب role_id من جدول admin
        $sql_role = "SELECT role_id FROM lawyer WHERE lawyer_id = :lawyer_id";
        $stmt_role = $conn->prepare($sql_role);
        $stmt_role->bindParam(':lawyer_id', $lawyer_id, PDO::PARAM_INT);
        $stmt_role->execute();
        $role = $stmt_role->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            $role_id = $role['role_id'];

            // جلب الصلاحيات باستخدام role_id
            $sql_permissions = "SELECT
                                    page_permissions.page_name,
                                    page_permissions.can_read AS page_read,
                                    page_permissions.can_write AS page_write,
                                    page_permissions.can_add AS page_add,
                                    page_permissions.can_delete AS page_delete
                                FROM
                                    page_permissions
                                WHERE page_permissions.role_id = :role_id";
            
            $stmt_permissions = $conn->prepare($sql_permissions);
            $stmt_permissions->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt_permissions->execute();
            $permissions = $stmt_permissions->fetchAll(PDO::FETCH_ASSOC);

            // تحويل الصلاحيات إلى مصفوفة
            $pages = [
                'control' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'cases' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'add_old_session' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'expenses' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'payments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'attachments' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'lawyers' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'clients' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'assistants' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'expense_types' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'offices' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'courts' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'case_types' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'judicial_circuits' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'documents' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'notifications' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'message_customization' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'inbox' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'outbox' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'join_requests' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'roles' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'logo_contact' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'user_management' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'profiles' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'managers' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'adversaries' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'import' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'expenses_sessions' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'calendar' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
                'events' => ['read' => 0, 'write' => 0, 'add' => 0, 'delete' => 0],
            ];

            foreach ($permissions as $permission) {
                $page_name = $permission['page_name'];
                $pages[$page_name]['read'] = $permission['page_read'];
                $pages[$page_name]['write'] = $permission['page_write'];
                $pages[$page_name]['add'] = $permission['page_add'];
                $pages[$page_name]['delete'] = $permission['page_delete'];
            }

            $permissions_json = json_encode($pages);
        }
    }
}
?>
