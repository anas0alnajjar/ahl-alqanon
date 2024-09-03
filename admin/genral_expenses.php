<?php
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "logo.php";
    ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> -->
    <meta name="description" content="إدارة مستخدمين يتضمن الآدمن، المحامين، الإداريين، الموكلين ومديري المكاتب. قم بإدارة وتعديل الصلاحيات والبيانات لكل فئة من هؤلاء المستخدمين بكفاءة وسهولة.">

    <title>Admin - Expenses</title>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    
    

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_cases.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />




    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
        td, th {
            text-align: center;
            
        }
        div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
            padding-right: unset !important;
            max-height: 500px;
            overflow: auto;
            scroll-behavior: smooth;
            scrollbar-width: unset !important;
            scrollbar-color: black !important;
        }
        .dataTables_length{
            margin-bottom: 2%;
        }

        .modal-body .form-group, .modal-body .form-check {
            margin-bottom: 1rem;
        }
        .modal-body .form-check-inline {
            margin-right: 1rem;
            flex-basis: 45%;
        }

        label{
            cursor: pointer;
        }

        .form-check-inline {
            margin: 0 10px 10px 0; /* إضافة تباعد بين العناصر */
        }
        .form-check-label {
            margin-right: 5px; /* تباعد بسيط بين المدخل والتسمية */
        }
        .modal-body {
            max-height: 70vh; /* تحديد أقصى ارتفاع لمحتوى المودال */
            overflow-y: auto; /* إضافة تمرير عند تجاوز المحتوى للارتفاع المحدد */
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_paginate{
            direction: ltr;
        }
        #chart-container{
            width: 80%;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            #chart-container {
                width: 100%;
                margin: 0 auto;
            }
        }
        .hidden {
            display: none;
        }
        .message-card {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
            background-color: #f8f9fa;
            border: 2px dashed #d3d3d3;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #333;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
        }
        .message-card .icon {
            margin-right: 10px;
            color: #6c757d;
        }
        .bootstrap-datetimepicker-widget{
            top: 0 !important;
            bottom: auto !important;
            right: auto !important;
            left: 0 !important;
        }
        .data-switch-button{
            display:none !important;
        }
                .table-responsive {
            scrollbar-width: none !important;
        }
        #records_filter {
            display:none !important;
        }
    </style>
    
</head>

<body>



<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" style="text-align: right; direction: rtl;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="POST">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="officeIdModal" aria-label="المكتب">المكتب</label>
                            <select id="officeIdModal" class="form-select" name="office_id" required aria-label="اختر المكتب">
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php
                                    $sql = "SELECT `office_id`, `office_name` FROM offices";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $row["office_id"];
                                            $office_name = $row["office_name"];
                                            echo "<option value='$id' aria-label='$office_name'>$office_name</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="modalAmount" aria-label="المبلغ">المبلغ</label>
                            <input type="number" name="amount" id="modalAmount" class="form-control" required aria-label="المبلغ">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="cost_type" aria-label="النوع">النوع</label>
                            <select id="cost_type" class="form-select" name="cost_type" required aria-label="اختر النوع">
                                <option value="" disabled selected>اختر النوع...</option>
                                <?php
                                    $sql = "SELECT * FROM costs_type ORDER BY id DESC";
                                    $result54 = $conn->query($sql);
                                    if ($result54->rowCount() > 0) {
                                        while ($row = $result54->fetch(PDO::FETCH_ASSOC)) {
                                            $type_id = $row["id"];
                                            $type = $row["type"];
                                            echo "<option value='$type_id' aria-label='$type'>$type</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="dateGeo" aria-label="التاريخ ميلادي">التاريخ ميلادي</label>
                            <input type="date" class="form-control" id="dateGeo" name="date_geo" required aria-label="التاريخ ميلادي">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="date_hijri" aria-label="التاريخ هجري">التاريخ هجري</label>
                            <input type="text" class="form-control" id="date_hijri" name="date_hijri" required aria-label="التاريخ هجري">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label" for="notes" aria-label="ملاحظات">ملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4" aria-label="ملاحظات"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary colse555" data-dismiss="modal">إغلاق</button>
                <button id="saveExpe" type="button" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </div>
</div>




    


    <?php include "inc/navbar.php"; ?>
    <main class="container mt-5">
    
    <div class="filters row d-flex flex-wrap">
    <div class="col-12 col-md-3 mb-2">
        <label for="office_id" class="form-label">المكتب:</label>
        <select id="office_id" class="form-control" name="office_id">
            <option value="" selected>الكل</option>
            <?php
                $sql = "SELECT `office_id`, `office_name` FROM offices";
                $result = $conn->query($sql);
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $id = $row["office_id"];
                        $office_name = $row["office_name"];
                        echo "<option value='$id' $selected>$office_name</option>";
                    }
                }
            ?>
        </select>
    </div>
    <div class="col-12 col-md-3 mb-2">
        <label for="start_date" class="form-label">تاريخ البدء:</label>
        <input class="form-control" type="date" id="start_date">
    </div>
    <div class="col-12 col-md-3 mb-2">
        <label for="end_date" class="form-label">تاريخ النهاية:</label>
        <input class="form-control" type="date" id="end_date">
    </div>
    <div class="col-12 col-md-3 mb-2 align-self-end" >
        <div class="btn-group w-100" role="group" style="direction:ltr;" aria-label="Basic example">
            <button type="button" class="btn btn-dark btn-md" id="add_expense_btn">اضافة بند</button>
            <button type="button" class="btn btn-secondary btn-md" id="reser_button">الافتراضي</button>
            <button type="button" class="btn btn-primary btn-md" id="filter_button">فلترة</button>
        </div>
    </div>
    
</div>
      <div class="input-group text-center" style="max-width: 100%; min-width: 80%; direction: ltr;">
        <input type="text" class="form-control" id="searchInput" placeholder="ابحث هنا..." onkeyup="filterTable()">
        <div class="input-group-append">
            <button style="border-radius: 0;" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
    </div>
    <a href="home.php" class="btn btn-light btn-sm w-100 mt-3">الرئيسية</a>

        <br>
            <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-info mt-3 n-table" role="alert" style="max-width:100% !important;">
                        <?php 
                        if (isset($_GET['success'])) {
                            echo $_GET['success'];
                        } 
                        ?>
                    </div>
                <?php } ?>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                    <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <!-- Table -->
                            

                            <div class="table-responsive" style="padding:10px;">
                                <table id="records" class="display responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>المكتب</th>
                                            <th>المبلغ</th>
                                            <th>النوع</th>
                                            <th>التاريخ</th>
                                            <th>الاجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">الإجمالي</th>
                                            <th id="total_amount"></th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                            <div id="messageCard" class="hidden">
                                <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md lg:col-span-2 card message-card">
                                    <div class="flex justify-between mb-4 items-start">
                                        <div class="font-medium">
                                            <i class="icon fas fa-exclamation-circle"></i>
                                            لا توجد تكاليف مرتبطة بهذا المكتب
                                        </div>
                                    </div>
                                </div>
                            </div>
                                            <div id="expenseCard" class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-md lg:col-span-2 card hidden">
                            <div class="flex justify-between mb-4 items-start">
                                <div class="font-medium">مصروفات المكتب</div>
                            </div>
                            <div id="chart-container">
                                <canvas id="expenseChart"></canvas>
                            </div>
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                    </div>
                </div>
    </main>




       <!-- تضمين ملفات JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

    <script src="../js/script_expenses.js"></script>
    
    

    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(3) a").addClass('active');
        });
    </script>


            <script>
                $(document).ready(function(){ 
                    $('.colse555').on('click', function(){
                        $('#addModal').modal('hide');
                    });
                    $('#add_expense_btn').on('click', function(){
                        $('#addModal').modal('show');
                    });
                    $('.close').on('click', function(){
                        $('#addModal').modal('hide');
                    });
                });
            </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>    
    <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
    <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
    
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

    // دالة للتحقق من الحقول المطلوبة
    function validateFields(fields) {
        for (let field in fields) {
            // التحقق من أن الحقل غير ملاحظات وليس فارغاً
            if (fields[field] === '' && field !== 'notes') {
                return field;
            }
        }
        return null;
    }

    // دالة لحفظ البيانات باستخدام AJAX
    document.getElementById('saveExpe').addEventListener('click', function() {
        var formData = {
            office_id: document.getElementById('officeIdModal').value,
            amount: document.getElementById('modalAmount').value,
            cost_type: document.getElementById('cost_type').value,
            date_geo: document.getElementById('dateGeo').value,
            date_hijri: document.getElementById('date_hijri').value,
            notes: document.getElementById('notes').value
        };

        // تحقق من الحقول المطلوبة
        var invalidField = validateFields(formData);
        if (invalidField) {
            // عند وجود خطأ، قم بإيجاد العنصر الذي يتطابق مع الحقل غير المكتمل وقم بتلوينه بالأحمر والتركيز عليه
            var errorElement = document.querySelector(`[name=${invalidField}]`);
            errorElement.style.borderColor = 'red';
            errorElement.focus();

            // عرض رسالة للمستخدم
            Swal.fire({
                icon: 'error',
                title: 'حدث خطأ',
                text: 'يرجى تعبئة الحقل: ' + errorElement.getAttribute('aria-label'),
                confirmButtonText: 'حسناً'
            });
            return;
        }

        axios.post('req/save_overhead_costs.php', formData, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(function(response) {
            if (response.data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح',
                    text: 'تمت إضافة التكلفة بنجاح',
                    confirmButtonText: 'حسناً'
                });
                $('#addModal').modal('hide');
                window.location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'حدث خطأ',
                    text: response.data.message,
                    confirmButtonText: 'حسناً'
                });
            }
        })
        .catch(function(error) {
            Swal.fire({
                icon: 'error',
                title: 'حدث خطأ',
                text: 'لم يتم الحفظ، الرجاء المحاولة مرة أخرى',
                confirmButtonText: 'حسناً'
            });
        });
    });
});


</script>


</body>

</html>
<?php 
  } else {
    header("Location: ../login.php");
    exit;
  } 

?>