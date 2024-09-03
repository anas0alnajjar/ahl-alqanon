<?php
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Client') {

    // استدعاء ملف الاتصال بقاعدة البيانات هنا
    include "../DB_connection.php";
    include "get_office.php";

    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);

    if ($office_id !== null) {
        // استعلام لاحتساب عدد الجلسات القادمة المرتبطة بالعميل
        $query_open_sessions = "SELECT COUNT(*) AS sessions_coming
                                FROM sessions
                                LEFT JOIN cases ON cases.case_id = sessions.case_id 
                                WHERE session_date > CURRENT_DATE AND (cases.client_id = :client_id OR FIND_IN_SET(:client_id, cases.plaintiff))";

        // استعلام لاحتساب عدد الجلسات الكلي المرتبطة بالعميل
        $query_total_sessions = "SELECT COUNT(*) AS all_session_count
                                 FROM sessions
                                 LEFT JOIN cases ON cases.case_id = sessions.case_id
                                 WHERE cases.client_id = :client_id OR FIND_IN_SET(:client_id, cases.plaintiff)";

        // استعلام لاحتساب عدد القضايا الكلي المرتبطة بالعميل
        $query_total_cases = "SELECT COUNT(*) AS total_cases_count 
                              FROM cases 
                              WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)";

        $current_month = date('Y-m');

        // الدفعات المستحقة للشهر الحالي المرتبطة بالعميل
        $query_monthly_payment = "SELECT SUM(p.amount_paid) AS monthly_paid
                                  FROM payments p
                                  JOIN cases c ON p.case_id = c.case_id
                                  WHERE p.received IS NULL OR p.received !=1 AND DATE_FORMAT(p.payment_date, '%Y-%m') = :current_month AND (c.client_id = :client_id OR FIND_IN_SET(:client_id, c.plaintiff))";

        try {
            // تنفيذ استعلام عدد الجلسات القادمة
            $statement_open_sessions = $conn->prepare($query_open_sessions);
            $statement_open_sessions->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statement_open_sessions->execute();
            $open_sessions_result = $statement_open_sessions->fetch(PDO::FETCH_ASSOC);

            // تنفيذ استعلام عدد الجلسات الكلي
            $statement_total_sessions = $conn->prepare($query_total_sessions);
            $statement_total_sessions->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statement_total_sessions->execute();
            $total_sessions_result = $statement_total_sessions->fetch(PDO::FETCH_ASSOC);

            // تنفيذ استعلام عدد القضايا الكلي
            $statement_total_cases = $conn->prepare($query_total_cases);
            $statement_total_cases->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statement_total_cases->execute();
            $total_cases_result = $statement_total_cases->fetch(PDO::FETCH_ASSOC);

            // حساب النسبة
            $percentage = 0;
            if ($total_sessions_result && $total_sessions_result['all_session_count'] != 0) {
                $percentage = ($open_sessions_result['sessions_coming'] / $total_sessions_result['all_session_count']) * 100;
            }

            // تنفيذ استعلام الدفعات المستحقة للشهر الحالي
            $statement_monthly_payment = $conn->prepare($query_monthly_payment);
            $statement_monthly_payment->bindParam(':current_month', $current_month, PDO::PARAM_STR);
            $statement_monthly_payment->bindParam(':client_id', $user_id, PDO::PARAM_INT);
            $statement_monthly_payment->execute();
            $monthly_payment_result = $statement_monthly_payment->fetch(PDO::FETCH_ASSOC);

            // التأكد من وجود القيم الافتراضية
            $monthly_payment_result = $monthly_payment_result ?? ['monthly_paid' => 0];

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Office ID not found.";
    }

} else {
    header("Location: ../logout.php");
    exit;
}
?>
