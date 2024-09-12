<?php
session_start();

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        if ((isset($_POST['language_name'])) && isset($_POST['language_code']) && isset($_POST['is_default'])) {
            include '../../DB_connection.php';
            include '../language_keys.php';
            $language_name = $_POST['language_name'];
            $language_code = $_POST['language_code'];
            $is_default = $_POST['is_default'];
            $data = 'language_name=' . $language_name . '&language_code=' . $language_code .'&is_default='.$is_default;

            if (empty($language_name) || empty($language_code)) {
                $em = "جميع الحقول مطلوبة رجاءً قم بملئها";
                header("Location: ../add_language.php?error=$em&$data");
                exit;
            }
            if($is_default == 1){
                $sql = 'UPDATE languages SET is_default = ?';
                $stmt = $conn->prepare($sql);
                $stmt->execute([0]);
            }

            $sql  = "INSERT INTO languages( name, code,is_default) VALUES(?,?,?)";
            $stmt = $conn->prepare($sql);

            // تنفيذ الاستعلام
            $stmt->execute([$language_name, $language_code, $is_default]);
            $sql = "SELECT LAST_INSERT_ID()";
            $stmt = $conn->query($sql);
            $language_id = $stmt->fetchColumn();
            
            echo $language_id;
            
            
            if (check_Keys($conn)) {
                insert_keys($language_keys, $conn);
            }
            insert_translations($language_id, $conn);


            $sm = "تم حفظ اللغة بنجاح";
            header("Location: ../add_language.php?success=$sm");
            exit;
        } else {
            $em = "An error occurred";
            header("Location: ../add_language.php?error=$em");
            exit;
        }
    } else {
        header("Location: ../../logout.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
function check_Keys($conn)
{
    $sql = 'SELECT count(*) as total from translation_keys';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    //return $total;
    if ($total['total'] == 0) {
        return true;
    } else {
        return false;
    }

    //var_dump($results['total']);
    //var_dump($results);


}
function insert_keys($language_keys, $conn)
{
    // var_dump($language_keys);

    $sql = 'INSERT INTO translation_keys (name) VALUES (?)';
    $stmt = $conn->prepare($sql);
    $start_id = 1;
    foreach ($language_keys as  $language_key) {
        
        $stmt->execute([$language_key]);
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
