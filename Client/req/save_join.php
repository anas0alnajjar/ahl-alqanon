<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
if (
    isset($_POST['fname']) &&
    isset($_POST['lname']) &&
    isset($_POST['username']) &&
    isset($_POST['pass']) &&
    isset($_POST['address']) &&
    isset($_POST['gender']) &&
    isset($_POST['email_address']) &&
    isset($_POST['city']) &&
    isset($_POST['phone']) &&
    isset($_POST['as_a'])
) {
    include '../../DB_connection.php';
    include '../permissions_script.php';
    if ($pages['join_requests']['add'] == 0) {
        header("Location: ../home.php");
        exit();
    }

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $uname = $_POST['username'];
    $pass = $_POST['pass'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $email_address = $_POST['email_address'];
    $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null; // Optional field
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $as_a = $_POST['as_a'];

    function usernameIsUnique($uname, $conn)
    {
        // Check in all tables for the username
        $tables = ['admin', 'lawyer', 'helpers', 'ask_join', 'clients', 'managers_office'];
        foreach ($tables as $table) {
            $sql = "SELECT username FROM $table WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$uname]);
            if ($stmt->rowCount() >= 1) {
                return false;
            }
        }
        return true;
    }

    if (
        empty($fname) ||
        empty($lname) ||
        empty($uname) ||
        empty($pass) ||
        empty($address) ||
        empty($gender) ||
        empty($email_address) ||
        empty($city) ||
        empty($phone)
    ) {
        $response = ['error' => 'جميع الحقول مطلوبة'];
        echo json_encode($response);
        exit;
    } elseif (!usernameIsUnique($uname, $conn)) {
        $response = ['error' => 'اسم المستخدم مأخوذ، أختر واحد آخر'];
        echo json_encode($response);
        exit;
    } else {
        // hashing the password
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO ask_join(username, `password`, first_name, last_name, `address`, gender, email, date_of_birth, city, phone, as_a) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname, $pass, $fname, $lname, $address, $gender, $email_address, $date_of_birth, $city, $phone, $as_a]);
        $response = ['success' => 'تم الحفظ بنجاح'];
        echo json_encode($response);
        exit;
    }
} else {
    $response = ['error' => 'An error occurred'];
    echo json_encode($response);
    exit;
}
    } else {
        header("Location: ../../login.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
