<?php 
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admins') {
?>
<form id="myForm" class="shadow p-3 mt-5" action="" enctype="multipart/form-data" method="post">
    <h3>إضافة قضية</h3>
    <hr />
    <ul id="myTabs" class="nav nav-tabs">
        <li class="nav-item">
            <button id="case-info-tab" class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#case-info">المعلومات الرئيسية</button>
        </li>
    </ul>
    <div id="myTabsContent" class="tab-content mt-1">
        <div id="case-info" class="tab-pane fade show active">
            <div id="accordionMain">
                <div class="card">
                    <div id="heading1" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse55">القضية/الموكل</button>
                        </h2>
                    </div>
                    <div id="collapse55" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المكتب</label>
                                        <select id="office_id" class="" name="office_id" required>
                                            <option value="">اختر المكتب...</option>
                                            <?php
                                                $admin_id = $_SESSION['user_id'];

                                                // جلب المكاتب المرتبطة بالآدمن
                                                $sql = "SELECT * FROM offices WHERE admin_id = :admin_id ORDER BY office_id DESC";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                                $stmt->execute();

                                                if ($stmt->rowCount() > 0) {
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $office_id = $row["office_id"];
                                                        $office_name = $row["office_name"];
                                                        echo "<option value='$office_id'>$office_name</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>";
                                                }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم المحامي</label>
                                        <select id="lawyer_id" class="" name="lawyer_id" required>
                                            <option value="">اختر المحامي...</option>
                                            <?php
                                            // تأكد من أن جلسة المستخدم قد بدأت وأن المستخدم مسجل دخوله كآدمن
                                                // جلب المكاتب المرتبطة بالآدمن
                                                $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                                $stmt_offices = $conn->prepare($sql_offices);
                                                $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                                $stmt_offices->execute();
                                                $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                                if (!empty($offices)) {
                                                    $office_ids = implode(',', $offices);

                                                    // جلب المحامين المرتبطين بمكاتب الآدمن
                                                    $sql_lawyers = "SELECT * FROM lawyer WHERE office_id IN ($office_ids) ORDER BY lawyer_id";
                                                    $stmt_lawyers = $conn->prepare($sql_lawyers);
                                                    $stmt_lawyers->execute();

                                                    $lawyer_id_session = isset($_SESSION['lawyer_id']) ? $_SESSION['lawyer_id'] : '';
                                                    if ($stmt_lawyers->rowCount() > 0) {
                                                        while ($row = $stmt_lawyers->fetch(PDO::FETCH_ASSOC)) {
                                                            $lawyer_id = $row["lawyer_id"];
                                                            $lawyer_name = $row["lawyer_name"];
                                                            $lawyer_email = $row["lawyer_email"];
                                                            $selected = ($lawyer_id == $lawyer_id_session) ? 'selected' : '';
                                                            echo "<option value='$lawyer_id' data-client-email='$lawyer_email' $selected>$lawyer_name</option>";
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم الموكل</label>
                                        <select id="client_id" class="" name="client_id" required>
                                            <option value="">اختر موكلاً...</option>
                                            <?php
                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب الموكلين المرتبطين بمكاتب الآدمن
                                                $sql_clients = "SELECT * FROM clients WHERE office_id IN ($office_ids) ORDER BY clients.client_id";
                                                $stmt_clients = $conn->prepare($sql_clients);
                                                $stmt_clients->execute();

                                                $client_id_session = isset($_SESSION['client_name']) ? $_SESSION['client_name'] : '';
                                                if ($stmt_clients->rowCount() > 0) {
                                                    while ($row = $stmt_clients->fetch(PDO::FETCH_ASSOC)) {
                                                        $client_id = $row["client_id"];
                                                        $client_name = $row["first_name"] . " " . $row["last_name"];
                                                        $client_email = $row["email"];
                                                        $selected = ($client_id == $client_id_session) ? 'selected' : '';
                                                        echo "<option value='$client_id' data-client-email='$client_email' $selected>$client_name</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>لا توجد موكلين مرتبطين بك</option>";
                                                }
                                            } else {
                                                echo "<option value=''>لا توجد مكاتب مرتبطة بك</option>";
                                            }
                                            ?>
                                        </select>
                                        <?php if ($pages['clients']['add']) : ?>
                                        <button  style="font-size: smaller;float: left;text-decoration: none;padding: 0;" id="editClientBtn" type="button" class="btn btn-link mt-2">إضافة موكل</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">رقم ملف القضية</label>
                                        <input type="text" class="form-control" name="case_title" id="case_title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="position: relative;left:75%;" name="agency" <?php echo isset($_SESSION['agency']) && $_SESSION['agency'] === 'on' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" style="cursor:pointer;" for="flexSwitchCheckChecked">وكالة؟</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label id="legal-number" class="form-label">رقم القضية</label>
                                        <input type="text" class="form-control" name="case_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">نوع القضية</label>
                                        <select id="case_type" class="" name="case_type" required>
                                            <?php
                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب الأنواع المرتبطة بمكاتب الآدمن
                                                $sql = "SELECT * FROM types_of_cases WHERE office_id IN ($office_ids) ORDER BY id DESC";
                                                $result = $conn->query($sql);

                                                if ($result->rowCount() > 0) {
                                                    echo '<option value="" selected disabled>اختر النوع...</option>';
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        $type_id = $row["id"];
                                                        $type_case = $row["type_case"];
                                                        echo "<option value='$type_id'>$type_case</option>";
                                                    }
                                                } else {
                                                    echo '<option value="" selected disabled>لا توجد أنواع مرتبطة بك</option>';
                                                }
                                            } else {
                                                echo '<option value="" selected disabled>لا توجد مكاتب مرتبطة بك</option>';
                                            }
                                            ?>
                                        </select>


                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="cursor: pointer;" for="id_picture">صورة هوية الموكل</label>
                                        <input id="id_picture" class="form-control" name="id_picture" type="file" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading2" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse2">المدعي/المدعى عليه</button>
                        </h2>
                    </div>
                    <div id="collapse2" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المدعي</label>   
                                        <select id="plaintiff" class="" name="plaintiff[]" multiple>
                                        <?php
                                            echo '<option value="" selected disabled>اختر موكلاً...</option>';

                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب الموكلين المرتبطين بمكاتب الآدمن
                                                $sql = "SELECT * FROM clients WHERE office_id IN ($office_ids) ORDER BY clients.client_id DESC";
                                                $result = $conn->query($sql);

                                                if ($result->rowCount() > 0) {
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        $client_id = $row["client_id"];
                                                        $client_name = $row["first_name"] . " " . $row["last_name"];
                                                        echo "<option value='$client_id'>$client_name</option>";
                                                    }
                                                } else {
                                                    echo '<option value="" disabled>لا توجد موكلين مرتبطين بك</option>';
                                                }
                                            } else {
                                                echo '<option value="" disabled>لا توجد مكاتب مرتبطة بك</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الخصم</label>
                                        <select id="defendant" class="" name="defendant[]" multiple>
                                            <option value="" selected disabled>اختر...</option>
                                            <?php
                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب الخصوم المرتبطين بمكاتب الآدمن
                                                $sql = "SELECT * FROM adversaries WHERE office_id IN ($office_ids) ORDER BY id DESC";
                                                $result = $conn->query($sql);

                                                if ($result->rowCount() > 0) {
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        $adver_id = $row["id"];
                                                        $adver_name = $row["fname"] . " " . $row["lname"];
                                                        echo "<option value='$adver_id'>$adver_name</option>";
                                                    }
                                                } else {
                                                    echo '<option value="" disabled>لا توجد خصوم مرتبطين بك</option>';
                                                }
                                            } else {
                                                echo '<option value="" disabled>لا توجد مكاتب مرتبطة بك</option>';
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم المحكمة</label>
                                        <select id="court_name" class="" name="court_name">
                                            <?php
                                            // Assuming $admin_id is already defined
                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب المحاكم المرتبطة بمكاتب الآدمن
                                                $sql = "SELECT * FROM courts WHERE office_id IN ($office_ids) OR public = 1 ORDER BY id DESC";
                                                $result = $conn->query($sql);

                                                if ($result->rowCount() > 0) {
                                                    echo '<option value="" selected disabled>اختر المحكمة...</option>';
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        $court_id = $row["id"];
                                                        $court_name = $row["court_name"];
                                                        echo "<option value='$court_id'>$court_name</option>";
                                                    }
                                                } else {
                                                    echo '<option value="" selected disabled>لا توجد محاكم مرتبطة بك</option>';
                                                }
                                            } else {
                                                echo '<option value="" selected disabled>لا توجد مكاتب مرتبطة بك</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الدائرة</label>
                                        <select id="department" class="" name="department">
                                            <?php
                                            // Assuming $admin_id is already defined
                                            // جلب المكاتب المرتبطة بالآدمن
                                            $sql_offices = "SELECT office_id FROM offices WHERE admin_id = :admin_id";
                                            $stmt_offices = $conn->prepare($sql_offices);
                                            $stmt_offices->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
                                            $stmt_offices->execute();
                                            $offices = $stmt_offices->fetchAll(PDO::FETCH_COLUMN);

                                            if (!empty($offices)) {
                                                $office_ids = implode(',', $offices);

                                                // جلب الأقسام المرتبطة بمكاتب الآدمن
                                                $sql = "SELECT * FROM departments WHERE office_id IN ($office_ids) ORDER BY id DESC";
                                                $result = $conn->query($sql);

                                                if ($result->rowCount() > 0) {
                                                    echo '<option value="" selected disabled>اختر الدائرة...</option>';
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        $department_id = $row["id"];
                                                        $department_name = $row["type"];
                                                        echo "<option value='$department_id'>$department_name</option>";
                                                    }
                                                } else {
                                                    echo '<option value="" selected disabled>لا توجد دوائر مرتبطة بك</option>';
                                                }
                                            } else {
                                                echo '<option value="" selected disabled>لا توجد مكاتب مرتبطة بك</option>';
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم القاضي</label>
                                        <input type="text" class="form-control" name="judge_name">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading3" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse3">الجلسات</button>
                            
                        </h2>
                    </div>
                    <div id="collapse3" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3" style="max-width: 100%; overflow-x: auto; scrollbar-width: thin; max-height: 600px;">
                                        <div id="dynamic_cards" class="row">
                                            <!-- البطاقات ستتم إضافتها هنا بواسطة JavaScript -->
                                        </div>
                                        <div class="text-left">
                                            
                                        </div>
                                    </div>
                                    <button type="button" id="addRowBtn" class="btn btn-dark btn-sm m-2 sessions-add" style="float: left;">إضافة جلسة</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="heading4" class="card-header">
                        <h2 class="mb-0">
                            <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse4">الملاحظات/وصف القضية</button>
                        </h2>
                    </div>
                    <div id="collapse4" class="collapse show" data-parent="#accordionMain">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظات</label>
                                        <textarea class="form-control" name="notes" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">وصف القضية</label>
                                        <textarea id="editor" class="form-control" rows="3" name="case_description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-2 cases-add" id="addCase">إضافة</button>
    
</form>


<?php 

} else {
    header("Location: ../../login.php");
    exit;
}
} else {
header("Location: ../../login.php");
exit;
} 
?>