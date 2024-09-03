<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        

        if (isset($_POST['case_title']) && isset($_POST['case_type']) && isset($_POST['case_number']) && isset($_POST['case_description']) &&
            isset($_POST['client_id']) && isset($_POST['client_contact']) && isset($_POST['lawyer_id']) && isset($_POST['review_date']) &&
            isset($_POST['case_status']) && isset($_POST['case_id'])) {
            
            include '../../DB_connection.php';

            $case_title = $_POST['case_title'];
            $case_type = $_POST['case_type'];
            $case_number = $_POST['case_number'];
            $case_description = $_POST['case_description'];
            $client_id = $_POST['client_id'];
            $client_contact = $_POST['client_contact'];
            $lawyer_id = $_POST['lawyer_id'];
            $review_date = $_POST['review_date'];
            $case_status = $_POST['case_status'];
            $case_id = $_POST['case_id'];

            $data = 'case_id='.$case_id;

            if (empty($case_title) || empty($case_type) || empty($case_number) || empty($case_description) ||
                empty($client_id) || empty($client_contact) || empty($lawyer_id) || empty($review_date) ||
                empty($case_status) || empty($case_id)) {
                
                $em  = "All fields are required";
                header("Location: ../case-edit.php?error=$em&$data");
                exit;
            } else {
                $sql = "UPDATE cases SET
                        case_title=?, case_type=?, case_number=?, case_description=?, client_id=?, client_email=?, 
                        lawyer_id=?, review_date=?, case_status=?
                        WHERE case_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$case_title, $case_type, $case_number, $case_description, $client_id, $client_contact,
                                $lawyer_id, $review_date, $case_status, $case_id]);
                $sm = "Case successfully updated!";
                header("Location: ../case-edit.php?success=$sm&$data");
                exit;
            }
        } else {
            $em = "An error occurred";
            header("Location: ../cases.php?error=$em");
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
