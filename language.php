<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// إذا كانت الجلسة والجلسة تم تشغيلها في ملف آخر، يمكن تجنب إعادة التشغيل باستخدام `if (session_status() == PHP_SESSION_NONE) { session_start(); }`

// تعريف النصوص للغات المختلفة، يمكن إضافة المزيد من اللغات هنا
$texts = [
    'en' => [
        'welcome' => 'Welcome to our website!',
        'description' => 'This is the English version of the website.',
    ],
    'ar' => [
        'welcome' => 'مرحبًا بك في موقعنا!',
        'description' => 'هذا هو النسخة العربية من الموقع.',
    ],
    // لإضافة لغة جديدة، يمكنك إضافة مدخل هنا
    'fr' => [
        'welcome' => 'Bienvenue sur notre site!',
        'description' => 'Ceci est la version française du site.',
    ],
];

// التحقق من الجلسة وتحديد الدور واللغة
$role = $_SESSION['role'] ?? null;
$userId = $_SESSION['user_id'] ?? null;
$language = 'en'; // اللغة الافتراضية

if ($role && $userId) {
    switch ($role) {
        case 'Admin':
        case 'Admins':
            $sql = "SELECT lang FROM admin WHERE admin_id = ?";
            break;
        case 'Client':
            $sql = "SELECT lang FROM clients WHERE client_id = ?";
            break;
        case 'Helper':
            $sql = "SELECT lang FROM helpers WHERE id = ?";
            break;
        case 'Lawyer':
            $sql = "SELECT lang FROM lawyer WHERE lawyer_id = ?";
            break;
        case 'Managers':
            $sql = "SELECT lang FROM managers_office WHERE id = ?";
            break;
        default:
            die('دور غير معروف.');
    }

    // جلب اللغة من قاعدة البيانات
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && isset($userData['lang'])) {
        $language = $userData['lang'];
    }
}

// النصوص المحددة للغة المستخدم
$currentTexts = $texts[$language] ?? $texts['en']; // استخدام الإنجليزية كافتراضية إذا كانت اللغة غير معروفة

// دالة لتسهيل جلب النصوص من الملف
function __($key) {
    global $currentTexts;
    return $currentTexts[$key] ?? $key; // إذا لم يتم العثور على النص، يتم إرجاع المفتاح كما هو
}

