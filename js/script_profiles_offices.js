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
        url: "req/fetch_profiles.php",
        type: "POST",
        data: {
            start_date: start_date,
            end_date: end_date
        },
        dataType: "json",
        success: function(data) {
            console.log(data); // تحقق من البيانات القادمة
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
                "columns": [
                    { "data": "id", "title": "ID" },
                    {
                        "data": "office_name",
                        "title": "معاينة الصفحة",
                        "render": function(data, type, row, meta) {
                            return '<a style="text-decoration: none;" href="../offices/office_view.php?office=' + row.id + '" target="_blank">' + data + '</a>';
                        }
                    },
                    {
                        "data": "fname",
                        "title": "المسؤول"
                    },
                    {
                        "data": "address",
                        "title": "العنوان"
                    },
                    {
                        "data": "phone",
                        "title": "الهاتف",
                        "render": function(data, type, row) {
                            return '<span style="direction: ltr; unicode-bidi: embed;">' + data + '</span>';
                        }
                    },                    
                    {
                        "data": "email_address",
                        "title": "الإيميل"
                    },
                    {
                        "data": "desc1",
                        "title": "وصف مختصر",
                        "render": function(data, type, row, meta) {
                            // عرض أول 50 كلمة فقط من المحتوى
                            var words = data.split(' ').slice(0, 50);
                            var formattedWords = '';
                            for (var i = 0; i < words.length; i += 15) {
                                formattedWords += words.slice(i, i + 15).join(' ') + '<br>';
                            }
                            return `<div style="color: green;">${formattedWords}...</div>`;
                        }
                    },
                    {
                        "data": null,
                        "title": "الاجراءات",
                        "render": function(data, type, row, meta) {
                            let buttons = '';
                    
                            if (pages.profiles.write == 1) {
                                buttons += `<button class="btn btn-warning btn-sm" onclick="editProfile(${row.id})">تعديل</button> `;
                            }
                            if (pages.profiles.delete == 1) {
                                buttons += `<button class="btn btn-danger btn-sm" onclick="deleteProfile(${row.id})">حذف</button> `;
                            }
                    
                            // إضافة زر نسخ الرابط دائمًا بدون التحقق من الصلاحيات
                            buttons += `<button class="btn btn-primary btn-sm" onclick="copyLink(${row.id})">نسخ الرابط</button> `;
                            
                            return buttons;
                        },
                        "visible": true // عرض العمود دائمًا
                    }                    
                    
                ]
            });
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

fetch();

function editProfile(id) {
    window.location.href = 'profile-edit.php?profile=' + id;
}

function deleteProfile(id) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم حذف هذه الصفحة نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'req/profile-delete.php',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'تم الحذف!',
                            text: 'تم حذف الصفحة بنجاح.',
                            icon: 'success',
                            confirmButtonText: 'حسنًا'
                        }).then(() => {
                            // إعادة تحميل الصفحة أو تحديث الجدول لإظهار التغييرات
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonText: 'حسنًا'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء الحذف. يرجى المحاولة مرة أخرى لاحقًا.',
                        icon: 'error',
                        confirmButtonText: 'حسنًا'
                    });
                }
            });
        }
    });
}


// دالة للبحث الفوري
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        $('#records').DataTable().search(this.value).draw();
    });
});


function copyLink(id) {
    var link = `${window.location.origin}/offices/office_view.php?office=${id}`;
    navigator.clipboard.writeText(link).then(function() {
        Swal.fire({
            title: 'تم النسخ!',
            text: 'تم نسخ الرابط إلى الحافظة.',
            icon: 'success',
            confirmButtonText: 'حسنًا'
        });
    }, function() {
        Swal.fire({
            title: 'خطأ!',
            text: 'حدث خطأ أثناء نسخ الرابط. يرجى المحاولة مرة أخرى.',
            icon: 'error',
            confirmButtonText: 'حسنًا'
        });
    });
}