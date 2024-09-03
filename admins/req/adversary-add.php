<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
        
        if (isset($_POST['fname']) && isset($_POST['lname'])) {
            
            include '../../DB_connection.php';

            include '../permissions_script.php';
            if ($pages['adversaries']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            // جمع البيانات من النموذج
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email_address'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $lawyer_id = $_POST['lawyer_id'];
            $office_id = $_POST['office_id'];

            // إنشاء متغير data لتخزين القيم
            $data = http_build_query([
                'fname' => $fname,
                'lname' => $lname,
                'address' => $address,
                'gender' => $gender,
                'email_address' => $email_address,
                'date_of_birth' => $date_of_birth,
                'city' => $city,
                'phone' => $phone,
                'lawyer_id' => $lawyer_id,
                'office_id' => $office_id
            ]);

            // التحقق من القيم
            if (empty($fname)) {
                $em = "الاسم الأول مطلوب";
                $_SESSION['fname'] = isset($_POST['fname']) ? $_POST['fname'] : '';
                $_SESSION['lname'] = isset($_POST['lname']) ? $_POST['lname'] : '';
                $_SESSION['address'] = isset($_POST['address']) ? $_POST['address'] : '';
                $_SESSION['gender'] = isset($_POST['gender']) ? $_POST['gender'] : '';
                $_SESSION['email_address'] = isset($_POST['email_address']) ? $_POST['email_address'] : '';
                $_SESSION['date_of_birth'] = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
                $_SESSION['city'] = isset($_POST['city']) ? $_POST['city'] : '';
                $_SESSION['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
                
                header("Location: ../adversarie-add.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em = "الاسم الأخير مطلوب";
                $_SESSION['fname'] = isset($_POST['fname']) ? $_POST['fname'] : '';
                $_SESSION['lname'] = isset($_POST['lname']) ? $_POST['lname'] : '';
                $_SESSION['address'] = isset($_POST['address']) ? $_POST['address'] : '';
                $_SESSION['gender'] = isset($_POST['gender']) ? $_POST['gender'] : '';
                $_SESSION['email_address'] = isset($_POST['email_address']) ? $_POST['email_address'] : '';
                $_SESSION['date_of_birth'] = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
                $_SESSION['city'] = isset($_POST['city']) ? $_POST['city'] : '';
                $_SESSION['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
                
                header("Location: ../adversarie-add.php?error=$em&$data");
                exit;
            } 

            // بناء جملة SQL مع التحقق من القيم
            $sql = "INSERT INTO adversaries (fname, lname, `address`, email_address, date_of_birth, city, phone, gender, office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $email_address, $date_of_birth, $city, $phone, $gender, $office_id, $lawyer_id]);

            $sm = "تم إضافة الخصم بنجاح!";
            unset($_SESSION['fname']);
            unset($_SESSION['lname']);
            unset($_SESSION['address']);
            unset($_SESSION['gender']);
            unset($_SESSION['email_address']);
            unset($_SESSION['date_of_birth']);
            unset($_SESSION['city']);
            unset($_SESSION['phone']);
            unset($_SESSION['office_id']);
            unset($_SESSION['lawyer_id']);

            header("Location: ../adversaries.php?success=$sm");
            exit;
        } else {
            $em = "An error occurred";
            header("Location: ../adversaries.php?error=$em");
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
