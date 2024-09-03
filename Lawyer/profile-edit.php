<?php 
session_start();
if (isset($_SESSION['user_id']) && 
    isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Lawyer') {
      
       include "../DB_connection.php";
       include 'permissions_script.php';
       if ($pages['profiles']['write'] == 0) {
        header("Location: home.php");
        exit();
    }


       include "logo.php";

        
        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);

       $profile_id = isset($_GET['profile']) ? intval($_GET['profile']) : 0;

        if ($profile_id == 0) {
            header("Location: profiles.php");
            exit();
        }

        $profile = null;
        $headers = [];

        if ($profile_id > 0) {
            $stmt = $conn->prepare("SELECT * FROM profiles WHERE id = :profile_id");
            $stmt->bindParam(':profile_id', $profile_id);
            $stmt->execute();
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profile) {
                header("Location: profiles.php");
                exit();
            }

            // جلب بيانات الهيدر المرتبطة بـ office_id

            $stmt = $conn->prepare("SELECT header FROM headers WHERE profile_id = :profile_id");
            $stmt->bindParam(':profile_id', $profile_id);
            $stmt->execute();
            $headers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Add Profile</title>
	
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


        .ck-editor__editable[role="textbox"] {
    /* Editing area */
    min-height: 200px;
    text-align: right; /* Ensure text is aligned to the right */
    direction: rtl; /* Ensure text direction is right-to-left */
    }
    .ck-content .image {
        /* Block images */
        max-width: 80%;
        margin: 20px auto;
    }
    .ck.ck-toolbar.ck-toolbar_grouping>.ck-toolbar__items {
        flex-wrap: wrap;
    }
    .ck.ck-button.ck-off.ck-file-dialog-button {
        display: none !important;
    }
    .ck.ck-editor__editable_inline[dir=ltr] {
        text-align: right !important;
    }
    /* إضافة تنسيقات للقوائم لتظهر من اليمين إلى اليسار */
    .ck-content ol, .ck-content ul {
        text-align: right;
        direction: rtl;
        padding-right: 40px; /* Ensure padding on the right for RTL */
    }
    .ck-content ol {
        list-style-type: decimal;
    }
    .ck-content ul {
        list-style-type: disc;
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
        
        .img-thumbnail {
        max-width: 100px;
        max-height: 100px;
        margin: 5px;
        transition: transform 0.2s ease;
    }
    .hover-zoom:hover {
        transform: scale(1.2);
    }

  </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5 " style="direction: rtl;">
    <div class="btn-group mb-3" style="direction:ltr;">  
        <a href="home.php" class="btn btn-secondary">الرئيسية</a>
        <a href="profiles.php" class="btn btn-dark">رجوع</a>
    </div>

    <div class="row mb-3">
    <!-- عرض اللوغو في بطاقة منفصلة -->
    <?php if ($profile && $profile['logo']): ?>
        <div class="col-md-2 col-12 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">اللوغو</div>
                <div class="card-body text-center d-flex align-items-center justify-content-center">
                    <img src="../../profiles_photos/<?php echo $profile['logo']; ?>" alt="Logo" class="img-thumbnail hover-zoom" style="width: 100px;">
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- عرض صور الهيدر في بطاقة منفصلة -->
    <?php if (!empty($headers)): ?>
        <div class="col-md-10 col-12">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">صور الهيدر</div>
                <div class="card-body position-relative p-4">
                    <button id="scroll-left" class="btn btn-primary position-absolute d-none d-md-block" style="left: 10px; top: 50%; transform: translateY(-50%); z-index: 10; border-radius: 50%;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="scroll-right" class="btn btn-primary position-absolute d-none d-md-block" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; border-radius: 50%;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div id="image-container" class="d-flex overflow-auto" style="white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none;">
                        <style>
                            #image-container::-webkit-scrollbar {
                                display: none;
                            }
                        </style>
                        <?php foreach ($headers as $header): ?>
                            <img src="../../profiles_photos/<?php echo $header['header']; ?>" alt="Header Image" class="img-thumbnail hover-zoom mx-2" style="width: 100px; height: 100px;">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('scroll-left').onclick = function() {
        document.getElementById('image-container').scrollBy({ left: -100, behavior: 'smooth' });
    };
    document.getElementById('scroll-right').onclick = function() {
        document.getElementById('image-container').scrollBy({ left: 100, behavior: 'smooth' });
    };
</script>



    


    <form method="post" class="shadow p-3 form-w" id="profileForm" enctype="multipart/form-data">
    <h3>تعديل صفحة تعريفية</h3><hr>
    <div class="mb-3 form-row">
        <input type="hidden" id="office_id" name="office_id" value="<?=$OfficeId?>">
        <div class="col-md-6">
            <label class="form-label">اللوغو</label>
            <input type="file" class="form-control" name="logo">
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label" for="multiupload">اختر الصور للهيدر:</label>
            <input type="file" id="multiupload" name="upload_image[]" multiple class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input type="text" class="form-control" name="fname" value="<?php echo $profile ? $profile['fname'] : ''; ?>" required>
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">العنوان</label>
            <input type="text" class="form-control" name="address" value="<?php echo $profile ? $profile['address'] : ''; ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">الإيميل</label>
            <input type="email" class="form-control" name="email_address" value="<?php echo $profile ? $profile['email_address'] : ''; ?>" required>
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">خط الطول</label>
            <input type="text" class="form-control" name="longitude" value="<?php echo $profile ? $profile['longitude'] : ''; ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">خط العرض</label>
            <input type="text" class="form-control" name="latitude" value="<?php echo $profile ? $profile['latitude'] : ''; ?>">
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="tel" class="form-control" name="phone" id="phone" style="direction:ltr;" value="<?php echo $profile ? $profile['phone'] : ''; ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">الواتساب</label>
            <input type="text" class="form-control" name="whatsapp" value="<?php echo $profile ? $profile['whatsapp'] : ''; ?>">
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">رابط الفيس بوك</label>
            <input type="text" class="form-control" name="facebook" value="<?php echo $profile ? $profile['facebook'] : ''; ?>">
            <input type="hidden" name="profile_id" value="<?=$profile['id']?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">رابط التويتر</label>
            <input type="text" class="form-control" name="twitter" value="<?php echo $profile ? $profile['twitter'] : ''; ?>">
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">وصف مختصر</label>
            <textarea class="form-control" name="desc1" rows="3" required><?php echo $profile ? $profile['desc1'] : ''; ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">الـQR</label>
            <input type="file" class="form-control" name="qr">
            <?php if ($profile && $profile['qr']): ?>
                <div class="mt-2">
                    <img src="../../profiles_photos/<?php echo $profile['qr']; ?>" alt="QR Code" class="img-thumbnail hover-zoom" style="width: 100px;" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImageModal('../../profiles_photos/<?php echo $profile['qr']; ?>')">
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="mb-3 form-row">
        <div class="col-md-6">
            <label class="form-label">نبذة</label>
            <textarea class="form-control" name="desc2" id="editor" rows="6"><?php echo $profile ? $profile['desc2'] : ''; ?></textarea>
        </div>
    </div>
    <div class="progress m-3" style="height: 25px; display: none;direction:ltr;">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;height: 25px;"></div>
    </div>
    <button id="saveProfile" type="submit" class="btn btn-primary">تحديث</button>
    </form>


    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	


    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://unpkg.com/libphonenumber-js@1.9.25/bundle/libphonenumber-js.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <script src="../js/edit_profile.js"></script>
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