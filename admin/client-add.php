<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
      
       include "../DB_connection.php";
       include "logo.php";

       $fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : '';
       $lname = isset($_SESSION['lname']) ? $_SESSION['lname'] : '';
       $address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
       $email_address = isset($_SESSION['email_address']) ? $_SESSION['email_address'] : '';
       $date_of_birth = isset($_SESSION['date_of_birth']) ? $_SESSION['date_of_birth'] : '';
       $city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
       $phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
       $gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';

      
       $father_name = isset($_SESSION['father_name']) ? $_SESSION['father_name'] : '';
       $grandfather_name = isset($_SESSION['grandfather_name']) ? $_SESSION['grandfather_name'] : '';
       $national_num = isset($_SESSION['national_num']) ? $_SESSION['national_num'] : '';
       $alhi = isset($_SESSION['alhi']) ? $_SESSION['alhi'] : '';
       $street_name = isset($_SESSION['street_name']) ? $_SESSION['street_name'] : '';
       $num_build = isset($_SESSION['num_build']) ? $_SESSION['num_build'] : '';
       $num_unit = isset($_SESSION['num_unit']) ? $_SESSION['num_unit'] : '';
       $zip_code = isset($_SESSION['zip_code']) ? $_SESSION['zip_code'] : '';
       $subnumber = isset($_SESSION['subnumber']) ? $_SESSION['subnumber'] : '';
       $uname = isset($_SESSION['username']) ? $_SESSION['username'] : '';
       $office_id = isset($_SESSION['office_id']) ? $_SESSION['office_id'] : '';
       
       $role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : '';
       $client_passport = isset($_SESSION['client_passport']) ? $_SESSION['client_passport'] : '';
 

       if (isset($_GET['phone'])) $phone = $_GET['phone'];
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Add Client</title>

	
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">


  <style>
    *{
      direction: rtl;
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

  </style>
</head>

<body>
    <?php 
        include "inc/navbar.php";
     ?>
     <div class="container mt-5">
     <div class="btn-group mb-3" style="direction:ltr;">
        <a href="users.php" class="btn btn-light">إدارة المستخدمين</a>
        <a href="clients.php" class="btn btn-dark">الموكلين</a>
      </div>        

        <form method="post" class="shadow p-3 form-w" action="req/client-add.php" id="clientForm">
            <h3>إضافة عميل جديد</h3>
            <hr>
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
                    <input type="text" class="form-control" value="<?=$fname?>" name="fname">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العائلة</label>
                    <input type="text" class="form-control" value="<?=$lname?>" name="lname">
                </div>
                <div class="col-md-6 mb-3">
                  <!--  <label class="form-label">اسم الأب</label>-->
                    <input type="hidden" class="form-control" value="<?=$father_name?>" name="father_name">
                </div>
                <div class="col-md-6 mb-3">
                 <!--   <label class="form-label">اسم الجد</label>-->
                    <input type="hidden" class="form-control" value="<?=$grandfather_name?>" name="grandfather_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان</label>
                    <input type="text" class="form-control" value="<?=$address?>" name="address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الإيميل</label>
                    <input type="text" class="form-control" value="<?=$email_address?>" name="email_address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">سنة الولادة</label>
                    <input type="date" class="form-control" value="<?=$date_of_birth?>" name="date_of_birth">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الجنس</label><br>
                    <input type="radio" value="Male" <?php if(!isset($_SESSION['gender']) || $_SESSION['gender'] == 'Male') echo 'checked'; ?> name="gender"> ذكر
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" value="Female" <?php if(isset($_SESSION['gender']) && $_SESSION['gender'] == 'Female') echo 'checked'; ?> name="gender"> أنثى
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم القومي</label>
                    <input type="text" class="form-control" value="<?=$national_num?>" name="national_num">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم جواز السفر</label>
                    <input type="text" class="form-control" value="<?=$client_passport?>" name="client_passport">
                </div>
    
                <div class="col-md-6 mb-3">
                    <label class="form-label">الهاتف</label>
                    <div style="min-width:100%;">
                        <input style="direction:ltr;" type="tel" id="clientPhone" class="form-control" value="<?=$phone?>" name="phone">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الدور</label>
                    <select id="role_id" class="form-select" name="role_id">
                        <option value="" disabled selected>اختر الدور</option>
                        <?php
                            $sql = "SELECT `power_id`, `role`, `default_role_client` FROM powers";
                            $result = $conn->query($sql);
                            if ($result->rowCount() > 0) {
                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    $id = $row["power_id"];
                                    $role = $row["role"];
                                    $default_role_client = $row["default_role_client"];
                                    $selected = ($default_role_client == 1) ? "selected" : "";
                                    echo "<option value='$id' $selected>$role</option>";
                                }
                            }
                        ?>
                    </select>

                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">المكتب</label>
                    <select id="office_id" class="form-select" name="office_id" onchange="fetchLawyers()">
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
                <div class="col-md-6 mb-3">
                    <label class="form-label">المحامي</label>
                    <select id="lawyer_id" class="form-select" name="lawyer_id" style="display:none;">
                        <option value="" disabled selected>اختر المحامي</option>
                    </select>
                </div>
               
                <div class="col-md-6 mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="receive_emails" checked value="1"> استلام التذكيرات عبر الإيميل
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="receive_whatsupp" checked value="1"> استلام الرسائل عبر الواتس آب
                    </label>
                </div>
               
   
                <div class="col-md-6 mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" class="form-control" value="<?=$city?>" name="city">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الحي</label>
                    <input type="text" class="form-control" value="<?=$alhi?>" name="alhi">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الشارع</label>
                    <input type="text" class="form-control" value="<?=$street_name?>" name="street_name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم المبنى</label>
                    <input type="text" class="form-control" value="<?=$num_build?>" name="num_build">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الوحدة</label>
                    <input type="text" class="form-control" value="<?=$num_unit?>" name="num_unit">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الرمز البريدي</label>
                    <input type="text" class="form-control" value="<?=$zip_code?>" name="zip_code">
                </div>
          <!--      <div class="col-md-6 mb-3">
                    <label class="form-label">الرقم الفرعي</label>
                    <input type="text" class="form-control" value="<?=$subnumber?>" name="subnumber">
                </div>-->
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" value="<?=$uname?>" name="username">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">كلمة السر</label>
                    <div class="input-group" style="direction:ltr;">
                        <input style="direction:ltr;" type="text" class="form-control" name="pass" id="passInput">
                        <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="min-width:250px;max-width:400px;">تسجيل</button>
            </div>
        </form>
     </div>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(10) a").addClass('active');
        });

        function makePass(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@!#';
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
        document.getElementById('clientForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission
    var form = event.target;
    var requiredFields = [
        { name: 'fname', message: 'يرجى ملء حقل الاسم.' },
        { name: 'username', message: 'يرجى ملء حقل اسم المستخدم.' },
        { name: 'pass', message: 'يرجى ملء حقل كلمة السر.' },
      //  { name: 'email_address', message: 'يرجى ملء حقل الإيميل.' },
        { name: 'gender', message: 'يرجى اختيار الجنس.' },
        { name: 'phone', message: 'يرجى ملء حقل الهاتف.' },
      //  { name: 'lname', message: 'يرجى ملء حقل العائلة.' },
        { name: 'office_id', message: 'يرجى ملء حقل المكتب.' },
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
            customClass: {
                popup: 'swal2-custom-popup'
            }
        });
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
    } else {
        var formData = $(form).serialize();

        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'نجاح',
                        text: response.message,
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى لاحقاً.',
                });
            }
        });
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
    
    if (officeId) {
        // قم بعمل طلب AJAX للحصول على المحامين بناءً على office_id
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'api/fetch_lawyers.php?office_id=' + officeId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                lawyerSelect.style.display = 'block';
                lawyerSelect.innerHTML = '<option value="" disabled selected>اختر المحامي</option>'; // إعادة تعيين الخيارات
                response.forEach(function(lawyer) {
                    var option = document.createElement('option');
                    option.value = lawyer.lawyer_id;
                    option.textContent = lawyer.lawyer_name;
                    lawyerSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    } else {
        lawyerSelect.style.display = 'none';
        lawyerSelect.innerHTML = '<option value="" disabled selected>اختر المحامي</option>';
    }
}
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



