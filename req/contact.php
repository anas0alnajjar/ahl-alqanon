<?php
if (isset($_POST['email']) && isset($_POST['full_name']) && isset($_POST['message']) && isset($_POST['g-recaptcha-response'])) {

    include "../DB_connection.php";
    include "../data/setting.php";
    $setting = getSetting($conn);

    // تحقق من reCAPTCHA
    $recaptcha_secret = $setting['secret_key'];
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];

    $context = stream_context_create($options);
    $recaptcha_verify = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_success = json_decode($recaptcha_verify);

    if ($recaptcha_success->success && $recaptcha_success->score >= 0.5) {
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $message = $_POST['message'];

        if (empty($email)) {
            echo json_encode(['error' => 'الإيميل مطلوب']);
        } else if (empty($full_name)) {
            echo json_encode(['error' => 'الاسم مطلوب']);
        } else if (empty($message)) {
            echo json_encode(['error' => 'الرسالة مطلوبة']);
        } else {
            $sql = "INSERT INTO message (sender_full_name, sender_email, message) VALUES(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$full_name, $email, $message]);
            echo json_encode(['success' => 'Message sent successfully']);
        }
    } else {
        echo json_encode(['error' => 'Failed to verify reCAPTCHA. Please try again.']);
    }
} else {
    echo json_encode(['error' => 'All fields are required.']);
}
?>
