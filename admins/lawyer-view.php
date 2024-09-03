<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admins') {
        include "../DB_connection.php";
        include "logo.php";
        include "data/lawyers.php";

        if (isset($_GET['lawyer_id'])) {
            $lawyer_id = $_GET['lawyer_id'];
            $lawyer = getLawyerById($lawyer_id, $conn);

            if ($lawyer != 0) {
                // الاستعلام لجلب لوغو المكتب
                $office_logo_query = "SELECT logo FROM profiles WHERE office_id = ?";
                $office_logo_stmt = $conn->prepare($office_logo_query);
                $office_logo_stmt->execute([$lawyer['office_id']]);
                $office_logo_result = $office_logo_stmt->fetch(PDO::FETCH_ASSOC);
                $office_logo = $office_logo_result ? $office_logo_result['logo'] : null;

                // الاستعلام لجلب لوغو الآدمن
                $admin_id = $_SESSION['user_id'];
                $admin_logo_query = "SELECT logo FROM setting WHERE admin_id = $admin_id";
                $admin_logo_stmt = $conn->prepare($admin_logo_query);
                $admin_logo_stmt->execute();
                $admin_logo_result = $admin_logo_stmt->fetch(PDO::FETCH_ASSOC);
                $admin_logo = $admin_logo_result ? $admin_logo_result['logo'] : null;

                // تحديد مسار اللوغو النهائي
                if (!empty($lawyer['lawyer_logo'])) {
                    $logo_path = "../img/lawyers/" . $lawyer['lawyer_logo'];
                } elseif (!empty($office_logo)) {
                    $logo_path = "../profiles_photos/" . $office_logo;
                } else {
                    $logo_path = "../img/" . $admin_logo;
                }
                ?>
                <div class="container mt-3" style="direction: rtl;">
                    <div class="card shadow-sm">
                        <div class="logo-container text-center">
                            <img src="<?=$logo_path?>" class="logo-img" id="lawyer-logo" alt="Logo">
                            <label for="id_picture" class="edit-icon">
                                <i class="fas fa-pen"></i>
                            </label>
                            <input type="file" class="logo-input" id="id_picture" name="id_picture" data-id="<?=$lawyer['lawyer_id']?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?=$lawyer['username']?>@</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>اسم المحامي:</strong> <?= htmlspecialchars($lawyer['lawyer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>اسم المستخدم:</strong> <?= htmlspecialchars($lawyer['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>العنوان:</strong> <?= htmlspecialchars($lawyer['lawyer_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>تاريخ الميلاد:</strong> <?= htmlspecialchars($lawyer['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>الإيميل:</strong> <a style="text-decoration:none;" href="mailto:<?=$lawyer['lawyer_email']?>"> <?= htmlspecialchars($lawyer['lawyer_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                <li class="list-group-item"><strong>الجنس:</strong> <?php echo ($lawyer['lawyer_gender']=='Male') ? 'ذكر' : 'أنثى'; ?></li>
                                <li class="list-group-item"><strong>الهاتف:</strong> <?= htmlspecialchars($lawyer['lawyer_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>المدينة:</strong> <?= htmlspecialchars($lawyer['lawyer_city'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>عدد القضايا:</strong> <?= htmlspecialchars($lawyer['case_count'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>عدد الإداريين:</strong> <?= htmlspecialchars($lawyer['helper_count'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php 
            } else {
                echo "<p class='text-center'>لم يتم العثور على تفاصيل المحامي.</p>";
            }
        }
    } else {
        echo "<p class='text-center'>غير مصرح بالدخول.</p>";
    }
} else {
    echo "<p class='text-center'>غير مصرح بالدخول.</p>";
}
?>
