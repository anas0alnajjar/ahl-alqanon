<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
        
function deleteRow($id) {
    global $conn;
    $sql = "DELETE FROM `sessions` WHERE sessions_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    // يمكنك إرجاع أي رسالة أو إشارة نجاح لاحقاً
}
include '../../DB_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sessions_id'])) {
    // استدعاء الدالة المسؤولة عن حذف البيانات
    deleteRow($_POST['sessions_id']);
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