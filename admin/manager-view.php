<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "logo.php";
        include "data/managers.php";

        if (isset($_GET['manager_id'])) {
            $manager_id = $_GET['manager_id'];
            $manager = getManagerById($manager_id, $conn);

            if ($manager != 0) {
                // الاستعلام لجلب لوغو المكتب
                $office_logo_query = "SELECT logo FROM profiles WHERE office_id = ?";
                $office_logo_stmt = $conn->prepare($office_logo_query);
                $office_logo_stmt->execute([$manager['office_id']]);
                $office_logo_result = $office_logo_stmt->fetch(PDO::FETCH_ASSOC);
                $office_logo = $office_logo_result ? $office_logo_result['logo'] : null;

                // الاستعلام لجلب لوغو الآدمن
                $admin_logo_query = "SELECT logo FROM setting WHERE id = 1";
                $admin_logo_stmt = $conn->prepare($admin_logo_query);
                $admin_logo_stmt->execute();
                $admin_logo_result = $admin_logo_stmt->fetch(PDO::FETCH_ASSOC);
                $admin_logo = $admin_logo_result ? $admin_logo_result['logo'] : null;

                // تحديد مسار اللوغو النهائي
                if (!empty($manager['manager_logo'])) {
                    $logo_path = "../img/managers/" . $manager['manager_logo'];
                } elseif (!empty($office_logo)) {
                    $logo_path = "../profiles_photos/" . $office_logo;
                } else {
                    $logo_path = "../img/" . $admin_logo;
                }
                ?>
                <div class="container mt-3" style="direction: rtl;">
                    <div class="card shadow-sm">
                        <div class="logo-container text-center">
                            <img src="<?=$logo_path?>" class="logo-img" id="manager-logo" alt="Logo">
                            <label for="manager_picture" class="edit-icon">
                            </label>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?=$manager['username']?>@</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>اسم المدير:</strong> <?= htmlspecialchars($manager['manager_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>اسم المستخدم:</strong> <?= htmlspecialchars($manager['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>العنوان:</strong> <?= htmlspecialchars($manager['manager_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>تاريخ الميلاد:</strong> <?= htmlspecialchars($manager['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>الإيميل:</strong> <a style="text-decoration:none;" href="mailto:<?=$manager['manager_email']?>"> <?= htmlspecialchars($manager['manager_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                <li class="list-group-item"><strong>الجنس:</strong> <?php echo ($manager['manager_gender']=='Male') ? 'ذكر' : 'أنثى'; ?></li>
                                <li class="list-group-item"><strong>الهاتف:</strong> <?= htmlspecialchars($manager['manager_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>المدينة:</strong> <?= htmlspecialchars($manager['manager_city'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                                <li class="list-group-item"><strong>المكتب:</strong> <?= htmlspecialchars($manager['office_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php 
            } else {
                echo "<p class='text-center'>لم يتم العثور على تفاصيل المدير.</p>";
            }
        }
    } else {
        echo "<p class='text-center'>غير مصرح بالدخول.</p>";
    }
} else {
    echo "<p class='text-center'>غير مصرح بالدخول.</p>";
}
?>
