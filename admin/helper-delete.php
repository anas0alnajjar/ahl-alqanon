<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

  if ($_SESSION['role'] == 'Admin') {
     include "../DB_connection.php";
    
     function removeHelper($id, $conn){
        $sql  = "DELETE FROM helpers
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $re   = $stmt->execute([$id]);
        if ($re) {
          return 1;
        } else {
         return 0;
        }
     }

     $id = $_GET['id'];
     if (removeHelper($id, $conn)) {
     	$sm = "تم الحذف بنجاح!";
        header("Location: helpers.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: helpers.php?error=$em");
        exit;
     }


  }else {
    header("Location: cases.php");
    exit;
  } 
}else {
	header("Location: cases.php");
	exit;
} 