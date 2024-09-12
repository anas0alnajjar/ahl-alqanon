<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// $sName = "localhost";
// $uName = "bluest54_qanon";
// $pass = '9m{&L~PbPe-g';
// $db_name = "bluest54_qanon";


// try {
//     $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $conn->exec("SET NAMES 'utf8mb4'");
// } catch(PDOException $e) {
//     error_log("Connection Failed: ". $e->getMessage());
//     exit;
// }

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "ahl-alqanon-db"; // في البداية كانت "ahl_alqhanon"
$port = 3306; // في البداية كانت 3666

try {
    $conn = new PDO("mysql:host=$sName;port=$port;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    error_log("Connection Failed: ". $e->getMessage());
    exit;
}



