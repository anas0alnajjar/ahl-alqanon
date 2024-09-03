<?php
include '../../DB_connection.php';

$response = ['success' => false];

if (isset($_POST['image'])) {
    $image = $_POST['image'];
    $imagePath = '../../profiles_photos/' . $image;

    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            $stmt = $conn->prepare("DELETE FROM headers WHERE header = :header");
            $stmt->bindParam(':header', $image);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['message'] = 'حدث خطأ أثناء حذف الملف.';
        }
    } else {
        $response['message'] = 'الملف غير موجود.';
    }
} else {
    $response['message'] = 'لم يتم تحديد الملف.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
