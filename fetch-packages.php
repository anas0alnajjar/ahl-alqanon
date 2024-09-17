<?php 
include 'DB_connection.php';
function get_packages_by_category($conn,$category){
    $sql = 'SELECT * FROM packages where category = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$category]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($result){
        return $result;
    }
        return null;    

}