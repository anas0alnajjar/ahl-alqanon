<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        
        if (isset($_POST['fname']) && isset($_POST['lname'])) {
            
            include '../../DB_connection.php';
            include '../permissions_script.php';

            if ($pages['adversaries']['add'] == 0) {
                $response['message'] = "ليس لديك إذن لإضافة خصم.";
                echo json_encode($response);
                exit();
            }

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email_address'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $office_id = $_POST['office_id'];
            $lawyer_id = $_POST['lawyer_id'];

            if (empty($fname)) {
                $response['message'] = 'الاسم الأول مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($lname)) {
                $response['message'] = 'الاسم الأخير مطلوب';
                echo json_encode($response);
                exit();
            }

            $sql = "INSERT INTO adversaries (fname, lname, `address`, email_address, date_of_birth, city, phone, gender, 
                    office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $email_address, $date_of_birth, $city, $phone, $gender, 
                            $office_id, $lawyer_id]);

            $response['success'] = true;
            $response['message'] = 'تم إضافة الخصم بنجاح!';
            echo json_encode($response);
            exit();
        } else {
            $response['message'] = 'حدث خطأ غير متوقع';
            echo json_encode($response);
            exit();
        }
    } else {
        $response['message'] = 'ليس لديك الصلاحيات اللازمة';
        echo json_encode($response);
        exit();
    }
} else {
    $response['message'] = 'الرجاء تسجيل الدخول';
    echo json_encode($response);
    exit();
}
?>
