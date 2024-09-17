<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if (isset($_SESSION['role']) == 'Admin') {

        include '../../DB_connection.php';

        if (
            isset($_POST['plan_name']) && isset($_POST['plan_description'])
            && isset($_POST['plan_status']) && isset($_POST['plan_duration'])
            && isset($_POST['plan_price']) && isset($_POST['plan_category'])
            && isset($_POST['num_cases'])  && isset($_POST['num_clients'])
            && isset($_POST['num_helpers']) && isset($_POST['num_messages'])
            && isset($_POST['num_documents']) && isset($_POST['num_tasks'])
            && isset($_POST['num_sessions']) && isset($_POST['num_events'])
            && isset($_POST['num_lawyers']) && isset($_POST['num_offices'])
        ) {
            $plan_name = $_POST['plan_name'];
            $plan_description = $_POST['plan_description'];
            $plan_status = $_POST['plan_status'];
            $plan_duration = $_POST['plan_duration'];
            $plan_price = $_POST['plan_price'];
            $plan_category = $_POST['plan_category'];
            $num_cases = $_POST['num_cases'];
            $num_clients = $_POST['num_clients'];
            $num_helpers = $_POST['num_helpers'];
            $num_messages = $_POST['num_messages'];
            $num_documents = $_POST['num_documents'];
            $num_tasks = $_POST['num_tasks'];
            $num_sessions = $_POST['num_sessions'];
            $num_events = $_POST['num_events'];
            $num_lawyers = $_POST['num_lawyers'];
            $num_offices = $_POST['num_offices'];
            if (
                empty($_POST['plan_name']) || empty($_POST['plan_description'])
                || empty($_POST['plan_status']) || empty($_POST['plan_duration'])
                || empty($_POST['plan_price']) || empty($_POST['plan_category'])
                || empty($_POST['num_cases']) || empty($_POST['num_clients'])
                || empty($_POST['num_helpers']) || empty($_POST['num_messages'])
                || empty($_POST['num_documents']) || empty($_POST['num_tasks'])
                || empty($_POST['num_sessions']) || empty($_POST['num_events'])

            ) {
                $me = 'الرجاء ملء كل البيانات ';
                header("location: ../add-package.php?error=" . $me);
                exit;
            }
            if($plan_category == 1){
                $num_lawyers = null;
                $num_offices = null;
            }
            else if($plan_category == 2 ){
                if (empty($num_lawyers)) {
                    $me = 'الرجاء ملء حقل عدد المحامين ';
                    header("location: ../add-package.php?error=" . $me);
                    exit;
                }
                else{
                    $num_offices = null;
                }

            }
            else if($plan_category ==3){
                if (empty($num_offices) || empty($num_lawyers)) {
                    $me = 'الرجاء ملء حقلي عدد المحامين و عدد المكاتب ';
                    header("location: ../add-package.php?error=" . $me);
                    exit;
                }
                
            }
            
            $sql  = "INSERT INTO packages
            (name,description,status,duration,price,category,num_cases,num_clients,num_helpers,
            num_messages,num_documents,num_tasks,num_sessions,num_events,num_lawyers,num_offices)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $plan_name,
                $plan_description,
                $plan_status,
                $plan_duration,
                $plan_price,
                $plan_category,
                $num_cases,
                $num_clients,
                $num_helpers,
                $num_messages,
                $num_documents,
                $num_tasks,
                $num_sessions,
                $num_events,
                $num_lawyers,
                $num_offices
            ]);
            $success = 'تمت إضافة الباقة بنجاح';
            header("location: ../add-package.php?message=" . $success);
        }
    } else {
        header("Location: ../../login.php");
        exit;
    }
} else {
    header("Location: ../../logout.php");
    exit;
}
