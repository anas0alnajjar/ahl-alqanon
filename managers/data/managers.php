<?php 
// Check if the email is Unique
function usernamelIsUnique($uname, $conn, $manager_id = 0) {
    if ($manager_id == 0) {
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
                SELECT username FROM managers_office WHERE username = ? AND id != ?
                UNION
                SELECT username FROM clients WHERE username = ?
                UNION
                SELECT username FROM helpers WHERE username = ?
                UNION
                SELECT username FROM lawyer WHERE username = ?
                UNION
                SELECT username FROM ask_join WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname, $uname, $manager_id, $uname, $uname, $uname, $uname]);
        
        if ($stmt->rowCount() > 0) {
            return 0;
        } else {
            return 1;
        }
    }
  }

  // Get manager By Id 
function getManagerById($id, $conn){
    $sql = "SELECT managers_office.*, offices.office_name
            FROM managers_office
            LEFT JOIN offices ON managers_office.office_id = offices.office_id
            WHERE managers_office.id =?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
 
    if ($stmt->rowCount() == 1) {
      $manager = $stmt->fetch();
      return $manager;
    } else {
     return 0;
    }
 }
  

?>