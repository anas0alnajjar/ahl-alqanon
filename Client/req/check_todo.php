<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Client') {
        include '../../DB_connection.php';

    if(isset($_POST['id'])){


        $id = $_POST['id'];

        if(empty($id)){
        echo 'error';
        }else {
            $todos = $conn->prepare("SELECT id, read_by_client FROM todos WHERE id=?");
            $todos->execute([$id]);

            $todo = $todos->fetch();
            $uId = $todo['id'];
            $checked = $todo['read_by_client'];

            $uChecked = $checked ? 0 : 1;

            $res = $conn->query("UPDATE todos SET read_by_client=$uChecked WHERE id=$uId");

            if($res){
                echo $checked;
            }else {
                echo "error";
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