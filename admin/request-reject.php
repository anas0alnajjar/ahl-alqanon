<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['user_id'])) {

  if ($_SESSION['role'] == 'Admin') {
     include "../DB_connection.php";
     
     // DELETE
    function removeRequest($id, $conn){
        $sql  = "DELETE FROM ask_join
                WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $re   = $stmt->execute([$id]);
        if ($re) {
        return 1;
        } else {
        return 0;
        }
    }

     $id = $_GET['user_id'];
     if (removeRequest($id, $conn)) {
     	$sm = "تم الحذف بنجاح!";
        header("Location: requests.php?success=$sm");
        exit;
     }else {
        $em = "خطأ غير معروف، راجع الدعم الفني";
        header("Location: requests.php?error=$em");
        exit;
     }


  }else {
    header("Location: requests.php");
    exit;
  } 
}else {
	header("Location: requests.php");
	exit;
} 