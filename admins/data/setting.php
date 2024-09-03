<?php 

function getSetting($conn, $admin_id){
   $sql = "SELECT * FROM setting WHERE admin_id = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$admin_id]);

   if ($stmt->rowCount() == 1) {
     $settings = $stmt->fetch();
     return $settings;
   }else {
    return 0;
   }
}


