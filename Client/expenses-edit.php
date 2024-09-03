<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

    if ($_SESSION['role'] == 'Client') {
      
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';

        if ($pages['expenses']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
       

       function getExpenseById($id, $conn){
        $sql = "SELECT 
                 * FROM `overhead_costs` WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
     
        if ($stmt->rowCount() == 1) {
          $exp = $stmt->fetch();
          return $exp;
        } else {
         return 0;
        }
     }
       
       
       
       $id = $_GET['id'];
       $exp = getExpenseById($id, $conn);

       if ($exp == 0) {
         header("Location: genral_expenses.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Expenses</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
  <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />

  <style>
        body {
            direction: rtl;
            background-color: #f8f9fa; /* لون خلفية للصفحة */

        }

        .form-w {
            background: #fff; /* لون خلفية النموذج */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* ظل للنموذج */
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }

        @media (min-width: 576px) {
            .form-row {
                display: flex;
                gap: 10px;
            }

            .form-row .mb-3 {
                flex: 1;
                margin: 0;
            }

            .form-row .btn-primary {
                width: auto;
            }
        }
        .custom-checkbox .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            margin: 0;
            padding: 0;
            border: 2px solid #007bff; /* لون الحدود */
            border-radius: 5px;
            position: relative;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .custom-checkbox .form-check-input:checked {
            background-color: #007bff; /* لون الخلفية عند التحديد */
            border-color: #007bff; /* لون الحدود عند التحديد */
        }

        .custom-checkbox .form-check-input:checked::after {
            content: '\2714'; /* علامة الصح البيضاء */
            font-size: 16px;
            color: white; /* لون علامة الصح */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-checkbox .form-check-label {
            margin-right: 5px; /* تعديل المسافة بين النص والعلامة */
            cursor: pointer;
            display: inline-block;
            vertical-align: middle; /* لمحاذاة النص بشكل أفضل مع الـ checkbox */
            line-height: 1.5; /* تعديل طول السطر */
        }
        .form-label{
            cursor: pointer;
        }
        .bootstrap-datetimepicker-widget{
            bottom: 0 !important;
            right: auto !important;
        }
        .data-switch-button{
            display:none !important;
        }
    </style>
</head>
<body>
    <?php 
        include "inc/navbar.php";
        include "inc/footer.php";
    ?>
    
    <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">        
            <a href="genral_expenses.php" class="btn btn-secondary">رجوع</a>  
        </div>
        <div class="row">
            <div class="col-md-12">
            <form id="expenseForm" class="shadow p-3 form-w">
                <h3>تعديل معلومات المصروف</h3><hr>
                <div class="row mb-3">
                    <input type="hidden" value="<?=$OfficeId?>" name="office_id" id="officeIdModal">
                    <div class="col-md-6">
                        <label class="form-label" for="modalAmount" aria-label="المبلغ">المبلغ</label>
                        <input type="number" name="amount" id="modalAmount" class="form-control" required aria-label="المبلغ" value="<?=$exp['amount']?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cost_type" aria-label="النوع">النوع</label>
                        <select id="cost_type" class="form-select" name="cost_type" required aria-label="اختر النوع">
                            <option value="" disabled selected>اختر النوع...</option>
                            <?php
                                    if (!empty($OfficeId)) {
                                        // جلب الأنواع المرتبطة بالمكاتب
                                        $sql_types = "SELECT DISTINCT ct.id, ct.type 
                                                    FROM costs_type ct
                                                    WHERE ct.office_id IN ($OfficeId)
                                                    ORDER BY ct.id DESC";
                                        $stmt_types = $conn->prepare($sql_types);
                                        $stmt_types->execute();
                                        $result54 = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

                                        if ($stmt_types->rowCount() > 0) {
                                            foreach ($result54 as $row) {
                                                $type_id = $row["id"];
                                                $type = $row["type"];
                                                $id_type = $exp['type_id'];
                                                $selectedType = ($type_id == $id_type) ? "selected" : "";
                                                echo "<option value='$type_id' aria-label='$type' $selectedType>$type</option>";
                                            }
                                        } else {
                                            echo "<option value='' disabled>لا توجد أنواع مرتبطة</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة بالآدمن</option>";
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                   
                    <div class="col-md-6">
                        <label class="form-label" for="dateGeo" aria-label="التاريخ ميلادي">التاريخ ميلادي</label>
                        <input type="date" class="form-control" id="dateGeo" name="date_geo" required aria-label="التاريخ ميلادي" value="<?=$exp['pay_date']?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="date_hijri" aria-label="التاريخ هجري">التاريخ هجري</label>
                        <input type="text" class="form-control" id="date_hijri" name="date_hijri" required aria-label="التاريخ هجري" value="<?=$exp['pay_date_hijri']?>" />
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="notes" aria-label="ملاحظات">ملاحظات</label>
                        <textarea name="notes" id="notes" class="form-control" rows="4" aria-label="ملاحظات"><?=$exp['notes_expenses']?></textarea>
                    </div>
                </div>
                <input type="hidden" value="<?=$exp['id']?>" name="id">
                <button type="" class="btn btn-primary">تحديث</button>
            </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
    <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
    <script src="../js/libraries/sweetalert2.min.js"></script>
    <script>
            document.addEventListener("DOMContentLoaded", function() {
        // دالة لتحويل التاريخ الميلادي إلى هجري
        function convertToHijri(gregorianDate) {
            if (gregorianDate) {
                return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
            }
            return '';
        }

        // دالة لتحويل التاريخ الهجري إلى ميلادي
        function convertToGregorian(hijriDate) {
            if (hijriDate) {
                return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
            }
            return '';
        }

        // مراقبة التغيرات في حقول التواريخ
        function attachDateChangeEvents() {
            var hijriInput = document.getElementById('date_hijri');
            var gregorianInput = document.getElementById('dateGeo');

            gregorianInput.addEventListener('input', function() {
                hijriInput.value = convertToHijri(gregorianInput.value);
            });

            hijriInput.addEventListener('change', function() {
                gregorianInput.value = convertToGregorian(hijriInput.value);
            });

            $(hijriInput).hijriDatePicker({
                locale: "ar-sa",
                format: "DD-MM-YYYY",
                hijriFormat: "iYYYY-iMM-iDD",
                dayViewHeaderFormat: "MMMM YYYY",
                hijriDayViewHeaderFormat: "iMMMM iYYYY",
                showSwitcher: true,
                allowInputToggle: true,
                useCurrent: false,
                isRTL: true,
                viewMode: 'days',
                keepOpen: false,
                hijri: true,
                debug: false,
                showClear: true,
                showClose: true
            }).on('dp.change', function(e) {
                gregorianInput.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
            });
        }

        // استدعاء الدالة لمراقبة التغيرات
        attachDateChangeEvents();
    });           
    </script>                     
    <script>
        $(document).ready(function() {
            $('#expenseForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'req/update_expense.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التحديث بنجاح',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'حدث خطأ',
                                text: res.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'حدث خطأ',
                            text: 'تعذر تحديث البيانات',
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php 

  }else {
    header("Location: lawyers.php");
    exit;
  } 
}else {
	header("Location: login.php");
	exit;
} 

?>