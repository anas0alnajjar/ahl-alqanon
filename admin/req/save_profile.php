<?php
include '../../DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();

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

        $logo = '';
        if (!empty($_FILES['logo']['name'])) {
            $logo = time() . '_' . $_FILES['logo']['name'];
            move_uploaded_file($_FILES['logo']['tmp_name'], '../../profiles_photos/' . $logo);
        }

        $qr = '';
        if (!empty($_FILES['qr']['name'])) {
            $qr = time() . '_' . $_FILES['qr']['name'];
            move_uploaded_file($_FILES['qr']['tmp_name'], '../../profiles_photos/' . $qr);
        }

        $stmt = $conn->prepare("INSERT INTO profiles (office_id, fname, address, email_address, longitude, latitude, phone, whatsapp, facebook, twitter, desc1, desc2, logo, qr) VALUES (:office_id, :fname, :address, :email_address, :longitude, :latitude, :phone, :whatsapp, :facebook, :twitter, :desc1, :desc2, :logo, :qr)");
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
        $stmt->execute();

        $profile_id = $conn->lastInsertId();

        if (!empty($_FILES['upload_image']['name'][0])) {
            foreach ($_FILES['upload_image']['name'] as $key => $val) {
                $imageName = time() . '_' . $_FILES['upload_image']['name'][$key];
                move_uploaded_file($_FILES['upload_image']['tmp_name'][$key], '../../profiles_photos/' . $imageName);

                $stmt = $conn->prepare("INSERT INTO headers (office_id, profile_id, header) VALUES (:office_id, :profile_id, :header)");
                $stmt->bindParam(':office_id', $office_id);
                $stmt->bindParam(':profile_id', $profile_id);
                $stmt->bindParam(':header', $imageName);
                $stmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(array('status' => 'success'));
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method'));
}
?>
