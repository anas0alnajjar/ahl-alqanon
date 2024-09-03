<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['document_id'])) {

  if ($_SESSION['role'] == 'Admin') {
     include "../DB_connection.php";
     
     // DELETE
    function removeDocument($id, $conn){
        $sql  = "DELETE FROM documents
                WHERE document_id=?";
        $stmt = $conn->prepare($sql);
        $re   = $stmt->execute([$id]);
        if ($re) {
        return 1;
        } else {
        return 0;
        }
    }

     $id = $_GET['document_id'];
     if (removeDocument($id, $conn)) {
     	$sm = "Successfully deleted!";
        header("Location: documents.php?success=$sm");
        exit;
     }else {
        $em = "Unknown error occurred";
        header("Location: documents.php?error=$em");
        exit;
     }


  }else {
    header("Location: documents.php");
    exit;
  } 
}else {
	header("Location: documents.php");
	exit;
} 