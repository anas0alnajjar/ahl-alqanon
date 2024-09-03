<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        

if (isset($_POST['fname'])      &&
    isset($_POST['username'])   &&
    isset($_POST['lawyer_id']) &&
    isset($_POST['email_address']) &&
    isset($_POST['gender'])        &&
    isset($_POST['phone'])  ) {
    
    include '../../DB_connection.php';
    include "../data/lawyers.php";

    $fname = $_POST['fname'];
    $uname = $_POST['username'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $email_address = $_POST['email_address'];
    $date_of_birth = $_POST['date_of_birth'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $preferred_date = $_POST['preferred_date'];
    
    $lawyer_national = $_POST['lawyer_national'];
    $lawyer_passport = $_POST['lawyer_passport'];
    $role_id = $_POST['role_id'];
    $office_id = $_POST['office_id'];
    $stop_date = $_POST['stop_date'];

    $lawyer_id = $_POST['lawyer_id'];
    
    $stop_account = isset($_POST['stop']) ? 1 : 0;

    $data = 'lawyer_id='.$lawyer_id;


    if (empty($fname)) {
        $em  = "الاسم مطلوب";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else if (empty($uname)) {
        $em  = "اسم المستخدم مطلوب";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else if (!usernamelIsUnique($uname, $conn, $lawyer_id)) {
        $em  = "اسم المستخدم مأخوذ، اختر واحد آخر";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else if (empty($gender)) {
        $em  = "الجنس مطلوب";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else if (empty($email_address)) {
        $em  = "الإيميل مطلوب";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else if (empty($phone)) {
        $em  = "رقم الهاتف مطلوب";
        header("Location: ../lawyer-edit.php?error=$em&$data");
        exit;
    }else {
        $sql = "UPDATE lawyer SET
                username = ?, lawyer_name=?, `lawyer_address`=? ,lawyer_gender = ?, lawyer_city=?, lawyer_email=?, date_of_birth=?, lawyer_phone=?, preferred_date=?, lawyer_national=?, lawyer_passport=?, role_id=?, `stop`=?, office_id=?, stop_date=?
                WHERE lawyer_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname,$fname, $address, $gender,$city, $email_address, $date_of_birth, $phone, $preferred_date, $lawyer_national, $lawyer_passport, $role_id, $stop_account, $office_id, $stop_date, $lawyer_id]);
        $sm = "تم التحديث بنجاح!";
        header("Location: ../lawyer-edit.php?success=$sm&$data");
        exit;
    }
    
  }else {
    $em = "An error occurred";
    header("Location: ../lawyers.php?error=$em");
    exit;
  }

  }else {
    header("Location: ../../logout.php");
    exit;
  } 
}else {
    header("Location: ../../logout.php");
    exit;
} 
