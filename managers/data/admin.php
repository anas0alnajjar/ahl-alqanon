<?php 

function adminPasswordVerify($admin_pass, $conn, $admin_id){
   $sql = "SELECT * FROM admin
           WHERE admin_id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$admin_id]);

   if ($stmt->rowCount() == 1) {
     $admin = $stmt->fetch();
     $pass  = $admin['password'];

     if (password_verify($admin_pass, $pass)) {
     	return 1;
     }else {
     	return 0;
     }
   }else {
    return 0;
   }
}
function managerPasswordVerify($manager_pass, $conn, $manager_id){
   $sql = "SELECT * FROM managers_office
           WHERE id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$manager_id]);

   if ($stmt->rowCount() == 1) {
     $manager = $stmt->fetch();
     $pass  = $manager['manager_password'];

     if (password_verify($manager_pass, $pass)) {
     	return 1;
     }else {
     	return 0;
     }
   }else {
    return 0;
   }
}
?>