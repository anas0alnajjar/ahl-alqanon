<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
    	

if (isset($_POST['admin_pass']) &&
    isset($_POST['new_pass'])   &&
    isset($_POST['c_new_pass']) &&
    isset($_POST['lawyer_id'])) {
    
    include '../../DB_connection.php';
    include "../data/admin.php";

    include '../permissions_script.php';
        if ($pages['lawyers']['write'] == 0) {
            header("Location: ../home.php");
            exit();
        }

    $admin_pass = $_POST['admin_pass'];
    $new_pass = $_POST['new_pass'];
    $c_new_pass = $_POST['c_new_pass'];

    $lawyer_id = $_POST['lawyer_id'];
    $id = $_SESSION['user_id'];
    
    $data = 'lawyer_id='.$lawyer_id.'#change_password';

    if (empty($admin_pass)) {
		$em  = "كلمة سر الآدمن مطلوبة";
		header("Location: ../lawyer-edit.php?perror=$em&$data");
		exit;
	}else if (empty($new_pass)) {
		$em  = "كلمة السر الجديدة مطلوبة";
		header("Location: ../lawyer-edit.php?perror=$em&$data");
		exit;
	}else if (empty($c_new_pass)) {
		$em  = "تأكيد كلمة السر مطلوب";
		header("Location: ../lawyer-edit.php?perror=$em&$data");
		exit;
	}else if ($new_pass !== $c_new_pass) {
        $em  = "كلمات السر غير متطابقة";
        header("Location: ../lawyer-edit.php?perror=$em&$data");
        exit;
    }else if (!adminPasswordVerify($admin_pass, $conn, $id)) {
        $em  = "كلمة سر الآدمن غير صحيحة";
        header("Location: ../lawyer-edit.php?perror=$em&$data");
        exit;
    }else {
        // hashing the password
        $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $sql = "UPDATE lawyer SET
                `lawyer_password` = ?
                WHERE lawyer_id=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_pass, $lawyer_id]);
        $sm = "تم تحديث كلمة المرور بنجاح!";
        header("Location: ../lawyer-edit.php?psuccess=$sm&$data");
        exit;
	}
    
  }else {
  	$em = "An error occurred";
    header("Location: ../lawyer-edit.php?error=$em&$data");
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
