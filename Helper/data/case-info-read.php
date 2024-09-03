<?php

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
 ?>
<?php
    // Fetch all the columns from the database table
    $query = "SELECT cases.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.phone, clients.last_name AS client_last_name 
                FROM cases 
                LEFT JOIN lawyer ON cases.lawyer_id = lawyer.lawyer_id 
                LEFT JOIN clients ON cases.client_id = clients.client_id
                WHERE case_id= :id";
                $id = $_GET['id'];

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Fetch data as associative array
    $caseData = $stmt->fetch(PDO::FETCH_ASSOC);

    $queryExp = "SELECT SUM(amount) AS total_amount FROM expenses WHERE case_id = :id";
                $id = $_GET['id'];

    $stmtExp = $conn->prepare($queryExp);
    $stmtExp->bindParam(':id', $id);
    $stmtExp->execute();

    // Fetch data as associative array
    $expData = $stmtExp->fetch(PDO::FETCH_ASSOC);



    $client_name = $caseData['client_first_name'] . ' ' . $caseData['client_last_name'];

?>



    <div class="card">
        <div id="heading1" class="card-header">
            <h2 class="mb-0">
                <button class="btn btn-link" style="text-decoration: none;" type="button" data-toggle="collapse" data-target="#collapse55">القضية/الموكل</button>
            </h2>
        </div>
        <div id="collapse55" class="collapse show" data-parent="#accordionMain">
            <div class="card-body">
                <div class="row">
                <input type="hidden" value="<?=$OfficeId?>" name="office_id">
                <input type="hidden" value="<?=$caseData['lawyer_id']?>" name="lawyer_id" id="lawyer_id">
                <input type="hidden" value="<?=$caseData['helper_name']?>" name="helper_name[]" id="">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اسم الموكل</label>
                            <input type="text" class="form-control" value="<?=$caseData['client_first_name'] . ' ' . $caseData['client_last_name']?>" disabled>
                            <input type="hidden" value="<?=$caseData['client_id']?>" name="client_id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">عنوان القضية</label>
                            <input type="text" class="form-control" value="<?=$caseData['case_title']?>" name="case_title" id="case_title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="position: relative; left:75%;" name="agency" <?php echo isset($caseData['agency']) && $caseData['agency'] === 'on' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="flexSwitchCheckChecked" style="cursor: pointer;">وكالة؟</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" id="legal-number">رقم القضية</label>
                            <input type="text" class="form-control" value="<?=$caseData['case_number']?>" name="case_number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نوع القضية</label>
                            <select id="case_type" class="form-control" name="case_type">
                            <?php
                            if (!empty($OfficeId)) {
                                $sql = "SELECT * FROM types_of_cases WHERE office_id IN ($OfficeId) ORDER BY id DESC";
                                $result = $conn->query($sql);

                                if ($result->rowCount() > 0) {
                                    echo '<option value="" selected disabled>اختر النوع...</option>';
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $type_id = $row["id"];
                                        $type_case = $row["type_case"];
                                        $selected = ($type_id == $caseData['case_type'])? "selected" : "";
                                        echo "<option value='$type_id' $selected>$type_case</option>";
                                    }
                                } else {
                                    echo '<option value="" selected disabled>لا توجد أنواع مرتبطة بك</option>';
                                }
                            } else {
                                echo '<option value="" selected disabled>لا توجد مكاتب مرتبطة بك</option>';
                            }
                            ?>
                            </select>
                            <input type="hidden" class="form-control" value="<?=$caseData['case_id']?>" id="caseId" name="id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="cursor: pointer;" for="id_pictureMain">
                                <?php if (!empty($caseData['id_picture'])) { ?>
                                    تغيير صورة هوية الموكل
                                <?php } else { ?>
                                    صورة هوية الموكل
                                <?php } ?>
                            </label>
                            <input id="id_pictureMain" class="form-control" name="id_picture" type="file" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="row">
                        <?php if (!empty($caseData['id_picture'])) { ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="cursor: pointer;" for="id_picture">صورة هوية الموكل</label>
                                <img id="id_picture" class="img-fluid" src="../uploads/<?php echo $caseData['id_picture']; ?>" alt="<?php echo $caseData['id_picture']; ?>">
                            </div>
                        </div>
                        <?php } ?>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="claimantsInformation">
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
                            <select id="plaintiff" class="form-control" name="plaintiff[]" multiple>
                                <option disabled="disabled" selected="selected"></option>
                                <?php
                                if (!empty($user_id)) {
                                    $clients = get_clients_for_helper($conn, $user_id);

                                    // افصل معرفات العملاء المربوطة بالقضية إلى مصفوفة
                                    $selected_clients = explode(',', $caseData['plaintiff']);
                                    if (count($clients) > 0) {
                                        foreach ($clients as $row) {
                                            $client_id = $row["client_id"];
                                            $client_name = $row["first_name"] . " " . $row["last_name"];
                                            $client_email = $row["email"];
                                            $selected = in_array($client_id, $selected_clients) ? 'selected' : '';
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
                        </div>
                    </div>

                    <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الخصم</label>
                        <select id="defendant" class="form-control" name="defendant[]" multiple>
                            <option disabled="disabled" selected="selected"></option>
                            <?php
                            if (!empty($user_id)) {
                                $adversaries = get_adversaries_for_helper($conn, $user_id);
                                if (count($adversaries) > 0) {
                                    foreach ($adversaries as $row) {
                                        $adver_id = $row["id"];
                                        $adver_name = $row["fname"] . " " . $row["lname"];
                                        $selectedValues2 = explode(',', $caseData['defendant']);
                                        $selected = in_array($adver_id, $selectedValues2) ? 'selected' : '';
                                        echo "<option value='$adver_id' $selected>$adver_name</option>";
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
                            <select id="court_name" class="form-control" name="court_name">
                            <option value="" selected disabled>اختر المحكمة...</option>
                                <?php
                                if (!empty($OfficeId)) {
                                    $sql = "SELECT * FROM courts WHERE office_id IN ($OfficeId) ORDER BY id DESC";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $court_id = $row["id"];
                                            $court_name = $row["court_name"];
                                            $selected = ($court_id == $caseData['court_name']) ? "selected" : "";
                                            echo "<option value='$court_id' $selected>$court_name</option>";
                                        }
                                    }
                                } else {
                                    echo "<option value='' selected>لا توجد مكاتب مرتبطة بك</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الدائرة</label>
                            <select id="department" class="form-control" name="department">
                            <option value="" selected disabled>اختر الدائرة...</option>
                                <?php
                                if (!empty($OfficeId)) {
                                    // Get the departments related to the admin's offices
                                    $sql = "SELECT * FROM departments WHERE office_id IN ($OfficeId) ORDER BY id DESC";
                                    $result = $conn->query($sql);
                                    if ($result->rowCount() > 0) {
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $department_id = $row["id"];
                                            $department_name = $row["type"];
                                            $selected = ($department_id == $caseData['department']) ? "selected" : "";
                                            echo "<option value='$department_id' $selected>$department_name</option>";
                                        }
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
                            <label class="form-label">اسم القاضي</label>
                            <input type="text" class="form-control" name="judge_name" value="<?=$caseData['judge_name']?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
    

<?php 

} else {
    header("Location: ../cases.php");
    exit;
}
} else {
header("Location: ../cases.php");
exit;
} 
?>