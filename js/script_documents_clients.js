var langOptions = {
    "sProcessing": "جارٍ التحميل...",
    "sLengthMenu": "أظهر _MENU_ مدخلات",
    "sZeroRecords": "لم يعثر على أية سجلات",
    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
    "sInfoPostFix": "",
    "sSearch": "ابحث هنا...",
    "sUrl": "",
    "oPaginate": {
        "sFirst": "الأول",
        "sPrevious": "السابق",
        "sNext": "التالي",
        "sLast": "الأخير"
    },
    "oAria": {
        "sSortAscending": ": تفعيل لترتيب العمود تصاعدياً",
        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
    }
};

function fetch(start_date, end_date) {
    $.ajax({
        url: "req/fetch_documents.php",
        type: "POST",
        data: {
            start_date: start_date,
            end_date: end_date
        },
        dataType: "json",
        success: function(data) {
            console.log(data); // تحقق من البيانات القادمة

            // تعريف الأعمدة الأساسية
            let columns = [
                { "data": "document_id", "title": "ID" },
                {
                    "data": "title",
                    "title": "عنوان العقد/المستند",
                    "render": function(data, type, row, meta) {
                        return '<a style="text-decoration: none;" href="document-view.php?document_id=' + row.document_id + '">' + data + '</a>';
                    }
                }
            ];

            
            // إضافة باقي الأعمدة
            columns.push(
                {
                    "data": "content",
                    "title": "تفاصيل المستند /العقد",
                    "render": function(data, type, row, meta) {
                        // عرض أول 50 كلمة فقط من المحتوى
                        var words = data.split(' ').slice(0, 5);
                        var formattedWords = '';
                        for (var i = 0; i < words.length; i += 15) {
                            formattedWords += words.slice(i, i + 15).join(' ') + '<br>';
                        }
                        // تعديل الصور داخل المحتوى
                        var formattedContent = formattedWords.replace(
                            /<img/g,
                            '<img style="width: 200px; max-width: 100%; height: 100px; aspect-ratio: 587 / 570; object-fit: contain;"'
                        );
                        return `<div style="color: green;">${formattedContent}...</div>`;
                    }
                },
                {
                    "data": null,
                    "title": "الاجراءات",
                    "render": function(data, type, row, meta) {
                        let buttons = '';
                        if (pages.documents.write == 1) {
                            buttons += `<button class="btn btn-warning btn-sm" onclick="editDocument(${row.document_id})">تعديل</button>`;
                        }
                        if (pages.documents.delete == 1) {
                            buttons += ` <button class="btn btn-danger btn-sm" onclick="deleteDocument(${row.document_id})">حذف</button>`;
                        }
                        return buttons || ''; // إذا لم يكن هناك أي أزرار، عرض محتوى فارغ
                    },
                    "visible": pages.documents.write == 1 || pages.documents.delete == 1 // عرض العمود فقط إذا كانت أي من الصلاحيات موجودة
                }
            );

            // تهيئة DataTable
            $('#records').DataTable({
                "data": data,
                "destroy": true,  // للسماح بإعادة تهيئة الجدول
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [],
                "responsive": true,
                "language": langOptions,
                "orderCellsTop": true,
                "fixedHeader": true,
                "processing": true,
                "order": [[0, "desc"]],
                "columns": columns
            });
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

fetch();

function editDocument(id) {
    window.location.href = 'document-edit.php?document_id=' + id;
}

function deleteDocument(id) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم حذف هذا المستند نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إذا قام المستخدم بالموافقة على الحذف، قم بتوجيهه لصفحة الحذف
            window.location.href = 'req/document-delete.php?document_id=' + id;
        }
    })
}

// دالة للبحث الفوري
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        $('#records').DataTable().search(this.value).draw();
    });
});


