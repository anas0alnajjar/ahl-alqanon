<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {


        if (isset($_POST['fname']) &&
            isset($_POST['lname']) &&
            isset($_POST['id']) &&
            isset($_POST['address']) &&
            isset($_POST['email']) &&
            isset($_POST['gender']) &&
            isset($_POST['date_of_birth']) &&
            isset($_POST['city']) &&
            isset($_POST['phone'])) {

            include '../../DB_connection.php';

            include '../permissions_script.php';
                if ($pages['adversaries']['write'] == 0) {
                    header("Location: ../home.php");
                    exit();
                }


            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $address = $_POST['address'];
            $gender = $_POST['gender'];
            $email_address = $_POST['email'];
            $date_of_birth = $_POST['date_of_birth'];
            $city = $_POST['city'];
            $phone = $_POST['phone'];
            $lawyer_id = $_POST['lawyer_id'];
            $id = $_POST['id'];


            $office_id = $_POST['office_id'];



            $data = 'id='.$id;

            if (empty($fname)) {
                $em  = "الاسم الأول مطلوب";
                header("Location: ../get-adversarie-info.php?error=$em&$data");
                exit;
            } else if (empty($lname)) {
                $em  = "الاسم الأخير مطلوب";
                header("Location: ../get-adversarie-info.php?error=$em&$data");
                exit;
            } 



            
            $sql = "UPDATE adversaries SET
            fname=?, lname=?, `address`=?, email_address=?, date_of_birth=?, city=?, phone=?, gender=?,
            office_id=?, lawyer_id=? ";

            $params = [$fname, $lname, $address, $email_address, $date_of_birth, $city, $phone, $gender, $office_id, $lawyer_id];


            $sql .= " WHERE id=?";
            $params[] = $id;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $sm = "تم التحديث بنجاح!";
            header("Location: ../get-adversarie-info.php?success=$sm&$data");
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
