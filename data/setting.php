<?php 

function getSetting($conn){
   $sql = "SELECT * FROM setting WHERE id = 1;";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() == 1) {
     $settings = $stmt->fetch();
     return $settings;
   }else {
    return 0;
   }
}

