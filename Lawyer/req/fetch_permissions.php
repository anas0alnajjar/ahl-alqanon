<?php
// افتح اتصالاً بقاعدة البيانات
// require '../../DB_connection.php';

// // استعلام SQL لاسترداد البيانات بناءً على role_id
// $sql = "SELECT p.role, p.office_id, pp.page_name, 
// pp.can_read AS read_permission, 
// pp.can_write AS write_permission, 
// pp.can_add AS add_permission, 
// pp.can_delete AS delete_permission
// FROM powers p
// INNER JOIN page_permissions pp ON p.power_id = pp.role_id
// WHERE p.power_id = :role_id;";

// $stmt = $conn->prepare($sql);
// $stmt->bindParam(':role_id', $_POST['role_id']);
// $stmt->execute();
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // تنظيم البيانات في صيغة مناسبة للجافا سكريبت
// $permissions = [];
// foreach ($result as $row) {
//     $permissions[$row['page_name']] = [
//         'read_permission' => $row['read_permission'],
//         'write_permission' => $row['write_permission'],
//         'add_permission' => $row['add_permission'],
//         'delete_permission' => $row['delete_permission'],
//     ];
// }
// $data = [
//     'role' => $result[0]['role'], 
//     'office_id' => $result[0]['office_id'],
//     'permissions' => $permissions,
// ];

// // إعادة البيانات بتنسيق JSON
// echo json_encode($data);

// إغلاق اتصال قاعدة البيانات
?>



