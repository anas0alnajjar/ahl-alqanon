<?php  

// All Message 
function getAllMessages($conn){
   $sql = "SELECT * FROM message ORDER BY message_id DESC";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() >= 1) {
     $messages = $stmt->fetchAll();
     return $messages;
   }else {
    return 0;
   }
}