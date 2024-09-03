<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "logo.php";

        if (isset($_GET['id'])) {
            $helper_id = $_GET['id'];

            function getHelperById($id, $conn){
                $sql = "SELECT * FROM `helpers`
                        LEFT JOIN offices ON offices.office_id = helpers.office_id 
                        WHERE id =?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id]);

                if ($stmt->rowCount() == 1) {
                    $helper = $stmt->fetch();
                    return $helper;
                } else {
                    return 0;
                }
            }

            $helper = getHelperById($helper_id, $conn);    

            if ($helper != 0) {
                ?>
                <div class="container mt-3" style="direction: rtl;">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white text-center">
                            <?php if (!empty($helper['username'])) { ?>
                                <h5 class="card-title">@<?= htmlspecialchars($helper['username'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($helper['helper_name'])) { ?>
                                    <li class="list-group-item">الاسم: <?= htmlspecialchars($helper['helper_name'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                                <?php if (!empty($helper['username'])) { ?>
                                    <li class="list-group-item">اسم المستخدم: <?= htmlspecialchars($helper['username'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                                <li class="list-group-item">
                                    عدد القضايا:
                                    <?php
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM cases WHERE FIND_IN_SET(:helper_id, helper_name)");
                                        $stmt->execute(['helper_id' => $helper['id']]);
                                        $case_count = $stmt->fetchColumn();
                                        echo htmlspecialchars($case_count ?? '0', ENT_QUOTES, 'UTF-8');
                                    ?>
                                </li>
                                <?php
                                    $stmt = $conn->prepare("SELECT lawyer_name FROM lawyer WHERE lawyer_id = :lawyer_id");
                                    $stmt->execute(['lawyer_id' => $helper['lawyer_id']]); 
                                    $lawyer_name = $stmt->fetchColumn();
                                    if (!empty($lawyer_name)) { 
                                ?>
                                    <li class="list-group-item">اسم المحامي: <?= htmlspecialchars($lawyer_name, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                                <?php if (!empty($helper['office_name'])) { ?>
                                    <li class="list-group-item">اسم المكتب: <?= htmlspecialchars($helper['office_name'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                                <?php if (!empty($helper['national_helper'])) { ?>
                                    <li class="list-group-item">الرقم القومي: <?= htmlspecialchars($helper['national_helper'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                                <?php if (!empty($helper['phone'])) { ?>
                                    <li class="list-group-item">رقم الهاتف: <span style="direction:ltr; display:inline-block;"><?= htmlspecialchars($helper['phone'], ENT_QUOTES, 'UTF-8') ?></span></li>
                                <?php } ?>
                                <?php if (!empty($helper['passport_helper'])) { ?>
                                    <li class="list-group-item">رقم جواز السفر: <?= htmlspecialchars($helper['passport_helper'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php 
            } else {
                echo "<p class='text-center'>لم يتم العثور على تفاصيل المساعد.</p>";
            }
        }
    } else {
        echo "<p class='text-center'>غير مصرح بالدخول.</p>";
    }
} else {
    echo "<p class='text-center'>غير مصرح بالدخول.</p>";
}
?>
