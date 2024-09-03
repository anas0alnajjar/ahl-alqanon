<?php
// All Lawyers 
function getAllLawyers($conn){
   $sql = "SELECT * FROM lawyer";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() >= 1) {
     $lawyers = $stmt->fetchAll();
     return $lawyers;
   } else {
      return 0;
   }
}

// DELETE
function removeLawyer($id, $conn){
   $sql  = "DELETE FROM lawyer
           WHERE lawyer_id=?";
   $stmt = $conn->prepare($sql);
   $re   = $stmt->execute([$id]);
   if ($re) {
     return 1;
   } else {
    return 0;
   }
}

// Get lawyer By Id 
function getLawyerById($id, $conn){
  $sql = "SELECT 
  l.*, 
  (SELECT COUNT(*) FROM cases c WHERE c.lawyer_id = l.lawyer_id) AS case_count,
  (SELECT COUNT(*) FROM helpers h WHERE h.lawyer_id = l.lawyer_id) AS helper_count
FROM 
  lawyer l
WHERE 
  l.lawyer_id =?";
  
   $stmt = $conn->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() == 1) {
     $lawyer = $stmt->fetch();
     return $lawyer;
   } else {
    return 0;
   }
}

// Check if the email is Unique
function usernamelIsUnique($uname, $conn, $lawyer_id = 0) {
  if ($lawyer_id == 0) {
      $sql = "SELECT username FROM `admin` WHERE username = ?
              UNION
              SELECT username FROM lawyer WHERE username = ?
              UNION
              SELECT username FROM helpers WHERE username = ?
              UNION
              SELECT username FROM clients WHERE username = ?
              UNION
              SELECT username FROM managers_office WHERE username = ?
              UNION
              SELECT username FROM ask_join WHERE username = ?";

      $stmt = $conn->prepare($sql);
      $stmt->execute([$uname, $uname, $uname,$uname, $uname, $uname]);
      
      if ($stmt->rowCount() > 0) {
          return 0;
      } else {
          return 1;
      }
  } else {
      $sql = "SELECT username FROM `admin` WHERE username = ?
              UNION
              SELECT username FROM lawyer WHERE username = ? AND lawyer_id != ?
              UNION
              SELECT username FROM clients WHERE username = ?
              UNION
              SELECT username FROM helpers WHERE username = ?
              UNION
              SELECT username FROM managers_office WHERE username = ?
              UNION
              SELECT username FROM ask_join WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$uname, $uname, $lawyer_id, $uname, $uname, $uname, $uname]);
      
      if ($stmt->rowCount() > 0) {
          return 0;
      } else {
          return 1;
      }
  }
}


