<?php
// All Clients 
function getAllClients($conn){
   $sql = "SELECT * FROM clients";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if ($stmt->rowCount() >= 1) {
     $clients = $stmt->fetchAll();
     return $clients;
   } else {
      return 0;
   }
}

// DELETE
function removeClient($id, $conn){
   $sql  = "DELETE FROM clients
           WHERE client_id=?";
   $stmt = $conn->prepare($sql);
   $re   = $stmt->execute([$id]);
   if ($re) {
     return 1;
   } else {
    return 0;
   }
}

// Get Client By Id 
function getClientById($id, $conn){
   $sql = "SELECT * FROM clients
           WHERE client_id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$id]);

   if ($stmt->rowCount() == 1) {
     $client = $stmt->fetch();
     return $client;
   } else {
    return 0;
   }
}

// Check if the email is Unique

function usernamelIsUnique($uname, $conn, $client_id = 0) {
  if ($client_id == 0) {
      $sql = "SELECT username FROM admin WHERE username = ?
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
      $stmt->execute([$uname, $uname, $uname, $uname, $uname, $uname]);
  } else {
      $sql = "SELECT username FROM admin WHERE username = ?
              UNION
              SELECT username FROM lawyer WHERE username = ?
              UNION
              SELECT username FROM helpers WHERE username = ?
              UNION
              SELECT username FROM ask_join WHERE username = ?
              UNION
              SELECT username FROM managers_office WHERE username = ?
              UNION
              SELECT username FROM clients WHERE username = ? AND client_id != ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$uname, $uname, $uname, $uname, $uname, $uname, $client_id]);
  }

  if ($stmt->rowCount() > 0) {
      return 0;
  } else {
      return 1;
  }
}


