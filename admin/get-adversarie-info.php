<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['id'])) {

    if ($_SESSION['role'] == 'Admin') {
      
       include "../DB_connection.php";
       include "logo.php";

       function getAdverById($id, $conn){
        $sql = "SELECT * FROM adversaries
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
     
        if ($stmt->rowCount() == 1) {
          $adversarie = $stmt->fetch();
          return $adversarie;
        } else {
         return 0;
        }
     }

       
       
       
      
       $adversarie_id = $_GET['id'];
       $adver = getAdverById($adversarie_id, $conn);

       if ($adver == 0) {
         header("Location: adversaries.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Edit Adversarie</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    

    

    <!-- <link href="../css/bootstrap-datetimepicker.css?v2" rel="stylesheet" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
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

  </style>
</head>
<body>
<?php include "inc/navbar.php"; ?>
    <div class="container mt-5">
    <div class="btn-group" style="direction:ltr;">
        <a href="home.php" class="btn btn-light">الرئيسية</a>
        <a href="adversaries.php" class="btn btn-dark">رجوع</a>
    </div>
        <form method="post" class="shadow p-3 mt-3 form-w" action="req/adver-edit.php" id="clientForm">
            <h3>تعديل معلومات الخصم</h3><hr>
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
                    <input type="text" class="form-control" value="<?=$adver['fname']?>" name="fname">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">العائلة</label>
                    <input type="text" class="form-control" value="<?=$adver['lname']?>" name="lname">
                </div>

                <div class="col-md-6 mb-3">
                <label class="form-label">المكتب</label>
                            <select id="office_id" class="form-select" name="office_id"  onchange="fetchLawyers()">
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php
                                    $sql = "SELECT `office_id`, `office_name` FROM offices";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $row["office_id"];
                                            $office_id = $adver['office_id'];
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
                    <select id="lawyer_id" class="form-select" name="lawyer_id" data-selected-lawyer="<?=$adver['lawyer_id']?>" style="display:none;">
                        <option value="" disabled>اختر المحامي</option>
                        <!-- خيارات المحامين ستُضاف هنا بواسطة JavaScript -->
                    </select>

                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان</label>
                    <input type="text" class="form-control" value="<?=$adver['address']?>" name="address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الإيميل</label>
                    <input type="email" class="form-control" value="<?=$adver['email_address']?>" name="email">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">سنة الولادة</label>
                    <input type="date" class="form-control" value="<?=$adver['date_of_birth']?>" name="date_of_birth">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الجنس</label><br>
                    <input type="radio" value="Male" <?php if($adver['gender'] == 'Male') echo 'checked'; ?> name="gender"> ذكر
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" value="Female" <?php if($adver['gender'] == 'Female') echo 'checked'; ?> name="gender"> أنثى
                </div>
           
                <div class="col-md-6 mb-3">
                    <label class="form-label">الهاتف</label>
                    <input type="text" class="form-control" value="<?=$adver['phone']?>" name="phone">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" class="form-control" value="<?=$adver['city']?>" name="city">
                </div>
         
            </div>

            <input type="hidden" value="<?=$adver['id']?>" name="id">
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="min-width:250px;max-width:400px;" >تحديث</button>
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
            { name: 'gender', message: 'يرجى اختيار الجنس.' },
            { name: 'lname', message: 'يرجى ملء حقل الهاتف.' }
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
             $("#navLinks li:nth-child(4) a").addClass('active');
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