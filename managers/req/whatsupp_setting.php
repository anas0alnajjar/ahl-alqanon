<?php

// جلب إعدادات واتس آب من قاعدة البيانات
$sqlSetting = "SELECT `host_whatsapp`, `token_whatsapp` FROM `setting` WHERE `admin_id` = 1";
$stmtSetting = $conn->prepare($sqlSetting);
$stmtSetting->execute();
$settings = $stmtSetting->fetch(PDO::FETCH_ASSOC);

// Function to send WhatsApp message
if (!function_exists('sendWhatsAppMessage')) {
    function sendWhatsAppMessage($recipient, $message, $settings) {
        $params = array(
            'token' => $settings['token_whatsapp'],
            'to' => $recipient,
            'body' => $message
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $settings['host_whatsapp'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log("Curl Error: " . $err);
            return false;
        } else {
            return true;
        }
    }
}

?>
