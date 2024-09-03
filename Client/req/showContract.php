<?php 
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'Client') {
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
        #editorShow {
            text-align: justify;
            margin: 15px auto;
            direction: rtl;
            border: 1px solid #ced4da;
            padding: 0 10px;
                border-radius: 10px;
        }
        #editorShow p {
            text-align: justify !important;
            margin: 15px 0;
            line-height: 1.6;
            font-size: 16px;
        }
        #editorShow h1, #editorShow h2, #editorShow h3, #editorShow h4, #editorShow h5, #editorShow h6 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        #editorShow ul, #editorShow ol {
            margin: 15px 0;
            padding-left: 40px;
        }
        #editorShow table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        #editorShow th, #editorShow td {
            border: 1px solid #dddddd;
            text-align: right;
            padding: 8px;
        }
        #editorShow th {
            background-color: #f2f2f2;
        }
        #editorShow img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 15px auto;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: right;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
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
        
    </div>

    <script>
    const pdf_btn = document.querySelector('#download-pdf');
    const content = document.querySelector('#editorShow');

    
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
