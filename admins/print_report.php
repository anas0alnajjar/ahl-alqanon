<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {

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

        // Fetch expenses with type
        $sql_expenses = "SELECT expenses.*
                        FROM expenses 
                        WHERE case_id = :case_id ORDER BY id DESC";
        $stmt_expenses = $conn->prepare($sql_expenses);
        $stmt_expenses->bindParam(':case_id', $id); 
        $stmt_expenses->execute();
        $expenses = $stmt_expenses->fetchAll();

        // Fetch payments
        $sql_payment = "SELECT * FROM payments WHERE case_id = :case_id ORDER BY id DESC";
        $stmt_payment = $conn->prepare($sql_payment);
        $stmt_payment->bindParam(':case_id', $id); 
        $stmt_payment->execute();
        $payments = $stmt_payment->fetchAll();

        // Calculate total expenses and payments
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
            $totalExpenses += $expense['amount'];
        }

        $totalPayments = 0;
        foreach ($payments as $payment) {
            $totalPayments += $payment['amount_paid'];
        }

        $balance = $totalExpenses -  $totalPayments ;
        $isDeficit = $balance < 0;
        $balance = abs($balance);

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
    
    <title>تقرير القضية</title>
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

        .balance-positive {
            color: red;
        }

        .balance-negative {
            color: green;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .print-button {
            margin: 20px 0;
            text-align: center;
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
        <h1>تقرير الاستحقاق المالي</h1>
        <p style="text-align:right;">اسم المحامي: <?php echo htmlspecialchars($caseData['lawyer_name']); ?></p>
        <p style="text-align:right;">اسم الموكل: <?php echo htmlspecialchars($caseData['client_first_name'] . ' ' . $caseData['client_last_name']); ?></p>
    </header>
    <h2>مصاريف الجلسات</h2>
    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>    
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['pay_date']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($expense['amount'], 0)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>الإجمالي</td>
                        <td><?php echo htmlspecialchars(number_format($totalExpenses, 0)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <h2>المدفوعات</h2>
    <div class="card">
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>وضع الاستلام</th>
                        <th>طريقة الدفع</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td style="color: <?php echo $payment['received'] == 1 ? 'green' : 'red'; ?>">
                                <?php echo $payment['received'] == 1 ? 'مستلمة' : 'غير مستلمة'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($payment['amount_paid'], 0)); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">الإجمالي</td>
                        <td colspan="2"><?php echo htmlspecialchars(number_format($totalPayments, 0)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <h2>الميزانية</h2>
    <div class="card">
        <div class="card-body">
            <p>الإجمالي للمصروفات المرتبطة بالجلسات: <?php echo htmlspecialchars(number_format($totalExpenses, 0)); ?> </p>
            <p>الإجمالي للمدفوعات: <?php echo htmlspecialchars(number_format($totalPayments, 0)); ?> </p>
        </div>
    </div>
    <div class="print-button no-print">
        <button onclick="window.history.back()" class="btn btn-dark no-print">رجوع</button>
        <a href="home.php" class="btn btn-secondary no-print">الصفحة الرئيسية</a>
        <button onclick="printPage()" class="btn btn-primary no-print">طباعة</button>
    </div>
    <footer>
        <p>&copy; <?=$footer_text?> </p>
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
