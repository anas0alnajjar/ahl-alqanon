<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Managers') {
      
       include "../DB_connection.php";
       include "logo.php";
       include 'permissions_script.php';

        if ($pages['managers']['add'] == 0) {
            header("Location: home.php");
            exit();
        }

       $manager_name = '';
       $manager_email = '';
       $manager_phone = '';
       $manager_address = '';
       $manager_city = '';
       $manager_username = '';
       $date_of_birth = '';


       $manager_national = '';
       $manager_passport = '';
       $role_id = '';
       $office_id = '';



       if (isset($_GET['manager_name'])) $manager_name = $_GET['manager_name'];
       if (isset($_GET['email_address'])) $manager_email = $_GET['email_address'];
       if (isset($_GET['phone'])) $manager_phone = $_GET['phone'];
       if (isset($_GET['address'])) $manager_address = $_GET['address'];
       if (isset($_GET['date_of_birth'])) $date_of_birth = $_GET['date_of_birth'];
       if (isset($_GET['city'])) $manager_city = $_GET['city'];
       if (isset($_GET['username'])) $manager_username = $_GET['username'];

       if (isset($_GET['manager_national'])) $manager_national = $_GET['manager_national'];
       if (isset($_GET['manager_passport'])) $manager_passport = $_GET['manager_passport'];
       if (isset($_GET['role_id'])) $role_id = $_GET['role_id'];
       if (isset($_GET['office_id'])) $office_id = $_GET['office_id'];

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Add manager</title>
	
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
    <?php 
    include "inc/navbar.php"; 
    include "inc/footer.php";
    ?>
    
    <div class="container mt-5 " style="direction: rtl;">
    <div class="btn-group mb-3" style="direction:ltr;">
        <a href="managers.php" class="btn btn-light">المدراء</a>
        <a href="users.php" class="btn btn-dark user_management">إدارة المستخدمين</a>
    </div>
        <form method="post" class="shadow p-3 form-w" action="req/manager-add.php" id="managerForm">
                    <h3>إضافة مدير مكتب جديد</h3><hr>
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
                            <input type="text" class="form-control" value="<?=$manager_name?>" name="manager_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الدور</label>
                            <select id="role_id" class="form-select" name="role_id">
                            <option value="" disabled selected>اختر الدور</option>
                            <?php
                                // جلب المكاتب المرتبطة بالآدمن الحالي
                                $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                $stmt_offices = $conn->prepare($sql_offices);
                                $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                $stmt_offices->execute();
                                $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                if (!empty($offices)) {
                                    $office_ids = implode(',', $offices);

                                    // جلب الأدوار المرتبطة بالمكاتب
                                    $sql_roles = "SELECT `power_id`, `role` FROM powers WHERE office_id IN ($office_ids)";
                                    $stmt_roles = $conn->prepare($sql_roles);
                                    $stmt_roles->execute();
                                    $result = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt_roles->rowCount() > 0) {
                                        foreach ($result as $row) {
                                            $id = $row["power_id"];
                                            $role = $row["role"];
                                            $selected = ($id == $role_id) ? "selected" : "";
                                            echo "<option value='$id' $selected>$role</option>";
                                        }
                                    }
                                }
                            ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                            <label class="form-label">المكتب</label>
                            <select id="office_id" class="form-select" name="office_id">
                            <option value="" disabled selected>اختر المكتب</option>
                            <?php
                                $admin_id = $_SESSION['user_id']; // معرف الآدمن الحالي
                                $sql = "SELECT `office_id`, `office_name` FROM offices WHERE admin_id = :admin_id";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($stmt->rowCount() > 0) {
                                    foreach ($result as $row) {
                                        $id = $row["office_id"];
                                        $office_name = $row["office_name"];
                                        $selected = ($id == $office_id) ? "selected" : "";
                                        echo "<option value='$id' $selected>$office_name</option>";
                                    }
                                }
                            ?>

                            </select>
                        </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">العنوان</label>
                            <input type="text" class="form-control" value="<?=$manager_address?>" name="manager_address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الإيميل</label>
                            <input type="text" class="form-control" value="<?=$manager_email?>" name="mangaer_email">
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الولادة</label>
                            <input type="date" class="form-control" value="<?=$date_of_birth?>" name="date_of_birth">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label><br>
                            <input type="radio" value="Male" checked name="manager_gender"> ذكر
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="Female" name="manager_gender"> أنثى
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" value="<?=$manager_username?>" name="username">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كلمة السر</label>
                            <div class="input-group" style="direction:ltr;">
                                <input type="text" class="form-control" name="manager_password" id="passInput">
                                <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" value="<?=$manager_city?>" name="manager_city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الهاتف</label>
                            <div style="min-width:100%;">
                                <input style="direction:ltr;" type="tel" id="managerPhone" class="form-control" value="<?=$manager_phone?>" name="manager_phone">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 form-row">
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" value="<?=$manager_national?>" name="manager_national" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم جواز السفر</label>
                            <input type="text" class="form-control" value="<?=$manager_passport?>" name="manager_passport">
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
    document.getElementById('managerForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        var form = event.target;
        var requiredFields = [
            { name: 'manager_name', message: 'يرجى ملء حقل الاسم.' },
            { name: 'username', message: 'يرجى ملء حقل اسم المستخدم.' },
            { name: 'manager_password', message: 'يرجى ملء حقل كلمة السر.' },
            { name: 'manager_address', message: 'يرجى ملء حقل العنوان.' },
            { name: 'mangaer_email', message: 'يرجى ملء حقل الإيميل.' },
            { name: 'date_of_birth', message: 'يرجى ملء حقل تاريخ الولادة.' },
            { name: 'manager_gender', message: 'يرجى اختيار الجنس.' },
            { name: 'manager_city', message: 'يرجى ملء حقل المدينة.' },
            { name: 'manager_phone', message: 'يرجى ملء حقل الهاتف.' },
            { name: 'role_id', message: 'يرجى اختيار الدور.' },
            { name: 'office_id', message: 'يرجى اختيار المكتب.' }
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
            var input = document.querySelector("#managerPhone");
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



