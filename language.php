

<?php
include 'DB_connection.php';
/*echo 'jnjkdslfn';
exit;*/
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['lang_id'])) {
    $role = $_SESSION['role'] ?? null;
    $userId = $_SESSION['user_id'] ?? ($_SESSION['admin_id'] ?? null);
    if ($role && $userId) {
        switch ($role) {
            case 'Admin':
                $sql = "SELECT language_id FROM admin WHERE admin_id = ?";
                break;
                case 'Admins':
                    $sql = "SELECT language_id FROM admin WHERE admin_id = ?";
                    break;
            case 'Client':
                $sql = "SELECT language_id FROM clients WHERE client_id = ?";
                break;
            case 'Helper':
                $sql = "SELECT language_id FROM helpers WHERE id = ?";
                break;
            case 'Lawyer':
                $sql = "SELECT language_id FROM lawyer WHERE lawyer_id = ?";
                break;
            case 'Managers':
                $sql = "SELECT language_id FROM managers_office WHERE id = ?";
                break;
            default:
                die('دور غير معروف.');
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($userData['language_id'])) {
            $language_id = getDefaultLanguageId($conn);
            if(!($language_id == null)){

            
            $_SESSION['lang_id'] = $language_id;
            $currentTexts = getLanguageIndex($conn, $language_id);
            updatelanguage_id($conn, $role, $userId, $language_id); 
                
            }
            else
            {
                $currentTexts = [];

            }
        } 
        else 
        {
            $language_id = $userData['language_id'];
            $_SESSION['lang_id'] = $language_id;
            $currentTexts = getLanguageIndex($conn, $language_id);
        }
    }
}
else 
{
    $language_id = $_SESSION['lang_id']; 
    $currentTexts = getlanguageIndex($conn, $language_id);
    

}
//}
function getDefaultLanguageId($conn)
{
    $sql = 'SELECT id FROM languages WHERE is_default = 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $language_id = $stmt->fetch(PDO::FETCH_ASSOC);
    if($language_id)
    return $language_id['id'];
else{
    return null;
}
}
function getlanguageIndex($conn, $language_id)
{
    $sql = 'SELECT 
        tk.name AS translation_key_name,
        tr.translated_text
    FROM 
        translations tr
    JOIN 
        translation_keys tk 
    ON 
        tr.translation_key_id = tk.id
    JOIN 
        languages lang
    ON 
        tr.language_id = lang.id
    WHERE 
        lang.id = :language_id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':language_id', $language_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}
function updatelanguage_id($conn, $role, $user_id, $language_id)
{
    switch ($role) {
        case 'Admin':
            $sql = "UPDATE admin SET language_id = :language_id WHERE admin_id = :id";
            break;
        case 'Client':
            $sql = "UPDATE clients SET language_id = :language_id WHERE client_id = :id";
            break;
        case 'Helper':
            $sql = "UPDATE helper SET language_id = :language_id WHERE id = :id";
            break;
        case 'Lawyer':
            $sql = "UPDATE lawyer SET language_id = :language_id WHERE lawyer_id = :id";
            break;
        case 'Managers':
            $sql = "UPDATE managers_office SET language_id = :language_id WHERE id = :id";
            break;
        default:
            die('دور غير معروف.');
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':language_id', $language_id, PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);


    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

    function __($key) {
        global $currentTexts;
        foreach ($currentTexts as $item) {
            if ($item['translation_key_name'] === $key) {
                return $item['translated_text'] ?: $key;
            }
            
        }
        return $key;
        
    }

/*include 'DB_connection.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(isset($_SESSION['lang_id'])){
    if(empty($_SESSION['lang_id'])){
        $role = $_SESSION['role'] ?? null;
        var_dump($role);
        exit;
$userId = $_SESSION['user_id'] ?? ($_SESSION['admin_id'] ?? null); // التعامل مع كل من user_id و admin_id
//$language = 'en'; // اللغة الافتراضية
        if ($role && $userId) {
            switch ($role) {
                case 'Admin':
                    $sql = "SELECT lang FROM admin WHERE admin_id = ?";

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
            $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($userId);
    exit;
    if ($userData && isset($userData['lang'])) {
        $language = $userData['lang'];
        
    }
        }
        
        
    }


}

// إذا كانت الجلسة والجلسة تم تشغيلها في ملف آخر، يمكن تجنب إعادة التشغيل باستخدام `if (session_status() == PHP_SESSION_NONE) { session_start(); }`

// تعريف النصوص للغات المختلفة، يمكن إضافة المزيد من اللغات هنا
$texts = [
    'en' => [
        'cases' => 'Cases',
        'description' => 'This is the English version of the website.',
    ],
    'ar' => [
        'cases' => 'قضايا',
        'description' => 'هذا هو النسخة العربية من الموقع.',
    ],
    // لإضافة لغة جديدة، يمكنك إضافة مدخل هنا
    'fr' => [
        'cases' => 'Problèmes',
        'description' => 'Ceci est la version française du site.',
    ],
];

// التحقق من الجلسة وتحديد الدور واللغة
// التحقق من الجلسة وتحديد الدور واللغة
$role = $_SESSION['role'] ?? null;
$userId = $_SESSION['user_id'] ?? ($_SESSION['admin_id'] ?? null); // التعامل مع كل من user_id و admin_id
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

    // جلب اللغة من قاعدة البيانات باستخدام $conn بدلاً من $pdo
    $stmt = $conn->prepare($sql);
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
*/