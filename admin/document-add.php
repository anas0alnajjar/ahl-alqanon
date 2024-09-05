<?php 
    session_start();
    if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

        if ($_SESSION['role'] == 'Admin') {
        
        
        include "../DB_connection.php";
        include "logo.php";


        $content = '';
        $lawer_name = '';
        $client_name = '';
        $document_title = '';
        $lawer_logo = '';


        if (isset($_GET['content'])) $content = $_GET['content'];
        if (isset($_GET['lawer_name'])) $lawer_nameGet = $_GET['lawer_name'];
        if (isset($_GET['client_name'])) $client_nameGet = $_GET['client_name'];
        if (isset($_GET['document_title'])) $document_title = $_GET['document_title'];
        if (isset($_GET['office_id'])) $office_id = $_GET['office_id'];


    ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Add Case</title>
        
        <!-- Favicon -->
        <link rel="icon" href="../img/<?=$setting['logo']?>">
        
        <!-- CSS Files -->
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css">

        <!-- JavaScript Files -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
        <script src="https://cdn.ckeditor.com/ckfinder/3.5.0/ckfinder.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <!-- Inline JavaScript for Initialization -->
        <script>
            $(document).ready(function () {
                $('select').selectize({
                    sortField: 'text'
                });
            });
        </script>
        
        <!-- Inline CSS -->
        <style>
            * {
                direction: rtl;
            }
            .form-w {
                max-width: 900px !important;
            }
            #container {
                width: 100%;
                margin: 20px auto;
                overflow: hidden; /* Corrected 'over-flow:hide' to 'overflow: hidden' */
            }
            .ck-editor__editable[role="textbox"] {
                /* Editing area */
                min-height: 200px;
                text-align: right; /* Ensure text is aligned to the right */
                direction: rtl; /* Ensure text direction is right-to-left */
            }
            .ck-content .image {
                /* Block images */
                max-width: 80%;
                margin: 20px auto;
            }
            .ck.ck-toolbar.ck-toolbar_grouping>.ck-toolbar__items {
                flex-wrap: wrap;
            }
            .ck.ck-button.ck-off.ck-file-dialog-button {
                /* display: none !important; */
            }
            .ck.ck-editor__editable_inline[dir=ltr] {
                text-align: right;
            }
            /* إضافة تنسيقات للقوائم لتظهر من اليمين إلى اليسار */
            .ck-content ol, .ck-content ul {
                text-align: right;
                direction: rtl;
                padding-right: 40px; /* Ensure padding on the right for RTL */
            }
            .ck-content ol {
                list-style-type: decimal;
            }
            .ck-content ul {
                list-style-type: disc;
            }
            .error {
                border-color: #dc3545 !important; /* Change border color for invalid fields */
            }
            button[data-cke-tooltip-text="Insert image or file"] {
                display: none !important;
            }
            .ck-editor__editable {
            max-height: 600px; /* قم بتعديل القيمة حسب الحاجة */
            overflow: auto;
        }

        </style>
    </head>

    <body>
        <?php 
            include "inc/navbar.php";
        ?>
<div class="container mt-5">
    <a href="documents.php" class="btn btn-dark mb-3">الرجوع</a>

    <form method="post" class="shadow p-3 form-w" action="req/document-add.php" enctype="multipart/form-data" id="documentForm">
        <h3>إضافة وثيقة</h3>
        <hr>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success" role="alert">
                <?=$_GET['success']?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">العنوان</label>
                <input type="text" class="form-control" value="<?=$document_title?>" name="document_title">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">المكتب</label>
                <select id="office_id" class="form-control" name="office_id">
                    <option value="" disabled selected>اختر المكتب...</option>
                    <?php
                        $sql = "SELECT `office_id`, `office_name` FROM offices";
                        $result = $conn->query($sql);
                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $id = $row["office_id"];
                                $office_name = $row["office_name"];
                                $selected = ($id == $office_id) ? "selected" : "";
                                echo "<option value='$id' $selected>$office_name</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم الموكل</label>
                <select id="client_name" class="form-control" name="client_name">
                    <option value="" selected>اختر عميلاً...</option>
                    <?php
                        include "../DB_connection.php";
                        $sql = "SELECT DISTINCT client_id, first_name, last_name FROM clients ORDER BY client_id;";
                        $result = $conn->query($sql);
                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $client_id = $row["client_id"];
                                $client_name = $row["first_name"]  . " " . $row["last_name"];
                                $selected = ($client_id == $client_nameGet) ? "selected" : "";
                                echo "<option value='$client_id' $selected>$client_name</option>";
                            }
                        }            
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">المحامي</label>
                <select id="lawers" class="form-control" name="lawer_name">
                    <option value="">اختر محامي...</option>
                    <?php
                        $sql = "SELECT DISTINCT lawyer_id, lawyer_name FROM lawyer ORDER BY lawyer_id;";
                        $result = $conn->query($sql);
                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $lawyer_id = $row["lawyer_id"];
                                $lawyer_name = $row["lawyer_name"];
                                $selected = ($lawyer_id == $lawer_nameGet) ? "selected" : "";
                                echo "<option value='$lawyer_id' $selected>$lawyer_name</option>";
                            }
                        }            
                    ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">تحميل ملف PDF</label>
            <input type="file" class="form-control" name="attachments" accept="application/pdf">
        </div>

        <div id="editor" class="mt-3 mb-3">
            <label class="form-label">الوثيقة</label>
            <textarea name="content" id="edit" class="form-control" placeholder="هذا الحقل مطلوب"><?=$content?></textarea>
        </div>
        
        <textarea id="hiddenTextarea" style="display:none;"></textarea>
        
        <div class="mt-3 mb-3">
            <label for="form-label mb-2 mt-2">الملاحظات</label>
            <textarea id="notes" name="notes" class="form-control"></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <button type="submit" class="btn btn-primary btn-block">إضافة</button>
                <a class="btn btn-secondary btn-block" id="fileButton">تحميل ملف ورد</a>
            </div>
            
        </div>
    </form>
</div>

        
        
        
        <script>
            $(document).ready(function(){
                $("#navLinks li:nth-child(5) a").addClass('active');
            });
        </script>
        

        <!-- Scripts fot Documents -->
        
        <script src="../documents/js/mammoth.browser.min.js"></script>
        <script src="../documents/js/script.js"></script>
        
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
document.getElementById('documentForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission
    var form = event.target;
    var requiredFields = [
        { name: 'document_title', message: 'يرجى ملء حقل العنوان.' },
        { name: 'lawer_name', message: 'يرجى ملء حقل اسم المحامي.' }
    ];

    var isValid = true;
    var firstInvalidField = null;
    var firstInvalidMessage = '';

    requiredFields.forEach(function(field) {
        var input = form.querySelector(`[name="${field.name}"]`);
        if (!input || !input.value.trim()) {
            input.classList.add('error');
            isValid = false;
            if (!firstInvalidField) {
                firstInvalidField = input;
                firstInvalidMessage = field.message;
            }
        } else {
            input.classList.remove('error');
        }
    });

    // Check for select field specifically
    var clientSelect = $('#client_name')[0].selectize;
    if (!clientSelect.getValue()) {
        clientSelect.$control.addClass('error');
        isValid = false;
        if (!firstInvalidField) {
            firstInvalidField = clientSelect.$control[0];
            firstInvalidMessage = 'يرجى اختيار الموكل.';
        }
    } else {
        clientSelect.$control.removeClass('error');
    }

    // Check for select field specifically
    var lawyerSelect = $('#lawers')[0].selectize;
    if (!lawyerSelect.getValue()) {
        lawyerSelect.$control.addClass('error');
        isValid = false;
        if (!firstInvalidField) {
            firstInvalidField = lawyerSelect.$control[0];
            firstInvalidMessage = 'يرجى اختيار المحامي.';
        }
    } else {
        lawyerSelect.$control.removeClass('error');
    }
    // Check for select field specifically
    var officeSelect = $('#office_id')[0].selectize;
    if (!officeSelect.getValue()) {
        officeSelect.$control.addClass('error');
        isValid = false;
        if (!firstInvalidField) {
            firstInvalidField = officeSelect.$control[0];
            firstInvalidMessage = 'يرجى اختيار المحامي.';
        }
    } else {
        officeSelect.$control.removeClass('error');
    }

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: firstInvalidMessage
        });
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
    } else {
        form.submit(); // Submit the form if all fields are valid
    }
});

        </script>


        

    </body>
    </html>
<?php 

    }else {
        header("Location: ../login.php");
        exit;
    } 
    }else {
        header("Location: ../login.php");
        exit;
    } 

?>
