<?php
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include '../../DB_connection.php';

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $profile_id = $_GET['id'];
        
            try {
                // ابدأ المعاملة
                $conn->beginTransaction();
        
                // جلب مسارات الصور المرتبطة بالبروفايل
                $stmt = $conn->prepare("SELECT logo, qr FROM profiles WHERE id = :profile_id");
                $stmt->bindParam(':profile_id', $profile_id);
                $stmt->execute();
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($profile) {
                    // حذف الصور إذا كانت موجودة
                    if (!empty($profile['logo']) && file_exists('../../profiles_photos/' . $profile['logo'])) {
                        unlink('../../profiles_photos/' . $profile['logo']);
                    }
                    if (!empty($profile['qr']) && file_exists('../../profiles_photos/' . $profile['qr'])) {
                        unlink('../../profiles_photos/' . $profile['qr']);
                    }
        
                    // جلب مسارات صور الهيدر المرتبطة بالبروفايل
                    $stmt = $conn->prepare("SELECT header FROM headers WHERE profile_id = :profile_id");
                    $stmt->bindParam(':profile_id', $profile_id);
                    $stmt->execute();
                    $headers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                    // حذف صور الهيدر إذا كانت موجودة
                    foreach ($headers as $header) {
                        if (!empty($header['header']) && file_exists('../../profiles_photos/' . $header['header'])) {
                            unlink('../../profiles_photos/' . $header['header']);
                        }
                    }
        
                    // حذف سجلات الهيدر المرتبطة بالبروفايل
                    $stmt = $conn->prepare("DELETE FROM headers WHERE profile_id = :profile_id");
                    $stmt->bindParam(':profile_id', $profile_id);
                    $stmt->execute();
        
                    // حذف سجل البروفايل
                    $stmt = $conn->prepare("DELETE FROM profiles WHERE id = :profile_id");
                    $stmt->bindParam(':profile_id', $profile_id);
                    $stmt->execute();
        
                    // اتمام المعاملة
                    $conn->commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Profile deleted successfully'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Profile not found'));
                }
            } catch (Exception $e) {
                // استرجاع المعاملة في حالة وجود خطأ
                $conn->rollBack();
                echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid request method or missing ID'));
        }
?>
<?php 

}else {
  header("Location: ../login.php");
  exit;
} 
}else {
  header("Location: ../login.php");
  exit;
} 

?>