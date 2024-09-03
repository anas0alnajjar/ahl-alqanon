<?php 
session_start();

// التحقق من صلاحيات الجلسة
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Managers') {


// تضمين الملفات الضرورية
include "../DB_connection.php";
include 'permissions_script.php';
include "logo.php";
include "get_office.php";

// التحقق من صلاحيات القراءة
if ($pages['roles']['read'] == 0) {
    header("Location: home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$OfficeId = getOfficeId($conn, $user_id);

if (empty($OfficeId)) {
    echo "No data found.";
    exit();
}

// إعداد الترحيل (Pagination)
$page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
$total_records_per_page = 10;
$offset = ($page_number - 1) * $total_records_per_page;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// قاعدة الاستعلام لحساب إجمالي السجلات مع شرط البحث واستبعاد الأدوار الخاصة بالمستخدم
$count_sql = "SELECT COUNT(*) 
              FROM powers p 
              INNER JOIN offices o ON p.office_id = o.office_id 
              WHERE o.office_id = :office_id
              AND p.power_id NOT IN (
                  SELECT role_id FROM managers_office WHERE id = :user_id
              )";
$params = [':office_id' => $OfficeId, ':user_id' => $user_id];

if (!empty($search)) {
    $count_sql .= " AND (p.role LIKE :search OR o.office_name LIKE :search)";
    $params[':search'] = "%$search%";
}

try {
    // حساب إجمالي السجلات
    $stmt = $conn->prepare($count_sql);
    $stmt->execute($params);
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $total_records_per_page);

    // استعلام لجلب البيانات مع الترحيل وشرط البحث واستبعاد الأدوار الخاصة بالمستخدم
    $sql = "SELECT p.power_id, p.role, o.office_name, 
                COALESCE(a.count, 0) + COALESCE(l.count, 0) + COALESCE(h.count, 0) + COALESCE(c.count, 0) + COALESCE(m.count, 0) AS user_count
            FROM powers p
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM admin GROUP BY role_id
            ) a ON p.power_id = a.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM lawyer GROUP BY role_id
            ) l ON p.power_id = l.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM helpers GROUP BY role_id
            ) h ON p.power_id = h.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM clients WHERE username IS NOT NULL AND username != '' GROUP BY role_id
            ) c ON p.power_id = c.role_id
            LEFT JOIN (
                SELECT role_id, COUNT(*) AS count FROM managers_office GROUP BY role_id
            ) m ON p.power_id = m.role_id
            INNER JOIN offices o ON p.office_id = o.office_id
            WHERE o.office_id = :office_id
            AND p.power_id NOT IN (
                SELECT role_id FROM managers_office WHERE id = :user_id
            )";

    if (!empty($search)) {
        $sql .= " AND (p.role LIKE :search OR o.office_name LIKE :search)";
    }

    $sql .= " ORDER BY p.power_id DESC LIMIT :offset, :limit";
    
    // تحضير وتنفيذ الاستعلام
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $total_records_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':office_id', $OfficeId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
} catch (Exception $e) {
    // التعامل مع الأخطاء
    echo "An error occurred: " . $e->getMessage();
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Powers</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .modal-body .form-group, .modal-body .form-check {
            margin-bottom: 1rem;
        }
        .modal-body .form-check-inline {
            margin-right: 1rem;
            flex-basis: 45%;
        }

        label{
            cursor: pointer;
        }

        .form-check-inline {
            margin: 0 10px 10px 0; /* إضافة تباعد بين العناصر */
        }
        .form-check-label {
            margin-right: 5px; /* تباعد بسيط بين المدخل والتسمية */
        }
        .modal-body {
            max-height: 70vh; /* تحديد أقصى ارتفاع لمحتوى المودال */
            overflow-y: auto; /* إضافة تمرير عند تجاوز المحتوى للارتفاع المحدد */
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
        }
        .form-group {
            margin-bottom: 1rem;
        }

      thead th {
                cursor: pointer;
            }
            input[type="checkbox"] {
                appearance: none;
                width: 20px;
                height: 20px;
                background-color: #fff;
                border: 1px solid #ced4da;
                border-radius: 3px;
                cursor: pointer;
                vertical-align: middle;
            }

              input[type="checkbox"]:checked {
                background-color: #007bff;
            }

    </style>
</head>
<body>


<!-- Powers Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true" style="text-align:right; direction:rtl;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">إدارة الصلاحيات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="roleForm" method="POST">
                    <div class="form-group">
                        <label class="mb-2" for="role_name">الاسم</label>
                        <input type="text" class="form-control" id="role_name" name="role_name" required>
                        <input type="hidden"  id="office_id" name="office_id" value="<?=$OfficeId?>" required>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">المحامي</label>
                            <select id="lawyer_id" class="form-control" name="lawyer_id[]" multiple>
                            <option value="">اختر المحامي...</option>
                            <?php
                                if (!empty($OfficeId)) {
                                    $sql_lawyers = "SELECT * FROM lawyer WHERE office_id IN ($OfficeId) ORDER BY lawyer_id";
                                    $stmt_lawyers = $conn->prepare($sql_lawyers);
                                    $stmt_lawyers->execute();
                                    if ($stmt_lawyers->rowCount() > 0) {
                                        while ($row = $stmt_lawyers->fetch(PDO::FETCH_ASSOC)) {
                                            $lawyer_id = $row["lawyer_id"];
                                            $lawyer_name = $row["lawyer_name"];
                                            echo "<option value='$lawyer_id'>$lawyer_name</option>";
                                        }
                                    } else {
                                        echo "<option value=''>لا توجد محامون مرتبطين بك</option>";
                                    }
                                } else {
                                    echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>                    
                    <div style="display: ruby-text;">
                    <h5>صلاحيات الصفحات</h5>
                        <button type="button" id="selectAll" class="btn btn-link" style="font-size:smaller;float:left;text-decoration:none;box-shadow: none;">تحديد الكل</button>
                    </div>
                    <div class="table-responsive" style="position:relative;max-height: 400px;overflow-y:auto;scrollbar-width: thin;">
                        <table class="table table-hover">
                                 <thead style="position: sticky; top: -1px; background: #eee; z-index: 1;">
                                        <tr>
                                            <th>الصفحة</th>
                                            <th>
                                                <input type="checkbox" id="checkAllRead" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 1)">
                                                قراءة
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllEdit" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 2)">
                                                تعديل
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllCreate" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 3)">
                                                إنشاء
                                                
                                            </th>
                                            <th>
                                                <input type="checkbox" id="checkAllDelete" class="column-checkbox" onclick="toggleColumnCheckboxes(this, 4)">
                                                حذف
                                                
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <tr>
                                    <td >الصفحة الرئيسية</td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="home_read" name="permissions[home][read]" value="1" checked disabled>
                                        <input type="hidden" name="permissions[home][read]" value="1">
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td>لوحة التحكم</td>
                                    <td ><input type="checkbox" class="form-check-input" id="control_read" name="permissions[control][read]" value="1"></td>
                                    <td colspan="3"></td>
                                    
                                </tr>
                                <!-- Repeat for other pages -->
                                <tr>
                                    <td>القضايا</td>
                                    <td><input type="checkbox" class="form-check-input" id="cases_read" name="permissions[cases][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="cases_write" name="permissions[cases][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="cases_add" name="permissions[cases][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="cases_delete" name="permissions[cases][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الجلسات</td>
                                    <td><input type="checkbox" class="form-check-input" id="sessions_read" name="permissions[sessions][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="sessions_write" name="permissions[sessions][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="sessions_add" name="permissions[sessions][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="sessions_delete" name="permissions[sessions][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>إضافة جلسة بتاريخ قديم</td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="checkbox" class="form-check-input" id="oldsessions_add" name="permissions[add_old_session][add]" value="1"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>المصاريف الإجمالية</td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_read" name="permissions[expenses][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_write" name="permissions[expenses][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_add" name="permissions[expenses][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_delete" name="permissions[expenses][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>مصاريف الجلسات</td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_sessions_read" name="permissions[expenses_sessions][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_sessions_write" name="permissions[expenses_sessions][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_sessions_add" name="permissions[expenses_sessions][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="expenses_sessions_delete" name="permissions[expenses_sessions][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>المدفوعات</td>
                                    <td><input type="checkbox" class="form-check-input" id="payments_read" name="permissions[payments][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="payments_write" name="permissions[payments][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="payments_add" name="permissions[payments][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="payments_delete" name="permissions[payments][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>المرفقات</td>
                                    <td><input type="checkbox" class="form-check-input" id="attachment_read" name="permissions[attachments][read]" value="1"></td>
                                    <td></td>
                                    <td><input type="checkbox" class="form-check-input" id="attachment_add" name="permissions[attachments][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="attachment_delete" name="permissions[attachments][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>المحامين</td>
                                    <td><input type="checkbox" class="form-check-input" id="lawyers_read" name="permissions[lawyers][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="lawyers_write" name="permissions[lawyers][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="lawyers_add" name="permissions[lawyers][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="lawyers_delete" name="permissions[lawyers][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الموكلين</td>
                                    <td><input type="checkbox" class="form-check-input" id="clients_read" name="permissions[clients][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="clients_write" name="permissions[clients][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="clients_add" name="permissions[clients][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="clients_delete" name="permissions[clients][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الإداريين</td>
                                    <td><input type="checkbox" class="form-check-input" id="helpers_read" name="permissions[assistants][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="helpers_write" name="permissions[assistants][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="helpers_add" name="permissions[assistants][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="helpers_delete" name="permissions[assistants][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>أنواع المصروفات</td>
                                    <td><input type="checkbox" class="form-check-input" id="type_expenses_read" name="permissions[expense_types][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="type_expenses_write" name="permissions[expense_types][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="type_expenses_add" name="permissions[expense_types][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="type_expenses_delete" name="permissions[expense_types][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>المحاكم</td>
                                    <td><input type="checkbox" class="form-check-input" id="courts_read" name="permissions[courts][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="courts_write" name="permissions[courts][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="courts_add" name="permissions[courts][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="courts_delete" name="permissions[courts][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الخصوم</td>
                                    <td><input type="checkbox" class="form-check-input" id="adversaries_read" name="permissions[adversaries][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="adversaries_write" name="permissions[adversaries][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="adversaries_add" name="permissions[adversaries][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="adversaries_delete" name="permissions[adversaries][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>أنواع القضايا</td>
                                    <td><input type="checkbox" class="form-check-input" id="types_case_read" name="permissions[case_types][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="types_case_write" name="permissions[case_types][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="types_case_add" name="permissions[case_types][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="types_case_delete" name="permissions[case_types][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الدوائر القضائية</td>
                                    <td><input type="checkbox" class="form-check-input" id="departments_types_read" name="permissions[judicial_circuits][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="departments_types_write" name="permissions[judicial_circuits][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="departments_types_add" name="permissions[judicial_circuits][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="departments_types_delete" name="permissions[judicial_circuits][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الوثائق / العقود</td>
                                    <td><input type="checkbox" class="form-check-input" id="documents_read" name="permissions[documents][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="documents_write" name="permissions[documents][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="documents_add" name="permissions[documents][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="documents_delete" name="permissions[documents][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>الاشعارات</td>
                                    <td><input type="checkbox" class="form-check-input" id="todo_read" name="permissions[notifications][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="todo_write" name="permissions[notifications][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="todo_add" name="permissions[notifications][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="todo_delete" name="permissions[notifications][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>تخصيص الرسائل</td>
                                    <td><input type="checkbox" class="form-check-input" id="templates_read" name="permissions[message_customization][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="templates_write" name="permissions[message_customization][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="templates_add" name="permissions[message_customization][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="templates_delete" name="permissions[message_customization][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>صندوق الصادر</td>
                                    <td ><input type="checkbox" class="form-check-input" id="outbox_read" name="permissions[outbox][read]" value="1"></td>
                                    <td colspan="3"></td>
                                    
                                </tr>
                                <tr>
                                    <td>الأدوار</td>
                                    <td><input type="checkbox" class="form-check-input" id="roles_read" name="permissions[roles][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="roles_write" name="permissions[roles][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="roles_add" name="permissions[roles][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="roles_delete" name="permissions[roles][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>اعدادات الاتصال</td>
                                    <td><input type="checkbox" class="form-check-input" id="settings_read" name="permissions[logo_contact][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="settings_write" name="permissions[logo_contact][write]" value="1"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>إدارة المستخدمين</td>
                                    <td><input type="checkbox" class="form-check-input" id="users_read" name="permissions[user_management][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="users_write" name="permissions[user_management][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="users_add" name="permissions[user_management][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="users_delete" name="permissions[user_management][delete]" value="1"></td>
                                </tr>
                                <tr>
                                    <td>صفحات المكاتب</td>
                                    <td><input type="checkbox" class="form-check-input" id="profiles_read" name="permissions[profiles][read]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="profiles_write" name="permissions[profiles][write]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="profiles_add" name="permissions[profiles][add]" value="1"></td>
                                    <td><input type="checkbox" class="form-check-input" id="profiles_delete" name="permissions[profiles][delete]" value="1"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">إغلاق</button>
                <button id="saveRole" type="button" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Powers Modal -->




<!-- Nav bar -->
    <?php 
        include "inc/navbar.php";
    ?>
<!-- End of NavBar -->

    <?php 
    if (!empty($result) || (empty($result) && !empty($search))) {
    ?>
    <div class="container mt-5" style="direction: rtl;">
        <div class="btn-group" style="direction:ltr;">
            <a href="home.php" class="btn btn-light">الرئيسية</a>
            <?php if ($pages['roles']['add']) : ?>
                <button type="button" style="text-align: right;" class="btn btn-dark" id="addRole">إضافة دور جديد</button>
            <?php endif; ?>
        </div>
        <form action="powers.php" class="mt-3 n-table" method="GET">
            <div class="input-group mb-3" style="width:100%;direction: ltr;">
                <input style="direction:rtl;" type="text" class="form-control" name="search" placeholder="ابحث هنا..." value="<?php echo htmlentities($search); ?>">
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
    <table class="table table-bordered mt-3 n-table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">الاسم</th>
                <th scope="col">عدد المستخدمين</th>
                <?php if ($pages['roles']['delete']) : ?>
                    <th scope="col">الاجراءات</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php 
        $i = 1;
        foreach ($result as $row): ?>
            <tr>
                <th scope="row"><?=$i?></th>
                <td>
                    <a href="edit_power.php?id=<?=$row['power_id']?>" style="text-decoration:none; cursor:pointer;" class="editRole" data-id="<?=$row['power_id']?>">
                        <?=$row['role']?>
                    </a>
                </td>
                <td><?=$row['user_count']?></td>
                <?php if ($pages['roles']['delete']) : ?>
                <td>
                    <a href="#" class="btn btn-danger btn-sm m-auto" onclick="confirmDelete(<?=$row['power_id']?>)">حذف</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php $i++; // Increment $i for the next row ?> 
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
        <div class="alert alert-info d-flex align-items-center mt-3" role="alert" style="max-width: 400px; margin-left: auto;margin-right: 2rem;justify-content: space-around;">
            <span>لا يوجد أدوار حتى الآن</span>
            <button type="button" style="text-align: right;" class="btn btn-dark" id="addRole2">إضافة دور</button>
        </div>
    <?php } ?>


    <script>
                $(document).ready(function(){
            $('#addRole').on('click', function(){
                $('#roleModal').modal('show');
            });
            $('#addRole2').on('click', function(){
                $('#roleModal').modal('show');
            });
            $('#colse555').on('click', function(){
                $('#roleModal').modal('hide');
            });
            $('.close').on('click', function(){
                $('#roleModal, #editRoleModal').modal('hide');
            });
        });
    </script>

    <script>
    $(document).ready(function(){
    $('#saveRole').on('click', function(event){
        event.preventDefault(); // منع إعادة تحميل الصفحة

        var roleName = $('#role_name').val();
        if (roleName === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد اسم الدور',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }
       
        var office_id = $('#office_id').val();
        if (office_id === '') {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'يجب تحديد مكتب',
                confirmButtonColor: '#dc3545'
            });
            return; // إيقاف التنفيذ
        }

        var formData = new FormData(document.getElementById('roleForm'));

        $.ajax({
            url: 'req/save_role.php',
            type: 'POST',
            data: formData,
            processData: false, // عدم معالجة البيانات (تجعلها سلسلة استعلام)
            contentType: false, // عدم تعيين نوع المحتوى
            success: function(response){
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح',
                        text: jsonResponse.message
                    }).then(function(){
                        $('#roleModal').modal('hide');
                        $('#roleForm')[0].reset();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: jsonResponse.message
                    });
                }
            },
            error: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء حفظ البيانات'
                });
            }
        });
    });
});

    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // وظيفة لتحديد أو إلغاء تحديد جميع الخانات في عمود معين
        function toggleColumnCheckboxes(headerCheckbox, columnIndex) {
            const checkboxes = document.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1}) input[type="checkbox"]`);
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) { // تجنب تغيير حالة الخانات المؤمنة
                    checkbox.checked = !allChecked;
                }
            });
        }

        // وظيفة لتحديد جميع الخانات
        document.getElementById('selectAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) { // تجنب تغيير حالة الخانات المؤمنة
                    checkbox.checked = !allChecked;
                }
            });
            this.textContent = allChecked ? 'تحديد الكل' : 'إلغاء تحديد الكل';
        });

        // إضافة مستمع للأحداث لكل خانة checkbox في الجسم لتحديث حالة الـ checkbox في الرأس
        document.querySelectorAll('thead input[type="checkbox"]').forEach(headerCheckbox => {
            const columnIndex = headerCheckbox.closest('th').cellIndex;
            headerCheckbox.addEventListener('change', function() {
                toggleColumnCheckboxes(this, columnIndex);
            });
        });

        // إضافة مستمع للأحداث لكل خانة checkbox في الجسم لتحديث حالة الـ checkbox في الرأس عند تغيير حالته
        document.querySelectorAll('tbody input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const columnIndex = this.closest('td').cellIndex;
                const headerCheckbox = document.querySelector(`thead th:nth-child(${columnIndex + 1}) input[type="checkbox"]`);
                const allChecked = Array.from(document.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1}) input[type="checkbox"]`)).every(cb => cb.checked);
                headerCheckbox.checked = allChecked;
            });
        });
    });
</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/js/standalone/selectize.min.js"></script>



<script>
    function confirmDelete(roleId) {
        // إرسال طلب AJAX للتحقق من عدد المستخدمين
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "req/checkRoleUsers.php?id=" + roleId, true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                var userCount = response.user_count;

                if (userCount === 0) {
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: "هل أنت متأكد من الحذف؟",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "req/deleteRole.php?id=" + roleId;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: "هناك " + userCount + " مستخدمين لن يستطيعوا الدخول حتى وضع لهم رول. هل أنت متأكد من الحذف؟",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "req/deleteRole.php?id=" + roleId;
                        }
                    });
                }
            }
        };
        xhr.send();
        return false; // منع التصفح الافتراضي
    }
</script>
<script>
    $(document).ready(function() {
    // تهيئة Selectize للمكتب
    var officeSelect = $('#lawyer_id').selectize({
        create: false,
        sortField: 'text'
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













<!-- To use when Design Powers -->

<!-- 
SELECT
    powers.role,
    offices.office_name,
    page_permissions.page_name,
    page_permissions.can_read AS page_read,
    page_permissions.can_write AS page_write,
    page_permissions.can_add AS page_add,
    page_permissions.can_delete AS page_delete
FROM
    powers
JOIN offices ON powers.office_id = offices.office_id
LEFT JOIN page_permissions ON powers.power_id = page_permissions.role_id
WHERE powers.power_id = 8; -->