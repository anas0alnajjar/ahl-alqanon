<?php
include "../../DB_connection.php";

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lawyer_id = $_POST['lawyer_id'];
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/lawyers/';
    
    // Check if files are uploaded
    if (!empty($_FILES['lawyer_logo']['tmp_name'])) {
        // Retrieve old picture path if exists
        $oldPictureQuery = $conn->prepare("SELECT lawyer_logo FROM lawyer WHERE lawyer_id = ?");
        $oldPictureQuery->execute([$lawyer_id]);
        $oldPicture = $oldPictureQuery->fetchColumn();

        // Handle uploaded picture file
        $pictureTmpName = $_FILES['lawyer_logo']['tmp_name'];
        $pictureExtension = pathinfo($_FILES['lawyer_logo']['name'], PATHINFO_EXTENSION); // Get the file extension
        $pictureName = uniqid('lawyer_logo', true) . '_' . bin2hex(random_bytes(8)) . '.' . $pictureExtension; // Generate a unique filename
        $picturePath = $target_dir . $pictureName;

        if (move_uploaded_file($pictureTmpName, $picturePath)) {
            // Update the database with the new picture name
            $updateQuery = $conn->prepare("UPDATE lawyer SET lawyer_logo = ? WHERE lawyer_id = ?");
            $updateQuery->execute([$pictureName, $lawyer_id]);

            $response['success'] = true;
            $response['new_logo'] = $pictureName;

            // Delete old picture if it exists
            if ($oldPicture && file_exists($target_dir . $oldPicture)) {
                unlink($target_dir . $oldPicture);
            }
        } else {
            echo "Error: Failed to move uploaded file.";
            exit();
        }
    }

    echo json_encode($response);
    exit();
} else {
    header("Location: lawyers.php");
    exit;
}
?>
