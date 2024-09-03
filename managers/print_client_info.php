<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Managers') {

        include "../DB_connection.php"; // تأكد من تعديل هذا المسار ليتناسب مع مسار ملف الاتصال بقاعدة البيانات
        include "logo.php"; // تأكد من تعديل هذا المسار ليتناسب مع مسار ملف الاتصال بقاعدة البيانات

        $client_id = $_GET['id'];

        // Fetch client details
        $sql_client = "SELECT * FROM clients WHERE client_id=?";
        $stmt_client = $conn->prepare($sql_client);
        $stmt_client->execute([$client_id]);
        $client_info = $stmt_client->fetch();

        // Fetch cases count
        $sql_cases = "SELECT COUNT(case_id) AS cases FROM cases WHERE client_id=?";
        $stmt_cases = $conn->prepare($sql_cases);
        $stmt_cases->execute([$client_id]);
        $count_cases = $stmt_cases->fetch();

        // Fetch office details
        $office_id = $client_info['office_id'];
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
    
    <title>تقرير الموكل</title>
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
            font-weight: 700;
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
        <h1>معلومات الموكل</h1>
    </header>
    <div class="card">
        <div class="card-body">
            <table>
                <?php if (!empty($client_info['first_name'])): ?>
                <tr>
                    <th>الاسم الأول</th>
                    <td><?php echo $client_info['first_name'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['last_name'])): ?>
                <tr>
                    <th>اسم العائلة</th>
                    <td><?php echo $client_info['last_name'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['father_name'])): ?>
                <tr>
                    <th>اسم الأب</th>
                    <td><?php echo $client_info['father_name'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['grandfather_name'])): ?>
                <tr>
                    <th>اسم الجد</th>
                    <td><?php echo $client_info['grandfather_name'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['address'])): ?>
                <tr>
                    <th>العنوان</th>
                    <td><?php echo $client_info['address'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['email'])): ?>
                <tr>
                    <th>الإيميل</th>
                    <td><?php echo $client_info['email'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['date_of_birth'])): ?>
                <tr>
                    <th>سنة الولادة</th>
                    <td><?php echo $client_info['date_of_birth'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['gender'])): ?>
                <tr>
                    <th>الجنس</th>
                    <td><?php echo $client_info['gender'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['national_num'])): ?>
                <tr>
                    <th>الرقم الوطني</th>
                    <td><?php echo $client_info['national_num'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['phone'])): ?>
                <tr>
                    <th>الهاتف</th>
                    <td><?php echo $client_info['phone'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['city'])): ?>
                <tr>
                    <th>المدينة</th>
                    <td><?php echo $client_info['city'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['alhi'])): ?>
                <tr>
                    <th>الحي</th>
                    <td><?php echo $client_info['alhi'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['street_name'])): ?>
                <tr>
                    <th>اسم الشارع</th>
                    <td><?php echo $client_info['street_name'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['num_build'])): ?>
                <tr>
                    <th>رقم المبنى</th>
                    <td><?php echo $client_info['num_build'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['num_unit'])): ?>
                <tr>
                    <th>رقم الوحدة</th>
                    <td><?php echo $client_info['num_unit'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['zip_code'])): ?>
                <tr>
                    <th>الرمز البريدي</th>
                    <td><?php echo $client_info['zip_code'] ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($client_info['subnumber'])): ?>
                <tr>
                    <th>الرقم الفرعي</th>
                    <td><?php echo $client_info['subnumber'] ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>عدد القضايا</th>
                    <td><?php echo $count_cases['cases'] ?></td>
                </tr>
            </table>
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
