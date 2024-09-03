// Fetch records
var langOptions = {
    "sProcessing":   "جارٍ التحميل...",
    "sLengthMenu":   "أظهر _MENU_ مدخلات",
    "sZeroRecords":  "لم يعثر على أية سجلات",
    "sInfo":         "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
    "sInfoEmpty":    "يعرض 0 إلى 0 من أصل 0 سجل",
    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
    "sInfoPostFix":  "",
    "sSearch":       "ابحث هنا...",
    "sUrl":          "",
    "oPaginate": {
        "sFirst":    "الأول",
        "sPrevious": "السابق",
        "sNext":     "التالي",
        "sLast":     "الأخير"
    },
    "oAria": {
        "sSortAscending":  ": تفعيل لترتيب العمود تصاعدياً",
        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
    }
};


function fetch(start_date, end_date) {
$.ajax({
url: "req/fetch_services.php",
type: "POST",
data: {
start_date: start_date,
end_date: end_date
},
dataType: "json",
success: function(data) {
// Datatables
var i = "1";

$('#records').DataTable({
    "data": data,
    // buttons
    "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "buttons": [
'copy', 'csv', 'excel',
],


    // responsive
    "responsive": true,
    "language": langOptions,
    orderCellsTop: true,
    fixedHeader: true,
    processing: true,
    // serverSide: true,
    "columns": [{
            "data": "id",
        },{
        
        "data": "service_provider",
        "render": function(data, type, row, meta) {
        return '<a style="text-decoration: none;" href="services-view.php?id=' + row.id + '">' + data + '</a>';
        }
        },
        {
            "data": "phone",
            "render": function(data, type, row, meta) {
                return '<a style="text-decoration: none;" href="tel:' + row.phone + '">' + data + '</a>';
            }
        },
        {
            "data": "service_types",
        },
        {
            "data": "address",
        },
        {
            "data": "temporary_or_permanent"
        },
        {
            "data": "evidence",
        },
        {
            "data": "who_response",
        }
    ]
});
}
});
}
fetch();

$('table, tr, td').on('copy', function(event) {
    event.preventDefault();
    return false;
});

// منع عملية اللصق لجميع العناصر
$('table, tr, td').on('select', function(event) {
    event.preventDefault();
    return false;
});
