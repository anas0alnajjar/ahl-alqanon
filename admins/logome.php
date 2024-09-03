<?php
     include "../DB_connection.php";

    if(!isset($_GET['id'])){
        header('Location: templates.php');
    }

// جلب الرسالة من قاعدة البيانات
$stmt = $conn->prepare("SELECT message_text FROM templates WHERE id = 1");
$stmt->execute();
$message = $stmt->fetch(PDO::FETCH_ASSOC)['message_text'];

// تحويل المتغيرات الحقيقية إلى أسماء عربية
$variables = [
    '{$client_first_name}' => 'الاسم الأول للعميل',
    '{$case_title}' => 'عنوان القضية',
    '{$dueDate}' => 'تاريخ الاستحقاق',
    '{$dueHour}' => 'ساعة الاستحقاق'
];

foreach ($variables as $key => $value) {
    $message = str_replace($key, $value, $message);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMessage = $_POST['message_text'];

    // إعادة تحويل الأسماء العربية إلى المتغيرات الحقيقية قبل الحفظ
    foreach ($variables as $key => $value) {
        $newMessage = str_replace($value, $key, $newMessage);
    }



    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE templates SET message_text = ? WHERE id = ?");
    $stmt->bindParam(1, $newMessage, PDO::PARAM_STR);
    $stmt->bindParam(2, $id, PDO::PARAM_STR);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل الرسالة</title>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <style>
        
    </style>
</head>
<body>
    <h2>تعديل الرسالة</h2>
    <form method="post">
        <textarea name="message_text" id="editor"><?php echo htmlspecialchars($message); ?></textarea>
        <br>
        <input type="submit" value="حفظ التعديلات">
    </form>
    <h3>المتغيرات:</h3>
    <div class="variable" draggable="true" ondragstart="drag(event)">الاسم الأول للعميل</div>
    <div class="variable" draggable="true" ondragstart="drag(event)">عنوان القضية</div>
    <div class="variable" draggable="true" ondragstart="drag(event)">تاريخ الاستحقاق</div>
    <div class="variable" draggable="true" ondragstart="drag(event)">ساعة الاستحقاق</div>
    <script>
            let editor;

            // التحقق مما إذا كان CKEditor محملاً بالفعل
            if (typeof ClassicEditor !== 'undefined' && !editor) {
                ClassicEditor
                    .create(document.querySelector('#editor'))
                    .then(newEditor => {
                        editor = newEditor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }

            function drag(ev) {
                ev.dataTransfer.setData("text", ev.target.innerText);
            }

            function drop(ev) {
                ev.preventDefault();
                var data = ev.dataTransfer.getData("text");
                editor.model.change(writer => {
                    const insertPosition = editor.model.document.selection.getFirstPosition();
                    writer.insertText(data, { bold: true, color: '#007bff' }, insertPosition);
                });
            }

            function allowDrop(ev) {
                ev.preventDefault();
            }

            function insertText(text) {
                editor.model.change(writer => {
                    const insertPosition = editor.model.document.selection.getFirstPosition();
                    writer.insertText(text, { bold: true, color: '#007bff' }, insertPosition);
                });
            }

            document.querySelectorAll('.variable').forEach(function(el) {
                el.addEventListener('dragstart', drag);
                el.addEventListener('click', function() {
                    insertText(el.innerText);
                });
            });

            document.querySelector('#editor').addEventListener('drop', drop);
            document.querySelector('#editor').addEventListener('dragover', allowDrop);
        </script>
</body>
</html>


































<!-- Modal Edit For Dues by Dues-->
<div class="modal fade" id="editMessageModalDues" tabindex="-1" aria-labelledby="editMessageModalLabelDues" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="max-height:500px;overflow:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="editMessageModalLabelDues">تعديل الرسالة</h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="EditmessageFormDues" method="post">
                    <div class="form-group">
                        <label for="editEditorDues" class="form-label">نص الرسالة</label>
                        <textarea name="message_text" id="editEditorDues" class="form-control" ondrop="dropEditDues(event)" ondragover="allowDrop(event)"></textarea>
                    </div>
                    <input type="hidden" id="messageIdDuesEdit" name="messageId">
                    <input type="hidden" id="officeIdDuesEdit" name="office_id">
                    <input type="hidden" id="forWhomDuesEdit" name="for_whom">
                    <h5>المتغيرات:</h5>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('الاسم الأول للعميل')">الاسم الأول للعميل</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('اسم العائلة للعميل')">اسم العائلة للعميل</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('عنوان القضية')">عنوان القضية</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('تاريخ الجلسة')">تاريخ الجلسة</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('ساعة الجلسة')">ساعة الجلسة</div>
                    <div class="variable-Dues" draggable="true" ondragstart="drag(event)" onclick="insertVariableEditDues('اسم المحامي')">اسم المحامي</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">إغلاق</button>
                <button type="button" id="editMsgSessionsByDues" class="btn btn-success btn-block">حفظ التعديلات</button>
                </form>
            </div>
        </div>
    </div>
</div>


