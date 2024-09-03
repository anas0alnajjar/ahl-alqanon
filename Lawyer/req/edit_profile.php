<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] == 'Lawyer') {
include '../../DB_connection.php';
include '../permissions_script.php';
if ($pages['profiles']['write'] == 0) {
    header("Location: ../home.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();

        $profile_id = $_POST['profile_id']; // Assuming you have profile_id to identify the profile to update
        $office_id = $_POST['office_id'];
        $fname = $_POST['fname'];
        $address = $_POST['address'];
        $email_address = $_POST['email_address'];
        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];
        $phone = $_POST['phone'];
        $whatsapp = $_POST['whatsapp'];
        $facebook = $_POST['facebook'];
        $twitter = $_POST['twitter'];
        $desc1 = $_POST['desc1'];
        $desc2 = $_POST['desc2'];

        // Fetch old images to check
        $stmt = $conn->prepare("SELECT logo, qr FROM profiles WHERE id = :profile_id");
        $stmt->bindParam(':profile_id', $profile_id);
        $stmt->execute();
        $oldImages = $stmt->fetch(PDO::FETCH_ASSOC);

        // Handle logo
        $logo = $oldImages ? $oldImages['logo'] : '';
        if (!empty($_FILES['logo']['name'])) {
            if ($logo && file_exists('../../profiles_photos/' . $logo)) {
                unlink('../../profiles_photos/' . $logo);
            }
            $logo = time() . '_' . $_FILES['logo']['name'];
            move_uploaded_file($_FILES['logo']['tmp_name'], '../../profiles_photos/' . $logo);
        }

        // Handle QR
        $qr = $oldImages ? $oldImages['qr'] : '';
        if (!empty($_FILES['qr']['name'])) {
            if ($qr && file_exists('../../profiles_photos/' . $qr)) {
                unlink('../../profiles_photos/' . $qr);
            }
            $qr = time() . '_' . $_FILES['qr']['name'];
            move_uploaded_file($_FILES['qr']['tmp_name'], '../../profiles_photos/' . $qr);
        }

        // Update profile information
        $stmt = $conn->prepare("UPDATE profiles SET office_id = :office_id, fname = :fname, address = :address, email_address = :email_address, longitude = :longitude, latitude = :latitude, phone = :phone, whatsapp = :whatsapp, facebook = :facebook, twitter = :twitter, desc1 = :desc1, desc2 = :desc2, logo = :logo, qr = :qr WHERE id = :profile_id");
        $stmt->bindParam(':office_id', $office_id);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':email_address', $email_address);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':whatsapp', $whatsapp);
        $stmt->bindParam(':facebook', $facebook);
        $stmt->bindParam(':twitter', $twitter);
        $stmt->bindParam(':desc1', $desc1);
        $stmt->bindParam(':desc2', $desc2);
        $stmt->bindParam(':logo', $logo);
        $stmt->bindParam(':qr', $qr);
        $stmt->bindParam(':profile_id', $profile_id);
        $stmt->execute();

        // Handle header images
        if (!empty($_FILES['upload_image']['name'][0])) {
            // Delete old header images
            $stmt = $conn->prepare("SELECT header FROM headers WHERE profile_id = :profile_id");
            $stmt->bindParam(':profile_id', $profile_id);
            $stmt->execute();
            $oldHeaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($oldHeaders as $header) {
                if (file_exists('../../profiles_photos/' . $header['header'])) {
                    unlink('../../profiles_photos/' . $header['header']);
                }
            }
            $stmt = $conn->prepare("DELETE FROM headers WHERE profile_id = :profile_id");
            $stmt->bindParam(':profile_id', $profile_id);
            $stmt->execute();

            // Add new header images
            foreach ($_FILES['upload_image']['name'] as $key => $val) {
                $imageName = time() . '_' . $_FILES['upload_image']['name'][$key];
                move_uploaded_file($_FILES['upload_image']['tmp_name'][$key], '../../profiles_photos/' . $imageName);

                $stmt = $conn->prepare("INSERT INTO headers (profile_id, header) VALUES (:profile_id, :header)");
                $stmt->bindParam(':profile_id', $profile_id);
                $stmt->bindParam(':header', $imageName);
                $stmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(array('status' => 'success'));
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(array('status' => 'error', 'message' => 'Exception: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method'));
}
} else {
    header("Location: ../../logout.php");
    exit;
}
?>
