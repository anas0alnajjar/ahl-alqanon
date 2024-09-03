// // Fetch records
// var langOptions = {
//     "sProcessing":   "جارٍ التحميل...",
//     "sLengthMenu":   "أظهر _MENU_ مدخلات",
//     "sZeroRecords":  "لم يعثر على أية سجلات",
//     "sInfo":         "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
//     "sInfoEmpty":    "يعرض 0 إلى 0 من أصل 0 سجل",
//     "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
//     "sInfoPostFix":  "",
//     "sSearch":       "ابحث هنا...",
//     "sUrl":          "",
//     "oPaginate": {
//         "sFirst":    "الأول",
//         "sPrevious": "السابق",
//         "sNext":     "التالي",
//         "sLast":     "الأخير"
//     },
//     "oAria": {
//         "sSortAscending":  ": تفعيل لترتيب العمود تصاعدياً",
//         "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
//     }
// };


// function fetch(start_date, end_date) {
// $.ajax({
// url: "req/fetch.php",
// type: "POST",
// data: {
// start_date: start_date,
// end_date: end_date
// },
// dataType: "json",
// success: function(data) {
// // Datatables
// var i = "1";

// $('#records').DataTable({
//     "data": data,
//     // buttons
//     "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
//         "<'row'<'col-sm-12'tr>>" +
//         "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
//         "buttons": [
// 'copy', 'csv', 'excel',
// ],


//  // responsive
// "responsive": true,
// "language": langOptions,
// orderCellsTop: true,
// fixedHeader: true,
// processing: true,
// "order": [[0, "desc"]],
// // serverSide: true,
// "columns": [
//     {
//         "data": "case_id",
//         "title": "ID"
//     },
//     {
//         "data": "case_title",
//         "title": "عنوان القضية",
//         "render": function(data, type, row, meta) {
//             return '<a style="text-decoration: none;" href="case-view.php?id=' + row.case_id + '">' + data + '</a>';
//         }
//     },
//     {
//         "data": "case_type",
//         "title": "نوع القضية",
//         "render": function(data, type, row, meta) {
//             return `${row.case_type}`;
//         }
//     },
//     {
//         "data": "case_number",
//         "title": "رقم القضية"
//     },
//     {
//         "data": "client_name",
//         "title": "اسم الموكل",
//         "render": function(data, type, row, meta) {
//             return `${row.client_name}`;
//         }
//     },
//     {
//         "data": "last_modified",
//         "title": "آخر تعديل",
//         "render": function(data, type, row, meta) {
//             return `${row.last_modified}`;
//         }
//     },
//     {
//         "data": "plaintiff",
//         "title": "المدعي"
//     },
//     {
//         "data": "defendant",
//         "title": "المدعى عليه"
//     },
//     {
//         "data": "court_name",
//         "title": "اسم المحكمة"
//     },
//     {
//         "data": "session_details",
//         "title": "تفاصيل الجلسة",
//         "render": function(data, type, row, meta) {
//             if (data.includes('رقم الجلسة')) {
//                 return `<div style="color: green;">${data}</div>`;
//             } else {
//                 return `<div style="color: red;">${data}</div>`;
//             }
//         }
//     },
//     {
//         "data": "case_description",
//         "title": "وصف القضية"
//     }
// ]
// });

// }
// });
// }
// fetch();

