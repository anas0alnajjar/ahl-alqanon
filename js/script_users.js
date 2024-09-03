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

$(document).ready(function() {
    var table = $('#records').DataTable({
        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "buttons": [],
        "responsive": true,
        "language": langOptions,
        orderCellsTop: true,
        fixedHeader: true,
        processing: true,
        "order": [[0, "desc"]],
        "columns": [
            { "data": "id", "title": "ID" },
            {
                "data": "username",
                "title": "اسم المستخدم",
                "render": function(data, type, row) {
                    var url = "";
                    switch (row.source) {
                        case 'محامي':
                            url = "lawyer-edit.php?lawyer_id=" + row.id;
                            break;
                        case 'موكل':
                            url = "client-edit.php?client_id=" + row.id;
                            break;
                        case 'إداري':
                            url = "get-helper-info.php?id=" + row.id;
                            break;
                        case 'آدمن':
                            url = "admin-edit.php?admin_id=" + row.id;
                            break;
                        case 'مدير مكتب':
                            url = "manager-edit.php?manager_id=" + row.id;
                            break;
                    }
                    return '<a style="text-decoration:none;" href="' + url + '">' + data + '</a>';
                }
            },
            {
                "data": "role",
                "title": "الدور",
                "render": function(data, type, row) {
                    if (row.role == 'لم يتم تحديد رول له بعد') {
                        return "لم يتم تحديد رول له بعد";
                    } else {
                        return '<a style="text-decoration:none;" href="edit_power.php?id=' + row.role_id + '" class="editRole" data-type="'+ row.source +'" data-id="' + row.role_id + '">' + row.role + '</a>';
                    }
                }
            },
            {
                "data": "source",
                "title": "النوع"
            },
            {
                "data": null,
                "title": "الاجراءات",
                "render": function(data, type, row, meta) {
                    return '<button class="btn btn-danger btn-sm delete-button" data-id="' + row.id + '" data-source="' + row.source + '">حذف</button>';
                }
            }
        ]
    });

    // حدث الإدخال في حقل البحث الشخصي
    $('#searchInput').on('input', function() {
        var searchText = $(this).val();
        table.search(searchText).draw();
    });

    function fetch(start_date, end_date) {
        $.ajax({
            url: "req/fetch_users.php",
            type: "POST",
            data: {
                start_date: start_date,
                end_date: end_date
            },
            dataType: "json",
            success: function(data) {
                table.clear().rows.add(data).draw();
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }

    fetch(); // استدعاء الدالة fetch عند تحميل الصفحة
});


$(document).ready(function() {
    $('#records').on('click', '.delete-button', function() {
        var id = $(this).data('id');
        var source = $(this).data('source');

        if (source === 'آدمن' && id === 1) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'لا يمكن حذف الآدمن الرئيسي!',
            });
            return;
        }

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'req/delete-users.php',
                    type: 'POST',
                    data: {
                        id: id,
                        source: source
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم !',
                                text: 'تم حذف المستخدم بنجاح.',
                                showConfirmButton: false,
                                timer: 2000,
                                willClose: function() {
                                    window.location.reload();
                                }
                            });
                        } else {
                            console.log(response);
                            Swal.fire(
                                'خطأ!',
                                'حدث خطأ أثناء الحذف: ' + response.error,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء الحذف: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
});







