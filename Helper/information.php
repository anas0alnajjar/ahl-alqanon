<?php
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Helper') {

    // استدعاء ملف الاتصال بقاعدة البيانات هنا
    include "../DB_connection.php";
    include "get_office.php";

    $user_id = $_SESSION['user_id'];
    $office_id = getOfficeId($conn, $user_id);

    if ($office_id !== null) {
        // استعلام لاحتساب عدد الموكلين المرتبطين بالمساعد
        $query_clients_count = "SELECT COUNT(DISTINCT clients.client_id) AS clients_count 
                                FROM clients 
                                JOIN cases ON (clients.client_id = cases.client_id OR FIND_IN_SET(clients.client_id, cases.plaintiff))
                                WHERE FIND_IN_SET(:helper_id, cases.helper_name)";
        
        // استعلام لاحتساب عدد الجلسات القادمة المرتبطة بالمساعد
        $query_open_sessions = "SELECT COUNT(*) AS sessions_coming
                                FROM sessions
                                LEFT JOIN cases ON cases.case_id = sessions.case_id 
                                WHERE session_date > CURRENT_DATE AND FIND_IN_SET(:helper_id, cases.helper_name)";

        // استعلام لاحتساب عدد الجلسات الكلي المرتبطة بالمساعد
        $query_total_sessions = "SELECT COUNT(*) AS all_session_count
                                 FROM sessions
                                 LEFT JOIN cases ON cases.case_id = sessions.case_id
                                 WHERE FIND_IN_SET(:helper_id, cases.helper_name)";

        // استعلام لاحتساب عدد القضايا الكلي المرتبطة بالمساعد
        $query_total_cases = "SELECT COUNT(*) AS total_cases_count FROM cases WHERE FIND_IN_SET(:helper_id, helper_name)";

        $current_month = date('Y-m');

        // استعلام لاحتساب التكاليف المرتبطة بالشهر الحالي والمرتبطة بالمساعد
        $query_monthly_costs = "SELECT SUM(e.amount) AS monthly_costs
                                FROM expenses e 
                                JOIN cases c ON e.case_id = c.case_id
                                WHERE DATE_FORMAT(e.pay_date, '%Y-%m') = :current_month AND FIND_IN_SET(:helper_id, c.helper_name)";

        // الدفعات المستحقة للشهر الحالي المرتبطة بالمساعد
        $query_monthly_payment = "SELECT SUM(p.amount_paid) AS monthly_paid
                                  FROM payments p
                                  JOIN cases c ON p.case_id = c.case_id
                                  WHERE p.received IS NULL AND DATE_FORMAT(p.payment_date, '%Y-%m') = :current_month AND FIND_IN_SET(:helper_id, c.helper_name)";

        $query_monthly_payment_total = "SELECT SUM(p.amount_paid) AS monthly_paid
                                        FROM payments p
                                        JOIN cases c ON p.case_id = c.case_id
                                        WHERE DATE_FORMAT(p.payment_date, '%Y-%m') = :current_month AND FIND_IN_SET(:helper_id, c.helper_name)";

        try {
            // تنفيذ استعلام عدد الموكلين
            $statement_clients_count = $conn->prepare($query_clients_count);
            $statement_clients_count->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_clients_count->execute();
            $clients_count_result = $statement_clients_count->fetch(PDO::FETCH_ASSOC);
            
            // تنفيذ استعلام عدد الجلسات القادمة
            $statement_open_sessions = $conn->prepare($query_open_sessions);
            $statement_open_sessions->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_open_sessions->execute();
            $open_sessions_result = $statement_open_sessions->fetch(PDO::FETCH_ASSOC);

            // تنفيذ استعلام عدد الجلسات الكلي
            $statement_total_sessions = $conn->prepare($query_total_sessions);
            $statement_total_sessions->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_total_sessions->execute();
            $total_sessions_result = $statement_total_sessions->fetch(PDO::FETCH_ASSOC);

            // تنفيذ استعلام عدد القضايا الكلي
            $statement_total_cases = $conn->prepare($query_total_cases);
            $statement_total_cases->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_total_cases->execute();
            $total_cases_result = $statement_total_cases->fetch(PDO::FETCH_ASSOC);

            // حساب النسبة
            $percentage = 0;
            if ($total_sessions_result && $total_sessions_result['all_session_count'] != 0) {
                $percentage = ($open_sessions_result['sessions_coming'] / $total_sessions_result['all_session_count']) * 100;
            }

            // استعلام حساب المصروفات الشهرية
            $statement_monthly_costs = $conn->prepare($query_monthly_costs);
            $statement_monthly_costs->bindParam(':current_month', $current_month, PDO::PARAM_STR);
            $statement_monthly_costs->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_monthly_costs->execute();
            $monthly_costs_result = $statement_monthly_costs->fetch(PDO::FETCH_ASSOC);

            // استعلام حساب الدفعات الشهرية
            $statement_monthly_payment = $conn->prepare($query_monthly_payment);
            $statement_monthly_payment->bindParam(':current_month', $current_month, PDO::PARAM_STR);
            $statement_monthly_payment->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_monthly_payment->execute();
            $monthly_payment_result = $statement_monthly_payment->fetch(PDO::FETCH_ASSOC);

            // استعلام حساب الدفعات الشهرية الكلية
            $statement_monthly_payment_total = $conn->prepare($query_monthly_payment_total);
            $statement_monthly_payment_total->bindParam(':current_month', $current_month, PDO::PARAM_STR);
            $statement_monthly_payment_total->bindParam(':helper_id', $user_id, PDO::PARAM_INT);
            $statement_monthly_payment_total->execute();
            $monthly_payment_result_total = $statement_monthly_payment_total->fetch(PDO::FETCH_ASSOC);

            // التأكد من وجود القيم الافتراضية
            $monthly_costs_result = $monthly_costs_result ?? ['monthly_costs' => 0];
            $monthly_payment_result_total = $monthly_payment_result_total ?? ['monthly_paid' => 0];

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
