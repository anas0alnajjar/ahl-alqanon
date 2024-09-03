<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";
    include 'permissions_script.php';
    if ($pages['offices']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    
    $admin_id = $_SESSION['user_id']; // Get admin_id from session

    $page_number = isset($_GET['page_number']) ? $_GET['page_number'] : 1;
    $total_records_per_page = 10;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Base SQL query
    $sql = "SELECT * FROM offices WHERE admin_id = :admin_id";

    // Prepare statement with placeholders
    if (!empty($search)) {
        $sql .= " AND office_name LIKE :search";
    }

    $sql .= " ORDER BY office_id DESC LIMIT :offset, :total_records_per_page";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':total_records_per_page', $total_records_per_page, PDO::PARAM_INT);

    // Execute main query with search criteria and pagination
    $stmt->execute();
    $result = $stmt->fetchAll();

    // Count total records
    if (!empty($search)) {
        $sqlCount = "SELECT COUNT(*) FROM offices WHERE admin_id = :admin_id AND office_name LIKE :search";
        $stmtCount = $conn->prepare($sqlCount);
        $stmtCount->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmtCount->bindParam(':search', $searchParam, PDO::PARAM_STR);
    } else {
        $sqlCount = "SELECT COUNT(*) FROM offices WHERE admin_id = :admin_id";
        $stmtCount = $conn->prepare($sqlCount);
        $stmtCount->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    }

    $stmtCount->execute();
    $total_records = $stmtCount->fetchColumn();
    $total_pages = ceil($total_records / $total_records_per_page);


?>

<?php
function truncate_text($text, $limit) {
    // تحقق إذا كانت القيمة فارغة أو null
    if (empty($text)) {
        return $text;
    }

    $words = explode(" ", $text);
    if (count($words) > $limit) {
        return implode(" ", array_slice($words, 0, $limit)) . "...";
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Offices</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <style>
    .n-table {
        min-width:100%;
    }
    .modal-content{
        max-height:90vh;
        overflow:auto;
    }
td{
        text-wrap: nowrap;
}
    </style>
</head>
<body>
    <?php include "inc/footer.php"; ?>  



    <div class="modal fade" id="add_office_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display:unset;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 5px;"></button>
                <h5 style="float: right;" class="modal-title" id="editModalLabel">إضافة المكتب</h5>
            </div>
            <div class="modal-body">
                <form method="post" class="shadow p-3 mt-5 form-w" action="req/office-add.php" enctype="multipart/form-data" style="direction:rtl;">
                    <?php if (isset($_GET['poperror'])) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_GET['poperror']?>
                        </div>
                    <?php } ?>
                    <!-- حقول النموذج... -->
                    <div class="mb-3">
                        <label for="office_name" class="form-label">اسم المكتب </label>
                        <input type="text" class="form-control" value="" name="office_name" id="office_name" required>
                    </div>
                        <input type="hidden" value="<?=$admin_id?>" name="admin_id" id="admin_id">

                    
                    <div class="form-check custom-checkbox mb-3">
                        <input type="checkbox" class="form-check-input" id="stopAdd" name="stop" value="1">
                        <label class="form-label" for="stopAdd">إيقاف مؤقت</label>
                    </div>
                    <div class="mb-3">
                        <label for="stop_dateAdd" class="form-label">تاريخ الإيقاف</label>
                        <input type="date" class="form-control" id="stop_dateAdd" name="stop_date">
                    </div>

                    <!-- Collapse for Print Settings -->
                    <div class="accordion mb-3" id="printSettings">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    إعدادات الطباعة
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#printSettings">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label for="header_image" class="form-label">الهيدر</label>
                                        <input type="file" class="form-control" id="header_image" name="header_image" accept="image/*">
                                        <img id="header_image_preview" src="#" alt="Header Image" style="display:none; width: 100%; margin-top: 10px;">
                                    </div>
                                    <div class="mb-3">
                                        <label for="footer_text" class="form-label">الفوتر</label>
                                        <textarea name="footer_text" id="footer_text" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button id="close000" type="button" class="btn btn-secondary">إغلاق</button>  
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>

<!-- End popup dialog box -->
<?php $button_text = ($pages['offices']['write'] == 0) ? 'تفاصيل المكتب' : 'تعديل المكتب'; ?>
<!-- Edit Modal -->
<div style="direction:rtl;" class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel"><?=$button_text?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 5px;"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="post" action="req/office-edit.php" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="officeInput" class="form-label">اسم المكتب</label>
            <input type="text" class="form-control" id="officeInput" name="office_name" required>
          </div>
          
          <input type="hidden" id="admin_id_edit" name="admin_id" required value="<?=$admin_id?>">
          
          <div class="form-check custom-checkbox mb-3">
            <input type="checkbox" class="form-check-input" id="stop" name="stop" value="1">
            <label class="form-label" for="stop">إيقاف مؤقت</label>
          </div>
          <div class="mb-3">
            <label for="stop_date" class="form-label">تاريخ الإيقاف</label>
            <input type="date" class="form-control" id="stop_date" name="stop_date">
          </div>
          <div class="mb-3">
            <label for="header_image_edit" class="form-label">الهيدر</label>
            <input type="file" class="form-control" id="header_image_edit" name="header_image" accept="image/*">
            <img id="header_image_preview_edit" src="#" alt="Header Image" style="display:none; width: 100%; margin-top: 10px;">
          </div>
          <div class="mb-3">
            <label for="footer_text_edit" class="form-label">الفوتر</label>
            <textarea name="footer_text" id="footer_text_edit" class="form-control"></textarea>
          </div>
          <input type="hidden" id="officeId" name="id">
          <button type="submit" class="btn btn-primary offices-write">تحديث</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Transfer Office -->
<div class="modal fade" id="transferOfficeModal" tabindex="-1" aria-labelledby="transferOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferOfficeModalLabel">ترحيل المكتب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferOfficeForm">
                    <div class="mb-3">
                        <label for="newOfficeSelect" class="form-label">اختر المكتب الجديد</label>
                        <select id="newOfficeSelect" class="form-select" name="new_office_id" required>
                            <option value="" selected disabled>اختر المكتب</option>
                            <?php
                            // جلب قائمة المكاتب لعرضها في القائمة المنسدلة
                            $sql = "SELECT office_id, office_name FROM offices WHERE admin_id = :admin_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':admin_id', $_SESSION['user_id']);
                            $stmt->execute();
                            $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($offices as $office) {
                                echo "<option value='{$office['office_id']}'>{$office['office_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="old_office_id" id="oldOfficeId">
                    <button type="submit" class="btn btn-primary">ترحيل</button>
                </form>
            </div>
        </div>
    </div>
</div>








    <?php 
        include "inc/navbar.php";
    ?>

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">
        <div class="btn-group mb-3" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
            <button class="btn btn-dark offices-add" id="office-add"> اضافة مكتب جديد</button>
        </div>
        
        <form action="offices.php" class="mt-3 n-table" method="GET">
        <div class="input-group mb-3 n-table" style="direction: ltr;">
            
            <input type="text" style="direction:rtl;" class="form-control" name="search" placeholder="ابحث هنا..." value="<?php echo htmlentities($search); ?>">
            
            <button class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
            
        </div>
        </form>
        
        <?php if (isset($_GET['success']) || (empty($result) && !empty($search))) { ?>
            <div class="alert alert-info mt-3 n-table" role="alert">
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
            <div class="alert alert-danger mt-3 n-table" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>

                <div class="table-responsive">
                    <table class="table table-responsive mt-3 n-table">
                        <thead>
                            <tr>
                                <th scope="col" style="font-weight:700;">#</th>
                                <th scope="col" style="font-weight:700;"> اسم المكتب </th>
                                <th scope="col" style="font-weight:700;"> الحالة </th>
                                <th scope="col" style="font-weight:700;"> الآدمن </th>
                                <th scope="col" style="font-weight:700;"> تاريخ التوقف </th>
                                <th scope="col" style="font-weight:700;"> الهيدر </th>
                                <th scope="col" style="font-weight:700;"> الفوتر </th>
                                <th scope="col" style="font-weight:700;"> الإجراءات </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $offset = ($page_number - 1) * $total_records_per_page;
                            $i = $offset + 1;
                            foreach ($result as $row): ?>
                                <tr>
                                    <th scope="row"><?=$i?></th>
                                    <td><?=$row['office_name']?></td>
                                    <td class="stop-status"><?= ($row['stop'] == 0) ? 'يعمل' : 'موقف موقتاً' ?></td>
                                    <?php 
                                    $admin_id = $row['admin_id'];   
                                    $sqlAdminName = "SELECT * FROM `admin` WHERE admin_id = :admin_id";
                                    $stmt = $conn->prepare($sqlAdminName);
                                    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $resultAdminName = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>                
                                    <td data-admin-id="<?=$row['admin_id']?>"><?= $resultAdminName['fname'] . ' ' . $resultAdminName['lname'] ?></td>
                                    <td><?= !empty($row['stop_date']) ? $row['stop_date'] : "لا يوجد تاريخ إيقاف" ?></td>
                                    <td data-header-image="<?=$row['header_image']?>"><?= $row['header_image'] ? "<img src='../../uploads/{$row['header_image']}' alt='Header Image' style='max-height:40px;width: 100px;'>" : "لا يوجد صورة" ?></td>
                                    <td style="display:none;"><?=$row['footer_text']?></td>
                                    <td id="footer_text"><?= truncate_text($row['footer_text'], 3) ?></td>
                                    <td style="text-wrap:nowrap;">
                                    <?php if ($pages['offices']['delete']) : ?>
                                        <button class="btn btn-info btn-sm m-auto transferOfficeBtn offices-delete" data-id="<?=$row['office_id']?>">ترحيل</button>
                                        <a href="req/office-delete.php?id=<?=$row['office_id']?>" class="btn btn-danger btn-sm m-auto delete-button offices-delete" data-id="<?=$row['office_id']?>">حذف</a>
                                        
                                    <?php endif; ?>
                                        <?php
                                        // Assuming $pages array and other necessary variables are already defined
                                        $button_text = ($pages['offices']['write'] == 0) ? 'عرض' : 'تعديل';
                                        
                                        ?>

                                        <button data-id="<?=$row['office_id']?>" class="btn btn-warning btn-sm m-auto offices-writee"><?=$button_text?></button>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>



<nav aria-label="Page navigation example">
            <ul class="pagination" style="direction: ltr;float:right;">
                <!-- Previous Page -->
                <li class="page-item <?php echo ($page_number <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page_number=<?php echo ($page_number - 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Previous</a>
                </li>

                <!-- First Page -->
                <?php if ($page_number > 3): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page_number=1<?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">1</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for ($i = max(1, $page_number - 2); $i <= min($total_pages, $page_number + 2); $i++): ?>
                    <li class="page-item <?php echo ($page_number == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page_number=<?php echo $i; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Last Page -->
                <?php if ($page_number < $total_pages - 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page_number=<?php echo $total_pages; ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Page -->
                <li class="page-item <?php echo ($page_number >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page_number=<?php echo ($page_number + 1); ?><?php echo (!empty($search) ? '&search=' . htmlentities($search) : ''); ?>">Next</a>
                </li>
            </ul>
        </nav>
        </div>
    </div>
    
    
    <?php } else { ?>

        <div class="alert alert-info d-flex align-items-center mt-3" role="alert" style="max-width: 600px; margin-left: auto;margin-right: 2rem;">
        <button href="" class="btn btn-dark mx-2" id="office-add">اضافة مكتب جديد</button>
        <span class="flex-grow-1 " style="text-align:right;direction:rtl;">لا يوجد أي مكتب حتى الآن </span>
    
</div>


    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
        <script>
      
      $(document).ready(function() {
          // فتح المودال عند الضغط على الزر
          $('#office-add').click(function() {
              $('#add_office_modal').modal('show');
          });
  
          // التحكم في إغلاق المودال بناءً على رسالة الخطأ أو النجاح
          <?php if (isset($_GET['poperror']) ){ ?>
              // إغلاق المودال بعد عرض رسالة الخطأ أو النجاح
              $('#add_office_modal').modal('show');
          <?php } ?>
      });
  </script>
  <script>
     $(document).ready(function() {
        
        $('.close-modal1').click(function() {
            $('#add_doc_modal').modal('hide');
        }); });
        $('.close-modal2').click(function() {
            $('#add_office_modal').modal('hide');
        }); 
        $('#close000').click(function() {
            $('#add_office_modal').modal('hide');
        }); 
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    // var editButtons = document.querySelectorAll("button[data-id]");
    var editButtons = document.querySelectorAll('.offices-writee');
    editButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var id = this.getAttribute("data-id");
            var row = this.closest("tr");
            var office_name = row.querySelector("td:nth-child(2)").innerText.trim();
            var stop = row.querySelector("td:nth-child(3)").innerText.trim() === 'موقف موقتاً';
            var admin_id = row.querySelector("td:nth-child(4)").getAttribute("data-admin-id").trim();
            var stop_date = row.querySelector("td:nth-child(5)").innerText.trim();
            var header_image = row.querySelector("td:nth-child(6)").getAttribute("data-header-image").trim();
            var footer_text = row.querySelector("td:nth-child(7)").innerText.trim();

            document.getElementById("officeId").value = id;
            document.getElementById("officeInput").value = office_name;
            document.getElementById("admin_id_edit").value = admin_id;
            document.getElementById("stop").checked = stop;
            document.getElementById("stop_date").value = stop_date;

            if (header_image && header_image !== "null") {
                document.getElementById("header_image_preview_edit").src = "../../uploads/" + header_image;
                document.getElementById("header_image_preview_edit").style.display = 'block';
            } else {
                document.getElementById("header_image_preview_edit").style.display = 'none';
                document.getElementById("header_image_preview_edit").src = "#";
            }

            document.getElementById("footer_text_edit").value = footer_text;

            var editModal = new bootstrap.Modal(document.getElementById("editModal"));
            editModal.show();
        });
    });

    document.getElementById('header_image_edit').addEventListener('change', function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('header_image_preview_edit').src = URL.createObjectURL(file);
            document.getElementById('header_image_preview_edit').style.display = 'block';
        }
    });
});

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
                text: "سيتم حذف كل البيانات المرتبطة بالمكتب (القضايا، الجلسات، المحامين..) ، وسيفقد كل مستخدمي المكتب بياناتهم وصلاحيتهم في الدخول !!",
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
<script>
document.getElementById('header_image').addEventListener('change', function() {
    const [file] = this.files;
    if (file) {
        document.getElementById('header_image_preview').src = URL.createObjectURL(file);
        document.getElementById('header_image_preview').style.display = 'block';
    }
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    var transferButtons = document.querySelectorAll('.transferOfficeBtn');
    transferButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var oldOfficeId = this.getAttribute('data-id');
            document.getElementById('oldOfficeId').value = oldOfficeId;

            // عرض المودال
            var transferModal = new bootstrap.Modal(document.getElementById('transferOfficeModal'));
            transferModal.show();

            // تحديث قائمة المكاتب لإخفاء المكتب الحالي
            var officeOptions = document.querySelectorAll('#newOfficeSelect option');
            officeOptions.forEach(function(option) {
                if (option.value === oldOfficeId) {
                    option.style.display = 'none';
                } else {
                    option.style.display = 'block';
                }
            });
        });
    });

    // إرسال النموذج عبر AJAX
    document.getElementById('transferOfficeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);

        fetch('req/transfer_office.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الترحيل بنجاح',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء عملية الترحيل',
            });
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
?>
