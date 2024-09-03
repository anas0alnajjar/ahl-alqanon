<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {

    require '../../DB_connection.php';

    include '../permissions_script.php';
    if ($pages['assistants']['add'] == 0) {
        header("Location: ../home.php");
        exit();
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $helper_name = $_POST['helper_name'];
    $username = $_POST['username'];
    // $lawyer_id = $_POST['lawyer_id555'];
    $lawyer_id = $_SESSION['user_id'];
    $phone = $_POST['phone'];
    $pass = $_POST['pass'];
    
    $role_id = $_POST['role_id'];
    $passport_helper = $_POST['passport_helper'];
    $national_helper = $_POST['national_helper'];
    $office_id = $_POST['office_id'];
    
    $pass = password_hash($pass, PASSWORD_DEFAULT);
    

    

    try {
        // التحقق من وجود اسم المستخدم في الجداول الثلاثة
        $checkSql = "SELECT username FROM `admin` WHERE username = ?
                     UNION
                     SELECT username FROM lawyer WHERE username = ?
                     UNION
                     SELECT username FROM helpers WHERE username = ?
                     UNION
                     SELECT username FROM clients WHERE username = ?
                     UNION
                     SELECT username FROM managers_office WHERE username = ?
                     UNION
                     SELECT username FROM ask_join WHERE username = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$username, $username, $username, $username, $username, $username]);
        
        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'اسم المستخدم موجود، اختر واحدًا آخر']);
        } else {
            $conn->beginTransaction();

            $sql = "INSERT INTO helpers (helper_name, username, pass, lawyer_id, phone, role_id, national_helper, passport_helper, office_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $helper_name, $username, $pass, $lawyer_id, $phone, $role_id, $national_helper, $passport_helper, $office_id
            ]);

            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'تمت إضافة المساعد بنجاح']);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'فشل في إضافة المساعد: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح']);
}

} else {
    header("Location: ../../logout.php");
    exit;
} 
} else {
header("Location: ../../logout.php");
exit;
} ?>
