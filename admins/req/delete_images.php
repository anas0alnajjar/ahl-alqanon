<?php
// delete.php

// تأكد من استخدام التصريحات المناسبة
header('Content-Type: application/json');

// تحقق من أن الطلب هو POST وأنه يحتوي على البيانات المطلوبة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قراءة بيانات الطلب
    $input = json_decode(file_get_contents('php://input'), true);

    // تحقق من وجود الرابط في البيانات
    if (isset($input['url'])) {
        $imageUrl = $input['url'];

        // استخراج المسار النسبي للملف من الرابط
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);

        // تحويل المسار إلى صيغة قابلة للاستخدام في النظام
        $imagePath = urldecode($imagePath);

        // المسار الكامل للملف
        $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

        // تحقق من أن الملف موجود على الخادم
        if (file_exists($fullImagePath)) {
            // محاولة حذف الملف
            if (unlink($fullImagePath)) {
                // إعادة استجابة ناجحة
                echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully.']);
            } else {
                // فشل في حذف الملف
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete image.']);
            }
        } else {
            // الملف غير موجود
            echo json_encode(['status' => 'error', 'message' => 'Image not found.']);
        }
    } else {
        // الرابط غير موجود في الطلب
        echo json_encode(['status' => 'error', 'message' => 'No image URL provided.']);
    }
} else {
    // إذا لم يكن الطلب POST
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
