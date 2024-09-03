<?php
include '../DB_connection.php';
include "../data/setting.php";
$setting = getSetting($conn);

$office_id = isset($_GET['office']) ? intval($_GET['office']) : 0;
$office_data = [];
$headers = [];

if ($office_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM profiles WHERE id = :office_id");
        $stmt->bindParam(':office_id', $office_id);
        $stmt->execute();
        $office_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($office_data) {
            $stmt = $conn->prepare("SELECT header FROM headers WHERE profile_id = :office_id");
            $stmt->bindParam(':office_id', $office_id);
            $stmt->execute();
            $headers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // إعادة التوجيه في حالة عدم وجود بيانات للـ office_id
            header("Location: ../index.php");
            exit();
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }
} else {
    // إعادة التوجيه في حالة عدم وجود الـ GET أو كان office_id غير صالح
    header("Location: ../index.php");
    exit();
}

$page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$page_url2 = "http://" . $_SERVER['HTTP_HOST'];
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مكتب <?php echo htmlspecialchars($office_data['fname']); ?></title>
    <link rel="stylesheet" href="../css/style.css">

    <link rel="icon" href="../../profiles_photos/<?php echo htmlspecialchars($office_data['logo']); ?>">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    
    <style>



        body {
            font-family: 'Cairo', sans-serif;
            background-color: #ffffff; /*#f8f9fa*/
        }
        .profile-header .item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .map-container {
            height: 300px;
            width: 100%;
        }

        .contact-icons a {
            display: block;
            margin: 5px 0;
            color: #272c3f;
        }

        .contact-icons a:hover {
            color: #272c3f; /* 007bff */
        }

        .card-custom {
            
            transition: transform 0.3s ease-in-out;
        }

        .card-custom .qr-code .qr:hover {
            transform: scale(1.1);
        }

        .visit-button {
            margin-top: 10px;
            
        }

        .profile-details {
            margin-top: 20px;
        }

        .profile-details .card {
            margin: 0 auto;
            max-width: 400px;
        }

        .rounded-circle {
            display: block;
            margin: 0 auto 10px;
        }

        .fadeIn {
            animation: fadeIn 1.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        #htmlCustom p {
            font-size: 1.2rem;
            direction: rtl;
            text-align: right;
            line-height: normal;
        }
        .custom-container {
            display: flex;
            flex-wrap: wrap;
        }
        .custom-container .col-md-8 {
            flex: 2;
        }
        .custom-container .col-md-4 {
            flex: 1;
        }
        .card-custom img {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .contact-icons {
        text-align: right;
        direction: rtl;
        font-family: Arial, sans-serif; /* تغيير الخط إلى خط احترافي */
    }
    .contact-icons a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit; /* يحافظ على اللون الافتراضي للنص */
        margin-bottom: 10px;
        transition: color 0.3s; /* تأثير انتقال عند تغيير اللون */
    }
    .contact-icons a:hover {
        color: #cfccc0; /* تغيير اللون عند التمرير بالماوس */
    }
    .contact-icons i {
        margin-left: 8px; /* مسافة بين الأيقونة والنص */
    }
    .contact-icons a:after {
        content: '';
        display: block;
        width: 0;
        height: 2px;
        background: #cfccc0; /* لون الخط عند التمرير بالماوس */
        transition: width 0.3s;
    }
    .contact-icons a:hover:after {
        width: 100%; /* عرض الخط الكامل عند التمرير بالماوس */
    }
    .contact-icons .phone-number {
        unicode-bidi: plaintext; /* يعرض الأرقام بشكل صحيح من اليمين لليسار */
    }
    /* أسلوب لجعل النص العربي من اليمين إلى اليسار */
#htmlCustom {
    direction: rtl;
    font-family: 'Cairo', sans-serif;
    padding: 20px;
}

/* تنسيق العناوين */
#htmlCustom h1,
#htmlCustom h2,
#htmlCustom h3,
#htmlCustom h4,
#htmlCustom h5,
#htmlCustom h6 {
    position: relative;
    padding-bottom: 10px;
    margin-bottom: 20px;
    color: #272c3f;
    font-weight: bold;
}

#htmlCustom h3 {
    text-align: center;
}

#htmlCustom h1::before,
#htmlCustom h2::before,
#htmlCustom h3::before,
#htmlCustom h4::before,
#htmlCustom h5::before,
#htmlCustom h6::before {
    content: '';
    position: absolute;
    right: 50%;
    bottom: 0;
    transform: translateX(50%);
    width: 50px;
    height: 3px;
    background-color: #272c3f;
}

/* تنسيق الفقرات */
#htmlCustom p {
    font-size: 1rem;
    line-height: 1.7;
    color: #272c3f; /* 343a40*/
    text-align: justify;
    margin-bottom: 15px;
}

#htmlCustom p::first-letter {
 /*   font-size: 1.5rem;
    font-weight: bold;*/
    color: #272c3f;
}

/* تنسيق القوائم */
#htmlCustom ul,
#htmlCustom ol {
    margin-bottom: 20px;
    padding-right: 20px;
}

#htmlCustom ul li,
#htmlCustom ol li {
    margin-bottom: 10px;
    font-size: 1rem;
    line-height: 1.7;
    color: #272c3f;
}

#htmlCustom ul {
    list-style: disc inside;
}

#htmlCustom ol {
    list-style: decimal inside;
}

/* تنسيق الصور */
#htmlCustom img {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* تنسيق الروابط */
#htmlCustom a {
    color: #272c3f;
    text-decoration: none;
    border-bottom: 1px dashed #272c3f;
}

#htmlCustom a:hover {
    color: #272c3f;
    border-bottom: 1px solid #272c3f !important;
}

/* تنسيق الجداول */
#htmlCustom table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    text-align: center;
}

#htmlCustom table th,
#htmlCustom table td {
    padding: 10px;
    border: 1px solid #272c3f;
}

#htmlCustom table th {
    background-color: #272c3f;
    color: #fff;
    font-weight: bold;
}

#htmlCustom table tr:nth-child(even) {
    background-color: #f8f9fa;
}

/* تنسيق روابط الصور */
#htmlCustom a img {
    border: none;
    box-shadow: none;
}
/* أسلوب عام للبطاقة */
.card-custom {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-custom .card-body {
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa, #ffffff); 
  /*  background: #272c3f;*/
}

/* تنسيق الشعار */
.card-custom .logo {
    width: 100px;
    height: 100px;
    padding: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* تنسيق العنوان */
.card-custom h2 {
    margin-top: 15px;
    font-size: 1.75rem;
    color: #272c3f;
    font-weight: bold;
}

/* تنسيق النص */
.card-custom p {
    font-size: 1rem;
    line-height: 1.7;
    color: #272c3f;;
    margin-bottom: 20px;
}

/* تنسيق رمز الـ QR */
.card-custom .qr-code {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.card-custom .qr-code .qr {
    width: 150px;
    height: 150px;
    padding: 10px;
    border-radius: 10px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
    .btn-primary, .btn-whatsapp {
        padding: 12px 24px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 18px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .btn-primary {
      /*  background: linear-gradient(45deg, #007bff, #0056b3);*/
        background: #272c3f;
}

    .btn-whatsapp {
      /*  background: linear-gradient(45deg, #25d366, #128c7e);*/
        background: #272c3f;
    }

    .btn-primary:hover, .btn-whatsapp:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        text-decoration: none;
        background: #272c3f;
        color: #f8f9fa;
    }

    .btn-primary::before, .btn-whatsapp::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 300%;
        height: 300%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%) rotate(45deg);
        transition: all 0.75s ease;
        border-radius: 50%;
        opacity: 0;


    }

    .btn-primary:hover::before, .btn-whatsapp:hover::before {
        width: 400%;
        height: 400%;
        opacity: 1;

    }

    .btn-primary i, .btn-whatsapp i {
        transition: transform 0.3s ease;

    }

    .btn-primary:hover i, .btn-whatsapp:hover i {
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        25% {
            transform: translateX(-4px);
        }
        50% {
            transform: translateX(4px);
        }
        75% {
            transform: translateX(-4px);
        }
    }
    
        @media (max-width: 940.98px) {
        .share-button {
            flex-direction: column;
            align-items: stretch;
        }

        .share-button .dropdown,
        .share-button .btn-whatsapp {
            width: 100%;
            margin-bottom: 10px;
            background: #272c3f;

        }

        .share-button .btn-whatsapp {
            margin-bottom: 0;
            background: #272c3f;

        }
        
    }
    
            .media {
            position: relative;
            padding-bottom: 56.25%; /* نسبة العرض إلى الارتفاع 16:9 */
            height: 0;
            overflow: hidden;
            max-width: 100%;
            background: #000;
            margin-bottom: 20px;
        }

        .media iframe,
        .media object,
        .media embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .media iframe {
            border-radius: 10px; /* حواف دائرية للفيديو */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* ظل خفيف للفيديو */
        }
    .dropbtn ,.btn-primary{
          background-color: #272c3f !important;
          font-weight: bold;

    }  
    .dropbtn:hover,.btn-primary:hover{
          background-color: #272c3f !important;
          font-weight: bold;

    }
    div.dropdown{
        border-radius:50px;
    }
    </style>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="profile-header mb-4">
                <div class="owl-carousel owl-theme">
                    <?php foreach ($headers as $header): ?>
                        <div class="item"><img src="../../profiles_photos/<?php echo htmlspecialchars($header['header']); ?>" alt="Header Image"></div>
                    <?php endforeach; ?>
                </div>
                <!-- أزرار مشاركة الصفحة وأرسل استشارتك الآن -->
                <div class="share-button mt-3 d-flex justify-content-between">
                    <div class="dropdown">
                        <button class="dropbtn btn btn-whatsapp dropdown-toggle" type="button" id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-share-alt"></i> مشاركة الصفحة
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                            <li><a class="dropdown-item" href="https://wa.me/?text=<?php echo urlencode($page_url); ?>" target="_blank"><i class="fab fa-whatsapp"></i> واتساب</a></li>
                            <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($page_url); ?>" target="_blank"><i class="fab fa-facebook"></i> فيسبوك</a></li>
                            <li><a class="dropdown-item" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($page_url); ?>" target="_blank"><i class="fab fa-twitter"></i> تويتر</a></li>
                            <li><a class="dropdown-item" href="mailto:?subject=Check this page&body=<?php echo urlencode($page_url); ?>" target="_blank"><i class="fas fa-envelope"></i> البريد الإلكتروني</a></li>
                            <li><a class="dropdown-item" href="../../profiles_photos/<?php echo htmlspecialchars($office_data['qr']); ?>" target="_blank"><i class="fas fa-solid fa-qrcode"></i> QR Code</a></li>
                          <!--  <li><a class="dropdown-item" href="https://wa.me/?text=<?php //echo urlencode($page_url2); ?>/profiles_photos/<?php //echo htmlspecialchars($office_data['qr']); ?>" target="_blank"><i class="fas fa-brands fa-square-whatsapp"></i> QR Code to whatsapp</a></li>-->
                        </ul>
                    </div>
                    

                    <a style="text-align:center;" href="https://api.whatsap.com/send/?phone=<?php echo htmlspecialchars($office_data['whatsapp']); ?>&text&type=phone_number&app_absent=0" target="_blank" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> أرسل استشارتك الآن
                    </a>
   

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card map-card">
                <div class="card-body">
                    <div id="map" class="map-container"></div>
                    <button class="dropbtn btn btn-success visit-button" onclick="getDirections()">
                        <i class="fas fa-map-marker-alt"></i> نتشرف بزيارتك
                    </button>
                    <div class="contact-icons mt-3" style="text-align: right; direction: rtl;">
                        <a href="tel:<?php echo htmlspecialchars($office_data['phone']); ?>" target="_blank" style="display: block; margin-bottom: 10px; direction:ltr;">
                             <?php echo htmlspecialchars($office_data['phone']); ?><i class="fas fa-phone"></i>
                        </a>
                        <a href="https://wa.me/<?php echo htmlspecialchars($office_data['whatsapp']); ?>" target="_blank" style="display: block; margin-bottom: 10px;">
                            <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($office_data['whatsapp']); ?>
                        </a>
                        <a href="<?php echo htmlspecialchars($office_data['facebook']); ?>" target="_blank" style="display: block; margin-bottom: 10px;">
                            <i class="fab fa-facebook"></i> <?php echo htmlspecialchars($office_data['facebook']); ?>
                        </a>
                        <a href="<?php echo htmlspecialchars($office_data['twitter']); ?>" target="_blank" style="display: block; margin-bottom: 10px;">
                            <i class="fab fa-twitter"></i> <?php echo htmlspecialchars($office_data['twitter']); ?>
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($office_data['email_address']); ?>" style="display: block; margin-bottom: 10px;">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($office_data['email_address']); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5 custom-container" style="padding: 3;">
<div class="col-md-12 col-sm-12">
    <div class="card card-custom mb-12">
        <div class="card-body text-center">
            <img src="../../profiles_photos/<?php echo htmlspecialchars($office_data['logo']); ?>" alt="Logo" class="rounded-circle logo">
            <h2><?php echo htmlspecialchars($office_data['fname']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($office_data['desc1'])); ?></p>
            <div class="qr-code">
                <img src="../../profiles_photos/<?php echo htmlspecialchars($office_data['qr']); ?>" alt="QR Code" class="qr">
            </div>
        </div>
    </div>
</div>
</div>
<div class="container mt-5 custom-container" style="padding: 3;">

         <div class="col-md-12 col-sm-12">
            <div class="card mb-12" style="min-width: 300px; max-width: 100%;">
                <div class="card-body" id="htmlCustom">
                    <h3 class="text-center">نبذة عن مكتبنا</h3>
                    <div><?php echo $office_data['desc2']; ?></div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <script>
         $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                items: 1,
                loop: true,
                nav: true,
                autoplay: true,
                autoplayTimeout: 3000,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn'
            });
        });

        </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>	
    <script>
    $(document).ready(function(){
        $('.dropdown-toggle').dropdown();
    });
</script>
    
    <script>
        function getDirections() {
            const lat = <?php echo htmlspecialchars($office_data['latitude']); ?>;
            const lng = <?php echo htmlspecialchars($office_data['longitude']); ?>;
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
        }

        function initMap() {
            const officeLocation = { lat: <?php echo htmlspecialchars($office_data['latitude']); ?>, lng: <?php echo htmlspecialchars($office_data['longitude']); ?> };
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: officeLocation
            });
            const marker = new google.maps.Marker({
                position: officeLocation,
                map: map
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=$setting['api_map']?>&callback=initMap"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var oembedElements = document.querySelectorAll('oembed[url]');
        oembedElements.forEach(function(element) {
            var iframe = document.createElement('iframe');
            iframe.setAttribute('width', '560');
            iframe.setAttribute('height', '315');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            
            // Extract video ID from YouTube URL
            var url = element.getAttribute('url');
            var videoId = url.split('v=')[1];
            var ampersandPosition = videoId.indexOf('&');
            if(ampersandPosition != -1) {
              videoId = videoId.substring(0, ampersandPosition);
            }
            iframe.setAttribute('src', 'https://www.youtube.com/embed/' + videoId);

            element.parentNode.replaceChild(iframe, element);
        });
    });
</script>

</body>
</html>
