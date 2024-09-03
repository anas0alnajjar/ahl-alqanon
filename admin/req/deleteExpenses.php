<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        
function deleteRow($id) {
    global $conn;
    $sql = "DELETE FROM `expenses` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    // يمكنك إرجاع أي رسالة أو إشارة نجاح لاحقاً
}
include '../../DB_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['expenses_id'])) {
    // استدعاء الدالة المسؤولة عن حذف البيانات
    deleteRow($_POST['expenses_id']);
}



?>
<?php
} else {
    header("Location: ../../login.php");
    exit;
}
} else {
header("Location: ../../login.php");
exit;
} 
?>