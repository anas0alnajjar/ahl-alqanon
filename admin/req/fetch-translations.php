<?php
if (!(session_status() == PHP_SESSION_ACTIVE)) {
    session_start();
}
if (isset($_SESSION['admin_id'], $_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../DB_connection.php";
    include __DIR__ . '/../language_keys.php';
    checkKeys($conn, $language_keys);

    if (isset($_GET['language_id'])) {
        $language_id = $_GET['language_id'];

        $stmt = $conn->prepare("SELECT 
                tr.id AS language_translation_key_id,
                tr.translated_text,
                tr.translation_key_id,
                tk.name AS translation_key_name
            FROM 
                translations tr
            JOIN 
                translation_keys tk 
            ON 
                tr.translation_key_id = tk.id
            WHERE 
            tr.language_id  = :language_id   
                ");
        $stmt->bindParam(':language_id', $language_id, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $language_id1 = $language_id;
        //var_dump($results);
    } else {
        return false;
    }


    //echo json_encode($results);
} else {
    header("Location: ../../logout.php");
    exit;
}

function checkKeys($conn, $keys)
{

    foreach ($keys as $key) {
        $sql = "SELECT count(*) FROM translation_keys where name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        if ($result == 0) {
            $insertSql = "INSERT INTO translation_keys (name) VALUES (?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt->execute([$key])) {
                $sql = "SELECT LAST_INSERT_ID()";
                $stmt = $conn->query($sql);
                $keyId = $stmt->fetchColumn();
                $getLanguagesSql = "SELECT id FROM languages";
                $languagesStmt = $conn->prepare($getLanguagesSql);
                $languagesStmt->execute();
                $languages = $languagesStmt->fetchAll(PDO::FETCH_ASSOC);
                $insertTranslationSql = "INSERT INTO translations (translation_key_id, language_id, translated_text) VALUES (?, ?, ?)";
                $insertTranslationStmt = $conn->prepare($insertTranslationSql);
                foreach ($languages as $language) {
                    $languageId = $language['id'];
                    $insertTranslationStmt->execute([$keyId, $languageId, '']);
                }
            }
        }
    }
}
function insert_translations($language_id, $conn)
{
    $sql = 'SELECT id FROM translation_keys';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    var_dump($ids);
    $sql1 = 'INSERT INTO translations (language_id,translation_key_id,translated_text) VALUES  (?,?,?)';
    $stmt = $conn->prepare($sql1);
    foreach ($ids as $id) {
        $stmt->execute([$language_id, $id, '']);
    }
}
