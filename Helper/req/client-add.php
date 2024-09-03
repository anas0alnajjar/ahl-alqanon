<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['address']) && isset($_POST['email_address']) && isset($_POST['gender']) && isset($_POST['date_of_birth']) && isset($_POST['city'])) {
            
            include '../../DB_connection.php';
            include "../data/usernamelIsUnique.php";
            include '../permissions_script.php';
           
            if ($pages['clients']['add'] == 0) {
                $response['message'] = "ليس لديك إذن لإضافة عميل.";
                echo json_encode($response);
                exit();
            }

            $fname = $_POST['fname'];
            $lawyer_id = $_POST['lawyer_id'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email_address'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $uname = isset($_POST['username']) && !empty($_POST['username']) ? $_POST['username'] : '';
            $pass = isset($_POST['pass']) && !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : '';

            $father_name = $_POST['father_name'];
            $grandfather_name = $_POST['grandfather_name'];
            $national_num = $_POST['national_num'];
            $alhi = $_POST['alhi'];
            $street_name = $_POST['street_name'];
            $num_build = $_POST['num_build'];
            $num_unit = $_POST['num_unit'];
            $zip_code = $_POST['zip_code'];
            $subnumber = $_POST['subnumber'];
            $office_id = $_POST['office_id'];
            $client_passport = $_POST['client_passport'];

            $sql_default_role = "SELECT power_id FROM powers WHERE default_role_client = 1 LIMIT 1";
            $stmt_default_role = $conn->prepare($sql_default_role);
            $stmt_default_role->execute();
            $role_id = $stmt_default_role->fetchColumn();

            $receive_whatsupp = isset($_POST['receive_whatsupp']) ? 1 : 0;
            $receive_emails = isset($_POST['receive_emails']) ? 1 : 0;

            if (empty($fname)) {
                $response['message'] = 'الاسم الأول مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($lname)) {
                $response['message'] = 'الاسم الأخير مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($address)) {
                $response['message'] = 'العنوان مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($gender)) {
                $response['message'] = 'الجنس مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($email_address)) {
                $response['message'] = 'البريد الإلكتروني مطلوب';
                echo json_encode($response);
                exit();
            } else if (empty($phone)) {
                $response['message'] = 'رقم الهاتف مطلوب';
                echo json_encode($response);
                exit();
            }

            if (!empty($uname)) {
                if (!usernamelIsUnique($uname, $conn)) {
                    $response['message'] = 'اسم المستخدم مأخوذ اختر واحد آخر';
                    if (empty($pass)) {
                        $response['message'] .= ' وكلمة السر مطلوبة';
                    }
                    echo json_encode($response);
                    exit();
                }

                if (empty($pass)) {
                    $response['message'] = 'كلمة السر مطلوبة';
                    echo json_encode($response);
                    exit();
                }
            }

            $sql = "INSERT INTO clients (first_name, last_name, `address`, gender, city, email, date_of_birth, phone, 
                    father_name, grandfather_name, national_num, alhi, street_name, num_build, num_unit, zip_code, 
                    subnumber, username, `password`, receive_whatsupp, receive_emails, client_passport, role_id, office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $gender, $city, $email_address, $date_of_birth, $phone, 
                            $father_name, $grandfather_name, $national_num, $alhi, $street_name, $num_build, 
                            $num_unit, $zip_code, $subnumber, !empty($uname) ? $uname : '', !empty($pass) ? $pass : '', $receive_whatsupp, $receive_emails, $client_passport, $role_id, $office_id, $lawyer_id]);

            $response['success'] = true;
            $response['message'] = 'تم إضافة الموكل بنجاح!';
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
