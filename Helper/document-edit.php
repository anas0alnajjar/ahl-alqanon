<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        include "../DB_connection.php";
        include "logo.php";

        include 'permissions_script.php';
        if ($pages['documents']['write'] == 0) {
            header("Location: home.php");
            exit();
        }

        include "get_office.php";
        $user_id = $_SESSION['user_id'];
        $OfficeId = getOfficeId($conn, $user_id);
        
        function getDocumentById($id, $conn){
            $sql = "SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                    FROM documents 
                    LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                    LEFT JOIN clients ON documents.client_id = clients.client_id 
                    WHERE document_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() == 1) {
                $case = $stmt->fetch();
                return $case;
            } else {
                return false;
            }
        }

        if(isset($_GET['document_id'])){
            $document_id = $_GET['document_id'];
            $document = getDocumentById($document_id, $conn);

            if ($document === false) {
                header("Location: documents.php");
                exit;
            }
        } else {
            header("Location: documents.php");
            exit;
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit document</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

        <!-- Links For Documents -->
        <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    




    <style>
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
            

            .ck.ck-toolbar>.ck-toolbar__items {
                align-items: start;
                display: flex;
                flex-flow: row wrap;
                flex-grow: 1;
                flex-direction: row-reverse;
            }
            select {
                display: none;
            }
            textarea {
                display: none;
            }

    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5" style="direction: rtl;">
        <a href="documents.php" class="btn btn-dark">الرجوع</a>
        <form method="post" class="shadow p-3 mt-5 form-w" action="req/document-edit.php" enctype="multipart/form-data">
            <h3>تعديل الوثيقة/ العقد</h3><hr>
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
            <div class="col-md-12 mb-3">
                <label class="form-label">العنوان</label>
                <input type="text" class="form-control" value="<?=$document['title']?>" name="title">
                <input type="hidden" class="form-control" id="office_id" value="<?=$OfficeId?>" name="office_id">
            </div>
            </div>
            <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">اسم الموكل</label>
                <input type="text" class="form-control" value="<?=$document['client_first_name'] . ' ' . $document['client_last_name']?>" disabled>
                <input type="hidden" value="<?=$document['client_id']?>" name="client_id">
                <input type="hidden" value="<?=$document['lawyer_id']?>" name="lawyer_id">
            </div>
            </div>
            <div class="mb-3">
                <label class="form-label">تغيير الملف المرفق</label>
                <input type="file" class="form-control" name="new_attachment" accept="application/pdf">
            </div>


            <input type="hidden" name="document_id" value="<?=$document['document_id']?>">
            <input type="hidden" name="old_attachment" value="<?=$document['attachments']?>">
            <!-- <button type="submit" class="btn btn-dark mt-3">تحديث الملف</button> -->

              <!-- Doument here -->
            
              <div id="editor" class="mt-3" style="text-align: justify;" id="editor" class="mt-3">

            <textarea name="content" id='edit' style="margin-top: 30px;">

            <?=$document['content']?>

            </textarea>

            
            </div>
            <textarea id="hiddenTextarea" style="display:none;"></textarea>
            <div class="mt-3">
                <label class="form-label">الملاحظات</label>
                <textarea class="form-control" name="notes" id="notes"><?=$document['notes']?></textarea>
            </div>


            <input type="hidden" name="document_id" value="<?=$document['document_id']?>">
            <div class="row">
            <div class="col-md-6 mt-3">
            <button type="submit" class="btn btn-primary">تحديث</button>
            <a class="btn btn-secondary btn-block" id="fileButton">تحميل ملف ورد</a>
            </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('select').selectize({
                sortField: 'text'
            });
        });  
    </script>



        <script src="../documents/js/mammoth.browser.min.js"></script>
        <script src="../documents/js/script.js"></script>

    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>
<?php 

    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
} 
?>
