<?php
session_start();
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        if (isset($_POST['fname']) && isset($_POST['lname'])) {
            
            include '../../DB_connection.php';

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email_address'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : null;
            $office_id = $_POST['office_id'];

            if (empty($fname)) {
                $response['message'] = 'الاسم الأول مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($lname)) {
                $response['message'] = 'الاسم الأخير مطلوب';
                echo json_encode($response);
                exit;
            } else if (empty($gender)) {
                $response['message'] = 'الجنس مطلوب';
                echo json_encode($response);
                exit;
            }

            $sql = "INSERT INTO adversaries (fname, lname, `address`, email_address, date_of_birth, city, phone, gender, 
                    office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $email_address, $date_of_birth, $city, $phone, $gender, $office_id, $lawyer_id]);

            $response['success'] = true;
            $response['message'] = 'تم إضافة الخصم بنجاح!';
            echo json_encode($response);
            exit;
        } else {
            $response['message'] = 'An error occurred';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Unauthorized';
        echo json_encode($response);
        exit;
    }
} else {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}
?>
