<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        if (isset($_POST['case_title']) &&
            isset($_POST['case_type']) &&
            isset($_POST['case_number']) &&
            isset($_POST['case_description']) &&
            isset($_POST['client_name']) &&
            isset($_POST['client_contact']) &&
            isset($_POST['lawer_name']) &&
            isset($_POST['review_date']) &&
            isset($_POST['case_status'])) {
                
            include '../../DB_connection.php';
            include '../send_email.php';
            

            $case_title = $_POST['case_title'];
            $case_type = $_POST['case_type'];
            $case_number = $_POST['case_number'];
            $case_description = $_POST['case_description'];
            $client_name = $_POST['client_name'];
            $client_contact = $_POST['client_contact'];
            $lawer_name = $_POST['lawer_name'];
            $review_date = $_POST['review_date'];
            $case_status = $_POST['case_status'];
            
            
            $data = 'case_title='.$case_title.'&case_type='.$case_type.'&case_number='.$case_number.'&case_description='.$case_description.'&client_name='.$client_name.'&client_contact='.$client_contact.'&lawer_name='.$lawer_name.'&review_date='.$review_date.'&case_status='.$case_status;
            
            if (empty($case_title) || empty($case_type) || empty($case_number) || empty($case_description) || empty($client_name) || empty($client_contact) || empty($lawer_name) || empty($review_date) || empty($case_status)) {
                $em = "All fields are required";
                header("Location: ../add_case.php?error=$em&$data");
                exit;
            }
             else {
                $sql  = "INSERT INTO cases(case_title, case_type, case_number, case_description, client_id, client_email, lawyer_id, review_date, case_status) VALUES(?,?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$case_title, $case_type, $case_number, $case_description, $client_name, $client_contact, $lawer_name, $review_date, $case_status]);
                $sm = "New client registered successfully";
                header("Location: ../add_case.php?success=$sm");
                
                $recipient = $client_contact;
                $subject = $case_title;
                $message = $case_description;

                sendEmail($recipient, $subject, $message);
                exit;
            }
        } else {
            $em = "An error occurred";
            header("Location: ../add_case.php?error=$em");
            exit;
        }
    } else {
        header("Location: ../../logout.php");
        exit;
    } 
} else {
    header("Location: ../../logout.php");
    exit;
} 
?>
