<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
      
       include "../DB_connection.php";
       include "logo.php";



       $lawyer_name = '';
       $lawyer_email = '';
       $lawyer_phone = '';
       $lawyer_address = '';
       $lawyer_city = '';
       $lawyer_username = '';
       $date_of_birth = '';


       $lawyer_national = '';
       $lawyer_passport = '';
       $role_id = '';



       if (isset($_GET['fname'])) $lawyer_name = $_GET['fname'];
       if (isset($_GET['email_address'])) $lawyer_email = $_GET['email_address'];
       if (isset($_GET['phone'])) $lawyer_phone = $_GET['phone'];
       if (isset($_GET['address'])) $lawyer_address = $_GET['address'];
       if (isset($_GET['email_address'])) $email_address = $_GET['email_address'];
       if (isset($_GET['date_of_birth'])) $date_of_birth = $_GET['date_of_birth'];
       if (isset($_GET['city'])) $lawyer_city = $_GET['city'];
       if (isset($_GET['username'])) $lawyer_username = $_GET['username'];

       if (isset($_GET['lawyer_national'])) $lawyer_national = $_GET['lawyer_national'];
       if (isset($_GET['lawyer_passport'])) $lawyer_passport = $_GET['lawyer_passport'];
       if (isset($_GET['role_id'])) $role_id = $_GET['role_id'];
       if (isset($_GET['office_id'])) $office_id = $_GET['office_id'];
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Add lawyer</title>
	
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <style>
body {
    direction: rtl;
    background: #f9f9f9;
}

.container {
    max-width: 900px;
    margin-right: 0; /* Initial value for small screens */
}

.error {
        border-color: #dc3545 !important; /* Change border color for invalid fields */
    }


.form-w {
    width: 100%;
    padding: 20px;
    margin: 20px 0;
    
    border-radius: 8px;
}

.form-label {
    font-weight: bold;
}

.btn-primary {
    width: 100%;
}

@media (min-width: 576px) {
    .btn-primary {
        width: auto;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
    }

    .form-row .form-group {
        flex: 1;
        min-width: calc(50% - 10px);
        margin: 5px;
    }
}

@media (min-width: 768px) {
    .container {
        max-width: 1200px;
        
    }
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .form-row > div {
        flex: 1 1 45%;
    }
    .form-row > div.full-width {
        flex: 1 1 100%;
    }
    .form-w {
        max-width: 900px;
        margin: 0;
    }
    .input-group {
        flex-wrap: nowrap;
    }
}

@media (min-width: 992px) {
    .container {
        margin-right: 5rem; /* Adjust the margin-right for larger screens */
    }
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
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5 " style="direction: rtl;">
    <div class="btn-group mb-3" style="direction:ltr;">  
        <a href="users.php" class="btn btn-light">إدارة المستخدمين</a>
        <a href="lawyers.php" class="btn btn-dark">المحامين</a>
    </div>
        <form method="post" class="shadow p-3 form-w" action="req/lawyer-add.php" id="lawyerForm">
                    <h3>إضافة محامي جديد</h3><hr>
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $_GET['error'] ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?= $_GET['success'] ?>
                        </div>
                    <?php } ?>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" value="<?=$lawyer_name?>" name="fname">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select id="role_id" class="form-select" name="role_id">
                                <option value="" disabled selected>اختر الدور</option>
                                <?php
                                    $sql = "SELECT `power_id`, `role`, `default_role_lawyer` FROM powers";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $row["power_id"];
                                            $role = $row["role"];
                                            $default_role_lawyer = $row["default_role_lawyer"];
                                            $selected = ($default_role_lawyer == 1) ? "selected" : "";
                                            echo "<option value='$id' $selected>$role</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                        <label class="form-label">المكتب</label>
                            <select id="office_id" class="form-select" name="office_id">
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php
                                    $sql = "SELECT `office_id`, `office_name` FROM offices";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $row["office_id"];
                                            $office_name = $row["office_name"];
                                            $selected = ($id == $office_id) ? "selected" : "";
                                            echo "<option value='$id' $selected>$office_name</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="<?=$lawyer_address?>" name="address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الإيميل</label>
                            <input type="text" class="form-control" value="<?=$lawyer_email?>" name="email_address">
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الولادة</label>
                            <input type="date" class="form-control" value="<?=$date_of_birth?>" name="date_of_birth">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" checked name="gender"> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" name="gender"> أنثى
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" value="<?=$lawyer_username?>" name="username">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة السر</label>
                            <div class="input-group" style="direction:ltr;">
                                <input type="text" class="form-control" name="pass" id="passInput">
                                <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" value="<?=$lawyer_city?>" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="lawyerPhone" class="form-control" value="<?=$lawyer_phone?>" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" value="<?=$lawyer_national?>" name="lawyer_national" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" value="<?=$lawyer_passport?>" name="lawyer_passport">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">تسجيل</button>
                </form>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	

    <script>
        $(document).ready(function(){
            $("#navLinks li:nth-child(8) a").addClass('active');
        });

        function makePass(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#!';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
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
    <script>
    document.getElementById('lawyerForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        var form = event.target;
        var requiredFields = [
            { name: 'fname', message: 'يرجى ملء حقل الاسم.' },
            { name: 'username', message: 'يرجى ملء حقل اسم المستخدم.' },
            { name: 'pass', message: 'يرجى ملء حقل كلمة السر.' },
            { name: 'email_address', message: 'يرجى ملء حقل الإيميل.' },
            { name: 'gender', message: 'يرجى اختيار الجنس.' },
            { name: 'phone', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'role_id', message: 'يرجى اختيار الدور.' },
            { name: 'office_id', message: 'يرجى اختيار الدور.' }
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
                text: 'يرجى ملء جميع الحقول المطلوبة.'
            });
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        } else {
            form.submit(); // Submit the form if all fields are valid
        }
    });

    document.getElementById('gBtn').addEventListener('click', function(e) {
        e.preventDefault();
        var passInput = document.getElementById('passInput');
        passInput.value = Math.random().toString(36).slice(-8);
    });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#lawyerPhone");
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
</body>
</html>
<?php 

  }else {
    header("Location: ../login.php");
    exit;
  } 
}else {
	header("Location: ../login.php");
	exit;
} 

?>



