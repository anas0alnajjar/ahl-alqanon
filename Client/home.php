<?php 
    session_start();
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'Client') {
            $user_id = $_SESSION['user_id'];
            $tables = array(
                'cases' => array('label' => 'القضايا', 'icon' => 'fa-balance-scale', 'url' => 'cases.php'),
                'documents' => array('label' => 'الوثائق', 'icon' => 'fa-file-text', 'url' => 'documents.php'),
                'todos' => array('label' => 'المهام', 'icon' => 'fa-tasks', 'url' => 'tasks.php')
            );

            include "../DB_connection.php";
            include '../language.php'; 
            include "logo.php";
            include 'permissions_script.php';

            try {
                // الحصول على office_id الخاص بالعميل
                $sql_office = "SELECT office_id FROM clients WHERE client_id = :client_id";
                $stmt_office = $conn->prepare($sql_office);
                $stmt_office->bindParam(':client_id', $user_id, PDO::PARAM_INT);
                $stmt_office->execute();
                $office_id = $stmt_office->fetchColumn();

                // التحقق من أن office_id تم العثور عليه
                if ($office_id) {
                    $tableCounts = [];
                    foreach ($tables as $table => $data) {
                        if ($table == 'cases') {
                            // جلب عدد القضايا التي بها معرف العميل
                            $sql = "SELECT COUNT(*) AS count 
                                    FROM cases 
                                    WHERE client_id = :client_id 
                                    OR FIND_IN_SET(:client_id, plaintiff)";
                        } elseif ($table == 'documents') {
                            // جلب عدد الوثائق المرتبطة بمعرف العميل
                            $sql = "SELECT COUNT(documents.document_id) AS count
                                    FROM documents 
                                    WHERE client_id = :client_id";
                        } elseif ($table == 'todos') {
                            // جلب عدد المهام غير المقروءة من قبل العميل
                            $sql = "SELECT COUNT(*) AS count 
                                    FROM todos 
                                    WHERE read_by_client != 1 OR read_by_client IS NULL
                                    AND (client_id = :client_id 
                                        OR case_id IN (SELECT case_id FROM cases WHERE client_id = :client_id OR FIND_IN_SET(:client_id, plaintiff)))";
                        } else {
                            continue; // في حالة وجود جداول أخرى لا تحتاج إلى التحقق
                        }

                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $count = $row['count'];

                        // تخزين قيمة الصفوف في المصفوفة باسم الجدول
                        $tableCounts[$table] = $count;
                    }



                } else {
                    $errors =  "No office found for this client.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        
        <link rel="icon" href="../img/<?=$setting['logo']?>">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-1eH6QY7EDqrIPf5bJoGNNd5tWJbF3xxzVJ/Or+EBkpemVBm0uw4ZyVZdunFw+JABmVxVBCjQihFvCe/n+VqhQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
        
        <!-- هذا من أجل متصفح فاير فوكس بحال لم يكن مستجيباً -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />


        <link rel="stylesheet" href="../css/style.css">
        <style>
    body {
        background-color: #f8f9fa;
        font-family: "Cairo", sans-serif;
        direction: rtl;
    }

    #containerFulid {
        padding-top: 20px;
    }

    .card {
        border: none;
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        text-align: center;
        padding: 20px;
    }

    .card-title {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .card-icon {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .badge {
        background-color: #ffc107;
        border-radius: 50%;
        padding: 5px 10px;
        position: absolute;
        top: 10px;
        right: 10px;
        color: #fff;
        font-size: 0.8rem;
    }

    a {
        color: unset;
        text-decoration: none;
    }

    /* أنيميشن للألوان */
    @keyframes colorChange {
        0% { background-position: 0% 30%; }
        100% { background-position: 30% 20%; }
    }

    .card:hover {
        background: linear-gradient(230deg, #fd7921, #fbb03b, #3db9fc, #8e44ad, #fd7921);
        background-size: 200% 200%;
        -webkit-animation: gradientBG 5s ease infinite;
        -moz-animation: gradientBG 5s ease infinite;
        animation: gradientBG 5s ease infinite;
    }

    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
    }

    .card:hover .card-body {
        color: white; /* تغيير لون النص إلى الأبيض */
    }

    .card-body {
        transition: color 0.5s ease; /* تأثير انتقالي على تغيير لون النص */
    }
    #client-card:hover {
        
        background: unset;
        background-size: unset;
        -webkit-animation: unset;
        -moz-animation: unset;
        animation: unset;
        color: green;

    }
    #clientName{
        color: black;
    }
    #clientName:hover{
        color: black;
    }
        </style>

    </head>
    <body>
    <?php include "inc/footer.php"; ?>    
        
        
        <?php include "inc/navbar.php"; ?>
                        <?php if (isset($errors)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?=$errors?>
                            </div>
                            
                        <?php exit; } ?>
        <div class="container-fluid" id="containerFulid">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if ($pages['cases']['read']) : ?>
            <div class="col-lg-6 cases">
                <div class="card cases-card">
                <a href="cases.php">
                    <div class="card-body">
                        <i class="fa fa-balance-scale card-icon"></i>
                        <h5 class="card-title"><?= __('cases') ?></h5>
                        <span class="badge bg-primary rounded-pill">
                            <i class="fas fa-balance-scale"></i> <?=$tableCounts['cases']?> <?= __('cases') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pages['documents']['read']) : ?>
            <div class="col-lg-6 documents">
                <div class="card documents-card">
                <a href="documents.php">  
                <div class="card-body">
                        <i class="fa fa-file-text card-icon"></i>
                        <h5 class="card-title"><?= __('documents')?></h5>
                        <span class="badge bg-info rounded-pill">
                            <i class="fas fa-file"></i> <?=$tableCounts['documents']?> <?= __('documents') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pages['notifications']['read']) : ?>
            <div class="col-lg-12 notifications">
                <div class="card">
                <a href="tasks.php">  
                <div class="card-body">
                        <i class="fa fa-tasks card-icon"></i>
                        <h5 class="card-title"><?= __('tasks') ?></h5>
                        <span class="badge bg-success rounded-pill">
                            <i class="fas fa-tasks"></i> <?=$tableCounts['todos']?> <?= __('task') ?>
                        </span>
                    </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>
            
            <div class="col-lg-6">
                <div class="card">
                <a style="text-decoration:none;" href="#" data-bs-toggle="modal" data-clientid="<?php echo $user_id; ?>" class="viewclient">
                        <div class="card-body">
                            <i class="fa fa-user card-icon"></i>
                            <h5 class="card-title"><?= __('profile') ?></h5>
                            <div class="col-md-4">
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                <a href="../logout.php">  
                <div class="card-body">
                        <i class="fas fa-sign-out-alt card-icon"></i>
                        <h5 class="card-title"><?= __('log_out') ?></h5>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_client_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">المعلومات الشخصية</h5>
                    <button style="float:left;margin:inherit;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height:70vh;overflow:auto;">
                    <div class="container mt-3">
                        <div id="client-card" class="card" style="width: auto;">
                            <div class="card-body text-center">
                                <li class="list-group-item text-center">
                                    <img id="lawyerLogo" src="#" class="img-fluid rounded mx-auto d-block" alt="Lawyer Logo" style="max-width: 150px; height: auto;">
                                </li>
                                <h5  style="margin:5%;" class="card-title" id="clientName"></h5>
                            </div>
                            <ul class="list-group list-group-flush mb-5">
                                <li class="list-group-item"><strong>الاسم الأول:</strong> <span id="clientFirstName"></span></li>
                                <li class="list-group-item"><strong>العائلة:</strong> <span id="clientLastName"></span></li>
                                <li class="list-group-item"><strong>العنوان:</strong> <span id="clientAddress"></span></li>
                                <li class="list-group-item"><strong>سنة التولد:</strong> <span id="clientBirthYear"></span></li>
                                <li class="list-group-item"><strong>الايميل:</strong> <a href="#" id="clientEmail"><i class="fas fa-envelope"></i> <span id="clientEmailText"></span></a></li>
                                <li class="list-group-item"><strong>الجنس:</strong> <span id="clientGender"></span></li>
                                <li class="list-group-item" style="direction: rtl;">
                                    <strong>الهاتف:</strong> 
                                    <a href="#" id="clientPhone">
                                        <i class="fas fa-phone"></i> 
                                        <span id="clientPhoneText" style="direction: ltr; display: inline-block;"></span>
                                    </a>
                                </li>
                                <li class="list-group-item"><strong>المدينة:</strong> <span id="clientCity"></span></li>
                                <li class="list-group-item"><strong>عدد القضايا:</strong> <span id="clientCaseCount"></span></li>
                                <li class="list-group-item"><strong>عدد الجلسات:</strong> <span id="clientSessionCount"></span></li>
                                <li class="list-group-item"><strong>المحامي:</strong> <span id="lawyerName"></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <a href="edit-profile.php?client_id=1" class="btn btn-primary">تعديل</a>
                </div>
            </div>
        </div>
    </div>







        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                $("#navLinks li:nth-child(1) a").addClass('active');
            });
        </script>

    <script>

    $(document).ready(function() {

    // فتح المودال عند الضغط على الزر

    $('.viewclient').click(function() {

        $('#view_client_modal').modal('show');

    }); });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const clientModal = new bootstrap.Modal(document.getElementById('view_client_modal'));

        function loadClientData(client_id) {
            console.log('Requesting data for client_id:', client_id); // Log the client_id
            fetch('req/get_client_info.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'client_id=' + encodeURIComponent(client_id)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Received data:', data); // Log the received data
                if (data && Object.keys(data).length > 0) {
                    document.getElementById('lawyerLogo').src = data.logo_path;
                    document.getElementById('clientName').innerText = data.first_name + ' ' + data.last_name;
                    document.getElementById('clientFirstName').innerText = data.first_name;
                    document.getElementById('clientLastName').innerText = data.last_name;
                    document.getElementById('clientAddress').innerText = data.address;
                    document.getElementById('clientBirthYear').innerText = data.date_of_birth;
                    document.getElementById('clientEmailText').innerText = data.email;
                    document.getElementById('clientEmail').href = 'mailto:' + data.email;
                    document.getElementById('clientGender').innerText = data.gender;
                    document.getElementById('clientPhoneText').innerText = data.phone;
                    document.getElementById('clientPhone').href = 'tel:' + data.phone;
                    document.getElementById('clientCity').innerText = data.city;
                    document.getElementById('clientCaseCount').innerText = data.num_cases;
                    document.getElementById('clientSessionCount').innerText = data.num_sessions;
                    document.getElementById('lawyerName').innerText = data.lawyer_name;

                    clientModal.show();
                } else {
                    console.error('No valid data found for the provided client ID.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Adding event listener for all buttons with class 'viewclient'
        document.querySelectorAll('.viewclient').forEach(button => {
            button.addEventListener('click', function () {
                const client_id = this.getAttribute('data-clientid');
                loadClientData(client_id);
            });
        });
    });

    </script>



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