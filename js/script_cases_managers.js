var langOptions = {
    "sProcessing": "جارٍ التحميل...",
    "sLengthMenu": "أظهر _MENU_ مدخلات",
    "sZeroRecords": "لم يعثر على أية سجلات",
    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
    "sInfoPostFix": "",
    "sSearch": "ابحث هنا...",
    "processing": "جارٍ التحميل...",
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
        url: "req/fetch.php",
        type: "POST",
        data: {
            start_date: start_date,
            end_date: end_date
        },
        dataType: "json",
        success: function(data) {
            // Datatables
            $('#records').DataTable({
                "data": data,
                "destroy": true,  // للسماح بإعادة تهيئة الجدول
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    'copy', 'csv', 'excel'
                ],
                "responsive": true,
                "language": langOptions,
                "order": [[0, "desc"]],
                "columns": [
                    {"data": "case_id", "title": "ID"},
                    {"data": "case_title", "title": "عنوان القضية", "render": function(data, type, row, meta) {
                        return '<a style="text-decoration: none;" href="case-view.php?id=' + row.case_id + '">' + data + '</a>';
                    }},
                    {"data": "type_case", "title": "نوع القضية"},
                    {"data": "case_number", "title": "رقم القضية"},
                    {"data": "client_name", "title": "اسم الموكل"},
                    {"data": "lawyer_name", "title": "اسم المحامي"},
                    {"data": "plaintiff_names", "title": "المدعي"},
                    {"data": "defendant_names", "title": "الخصم"},
                    {"data": "court_name1", "title": "المحكمة"},
                    {"data": "department_names", "title": "الدائرة"},
                    {"data": "case_description", "title": "الوصف"},
                    {"data": "session_details", "title": "الجلسات القادمة", "render": function(data, type, row, meta) {
                        if (data.includes('رقم الجلسة')) {
                            return `<div style="color: green;">${data}</div>`;
                        } else {
                            return `<div style="color: red;">${data}</div>`;
                        }
                    }}
                ]
            });

            // البحث الفوري
            $('#searchInput').on('keyup', function() {
                $('#records').DataTable().search(this.value).draw();
            });
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

fetch();