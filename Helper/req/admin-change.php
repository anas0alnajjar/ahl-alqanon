<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
    	

if (isset($_POST['new_pass'])   &&
    isset($_POST['c_new_pass']) &&
    isset($_POST['admin_id'])) {
    
    include '../../DB_connection.php';

    $new_pass = $_POST['new_pass'];
    $c_new_pass = $_POST['c_new_pass'];

    $admin_id = $_POST['admin_id'];
    
    
    $data = 'admin_id='.$admin_id.'#change_password';

    if (empty($new_pass)) {
		$em  = "كلمة السر الجديدة مطلوبة";
		header("Location: ../admin-profile.php?perror=$em&$data");
		exit;
	}else if (empty($c_new_pass)) {
		$em  = "لم يتم تأكيد كلمة المرور";
		header("Location: ../admin-profile.php?perror=$em&$data");
		exit;
	}else if ($new_pass !== $c_new_pass) {
        $em  = "كلمات السر غير متطابقة";
        header("Location: ../admin-profile.php?perror=$em&$data");
        exit;
    }else {
        // hashing the password
        $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

        $sql = "UPDATE `admin` SET
                `password` = ?
                WHERE admin_id=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_pass, $admin_id]);
        $sm = "تم تغيير كلمة السر بنجاح!";
        header("Location: ../admin-profile.php?psuccess=$sm&$data");
        exit;
	}
    
  }else {
  	$em = "An error occurred";
    header("Location: ../admin-profile.php?error=$em&$data");
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
