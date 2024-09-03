<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
        
        if (isset($_POST['fname']) &&
            isset($_POST['lname']) ) {
            
            include '../../DB_connection.php';

            include '../permissions_script.php';
            if ($pages['adversaries']['add'] == 0) {
                header("Location: ../home.php");
                exit();
            }

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $email_address = $_POST['email_address'] ?? '';
            $date_of_birth = $_POST['date_of_birth'] ?? '';
            $city = $_POST['city'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $lawyer_id = $_POST['lawyer_id'] ?? '';
            $office_id = $_POST['office_id'] ?? '';

            $data = "fname=$fname&lname=$lname&address=$address&gender=$gender&email_address=$email_address&date_of_birth=$date_of_birth&city=$city&phone=$phone&lawyer_id=$lawyer_id&office_id=$office_id";

            if (empty($fname)) {
                $em  = "الاسم الأول مطلوب";
                $_SESSION['fname'] = $fname;
                $_SESSION['lname'] = $lname;
                $_SESSION['address'] = $address;
                $_SESSION['gender'] = $gender;
                $_SESSION['email_address'] = $email_address;
                $_SESSION['date_of_birth'] = $date_of_birth;
                $_SESSION['city'] = $city;
                $_SESSION['phone'] = $phone;
                header("Location: ../adversarie-add.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "الاسم الأخير مطلوب";
                $_SESSION['fname'] = $fname;
                $_SESSION['lname'] = $lname;
                $_SESSION['address'] = $address;
                $_SESSION['gender'] = $gender;
                $_SESSION['email_address'] = $email_address;
                $_SESSION['date_of_birth'] = $date_of_birth;
                $_SESSION['city'] = $city;
                $_SESSION['phone'] = $phone;
                header("Location: ../adversarie-add.php?error=$em&$data");
                exit;
            } 

            // بناء جملة SQL مع التحقق من اسم المستخدم وكلمة المرور إذا كانا ممتلئين
            $sql = "INSERT INTO adversaries (fname, lname, `address`, email_address, date_of_birth, city, phone, gender, 
                    office_id, lawyer_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$fname, $lname, $address, $email_address, $date_of_birth, $city, $phone, $gender, 
                            $office_id, $lawyer_id]);

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
            
            header("Location: ../adversaries.php?success=$sm");
            exit;
        } else {
            $em = "حدث خطأ غير متوقع";
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
