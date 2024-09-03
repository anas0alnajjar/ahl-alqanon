<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
       include "../DB_connection.php";
       include "logo.php" 
    
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Setting</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="icon" href="../img/<?=$setting['logo']?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <style>
   .logo-container {
    position: relative;
    margin-bottom: 1rem;
    text-align: center;
    margin: 1rem auto;
    width: 200px;
    height: 200px;
}
.logo-img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
}
.logo-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 200px;
    height: 200px;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.edit-icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 200px;
    height: 200px;
    background-color: rgba(0, 0, 0, 0.5);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s;
}
.logo-container:hover .edit-icon {
    opacity: 1;
}
.progress {
    height: 30px;
}
.progress-bar {
    line-height: 30px;
    font-size: 16px;
    transition: width 0.5s ease; /* This adds the animation effect */
}
.form-w{
    min-width:100% !important;
}

    </style>
</head>
<body>
    <?php 
        include "inc/navbar.php";

     ?>
   <div class="container mt-5" style="direction: rtl;">
        <form method="post"
              class="shadow p-3 mt-5 form-w" 
              action="req/setting-edit.php">
        
        <h3 class="text-center">معلومات الشركة</h3>
        <div>
            <a href="home.php" class="btn btn-light w-100">الرئيسية</a>
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
        <div class="logo-container">
                    <img src="../img/<?=$setting['logo']?>" class="logo-img" id="logo" alt="Logo">
                    <label for="id_picture" class="edit-icon">
                            <i class="fas fa-pen"></i>
                        </label>
                    <input type="file" class="logo-input" id="id_picture" name="id_picture">
                </div>
    <div class="row mb-3">
        <div class="mb-3">
          <label class="form-label">اسم الشركة</label>
          <input type="text" 
                 class="form-control"
                 value="<?=$setting['company_name']?>" 
                 name="company_name">
        </div>
        <div class="mb-3">
          <label class="form-label">الشعار</label>
          <textarea 
                 class="form-control"
                 name="slogan"
              rows="4"><?=$setting['slogan']?></textarea>
        </div>
        </div>
        <div class="mb-3">
                <label class="form-label">من نحن</label>
                <textarea class="form-control" name="about"
                          rows="4"><?=$setting['about']?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">السنة الحالية</label>
          <input type="text" 
                 class="form-control"
                 value="<?=$setting['current_year']?>" 
                 name="current_year">
        </div>
        <div id="site-settings" class="tab-pane fade show active mb-3">
        <div id="accordionMain">
            <div class="card">
                <div id="heading1" class="card-header">
                    <h2 class="mb-0">
                        <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse1"> إعدادات الموقع </button>
                    </h2>
                </div>
                <div id="collapse1" class="collapse show" data-parent="#accordionMain">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label class="form-label" for="site_key">مفتاح الموقع جانب العميل</label>
                                <a href="https://www.google.com/recaptcha/admin/site/704621502/settings" target="_blank"><small>انقر هنا</small></a>
                                <input style="direction:ltr;" class="form-control" type="text" name="site_key" id="site_key" value="<?=$setting['site_key']?>" placeholder="Enter your site key (Site Key)" required>
                                
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label" for="secret_key">مفتاح الموقع جانب الخادم</label>
                                <input style="direction:ltr;" class="form-control" type="text" name="secret_key" id="secret_key" value="<?=$setting['secret_key']?>" placeholder="Enter your secret key" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label class="form-label" for="api_map">مفتاح الخرائط الخاص بغوغل</label>
                                <input style="direction:ltr;" class="form-control" type="text" name="api_map" id="api_map" value="<?=$setting['api_map']?>" placeholder="Enter your API map key" required>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allow_joining" id="allow_joining" <?=$setting['allow_joining'] ? 'checked' : ''?>>
                                    <label style="cursor: pointer;" class="form-check-label" for="allow_joining">السماح بطلبات الانضمام الخارجية</label>
                                </div>
                            </div>
                             <div class="col-md-6 mt-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allow_check" id="allow_check" <?=$setting['allow_check'] ? 'checked' : ''?>>
                                    <label style="cursor: pointer;" class="form-check-label" for="allow_check">السماح بالمصادقة عن طريق الإيميل</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                                <input style="direction:ltr;" class="form-control" type="text" name="host_whatsapp" id="host_whatsapp" value="<?=$setting['host_whatsapp']?>" placeholder="https://api.ultramsg.com/Put Yout instanceID Here/messages/chat">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="token_whatsapp">توكين الواتساب</label>
                                                <input style="direction:ltr;" class="form-control" type="text" name="token_whatsapp" id="token_whatsapp" value="<?=$setting['token_whatsapp']?>" placeholder="For example: kp38uy15lk2zncmnjdjqg">
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
                                                <input style="direction:ltr;" class="form-control" type="text" name="host_email" id="host_email" value="<?=$setting['host_email']?>" placeholder="smtp.gmail.com OR smtp.hostinger.com">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="username_email">اسم مستخدم الإيميل</label>
                                                <input style="direction:ltr;" class="form-control" type="text" name="username_email" id="username_email" value="<?=$setting['username_email']?>" placeholder="For example: anas@gmail.com OR anas@mywebsite.com">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="password_email">كلمة السر</label>
                                                <input style="direction:ltr;" class="form-control" type="password" name="password_email" id="password_email" value="<?=$setting['password_email']?>" placeholder="Your passwordApp From google or Your password hostiger Email" autocomplete="none">
                                            </div>
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" style="cursor: pointer;" for="port_email">بورت الإرسال</label>
                                                <input style="direction:ltr;" class="form-control" type="number" name="port_email" id="port_email" value="<?=$setting['port_email']?>" placeholder="Usually it's 465">
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
      <button type="submit" 
              class="btn btn-primary">
              تحديث</button>
            <button type="button" id="export-backup-btn" class="btn btn-dark">نسخة احتياطية</button>
        </div>

     </form>
 </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script>
        $(document).ready(function(){
             $("#navLinks li:nth-child(8) a").addClass('active');
        });
    </script>
    <script>
document.getElementById('id_picture').addEventListener('change', function() {
    var formData = new FormData();
    formData.append('logo', this.files[0]);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'req/update_logo_master.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('logo').src = "../img/" + response.new_logo;
            } else {
                console.log(xhr.responseText);
                alert('حدث خطأ أثناء تحديث اللوغو.');
            }
        } else {
            alert('حدث خطأ في الاتصال بالخادم.');
        }
    };
    xhr.send(formData);
});

    </script>
    <script>
document.getElementById('export-backup-btn').addEventListener('click', function() {
    var progressBarContainer = document.getElementById('progress-bar-container');
    var progressBar = document.getElementById('progress-bar');
    
    progressBarContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.setAttribute('aria-valuenow', 0);
    progressBar.innerText = '0%';

    // إرسال طلب AJAX إلى صفحة التصدير
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'req/export_backup.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.responseType = 'blob'; // تحديد نوع الرد كبيانات ثنائية

    xhr.onprogress = function(e) {
    // Get progress information from the response headers
    var progressData = JSON.parse(xhr.responseText);
    var current = progressData.current;
    var total = progressData.total;

    // Calculate percentage based on current and total
    var percentComplete = Math.round((current / total) * 100);

    // Update progress bar width and text
    progressBar.style.width = percentComplete + '%';
    progressBar.setAttribute('aria-valuenow', percentComplete);
    progressBar.innerText = percentComplete + '%';
    };


    xhr.onload = function() {
        if (xhr.status === 200) {
            var blob = new Blob([xhr.response], { type: 'application/zip' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'backup.zip';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            progressBar.style.width = '100%';
            progressBar.setAttribute('aria-valuenow', 100);
            progressBar.innerText = '100%';
        } else {
            console.error("Download failed.");
        }
        setTimeout(function() {
            progressBarContainer.style.display = 'none';
            progressBar.innerText = '0%'; // Reset progress text
        }, 2000);
    };

    xhr.onerror = function() {
        console.error("An error occurred during the transaction");
        progressBarContainer.style.display = 'none';
    };

    xhr.send();
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