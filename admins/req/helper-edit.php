<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
        

if (isset($_POST['helper_name'])      &&
    isset($_POST['phone']) ) {
    
    include '../../DB_connection.php';
    include '../permissions_script.php';
        if ($pages['assistants']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }




    $helper_name = $_POST['helper_name'];
    $phone = $_POST['phone'];
    
    $role_id = $_POST['role_id'];
    $national_helper = $_POST['national_helper'];
    $passport_helper = $_POST['passport_helper'];

    

    $helper_id = $_POST['id'];
    $office_id = $_POST['office_id'];
    $lawyer_id = $_POST['lawyer_id'];

    $stop_account = isset($_POST['stop']) ? 1 : 0;
    $stop_date = $_POST['stop_date'];
    

    $data = 'id='.$helper_id;

    if (empty($helper_name)) {
        $em  = "اسم المساعد مطلوب";
        header("Location: ../get-helper-info.php?error=$em&$data");
        exit;
    }else if (empty($phone)) {
        $em  = "رقم الهاتف مطلوب";
        header("Location: ../get-helper-info.php?error=$em&$data");
        exit;
    }else {
        $sql = "UPDATE helpers SET helper_name = ?, `phone`=? , role_id=?, passport_helper=?, national_helper=?, office_id=?, `stop`=?, stop_date=?, lawyer_id=?
                WHERE `id`=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$helper_name , $phone, $role_id, $passport_helper, $national_helper, $office_id, $stop_account, $stop_date, $lawyer_id, $helper_id]);
        $sm = "تم التحديث بنجاح!";
        header("Location: ../get-helper-info.php?success=$sm&$data");
        exit;
    }
    
  }else {
    $em = "An error occurred";
    header("Location: ../helpers.php?error=$em");
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
