<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";

    include 'permissions_script.php';
    if ($pages['clients']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    
    $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
    $total_records_per_page = 6;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // جلب المكاتب المرتبطة بالآدمن
    $admin_id = $_SESSION['user_id'];
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        // Base SQL query
        $sql = "SELECT * FROM clients WHERE office_id IN ($office_ids)";
        $searchParam = "%$search%";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (CONCAT(clients.first_name, ' ', clients.last_name) LIKE ? OR clients.address LIKE ? OR clients.city LIKE ? OR clients.email LIKE ? OR clients.phone LIKE ?)";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
        }
        
        // Add ORDER BY clause to the SQL query
        $sql .= " ORDER BY clients.client_id DESC";
        
        // Add LIMIT clause
        $sql .= " LIMIT " . (int)$offset . ", " . (int)$total_records_per_page;
        
        // Prepare statement
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        
        // Count total records
        if (empty($search)) {
            $total_records = $conn->query("SELECT COUNT(*) FROM clients WHERE office_id IN ($office_ids)")->fetchColumn();
        } else {
            $count_sql = "SELECT COUNT(*) FROM clients WHERE office_id IN ($office_ids) AND (CONCAT(clients.first_name, ' ', clients.last_name) LIKE ? OR clients.address LIKE ? OR clients.city LIKE ? OR clients.email LIKE ? OR clients.phone LIKE ?)";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->execute($params);
            $total_records = $count_stmt->fetchColumn();
        }
        
        $total_pages = ceil($total_records / $total_records_per_page);
    } else {
        $result = [];
        $total_records = 0;
        $total_pages = 0;
    }
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        *{
            direction: rtl;
        }
        svg {
            margin-left: 4px;
        }

    </style>
</head>
<body>

<div class="modal fade" id="view_client_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">عرض معلومات الموكل</h5>
                <button style="float:left;margin:inherit;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:70vh;overflow:auto;">
                <div class="container mt-3">
                    <div class="card" style="width: auto;">
                        <div class="card-body text-center">
                            <li class="list-group-item text-center">
                                <img id="lawyerLogo" src="#" class="img-fluid rounded mx-auto d-block" alt="Lawyer Logo" style="max-width: 150px; height: auto;">
                            </li>
                            <h5  style="margin:5%;" class="card-title" id="clientName"></h5>
                        </div>
                        <ul class="list-group list-group-flush">
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
            </div>
        </div>
    </div>
</div>

<!-- End -->
    <?php include "inc/navbar.php"; ?>
 

    <?php if (!empty($result) || (empty($result) && !empty($search))) { ?>
    <div class="container mt-5" style="direction: rtl;">
            <div class="btn-group mb-3" style="direction:ltr;">
                <a href="home.php" class="btn btn-light">الرئيسية</a>
                <?php if ($pages['clients']['add']) : ?>
                <a href="client-add.php" class="btn btn-dark clients-add">إضافة موكل</a>
                <?php endif; ?>
             </div> 
        <form action="clients.php" class="mt-3 align-center" method="GET">
            <div class="input-group mb-3 text-center" style="max-width: 100%;min-width:80%; direction: ltr;">
                <input type="text" class="form-control" name="search" placeholder="ابحث عن موكل هنا..." value="<?php echo htmlentities($search); ?>">
                <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </form>
        
        <?php if (isset($_GET['success']) || (empty($result) && !empty($search))) { ?>
            <div class="alert alert-info" role="alert">
                <?php 
                if (isset($_GET['success'])) {
                    echo $_GET['success'];
                } else {
                    echo "لم يتم إيجاد ما يطابق بحثك!";
                }
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>

        <?php if (!empty($result)) { ?>
            <div class="row">
                <?php foreach ($result as $row): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body" style="overflow:hidden;">
                                <h5 style="text-wrap:nowrap;" class="card-title">
                                <button style="text-decoration:none;" class="btn btn-link viewclient" data-clientid="<?php echo $row['client_id']; ?>">
                                    <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                                </button>
                                </h5>
                                <p style="text-wrap:nowrap;" class="card-text">
                                    <strong>الإيميل:</strong> 
                                    <a href="mailto:<?=$row['email']?>" style="text-decoration:none;color: #5927e5">
                                        <i class="fa fa-envelope"></i> <?=$row['email']?>
                                    </a>
                                </p>
                                <p style="text-wrap:nowrap;" class="card-text">
                                    <strong>الهاتف:</strong> 
                                    <a href="tel:<?=$row['phone']?>" style="text-decoration:none;color: #01abdc;">
                                        <i class="fa fa-phone"></i> <span style="direction:ltr;display:inline-block;"> <?=$row['phone']?></span>
                                    </a>
                                </p>
                                <?php if ($pages['clients']['write']) : ?>
                                <a style="text-wrap:nowrap;" href="client-edit.php?client_id=<?=$row['client_id']?>" class="btn btn-warning btn-sm clients-write">تعديل</a>
                                <?php endif; ?>
                                <?php if ($pages['clients']['delete']) : ?>
                                <a style="text-wrap:nowrap;" href="client-delete.php?client_id=<?=$row['client_id']?>" class="btn btn-danger btn-sm delete-button clients-delete" data-id="<?=$row['client_id']?>">حذف</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination" style="direction: ltr; float: right;">
                    <!-- Previous Page -->
                    <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="clients.php?page_number=<?php echo ($page_number - 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">السابق</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="clients.php?page_number=<?php echo $i; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Page -->
                    <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="clients.php?page_number=<?php echo ($page_number + 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">التالي</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>
    <?php } else { ?>
        <div class="alert alert-info w-450 d-flex align-items-center justify-content-between mt-3 mx-5" role="alert">
            <span>لا يوجد موكلين حتى الآن!</span>
            <?php if ($pages['clients']['add']) : ?>
            <a href="client-add.php" class="btn btn-primary">أضف موكل جديد</a>
            <?php endif; ?>
        </div>
    <?php } ?>
    <div id="additional-info-popup" style="display: none;">

    <!-- هنا ستظهر المعلومات الإضافية -->

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>


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




<script>
     $(document).ready(function() {
        $('.close-modal').click(function() {

            $('#view_client_modal').modal('hide');

        }); });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var deleteButtons = document.querySelectorAll(".delete-button");
    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function(event) {
            event.preventDefault(); // لمنع الانتقال الفوري للرابط

            var id = this.getAttribute("data-id");
            var href = this.getAttribute("href");

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف كل ما هو مرتبط بالموكل! وثائق، مدفوعات، قضايا...إلخ!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذفها!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href; // الانتقال إلى رابط الحذف إذا وافق المستخدم
                }
            });
        });
    });
});

</script>
<?php include "inc/footer.php"; ?>  
</body>


</html>

<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>

