<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['client_id'])) {

    if ($_SESSION['role'] == 'Admins') {
      
       include "../DB_connection.php";
       include "logo.php";

       include 'permissions_script.php';
        if ($pages['clients']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

       include "data/client.php";

       
       
       
      
       $client_id = $_GET['client_id'];
       $client = getClientById($client_id, $conn);

       if ($client == 0) {
         header("Location: clients.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Edit Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">

    
  
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    

    

    <!-- <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">


  <style>
    *{
      direction: rtl;
    }
    .error {
        border-color: #dc3545 !important; /* Change border color for invalid fields */
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

        .iti {
            position: relative;
            display: block;
        }

        .iti__country-list {
            left:0;
        }

  </style>
</head>
<body>
<?php include "inc/footer.php"; ?>  
<?php include "inc/navbar.php"; ?>
    <div class="container mt-5">
    <div class="btn-group" style="direction:ltr;">
        <a href="users.php" class="btn btn-secondary">إدارة المستخدمين</a>   
        <a href="clients.php" class="btn btn-dark">الموكلين</a>
    </div>
        <form method="post" class="shadow p-3 mt-3 form-w" action="req/client-edit.php" id="clientForm">
            <h3>تعديل معلومات الموكل</h3><hr>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?=$_GET['error']?>
                </div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?=$_GET['success']?>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" class="form-control" value="<?=$client['first_name']?>" name="fname">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الدور</label>
                    <select class="form-select" name="role_id" id="role_id">
                    <?php
                        $admin_id = $_SESSION['user_id'];

                        // الحصول على المكاتب المرتبطة بالآدمن
                        $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                        $stmt_offices = $conn->prepare($sql_offices);
                        $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                        $stmt_offices->execute();
                        $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                        if ($offices) {
                            $office_ids = implode(',', $offices);

                            // جلب الأدوار المرتبطة بالمكاتب
                            $sql = "SELECT p.power_id, p.role 
                                    FROM powers p
                                    WHERE office_id IN ($office_ids) OR p.default_role_client = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                foreach ($result as $row) {
                                    $id = $row["power_id"];
                                    $role = $row["role"];
                                    $selected = ($id == $client['role_id']) ? "selected" : "";
                                    echo "<option value='$id' $selected>$role</option>";
                                }
                            } else {
                                echo "<option value='' disabled>لا توجد أدوار مرتبطة</option>";
                            }
                        } else {
                            echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                        }
                        ?>

                    </select>
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label">المكتب</label>
                <select id="office_id" class="form-select" name="office_id" onchange="fetchLawyers()">
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php

                                    // جلب المكاتب المرتبطة بالآدمن
                                    $sql_offices = "SELECT `office_id`, `office_name` FROM offices WHERE admin_id = :admin_id";
                                    $stmt_offices = $conn->prepare($sql_offices);
                                    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                    $stmt_offices->execute();
                                    $result = $stmt_offices->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt_offices->rowCount() > 0) {
                                        foreach ($result as $row) {
                                            $id = $row["office_id"];
                                            $office_id = $client['office_id'];
                                            $office_name = $row["office_name"];
                                            $selected = ($id == $office_id) ? "selected" : "";
                                            echo "<option value='$id' $selected>$office_name</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                                    }
                                    ?>

                            </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">المحامي</label>
                    <select id="lawyer_id" class="form-select" name="lawyer_id" data-selected-lawyer="<?=$client['lawyer_id']?>" style="display:none;">
                        <option value="" disabled>اختر المحامي</option>
                        <!-- خيارات المحامين ستُضاف هنا بواسطة JavaScript -->
                    </select>

                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العائلة</label>
                    <input type="text" class="form-control" value="<?=$client['last_name']?>" name="lname">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الأب</label>
                    <input type="text" class="form-control" value="<?=$client['father_name']?>" name="father_name" id="father_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الجد</label>
                    <input type="text" class="form-control" value="<?=$client['grandfather_name']?>" name="grandfather_name" id="grandfather_name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان</label>
                    <input type="text" class="form-control" value="<?=$client['address']?>" name="address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الإيميل</label>
                    <input type="email" class="form-control" value="<?=$client['email']?>" name="email">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">سنة الولادة</label>
                    <input type="date" class="form-control" value="<?=$client['date_of_birth']?>" name="date_of_birth">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الجنس</label><br>
                    <input type="radio" value="Male" <?php if($client['gender'] == 'Male') echo 'checked'; ?> name="gender"> ذكر
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" value="Female" <?php if($client['gender'] == 'Female') echo 'checked'; ?> name="gender"> أنثى
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم القومي</label>
                    <input type="text" class="form-control" value="<?=$client['national_num']?>" name="national_num">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم جواز السفر</label>
                    <input type="text" class="form-control" value="<?=$client['client_passport']?>" name="client_passport">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">استلام التذكيرات عبر الإيميل</label>
                    <input type="checkbox" class="form-check-input" name="receive_emails" <?php if($client['receive_emails'] == 1) echo 'checked'; ?> value="1">
                </div>

               

                <div class="col-md-6 mb-3">
                    <label class="form-label">استلام الرسائل عبر الواتس آب</label>
                    <input type="checkbox" class="form-check-input" name="receive_whatsupp" <?php if($client['receive_whatsupp'] == 1) echo 'checked'; ?> value="1">
                </div>
           
                <div class="col-md-6 mb-3">
                    <label class="form-label">الهاتف</label>
                    <div style="min-width:100%;">
                    <input style="direction:ltr;" type="tel" id="clientPhone" class="form-control" value="<?=$client['phone']?>" name="phone">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" class="form-control" value="<?=$client['city']?>" name="city">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الحي</label>
                    <input type="text" class="form-control" value="<?=$client['alhi']?>" name="alhi">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الشارع</label>
                    <input type="text" class="form-control" value="<?=$client['street_name']?>" name="street_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم المبنى</label>
                    <input type="text" class="form-control" value="<?=$client['num_build']?>" name="num_build">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الوحدة</label>
                    <input type="text" class="form-control" value="<?=$client['num_unit']?>" name="num_unit">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرمز البريدي</label>
                    <input type="text" class="form-control" value="<?=$client['zip_code']?>" name="zip_code">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم الفرعي</label>
                    <input type="text" class="form-control" value="<?=$client['subnumber']?>" name="subnumber">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" value="<?=$client['username']?>" name="username">
                </div>

                <div class="col-md-6 mb-3" >
                    <label class="form-label">كلمة السر</label>
                    <div class="input-group" style="direction:ltr;">
                        <input style="direction:ltr;"   type="text" class="form-control" name="pass" id="passInput">
                        <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                    </div>
                </div>
                <div class="col-md-6 mt-5" >
                    <div class="input-group" >
                    <div class="form-check custom-checkbox">
                    <label class="form-label mx-2" for="stop">إيقاف مؤقت</label>    
                    <input type="checkbox" class="form-check-input" id="stop" name="stop" <?php if($client['stop'] == 1) echo 'checked'; ?> value="1">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">تاريخ الإيقاف</label>
                    <input type="date" class="form-control" value="<?=$client['stop_date']?>" name="stop_date">
                </div>
                
         
            </div>

            <input type="hidden" value="<?=$client['client_id']?>" name="client_id">
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-sm w-100" style="min-width:250px;" >تحديث</button>
            </div>
        </form>
    </div>

     
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    

    <script>
    document.getElementById('clientForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        var form = event.target;
        var requiredFields = [
            { name: 'fname', message: 'يرجى ملء حقل الاسم.' },
            { name: 'username', message: 'يرجى ملء حقل اسم المستخدم.' },
            { name: 'email', message: 'يرجى ملء حقل الإيميل.' },
            { name: 'gender', message: 'يرجى اختيار الجنس.' },
            { name: 'phone', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'lname', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'role_id', message: 'يرجى اختيار الدور.' }
        ];

        var isValid = true;
        var firstInvalidField = null;

        requiredFields.forEach(function(field) {
            var input = form.querySelector(`[name="${field.name}"]`);
            if (!input || !input.value) {
                input.classList.add('error');
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = input;
                }
            } else {
                input.classList.remove('error');
            }
        });

        // Check for select field specifically
        var roleSelect = form.querySelector('#role_id');
        if (!roleSelect.value) {
            roleSelect.classList.add('error');
            isValid = false;
            if (!firstInvalidField) {
                firstInvalidField = roleSelect;
            }
        } else {
            roleSelect.classList.remove('error');
        }

        if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'يرجى ملء جميع الحقول المطلوبة.',
                });
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        } else {
            form.submit(); // Submit the form if all fields are valid
        }
    });


    </script>
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(3) a").addClass('active');
        });

        function makePass(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
              result += characters.charAt(Math.floor(Math.random() * 
         charactersLength));

           }
           var passInput = document.getElementById('passInput');
           passInput.value = result;
        }

        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
          e.preventDefault();
          makePass(8);
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#clientPhone");
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





<script>
function fetchLawyers() {
    var officeId = document.getElementById('office_id').value;
    var lawyerSelect = document.getElementById('lawyer_id');
    var selectedLawyerId = lawyerSelect.getAttribute('data-selected-lawyer'); // الحصول على المحامي الحالي المختار
    
    if (officeId) {
        // قم بعمل طلب AJAX للحصول على المحامين بناءً على office_id
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'api/fetch_lawyers.php?office_id=' + officeId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                lawyerSelect.style.display = 'block';
                lawyerSelect.innerHTML = '<option value="">اختر المحامي</option>'; // إعادة تعيين الخيارات
                response.forEach(function(lawyer) {
                    var option = document.createElement('option');
                    option.value = lawyer.lawyer_id;
                    option.textContent = lawyer.lawyer_name;
                    if (lawyer.lawyer_id == selectedLawyerId) {
                        option.selected = true; // تحديد المحامي الحالي
                    }
                    lawyerSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    } else {
        lawyerSelect.style.display = 'none';
        lawyerSelect.innerHTML = '<option value="" disabled>اختر المحامي</option>';
    }
}

// استدعاء الدالة عند تحميل الصفحة إذا كان هناك مكتب محدد
document.addEventListener('DOMContentLoaded', function() {
    var officeId = document.getElementById('office_id').value;
    if (officeId) {
        fetchLawyers();
    }
});
    </script>

</body>
</html>
<?php 

  }else {
    header("Location: cases.php");
    exit;
  } 
}else {
	header("Location: cases.php");
	exit;
} 

?>