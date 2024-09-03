<?php

session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {

        include "../../DB_connection.php";


// استعلام SQL
$query = "SELECT 
          cases.case_title, 
          cases.case_id, 
          CONCAT(clients.first_name, ' ', clients.last_name) AS client_name,
          CASE 
              WHEN cases.agency = 'on' THEN 'وكالة'
              ELSE 'قضية' 
          END AS source
          FROM 
          cases 
          INNER JOIN 
          clients ON cases.client_id = clients.client_id  
          ORDER BY `case_id` DESC;";

// تنفيذ الاستعلام
$statement = $conn->prepare($query);
$statement->execute();
$response = $statement->fetchAll(PDO::FETCH_ASSOC);

// تنسيق البيانات كـ JSON
header('Content-Type: application/json');
echo json_encode($response);
?>  

<?php 

  }else {
    header("Location: ../index.php");
    exit;
  } 
}else {
	header("Location: ../index.php");
	exit;
} 

?>