<?php 
function usernamelIsUnique($uname, $conn) {
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
    }

?>