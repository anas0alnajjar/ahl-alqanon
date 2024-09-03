<?php

session_start();

// Check if user is logged in as admin
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {
    
    // Check if all required POST parameters are set
    if (isset($_POST['content'], $_POST['client_id_doc'], $_POST['lawyer_id_doc'], $_POST['case_id_doc'])) {
        
        // Include database connection file
        include '../../DB_connection.php';

        // Receive data from the form
        $content = $_POST['content'];
        $client_id = $_POST['client_id_doc'];
        $lawyer_id = $_POST['lawyer_id_doc'];
        $case_id = $_POST['case_id_doc'];
        $office_id = $_POST['office_id_doc'];
        $document_title = $_POST['document_title'];

        // Check if required fields are empty
        if (empty($content) || empty($document_title)) {
            $em = "All fields are required";
            echo json_encode(array("error" => $em));
            exit;
        } else {
            // If file is specified
            if (!empty($_FILES['attachments']['tmp_name'])) {
                $fileTmpName = $_FILES['attachments']['tmp_name'];
                $fileExtension = pathinfo($_FILES['attachments']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('reportFile_family_', true) . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension; 
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/pdf/' . $fileName;
                
                // Move uploaded file to destination directory
                if (move_uploaded_file($fileTmpName, $filePath)) {
                    $attachments_name = $fileName;
                } else {
                    $em = "Failed to move uploaded file";
                    echo json_encode(array("error" => $em));
                    exit;
                }
            } else {
                // If file is not specified, set name to empty
                $attachments_name = "";
            }

            // Prepare SQL statement
            $sql  = "INSERT INTO documents(title, content, client_id, lawyer_id, case_id, attachments, office_id) VALUES(?,?,?,?,?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Execute the statement
            if ($stmt->execute([$document_title, $content, $client_id, $lawyer_id, $case_id, $attachments_name, $office_id])) {
                $sm = "New document saved successfully";
                echo json_encode(array("success" => $sm));
                exit;
            } else {
                $em = "Failed to save document to database";
                echo json_encode(array("error" => $em));
                exit;
            }
        }
    } else {
        $em = "Missing POST parameters";
        // print_r($_POST) ;
        echo json_encode(array("error" => $em));
        exit;
    }
} else {
    // If user is not logged in as admin, redirect to logout page
    header("Location: ../../logout.php");
    exit;
}
