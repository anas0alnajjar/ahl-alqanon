<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Helper') {
        include "../DB_connection.php";
        include "logo.php";
        include 'permissions_script.php';
        if ($pages['documents']['read'] == 0) {
            header("Location: home.php");
            exit();
        }
        function getDocumentById($id, $conn){
            $sql = "SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name, offices.office_name 
                    FROM documents 
                    LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                    LEFT JOIN clients ON documents.client_id = clients.client_id 
                    LEFT JOIN offices ON documents.office_id = offices.office_id 
                    WHERE document_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() == 1) {
                $case = $stmt->fetch();
                return $case;
            } else {
                return false; // إرجاع قيمة غير صالحة عند عدم وجود القضية
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
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View document</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/<?=$setting['logo']?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

    <style>
        .form-w {
            max-width: 900px;
            width: 100%;
        }
        #editor {
            margin: 15px auto;
            width: 80%;
            direction: rtl;
        }
        p { 
            text-align: justify !important;
            margin: 15px auto;
            direction: rtl;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: right;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        #editor2 img, #editor img {
            page-break-inside: avoid; 
            display: block !important;
            margin: 0 auto !important; 
            max-width: 100% !important; 
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <?php include "inc/navbar.php"; ?>
    <div class="container mt-5" style="direction: rtl;">
        <a href="documents.php" class="btn btn-dark">الرجوع</a>
        <form method="post" class="shadow p-3 mt-5 form-w" action="">
            <i style="float:left;cursor: pointer;" id="directionIcon" class="fa fa-align-right"></i>
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
            <div class="mb-3 col-4">
                <label class="form-label">المكتب</label>
                <input type="text" class="form-control" value="<?=$document['office_name']?>" disabled>
                <input type="hidden" value="<?=$document['office_name']?>" name="office_name">
            </div>
            <div class="mb-3 col-4">
                <label class="form-label">الموكل</label>
                <input type="text" class="form-control" value="<?=$document['client_first_name'] . ' ' . $document['client_last_name']?>" disabled>
                <input type="hidden" value="<?=$document['client_id']?>" name="client_id">
            </div>
            <div class="mb-3 col-4">
                <label class="form-label">المحامي</label>
                <input type="text" class="form-control" value="<?=$document['lawyer_name']?> " disabled>
                <input type="hidden" value="<?=$document['lawyer_id']?>" name="lawyer_id">
            </div>
            </div>
            <?php if (!empty($document['attachments'])): ?>
                <div class="mb-3">
                    <label class="form-label">المرفقات</label>
                    <a href="../pdf/<?=$document['attachments']?>" download>
                        <br>
                        <i style="margin-left:2px;" class="fa fa-download"></i>
                    </a>
                </div>
            <?php endif; ?>
            <div style="text-align: justify; overflow-x: auto; width:100%; scrollbar-width: thin;max-height: 600px;" id="editor2" class="mt-3">
                <div class="document-title"><?=$document['title']?></div>
                <?=$document['content']?>
            </div>
            <?php if (!empty($document['notes'])): ?>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">الملاحظات:</label>
                                    <br><?= $document['notes'] ?>
                                </div>
                            <?php endif; ?>
            <div style="display:none;">
                <div style="text-align: justify; overflow-x: auto; width:100%; scrollbar-width: thin;" id="editor" class="mt-3">
                    <div class="document-title"><?=$document['title']?></div>
                    <?=$document['content']?>
                </div>

            </div>
            <a href="#" id="download-pdf" class="btn btn-info btn_print">تحميل الوثيقة</a>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $("select").select2();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const pdfBtn = document.querySelector('#download-pdf');
            const content = document.querySelector('#editor');

            pdfBtn.addEventListener('click', (e) => {
                e.preventDefault();

                const opt = {
                    margin:       0.5,
                    filename:     'document.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 1, logging: true, dpi: 192, letterRendering: true, useCORS: true },
                    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(content).save();
            });
        });
    </script>
    <script>
        document.getElementById("directionIcon").addEventListener("click", function() {
            var editor = document.getElementById("editor2");
            var paragraphs = editor.getElementsByTagName("p");
            var currentDirection = window.getComputedStyle(editor).direction;

            if (currentDirection === "rtl") {
                editor.style.direction = "ltr";
                document.getElementById("directionIcon").className = "fa fa-align-left";
            } else {
                editor.style.direction = "rtl";
                document.getElementById("directionIcon").className = "fa fa-align-right";
            }

            for (var i = 0; i < paragraphs.length; i++) {
                if (currentDirection === "rtl") {
                    paragraphs[i].style.direction = "ltr";
                    paragraphs[i].style.textAlign = "left";
                } else {
                    paragraphs[i].style.direction = "rtl";
                    paragraphs[i].style.textAlign = "right";
                }
            }
        });
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bobodyrap/4.0.0/js/bootstrap.min.js"></script>
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
