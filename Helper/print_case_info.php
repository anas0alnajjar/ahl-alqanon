<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Helper') {

        include "../DB_connection.php"; // تأكد من تعديل هذا المسار ليتناسب مع مسار ملف الاتصال بقاعدة البيانات
        include "logo.php"; // تأكد من تعديل هذا المسار ليتناسب مع مسار ملف الاتصال بقاعدة البيانات

        $id = $_GET['id'];

        // Fetch case details, lawyer name, and client name
        $query = "SELECT cases.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                  FROM cases 
                  LEFT JOIN lawyer ON cases.lawyer_id = lawyer.lawyer_id 
                  LEFT JOIN clients ON cases.client_id = clients.client_id
                  WHERE case_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $caseData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch sessions
        $sql_sessions = "SELECT * FROM sessions WHERE case_id = :case_id ORDER BY sessions_id DESC";
        $stmt_sessions = $conn->prepare($sql_sessions);
        $stmt_sessions->bindParam(':case_id', $id);
        $stmt_sessions->execute();
        $sessions = $stmt_sessions->fetchAll();

        // Fetch case type
        $case_type = null;
        if (!empty($caseData['case_type'])) {
            $sql_case_type = "SELECT type_case FROM types_of_cases WHERE id = :case_type";
            $stmt_case_type = $conn->prepare($sql_case_type);
            $stmt_case_type->bindParam(':case_type', $caseData['case_type']);
            $stmt_case_type->execute();
            $case_type = $stmt_case_type->fetchColumn();
        }

        // Fetch helpers
        $helpers = [];
        if (!empty($caseData['helper_id'])) {
            $helper_ids = explode(',', $caseData['helper_id']);
            $placeholders = implode(',', array_fill(0, count($helper_ids), '?'));
            $sql_helpers = "SELECT helper_name FROM helpers WHERE id IN ($placeholders)";
            $stmt_helpers = $conn->prepare($sql_helpers);
            $stmt_helpers->execute($helper_ids);
            $helpers = $stmt_helpers->fetchAll(PDO::FETCH_COLUMN);
        }

        // Fetch plaintiffs
        $plaintiffs = [];
        if (!empty($caseData['plaintiff'])) {
            $plaintiff_ids = explode(',', $caseData['plaintiff']);
            $placeholders = implode(',', array_fill(0, count($plaintiff_ids), '?'));
            $sql_plaintiffs = "SELECT CONCAT(first_name, ' ', last_name) AS name FROM clients WHERE client_id IN ($placeholders)";
            $stmt_plaintiffs = $conn->prepare($sql_plaintiffs);
            $stmt_plaintiffs->execute($plaintiff_ids);
            $plaintiffs = $stmt_plaintiffs->fetchAll(PDO::FETCH_COLUMN);
        }

        // Fetch defendants
        $defendants = [];
        if (!empty($caseData['defendant'])) {
            $defendant_ids = explode(',', $caseData['defendant']);
            $placeholders = implode(',', array_fill(0, count($defendant_ids), '?'));
            $sql_defendants = "SELECT CONCAT(fname, ' ', lname) AS name FROM adversaries WHERE id IN ($placeholders)";
            $stmt_defendants = $conn->prepare($sql_defendants);
            $stmt_defendants->execute($defendant_ids);
            $defendants = $stmt_defendants->fetchAll(PDO::FETCH_COLUMN);
        }

        // Fetch court name
        $court_name = null;
        if (!empty($caseData['court_name'])) {
            $sql_court = "SELECT court_name FROM courts WHERE id = :court_id";
            $stmt_court = $conn->prepare($sql_court);
            $stmt_court->bindParam(':court_id', $caseData['court_name']);
            $stmt_court->execute();
            $court_name = $stmt_court->fetchColumn();
        }

        // Fetch department name
        $department_name = null;
        if (!empty($caseData['department'])) {
            $sql_department = "SELECT type FROM departments WHERE id = :department_id";
            $stmt_department = $conn->prepare($sql_department);
            $stmt_department->bindParam(':department_id', $caseData['department']);
            $stmt_department->execute();
            $department_name = $stmt_department->fetchColumn();
        }

        // Fetch office details
        $office_id = $caseData['office_id'];
        $query_office = "SELECT * FROM offices WHERE office_id = :office_id";
        $stmt_office = $conn->prepare($query_office);
        $stmt_office->bindParam(':office_id', $office_id);
        $stmt_office->execute();
        $officeData = $stmt_office->fetch(PDO::FETCH_ASSOC);

        // Use office-specific header and footer if available, otherwise use defaults
        $header_image = !empty($officeData['header_image']) ? "../uploads/" . $officeData['header_image'] : "../img/" . $setting['logo'];
        $header_class = !empty($officeData['header_image']) ? "office-header" : "default-header";
        $footer_text = !empty($officeData['footer_text']) ? $officeData['footer_text'] : $setting['company_name'];
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    
    <title>معلومات القضية</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            margin: 20px;
        }

        header, footer {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .default-header img {
            width: 100px;
            height: auto;
        }

        .office-header img {
            width: 100%;
            height: 100px;
        }

        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
        }

        .card-body {
            padding: 10px;
        }

        h2 {
            font-size: 24px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            background-color: #f2f2f2;
        }

        tfoot {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .print-button {
            margin: 20px 0;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</head>
<body>
    <header class="<?=$header_class?>">
        <img src="<?=$header_image?>" alt="Logo">
        <h1>معلومات القضية</h1>
    </header>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($caseData['lawyer_name'])): ?>
                <p>اسم المحامي: <?php echo htmlspecialchars($caseData['lawyer_name']); ?></p>
            <?php endif; ?>
            <?php if (!empty($caseData['client_first_name']) || !empty($caseData['client_last_name'])): ?>
                <p>اسم الموكل: <?php echo htmlspecialchars($caseData['client_first_name'] . ' ' . $caseData['client_last_name']); ?></p>
            <?php endif; ?>
            <?php if (!empty($caseData['case_title'])): ?>
                <p>عنوان القضية: <?php echo htmlspecialchars($caseData['case_title']); ?></p>
            <?php endif; ?>
            <?php if (!empty($caseData['case_number'])): ?>
                <p>رقم القضية: <?php echo htmlspecialchars($caseData['case_number']); ?></p>
            <?php endif; ?>
            <?php if (!empty($case_type)): ?>
                <p>نوع القضية: <?php echo htmlspecialchars($case_type); ?></p>
            <?php endif; ?>
            <?php if (!empty($helpers)): ?>
                <p>الإداريين: <?php echo htmlspecialchars(implode(', ', $helpers)); ?></p>
            <?php endif; ?>
            <?php if (!empty($plaintiffs)): ?>
                <p>المدعي: <?php echo htmlspecialchars(implode(', ', $plaintiffs)); ?></p>
            <?php endif; ?>
            <?php if (!empty($defendants)): ?>
                <p>الخصم: <?php echo htmlspecialchars(implode(', ', $defendants)); ?></p>
            <?php endif; ?>
            <?php if (!empty($court_name)): ?>
                <p>اسم المحكمة: <?php echo htmlspecialchars($court_name); ?></p>
            <?php endif; ?>
            <?php if (!empty($department_name)): ?>
                <p>الدائرة: <?php echo htmlspecialchars($department_name); ?></p>
            <?php endif; ?>
            <?php if (!empty($caseData['judge_name'])): ?>
                <p>اسم القاضي: <?php echo htmlspecialchars($caseData['judge_name']); ?></p>
            <?php endif; ?>
            <?php if (!empty($caseData['id_picture'])): ?>
                <p>صورة هوية الموكل:</p>
                <img style="max-width:80%" src="../uploads/<?php echo $caseData['id_picture']; ?>" alt="ID Picture">
            <?php endif; ?>
        </div>
    </div>

    <h2>الجلسات</h2>
    <div class="card">
        <div class="card-body">
            <?php if (count($sessions) > 0): ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($session['session_number'])): ?>
                                <p>رقم الجلسة: <?php echo htmlspecialchars($session['session_number']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($session['session_date'])): ?>
                                <p>تاريخ الجلسة: <?php echo htmlspecialchars($session['session_date']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($session['session_hour'])): ?>
                                <p>ساعة الجلسة: <?php echo htmlspecialchars($session['session_hour']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>لا يوجد جلسات</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="print-button no-print">
        <button onclick="window.history.back()" class="btn btn-dark no-print">رجوع</button>
        <a href="home.php" class="btn btn-secondary no-print">الصفحة الرئيسية</a>
        <button onclick="printPage()" class="btn btn-primary no-print">طباعة</button>
    </div>

    <footer>
        <p>&copy; <?=$footer_text?></p>
    </footer>
</body>
</html>
<?php
    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
