<?php 
session_start();
function deleteRow($id) {
    global $conn;
    $sql = "DELETE FROM documents WHERE document_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    
}
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
include '../../DB_connection.php';
include '../permissions_script.php';

        if ($pages['documents']['delete'] == 0) {
            header("Location: ../home.php");
            exit();
        }
        
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['document_id'])) {
    // استدعاء الدالة المسؤولة عن حذف البيانات
    deleteRow($_POST['document_id']);

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