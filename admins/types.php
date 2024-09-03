<?php 

session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admins') {
    // Pagination
    include "../DB_connection.php";
    include "logo.php";

    include 'permissions_script.php';
    if ($pages['expense_types']['read'] == 0) {
        header("Location: home.php");
        exit();
    }
    
    $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
    $total_records_per_page = 10;
    $offset = ($page_number - 1) * $total_records_per_page;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $admin_id = $_SESSION['user_id'];

    // جلب مكاتب الآدمن
    $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
    $stmt_offices = $conn->prepare($sql_offices);
    $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt_offices->execute();
    $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($offices)) {
        $office_ids = implode(',', $offices);

        // Base SQL query with INNER JOIN to get office name
        $sql = "SELECT costs_type.*, offices.office_name 
                FROM costs_type 
                INNER JOIN offices ON costs_type.office_id = offices.office_id 
                WHERE costs_type.office_id IN ($office_ids)";

        // Prepare statement with placeholders for search
        if (!empty($search)) {
            $sql .= " AND (costs_type.type LIKE ? OR offices.office_name LIKE ?)";
            $sql .= " ORDER BY costs_type.id DESC LIMIT " . (int)$offset . ", " . (int)$total_records_per_page;
            $sqlCount = "SELECT COUNT(*) 
                         FROM costs_type 
                         INNER JOIN offices ON costs_type.office_id = offices.office_id 
                         WHERE costs_type.office_id IN ($office_ids)
                         AND (costs_type.type LIKE ? OR offices.office_name LIKE ?)";
            $stmt = $conn->prepare($sql);
            $stmtCount = $conn->prepare($sqlCount);
            $searchParam = "%$search%";
            $stmt->bindParam(1, $searchParam, PDO::PARAM_STR);
            $stmt->bindParam(2, $searchParam, PDO::PARAM_STR);
            $stmtCount->bindParam(1, $searchParam, PDO::PARAM_STR);
            $stmtCount->bindParam(2, $searchParam, PDO::PARAM_STR);

            // Execute query to count total records with search criteria
            $stmtCount->execute();
            $total_records = $stmtCount->fetchColumn();
            $total_pages = ceil($total_records / $total_records_per_page);

            // Execute main query with search criteria and pagination
            $stmt->execute();
        } else {
            $sql .= " ORDER BY costs_type.id DESC LIMIT " . (int)$offset . ", " . (int)$total_records_per_page;
            $stmt = $conn->prepare($sql);
            $total_records = $conn->query("SELECT COUNT(*) FROM costs_type WHERE office_id IN ($office_ids)")->fetchColumn();
            $total_pages = ceil($total_records / $total_records_per_page);
        }

        $stmt->execute();
        $result = $stmt->fetchAll();
    } else {
        $result = [];
        $total_pages = 0;
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Type</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <style>

    </style>
</head>
<body>



    <div class="modal fade" id="add_client_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="display:unset;">
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 5px;"></button>
                <h5 style="float: right;" class="modal-title" id="editModalLabel">إضافة النوع</h5>
                    
                </div>
                <div class="modal-body">

                    <form method="post" class="shadow p-3 mt-5 n-table" action="req/types-add.php" style="direction:rtl;">
                      <?php if (isset($_GET['poperror'])) { ?>
                          <div class="alert alert-danger" role="alert">
                          <?=$_GET['poperror']?>
                          </div>
                        <?php } ?>
                        <!-- حقول النموذج... -->
                      
                      <div class="mb-3">
                        <label for="types" class="form-label">نوع المصروف </label>
                        <input type="text" 
                              class="form-control"
                              value="" 
                              name="types"
                              id="types"
                              required>
                      </div>
                      <div class="mb-3">
                      <label class="form-label">المكتب</label>
                            <select id="office_id" class="form-select" name="office_id" required>
                                <option value="" disabled selected>اختر المكتب</option>
                                <?php
                                    // افتراض أن معرف الآدمن موجود في الجلسة
                                    $admin_id = $_SESSION['user_id'];

                                    // استعلام لجلب المكاتب المرتبطة بالآدمن فقط
                                    $sql = "SELECT `office_id`, `office_name` FROM offices WHERE admin_id = :admin_id";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // تحقق من وجود نتائج وعرضها في القائمة المنسدلة
                                    if ($stmt->rowCount() > 0) {
                                        foreach ($result2 as $row) {
                                            $id = $row["office_id"];
                                            $office_name = $row["office_name"];
                                            echo "<option value='$id'>$office_name</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                                    }
                                ?>
                            </select>
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

<!-- Edit Modal -->
<div style="direction:rtl;" class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">تعديل النوع</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute;left: 5px;"></button>
      </div>
      <div class="modal-body">
        <form id="editFormExpTypes" method="post" action="req/types-edit.php">
          <div class="mb-3">
            <label for="typeInput" class="form-label">النوع</label>
            <input type="text" class="form-control" id="typeInput" name="type" required>
          </div>
          <div class="mb-3">
            <label class="form-label">المكتب</label>
            <select id="officeIdEdit" class="form-select" name="office_id" required>
                <option value="" disabled>اختر المكتب</option>
                <?php
                    // تحقق من وجود نتائج وعرضها في القائمة المنسدلة
                    if ($stmt->rowCount() > 0) {
                        foreach ($result2 as $row) {
                            $id = $row["office_id"];
                            $office_name = $row["office_name"];
                            echo "<option value='$id'>$office_name</option>";
                        }
                    } else {
                        echo "<option value='' disabled>لا توجد مكاتب مرتبطة</option>";
                    }
                ?>
            </select>
          </div>
          <input type="hidden" id="typeId" name="id">
          <input type="hidden" id="officeIdCurrent" name="office_id_current">
          <button type="submit" class="btn btn-primary">تحديث</button>
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
            <div class="btn-group" style="direction:ltr;">
                <a href="home.php" class="btn btn-light">الرئيسية</a>
                <?php if ($pages['expense_types']['add']) : ?>
                    <button class="btn btn-dark" id="disability-add"> اضافة نوع جديد</button>
                <?php endif; ?>
            </div>
        <form action="types.php" class="mt-3 n-table" method="GET">
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
            <th scope="col" style="font-weight:700;"> النوع  </th>
            <th scope="col" style="font-weight:700;"> المكتب  </th>
            <?php if ($pages['expense_types']['write'] || $pages['expense_types']['delete']) : ?>
            <th scope="col" style="font-weight:700;"> الإجراءات  </th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $offset = ($page_number - 1) * $total_records_per_page;
        $i = $offset + 1; // البداية من الصفر
        foreach ($result as $row): ?>
            <tr>
                <th scope="row"><?=$i?></th>
                <td>
                    <?=$row['type']?>
                </td>
                <td>
                    <?=$row['office_name']?>
                </td>
                <?php if ($pages['expense_types']['write'] || $pages['expense_types']['delete']) : ?>
                <td style="text-wrap:nowrap;">
                <?php if ($pages['expense_types']['write']) : ?>                    
                    <button data-id="<?=$row['id']?>" data-office-id="<?=$row['office_id']?>" class="btn btn-warning btn-sm m-auto">تعديل</button>
                <?php endif; ?>                    
                <?php if ($pages['expense_types']['delete']) : ?>                    
                    <a href="req/types-delete.php?id=<?=$row['id']?>" class="btn btn-danger btn-sm m-auto delete-button" data-id="<?=$row['id']?>">حذف</a>
                <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php $i++; // زيادة القيمة بمقدار 1 للصفحة التالية ?> 
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

        <div class="alert alert-info d-flex align-items-center mt-3" role="alert" style="max-width: 400px; margin-left: auto;margin-right: 2rem;">
        <button href="" class="btn btn-dark mx-2" id="disability-add">اضافة نوع جديد</button>
        <span class="flex-grow-1 ">لا يوجد أنواع للمصاريف حتى الآن</span>
    
</div>


    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
        <script>
      
      $(document).ready(function() {
          // فتح المودال عند الضغط على الزر
          $('#disability-add').click(function() {
              $('#add_client_modal').modal('show');
          });
  
          // التحكم في إغلاق المودال بناءً على رسالة الخطأ أو النجاح
          <?php if (isset($_GET['poperror']) ){ ?>
              // إغلاق المودال بعد عرض رسالة الخطأ أو النجاح
              $('#add_client_modal').modal('show');
          <?php } ?>
      });
  </script>
  <script>
     $(document).ready(function() {
        
        $('.close-modal1').click(function() {
            $('#add_doc_modal').modal('hide');
        }); });
        $('.close-modal2').click(function() {
            $('#add_client_modal').modal('hide');
        }); 
        $('#close000').click(function() {
            $('#add_client_modal').modal('hide');
        }); 
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var editButtons = document.querySelectorAll("button[data-id]");
    editButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var id = this.getAttribute("data-id");
            var type = this.closest("tr").querySelector("td").innerText;
            var officeId = this.getAttribute("data-office-id"); // Assume office_id is stored in a data attribute

            document.getElementById("typeId").value = id;
            document.getElementById("typeInput").value = type;
            document.getElementById("officeIdCurrent").value = officeId; // Store current office_id in hidden input

            var officeSelect = document.getElementById("officeIdEdit");
            officeSelect.value = officeId; // Set the office select to the current office_id

            var editModal = new bootstrap.Modal(document.getElementById("editModal"));
            editModal.show();
        });
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
                text: "لا يمكنك التراجع عن هذا الإجراء!",
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
</body>
</html>

<?php 
} else {
    header("Location: ../login.php");
    exit;
}
?>
