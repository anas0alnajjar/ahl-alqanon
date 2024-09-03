<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Lawyer') {
        include "../DB_connection.php";
        include 'permissions_script.php';
        include "logo.php";

        if ($pages['cases']['add'] == 0) {
            header("Location: home.php");
            exit();
        }
        
        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Case</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />


    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    

    <!-- تضمين ملفات SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

<style>
    *{
        direction: rtl;
    }
    .invalid {
    border: 1px solid #dc3545 !important;
    }
    	/* تنسيق الجدول للشاشات الصغيرة */
	@media screen and (max-width: 767px) {
		.table-responsive {
			overflow-x: auto;
		}
		.table-responsive table {
			width: 100%;
			max-width: 100%;
		}
		.table-responsive .table-bordered th,
		.table-responsive .table-bordered td {
			white-space: nowrap;
		}
		.table-responsive th,
		.table-responsive td {
			padding: 0.5rem;
			vertical-align: top;
			border-top: 1px solid #dee2e6;
		}
		.table-responsive th {
			font-weight: bold;
		}
		.table-responsive thead th {
			vertical-align: bottom;
		}}
        
        .card .form-group {
    margin-bottom: 1rem;
}

        .card-body {
            padding: 1rem;
        }

        .btn-block {
            width: 100%;
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
        .error {
        border-color: #dc3545 !important; /* Change border color for invalid fields */
    }
    .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }
                select {
                max-height: 35px !important;
                display:none;
        }
</style>
</head>
<body>
<?php include "inc/footer.php"; ?>  
    
<!-- Add Client Modal -->
<div class="modal fade" id="edit_client_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
            
                <h5 class="modal-title" id="modalLabel">إضافة موكل</h5>
                <button type="button" class="close close-modal2" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 65vh;overflow:auto;">
                <!-- Form for adding a client -->
                <form method="post" class="shadow p-3 mt-4" id="clientAdd" action="req/client-add.php">
                    <!-- Form fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fname" class="form-label">الاسم الأول</label>
                            <input type="text" class="form-control" name="first_name" id="fname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lname" class="form-label">العائلة</label>
                            <input type="text" class="form-control" name="last_name" id="lname" required>
                        </div>
                        <input type="hidden" value="<?=$OfficeId?>" name="office_id">
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">اسم الأب</label>
                            <input type="text" class="form-control" name="father_name" id="father_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="grandfather_name" class="form-label">اسم الجد</label>
                            <input type="text" class="form-control" name="grandfather_name" id="grandfather_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <input type="text" class="form-control" name="address" id="address">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_address" class="form-label">الإيميل</label>
                            <input type="email" class="form-control" name="email" id="email_address" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">سنة الولادة</label>
                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" name="gender" checked> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" name="gender"> أنثى
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="national_num" class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" name="national_num" id="national_num">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="client_passport" class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" name="client_passport" id="client_passport">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" class="form-control" name="city" id="city">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="phone" class="form-control"  name="phone">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="alhi" class="form-label">الحي</label>
                            <input type="text" class="form-control" name="alhi" id="alhi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="street_name" class="form-label">اسم الشارع</label>
                            <input type="text" class="form-control" name="street_name" id="street_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="num_build" class="form-label">رقم المبنى</label>
                            <input type="text" class="form-control" name="num_build" id="num_build">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="num_unit" class="form-label">رقم الوحدة</label>
                            <input type="text" class="form-control" name="num_unit" id="num_unit">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zip_code" class="form-label">الرمز البريدي</label>
                            <input type="text" class="form-control" name="zip_code" id="zip_code">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subnumber" class="form-label">الرقم الفرعي</label>
                            <input type="text" class="form-control" name="subnumber" id="subnumber">
                            <input type="hidden"  name="lawyer_id" value="<?=$user_id?>">
                            <input type="hidden"  name="office_id" value="<?=$OfficeId?>" id="office_idModal">
                        </div>
                    </div>
             
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal2" data-bs-dismiss="modal">إغلاق</button>    
                    <button id="addClient" type="submit" class="btn btn-primary">تسجيل</button>    
                    </div>
                </form>
        </div>
    </div>
</div>
<!-- End Modal -->



<?php include "inc/navbar.php"; ?>

    
<div class="container-fluid mt-5" style="max-width: 90%;">
<div class="btn-group" style="direction:ltr;">
    <a href="home.php" class="btn btn-light">الرئيسية</a>
    <a href="cases.php" class="btn btn-dark">الرجوع</a>
    </div>
    <?php include("data/form-case-add.php") ?>
</div>


    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>   
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
    <script src="../js/bootstrap-hijri-datetimepicker.js?v2"></script>
    <script src="../js/add_case.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('flexSwitchCheckChecked').addEventListener('change', function() {
        var label = document.getElementById('legal-number');
        if (this.checked) {
            label.textContent = 'رقم الوكالة';
        } else {
            label.textContent = 'رقم القضية';
        }
    });

    // لضبط النص عند تحميل الصفحة بناءً على حالة الـ checkbox
    window.addEventListener('load', function() {
        var checkbox = document.getElementById('flexSwitchCheckChecked');
        var label = document.getElementById('legal-number');
        if (checkbox.checked) {
            label.textContent = 'رقم الوكالة';
        } else {
            label.textContent = 'رقم القضية';
        }
    });
</script>

<!-- Sessions -->
 <script>
        document.addEventListener("DOMContentLoaded", function() {
            var addButton = document.getElementById('addRowBtn');
            var cardsContainer = document.getElementById('dynamic_cards');
            var rowIndex = 0;
            var lawyers = [];

            // دالة لجلب بيانات المحامين باستخدام AJAX
            function fetchLawyers() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'api/lawyers.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        lawyers = JSON.parse(xhr.responseText);
                    } else {
                        console.error('فشل في جلب بيانات المحامين');
                    }
                };
                xhr.onerror = function() {
                    console.error('حدث خطأ أثناء جلب بيانات المحامين');
                };
                xhr.send();
            }

            // استدعاء دالة جلب بيانات المحامين عند تحميل الصفحة
            fetchLawyers();

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
            function attachDateChangeEvents(card) {
                var gregorianInput = card.querySelector('.geo-data-input');
                var hijriInput = card.querySelector('.hijri-date-input');

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

            // إضافة صف جديد عند النقر على الزر
            addButton.addEventListener('click', function() {
                rowIndex++;

                // إضافة بطاقة
                var newCard = document.createElement('div');
                newCard.className = 'col-sm-12 col-md-6 col-lg-4 mb-3';



                newCard.innerHTML = `
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <label>رقم الجلسة</label>
                                <input type="text" class="form-control form-control-sm" name="session_number[]" required>
                            </div>
                            <div class="form-group">
                                <label>تاريخ الجلسة ميلادي</label>
                                <input type="date" class="form-control form-control-sm geo-data-input" name="session_date[]" required onchange="validateDate(this)">
                            </div>
                            <div class="form-group">
                                <label>تاريخ الجلسة هجري</label>
                                <input type="text" class="form-control form-control-sm hijri-date-input" name="session_date_hjri[]" required onchange="validateHijriDate(this)">
                            </div>
                            <div class="form-group">
                                <label>ساعة الجلسة</label>
                                <input type="time" class="form-control form-control-sm" name="session_hour[]" required>
                            </div>
                            <div class="form-group">
                                <label>الملاحظات</label>
                                <textarea class="form-control form-control-sm" name="notes[]" rows="2"></textarea>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm btn-block deleteCardBtn">حذف</button>
                        </div>
                    </div>
                `;
                newCard.dataset.rowIndex = rowIndex;
                cardsContainer.appendChild(newCard);
                attachDateChangeEvents(newCard);
                newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            // حذف البطاقة عند النقر على زر الحذف
            cardsContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('deleteCardBtn')) {
                    var card = event.target.closest('.col-sm-12.col-md-6.col-lg-4');
                    card.remove();
                }
            });
        });
    </script>





<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#phone");
            if (input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "auto",
                    geoIpLookup: function(callback) {
                        fetch('https://ipinfo.io/json', { headers: { 'Accept': 'application/json' }})
                            .then(response => response.json())
                            .then(data => callback(data.country))
                            .catch(() => callback("us"));
                    },
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });

                input.addEventListener('blur', function() {
                    var phoneNumber = input.value;
                    var regionCode = iti.getSelectedCountryData().iso2;
                    try {
                        var parsedNumber = libphonenumber.parsePhoneNumberFromString(phoneNumber, regionCode.toUpperCase());
                        if (parsedNumber && parsedNumber.isValid()) {
                            input.value = parsedNumber.formatInternational();
                        } else {
                            alert('الرجاء إدخال رقم هاتف صحيح');
                        }
                    } catch (error) {
                        alert('حدث خطأ أثناء معالجة الرقم، الرجاء المحاولة مرة أخرى');
                    }
                });
            }
        });
    </script>
       <?php if ($pages['add_old_session']['add'] == 0) : ?>
<script>
    function validateDate(input) {
        var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        var hijriInput = input.closest('.form-group').parentElement.querySelector('.hijri-date-input');

        if (input.value < today) {
            input.value = ''; // تفريغ الحقل الميلادي
            if (hijriInput) {
                hijriInput.value = ''; // تفريغ الحقل الهجري
                hijriInput.classList.add('is-invalid');
                hijriInput.classList.remove('is-valid');
            }
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
            if (hijriInput) {
                hijriInput.classList.add('is-valid');
                hijriInput.classList.remove('is-invalid');
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function attachEventListeners() {
            // التحقق من تواريخ الجلسات عند تغيير التاريخ الميلادي
            document.querySelectorAll('.geo-data-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    validateDate(input);
                });
            });
        }

        attachEventListeners();

        // التأكد من تطبيق المستمعين للأحداث عند إضافة بطاقات جديدة
        var addButton = document.getElementById('addRowBtn');
        addButton.addEventListener('click', function() {
            setTimeout(attachEventListeners, 500); // انتظر نصف ثانية لتطبيق المستمعين
        });

        // مراقبة التغييرات في قيم الحقول بشكل دوري
        var prevGeoValues = new Map();

        setInterval(function() {
            document.querySelectorAll('.geo-data-input').forEach(function(geoInput) {
                var currentValue = geoInput.value;
                var prevValue = prevGeoValues.get(geoInput) || '';
                if (currentValue !== prevValue) {
                    prevGeoValues.set(geoInput, currentValue);
                    validateDate(geoInput);
                }
            });
        }, 1000); // تحقق كل ثانية
    });
</script>
<?php endif; ?>




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
