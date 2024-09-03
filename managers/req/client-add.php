<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Managers') {
        
        if (isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['address']) &&
            isset($_POST['email_address']) &&
            isset($_POST['gender']) &&
            isset($_POST['date_of_birth']) &&
            isset($_POST['city'])) {
            
            include '../../DB_connection.php';
            include "../data/usernamelIsUnique.php";
            include '../permissions_script.php';
           
            if ($pages['clients']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            // Assigning posted values to variables
            $fname = isset($_POST['fname']) ? $_POST['fname'] : '';
            $lname = isset($_POST['lname']) ? $_POST['lname'] : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $email_address = isset($_POST['email_address']) ? $_POST['email_address'] : '';
            $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
            $city = isset($_POST['city']) ? $_POST['city'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $lawyer_id = isset($_POST['lawyer_id']) ? $_POST['lawyer_id'] : '';
            $uname = isset($_POST['username']) ? $_POST['username'] : '';
            $pass = isset($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : '';
            $father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
            $grandfather_name = isset($_POST['grandfather_name']) ? $_POST['grandfather_name'] : '';
            $national_num = isset($_POST['national_num']) ? $_POST['national_num'] : '';
            $alhi = isset($_POST['alhi']) ? $_POST['alhi'] : '';
            $street_name = isset($_POST['street_name']) ? $_POST['street_name'] : '';
            $num_build = isset($_POST['num_build']) ? $_POST['num_build'] : '';
            $num_unit = isset($_POST['num_unit']) ? $_POST['num_unit'] : '';
            $zip_code = isset($_POST['zip_code']) ? $_POST['zip_code'] : '';
            $subnumber = isset($_POST['subnumber']) ? $_POST['subnumber'] : '';
            $office_id = isset($_POST['office_id']) ? $_POST['office_id'] : '';
            $client_passport = isset($_POST['client_passport']) ? $_POST['client_passport'] : '';
            $role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
            $receive_whatsupp = isset($_POST['receive_whatsupp']) ? 1 : 0;
            $receive_emails = isset($_POST['receive_emails']) ? 1 : 0;
            $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : '';

            $data = "fname=$fname&lname=$lname&address=$address&gender=$gender&email_address=$email_address&date_of_birth=$date_of_birth&city=$city&phone=$phone&lawyer_id=$lawyer_id&office_id=$office_id";

            // Validate required fields
            if (empty($fname)) {
                $em  = "الاسم الأول مطلوب";
                $_SESSION = array_merge($_SESSION, $_POST);
                header("Location: ../client-add.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "الاسم الأخير مطلوب";
                $_SESSION = array_merge($_SESSION, $_POST);
                header("Location: ../client-add.php?error=$em&$data");
                exit;
            } else if (empty($gender)) {
                $em  = "الجنس مطلوب";
                $_SESSION = array_merge($_SESSION, $_POST);
                header("Location: ../client-add.php?error=$em&$data");
                exit;
            } else if (empty($email_address)) {
                $em  = "البريد الإلكتروني مطلوب";
                $_SESSION = array_merge($_SESSION, $_POST);
                header("Location: ../client-add.php?error=$em&$data");
                exit;
            } else if (empty($phone)) {
                $em  = "رقم الهاتف مطلوب";
                $_SESSION = array_merge($_SESSION, $_POST);
                header("Location: ../client-add.php?error=$em&$data");
                exit;
            } 

            // Check if username is unique
            if (!empty($uname)) {
                if (!usernamelIsUnique($uname, $conn)) {
                    $em = "اسم المستخدم مأخوذ اختر واحد آخر";
                    if (empty($pass)) {
                        $em .= " وكلمة السر مطلوبة";
                    }
                    $_SESSION = array_merge($_SESSION, $_POST);
                    header("Location: ../client-add.php?error=$em&$data");
                    exit;
                }

                // Check if password is provided
                if (empty($pass)) {
                    $em = "كلمة السر مطلوبة";
                    $_SESSION = array_merge($_SESSION, $_POST);
                    header("Location: ../client-add.php?error=$em&$data");
                    exit;
                }
            }

            // SQL query to insert data
            $sql = "INSERT INTO clients (first_name, last_name, `address`, gender, city, email, date_of_birth, phone, 
                    father_name, grandfather_name, national_num, alhi, street_name, num_build, num_unit, zip_code, 
                    subnumber, username, `password`, receive_whatsupp, receive_emails, client_passport, role_id, office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $gender, $city, $email_address, $date_of_birth, $phone, 
                            $father_name, $grandfather_name, $national_num, $alhi, $street_name, $num_build, 
                            $num_unit, $zip_code, $subnumber, !empty($uname) ? $uname : '', !empty($pass) ? $pass : '', $receive_whatsupp, $receive_emails, $client_passport, $role_id, $office_id, $lawyer_id]);

            $sm = "تم إضافة الموكل بنجاح!";
            
            // Unset form data variables from session
            unset($_SESSION['fname']);
            unset($_SESSION['lname']);
            unset($_SESSION['address']);
            unset($_SESSION['gender']);
            unset($_SESSION['email_address']);
            unset($_SESSION['date_of_birth']);
            unset($_SESSION['city']);
            unset($_SESSION['phone']);
            unset($_SESSION['office_id']);
            unset($_SESSION['father_name']);
            unset($_SESSION['grandfather_name']);
            unset($_SESSION['national_num']);
            unset($_SESSION['alhi']);
            unset($_SESSION['street_name']);
            unset($_SESSION['num_build']);
            unset($_SESSION['num_unit']);
            unset($_SESSION['zip_code']);
            unset($_SESSION['subnumber']);
            unset($_SESSION['username']);

            if ($referrer == 'clients.php') {
                header("Location: ../clients.php?success=$sm&$data");
            } else {
                header("Location: ../users.php?success=$sm&$data");
            }

            exit;
        } else {
            $em = "An error occurred";
            if (isset($_POST['clients'])) {
                header("Location: ../clients.php?error=$em");
            } else {
                header("Location: ../users.php?error=$em");
            }
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
?>
