<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['admin_id'])) {

    if ($_SESSION['role'] == 'Admin') {
      
       include "../DB_connection.php";
       include "logo.php";

       include "data/admins.php";


       
       
       
       $admin_id = $_GET['admin_id'];
       $admin = getAdminById($admin_id, $conn);

       if ($admin == 0) {
         header("Location: users.php");
         exit;
       }


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Edit Admin</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="btn-group mb-3" style="direction:ltr;">        
            <a href="users.php" class="btn btn-secondary">رجوع</a>  
        </div>
        <div class="row">
            <div class="col-md-8">
            <form method="post" class="shadow p-3 form-w" action="req/admin-edit.php" enctype="multipart/form-data">
                <h3>تعديل معلومات الآدمن</h3>
                <div style="width:50px;height:50px;margin-top: -45px;float: left;">
                <img src="../img/<?=$admin['logo']?>" width="50" height="50" alt="Admin Logo">
                </div>
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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">الاسم الأول</label>
                        <input type="text" class="form-control" value="<?=$admin['fname']?>" name="fname">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">العائلة</label>
                        <input type="text" class="form-control" value="<?=$admin['lname']?>" name="lname">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" value="<?=$admin['username']?>" name="username" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="role_id">الدور</label>
                        <select id="role_id" class="form-select" name="role_id" required>
                            <option value="" disabled selected>اختر الدور</option>
                            <?php
                                $sql = "SELECT `power_id`, `role` FROM powers";
                                $result = $conn->query($sql);
                                if ($result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $role_id = $admin['role_id'];
                                        $id = $row["power_id"];
                                        $role = $row["role"];
                                        $selected = ($id == $role_id) ? "selected" : "";
                                        echo "<option value='$id' $selected>$role</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                if ($admin['admin_id'] == 1) {
                    $disabled = 'disabled';
                } else {
                    $disabled = '';
                }
                ?>
                <div class="row">
                    <div class="form-check custom-checkbox mb-3 col-md-6 mt-5">
                        <input type="checkbox" class="form-check-input" id="stop" name="stop" <?php if($admin['stop'] == 1) echo 'checked'; ?> value="1" <?=$disabled?>>
                        <label class="form-label" for="stop">إيقاف مؤقت</label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الإيقاف</label>
                        <input type="date" class="form-control" value="<?=$admin['stop_date']?>" name="stop_date" <?=$disabled?>>
                    </div>
                    <?php
                    if ($admin['admin_id'] != 1) { 
                    ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">لوغو الآدمن</label>
                            <input type="file" class="form-control" name="admin_logo">
                        </div>
                    <?php 
                    } 
                    ?>
                    </div>
                   
                
                <?php
                if ($admin['admin_id'] != 1) { 
                ?>
                <div id="case-info" class="tab-pane fade show active mb-3">
                    <div id="accordionMain">
                        <div class="card">
                            <div id="heading1" class="card-header">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse1"> إعدادات الواتساب </button>
                                </h2>
                            </div>
                            <div id="collapse1" class="collapse show" data-parent="#accordionMain">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="host_whatsapp">استضافة الواتساب</label>
                                            <a href="https://user.ultramsg.com/signup.php?lang=ar" target="_blank"><small>انقر هنا</small></a>
                                            <input style="direction:ltr;" class="form-control" type="text" name="host_whatsapp" id="host_whatsapp" value="<?=$admin['host_whatsapp']?>" placeholder="https://api.ultramsg.com/Put Your instanceID Here/messages/chat">
                                        </div>
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="token_whatsapp">توكين الواتساب</label>
                                            <input style="direction:ltr;" class="form-control" type="text" name="token_whatsapp" id="token_whatsapp" value="<?=$admin['token_whatsapp']?>" placeholder="For example: kp38uy15lk2zncmnjdjqg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="email-info" class="tab-pane fade show active mb-3">
                    <div id="accordionMain">
                        <div class="card">
                            <div id="heading2" class="card-header">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse2"> إعدادات الإيميل </button>
                                </h2>
                            </div>
                            <div id="collapse2" class="collapse show" data-parent="#accordionMain">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="host_email">استضافة الإيميل</label>
                                            <input style="direction:ltr;" class="form-control" type="text" name="host_email" id="host_email" value="<?=$admin['host_email']?>" placeholder="smtp.gmail.com OR smtp.hostinger.com">
                                        </div>
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="username_email">اسم مستخدم الإيميل</label>
                                            <input style="direction:ltr;" class="form-control" type="text" name="username_email" id="username_email" value="<?=$admin['username_email']?>" placeholder="For example: anas@gmail.com OR anas@mywebsite.com">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="password_email">كلمة السر</label>
                                            <input style="direction:ltr;" class="form-control" type="password" name="password_email" id="password_email" value="<?=$admin['password_email']?>" placeholder="Your passwordApp From google or Your password hostinger Email" autocomplete="none">
                                        </div>
                                        <div class="col-md-6 mb-6">
                                            <label class="form-label" style="cursor: pointer;" for="port_email">بورت الإرسال</label>
                                            <input style="direction:ltr;" class="form-control" type="number" name="port_email" id="port_email" value="<?=$admin['port_email']?>" placeholder="Usually it's 465">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3 mx-5" style="height: 30px; display: none;direction:ltr;" id="progress-bar-container">
                        <div style="direction:ltr;" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" id="progress-bar">0%</div>
                    </div>
                </div>
                <?php 
                } 
                ?>
                <input type="hidden" value="<?=$admin['admin_id']?>" name="admin_id">
                <button type="submit" class="btn btn-primary">تحديث</button>
            </form>

            </div>
            <div class="col-md-4">
                <form method="post" class="shadow p-3 form-w" action="req/admin-change.php" id="change_password">
                    <h3>تغيير كلمة السر</h3><hr>
                    <?php if (isset($_GET['perror'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_GET['perror']?>
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET['psuccess'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?=$_GET['psuccess']?>
                        </div>
                    <?php } ?>
                    <div class="mb-3">
                        <label class="form-label">كلمة السر الجديدة</label>
                        <div class="input-group mb-3" style="direction: ltr;">
                            <input type="password" class="form-control" name="new_pass" id="passInput">
                            <button class="btn btn-secondary" id="gBtn">عشوائي</button>
                        </div>
                    </div>
                    <input type="hidden" value="<?=$admin['admin_id']?>" name="admin_id">
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة السر</label>
                        <input type="text" class="form-control" name="c_new_pass" id="passInput2" style="direction: ltr;">
                    </div>
                    <button type="submit" class="btn btn-primary">تغيير</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to generate random password
        function makePass(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            var passInput = document.getElementById('passInput');
            var passInput2 = document.getElementById('passInput2');
            passInput.value = result;
            passInput2.value = result;
        }

        // Event listener for random password button
        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
            e.preventDefault();
            makePass(8); // Generate 8-character random password
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