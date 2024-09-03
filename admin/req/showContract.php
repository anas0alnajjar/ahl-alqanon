<?php 
session_start();
if (isset($_SESSION['admin_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";
        
        function getDocumentById($id, $conn){
            $sql = "SELECT documents.*, lawyer.lawyer_name, clients.first_name AS client_first_name, clients.last_name AS client_last_name 
                    FROM documents 
                    LEFT JOIN lawyer ON documents.lawyer_id = lawyer.lawyer_id 
                    LEFT JOIN clients ON documents.client_id = clients.client_id 
                    WHERE document_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            if ($stmt->rowCount() == 1) {
                return $stmt->fetch();
            } else {
                return false;
            }
        }

        if(isset($_GET['document_id'])){
            $document_id = $_GET['document_id'];
            $document = getDocumentById($document_id, $conn);

            if ($document === false) {
                echo "Document not found.";
                exit;
            }
        } else {
            echo "No document ID provided.";
            exit;
        }
?>
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
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .header, .footer {
                position: fixed;
                width: 100%;
                text-align: center;
            }
            .header {
                top: 0;
                background-color: #f2f2f2;
                padding: 10px 0;
            }
            .footer {
                bottom: 0;
                background-color: #f2f2f2;
                padding: 10px 0;
            }
        }
        
    </style>
    <link rel="icon" href="../../logo.png">
    <div class="container mt-5" style="direction: rtl;">
        <h3><?=$document['title']?></h3><hr>
        <i style="float:left;cursor: pointer;" id="directionIcon" class="fa fa-align-right"></i>
        
        <div class="mb-3">
            <label class="form-label">الموكل</label>
            <input type="text" class="form-control" value="<?=$document['client_first_name'] . ' ' . $document['client_last_name']?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">المحامي</label>
            <input type="text" class="form-control" value="<?=$document['lawyer_name']?> " disabled >
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
        
        <div style="text-align: justify;" id="editorShow" class="mt-3">
            <?=$document['content']?>
        </div>
        <a href="" id="download-pdf" class="btn btn-info btn_print">تحميل الوثيقة</a>
    </div>

    <script>
    const pdf_btn = document.querySelector('#download-pdf');
    const content = document.querySelector('#editorShow');

    pdf_btn.onclick = () => {
        const html_code = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?=$document['title']?></title>
                <link rel="stylesheet" href="../css/style.css">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
                <style>
                    #editorShow {
                        margin: 15px auto;
                        width: 80%;
                        direction: rtl;
                    }
                    div {
                        margin: 15px auto;
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
                    .header, .footer {
                        width: 100%;
                        text-align: center;
                    }
                    .header {
                        top: 0;
                        background-color: #f2f2f2;
                        padding: 10px 0;
                    }
                    .footer {
                        bottom: 0;
                        background-color: #f2f2f2;
                        padding: 10px 0;
                    }
                </style>
            </head>
            <body style="direction: ${content.style.direction};">
                <div class="header">
                    <img src="../../logo.png" alt="Logo" style="width: 100px;">
                </div>
                <main class="container mt-3" id="editorShow">${content.innerHTML}</main>
                <div class="footer">
                    <p>المحامي: <?=$document['lawyer_name']?> - الموكل: <?=$document['client_first_name'] . ' ' . $document['client_last_name']?></p>
                    <p>التوقيع: ____________________</p>
                </div>
            </body>
            </html>`;
                    
        const new_window = window.open('', '_blank');
        new_window.document.write(html_code);
        new_window.document.close(); 
        new_window.print();
        new_window.close(); 
    };

    document.getElementById("directionIcon").addEventListener("click", function() {
        var editor = document.getElementById("editorShow");
        var paragraphs = editor.getElementsByTagName("p");
        var currentDirection = window.getComputedStyle(editor).direction;

        if (currentDirection === "rtl") {
            editor.style.direction = "ltr";
            document.getElementById("directionIcon").className = "fa fa-align-left";
        } else {
            editor.style.direction = "rtl";
            document.getElementById("directionIcon").className = "fa fa-align-right";
        }

        // تحديث اتجاه النصوص داخل الـ editor
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
<?php 
    } else {
        echo "Unauthorized access.";
        exit;
    }
} else {
    echo "Unauthorized access.";
    exit;
} 
?>
