<?php
header('Content-Type: application/json');

// تحديد مسار مجلد التحميل بالنسبة للجذر
$targetDir = "../../images/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'].'/';

// التحقق من نوع الطلب إذا كان يحتوي على ملف محمل أو بيانات base64
if (isset($_FILES["upload"])) {
    // معالجة الملف المحمل
    $targetFile = $targetDir . basename($_FILES["upload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // التحقق من أن الملف هو صورة
    $check = getimagesize($_FILES["upload"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['error' => 'الملف ليس صورة.']);
        $uploadOk = 0;
    }

    // التحقق من حجم الملف (تحديد حجم أقصى 5MB)
    if ($_FILES["upload"]["size"] > 5000000) {
        echo json_encode(['error' => 'حجم الملف كبير جداً.']);
        $uploadOk = 0;
    }

    // السماح بأنواع معينة من الملفات
    $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedFileTypes)) {
        echo json_encode(['error' => 'فقط ملفات JPG, JPEG, PNG و GIF مسموح بها.']);
        $uploadOk = 0;
    }

    // التحقق من وجود أخطاء ومن ثم محاولة رفع الملف
    if ($uploadOk == 0) {
        echo json_encode(['error' => 'فشل رفع الملف.']);
    } else {
        if (move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFile)) {
            // تعديل الرابط ليتضمن المسار الكامل
            $fullUrl = $protocol . $domainName  . $targetFile;
            echo json_encode(['url' => $fullUrl]);
        } else {
            echo json_encode(['error' => 'حدث خطأ أثناء رفع الملف.']);
        }
    }
} else {
    // معالجة البيانات بصيغة base64
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['image'])) {
        $imageData = $data['image'];
        
        // استخراج نوع الصورة من بيانات base64
        if (preg_match("/data:image\/(.*?);base64,/", $imageData, $imageType)) {
            $imageType = $imageType[1]; // نوع الصورة (مثال: png أو jpeg)

            // إزالة بادئة base64
            $imageData = preg_replace("/data:image\/(.*?);base64,/", "", $imageData);
            $imageData = base64_decode($imageData);

            // توليد اسم فريد للملف
            $fileName = uniqid() . '.' . $imageType;
            $filePath = $targetDir . $fileName;

            // حفظ الصورة في المجلد
            if (file_put_contents($filePath, $imageData)) {
                $fullUrl = $protocol . $domainName  . $filePath;
                echo json_encode(['url' => $fullUrl]);
            } else {
                echo json_encode(['error' => 'Failed to save image.']);
            }
        } else {
            echo json_encode(['error' => 'Invalid base64 data.']);
        }
    } else {
        echo json_encode(['error' => 'No image data found.']);
    }
}
?>
