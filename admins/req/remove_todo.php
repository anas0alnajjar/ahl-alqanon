<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {
        include '../../DB_connection.php';
        include '../permissions_script.php';
        if ($pages['notifications']['read'] == 0) {
            header("Location: ../home.php");
            exit();
        }
    if(isset($_POST['id'])){
        

        $id = $_POST['id'];

        if(empty($id)){
        echo 0;
        }else {
            $stmt = $conn->prepare("DELETE FROM todos WHERE id=?");
            $res = $stmt->execute([$id]);

            if($res){
                echo 1;
            }else {
                echo 0;
            }
            $conn = null;
            exit();
    }
}else {
    header("Location: ../tasks.php?mess=error");
}
?>
<?php
} else {
        header("Location: ../../login.php");
        exit;
    } 
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>