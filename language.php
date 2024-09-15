<?php
include 'DB_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['lang_id'])) {
    $role = $_SESSION['role'] ?? null;
    $userId = $_SESSION['user_id'] ?? ($_SESSION['admin_id'] ?? null);

    if ($role && $userId) {
        switch ($role) {
            case 'Admin':
            case 'Admins': // توحيد الحالتين Admin و Admins
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
                die('دور غير معروف: ' . htmlspecialchars($role)); // تحسين الرسالة الافتراضية للطباعة
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($userData['language_id'])) {
            $language_id = getDefaultLanguageId($conn);
            if ($language_id !== null) {
                $_SESSION['lang_id'] = $language_id;
                $currentTexts = getLanguageIndex($conn, $language_id);
                updatelanguage_id($conn, $role, $userId, $language_id);
            } else {
                $currentTexts = [];
            }
        } else {
            $language_id = $userData['language_id'];
            $_SESSION['lang_id'] = $language_id;
            $currentTexts = getLanguageIndex($conn, $language_id);
        }
    }
} else {
    $language_id = $_SESSION['lang_id'];
    $currentTexts = getLanguageIndex($conn, $language_id);
}

function getDefaultLanguageId($conn) {
    $sql = 'SELECT id FROM languages WHERE is_default = 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $language_id = $stmt->fetch(PDO::FETCH_ASSOC);
    return $language_id ? $language_id['id'] : null;
}

function getLanguageIndex($conn, $language_id) {
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

function updatelanguage_id($conn, $role, $user_id, $language_id) {
    switch ($role) {
        case 'Admin':
        case 'Admins': // توحيد الحالتين Admin و Admins
            $sql = "UPDATE admin SET language_id = :language_id WHERE admin_id = :id";
            break;
        case 'Client':
            $sql = "UPDATE clients SET language_id = :language_id WHERE client_id = :id";
            break;
        case 'Helper':
            $sql = "UPDATE helpers SET language_id = :language_id WHERE id = :id";
            break;
        case 'Lawyer':
            $sql = "UPDATE lawyer SET language_id = :language_id WHERE lawyer_id = :id";
            break;
        case 'Managers':
            $sql = "UPDATE managers_office SET language_id = :language_id WHERE id = :id";
            break;
        default:
            die('دور غير معروف: ' . htmlspecialchars($role));
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':language_id', $language_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    return $stmt->execute();
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
