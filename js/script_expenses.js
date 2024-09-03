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
    function fetch(start_date = null, end_date = null, office_id = null) {
        $.ajax({
            url: "req/fetch_expenses.php",
            type: "POST",
            data: {
                start_date: start_date,
                end_date: end_date,
                office_id: office_id
            },
            dataType: "json",
            success: function(data) {
                $('#records').DataTable({
                    "data": data,
                    "destroy": true,
                    "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    "buttons": [
                        'copy', 
                        'csv', 
                        'excel',
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            },
                            customize: function (win) {
                                var totalAmount = calculateTotal(win.document.body.querySelector('table'));
                                $(win.document.body).find('table').append(
                                    '<tfoot><tr>' +
                                    '<th colspan="3">الإجمالي</th>' +
                                    '<th>' + totalAmount.toFixed(2) + '</th>' +
                                    '<th colspan="2"></th>' +
                                    '</tr></tfoot>'
                                );
                            }
                        }
                    ],
                    "responsive": true,
                    "language": langOptions,
                    orderCellsTop: true,
                    fixedHeader: true,
                    processing: true,
                    "order": [[0, "desc"]],
                    "columns": [
                        { "data": "expense_id", "title": "ID" },
                        {
                            "data": "type",
                            "title": "نوع المصروف",
                            "render": function(data, type, row) {
                                var url = "";
                                switch (row.source) {
                                    case 'sessions':
                                        url = "case-view.php?id=" + row.case_id + "&collapse_id=" + row.expense_id;
                                        break;
                                    default:
                                        url = "expenses-edit.php?id=" + row.expense_id;
                                        break;
                                }
                                return '<a style="text-decoration:none;color:#23a9f2;" href="' + url + '">' + data + '</a>';
                            }
                        },
                        { "data": "amount", "title": "المبلغ" },
                        { "data": "office_name", "title": "المكتب" },
                        { "data": "pay_date", "title": "التاريخ" },
                        {
                            "data": null,
                            "title": "الاجراءات",
                            "render": function(data, type, row, meta) {

                                    return '<button class="btn btn-danger btn-sm delete-button" data-id="' + row.expense_id + '" data-source="' + row.source + '">حذف</button>';

                                    
                                
                            }
                        }
                    ],
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api();
                        var pageTotal = api.column(2, { page: 'current' }).data().reduce((a, b) => {
                            return parseFloat(a) + parseFloat(b);
                        }, 0);
                        $(api.column(2).footer()).html(pageTotal.toFixed(2));
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }

    $('#filter_button').click(function() {
        const start_date = $('#start_date').val();
        const end_date = $('#end_date').val();
        const office_id = $('#office_id').val();
        fetch(start_date, end_date, office_id);
    });

    $('#reser_button').click(function() {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#office_id').val('');
        fetch();
    });

    fetch(); // استدعاء الدالة fetch عند تحميل الصفحة

    $('#searchInput').on('keyup', function() {
        filterTable();
    });

    // دالة للبحث الفوري
    function filterTable() {
        var searchText = $('#searchInput').val().trim().toLowerCase();
        var table = $('#records').DataTable();
        table.search(searchText).draw();
        updateTotal(table); // تحديث الإجمالي بعد البحث الفوري
    }

    // دالة لتحديث الإجمالي بعد البحث الفوري
    function updateTotal(table) {
        var totalAmount = 0;
        table.rows({ filter: 'applied' }).data().each(function(value, index) {
            totalAmount += parseFloat(value.amount);
        });
        $('#records tfoot th').eq(3).html(totalAmount.toFixed(2));
    }

    function calculateTotal(table) {
        var totalAmount = 0;
        $(table).find('tbody tr:visible').each(function() {
            totalAmount += parseFloat($(this).find('td').eq(2).text());
        });
        return totalAmount;
    }
});

// Here



$(document).ready(function() {
    var ctx = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'تكاليف المكتب حسب الأنواع',
                    font: {
                        size: 18
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });

    $("#filter_button").on("click", function() {
        var selectedOfficeId = $("#office_id").val();
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();

        $.ajax({
            url: "req/chartDataOffice.php",
            method: "POST",
            data: {
                office_id: selectedOfficeId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                var jsonData = JSON.parse(response);
                var groupedData = {};

                jsonData.forEach(function(expense) {
                    var type = expense.type;
                    if (!groupedData[type]) {
                        groupedData[type] = 0;
                    }
                    groupedData[type] += parseFloat(expense.amount);
                });

                var labels = Object.keys(groupedData);
                var data = Object.values(groupedData);

                if (labels.length === 0) {
                    $("#expenseCard").addClass("hidden");
                    $("#messageCard").removeClass("hidden");
                } else {
                    $("#messageCard").addClass("hidden");

                    var backgroundColors = [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ];
                    var borderColors = [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ];

                    var datasets = labels.map((label, index) => {
                        return {
                            label: label,
                            data: [data[index]],
                            backgroundColor: backgroundColors[index % backgroundColors.length],
                            borderColor: borderColors[index % borderColors.length],
                            borderWidth: 1
                        };
                    });

                    expenseChart.data.labels = ['المبالغ المدفوعة'];
                    expenseChart.data.datasets = datasets;
                    expenseChart.update();

                    $("#expenseCard").removeClass("hidden");
                }
            },
            error: function(xhr, status, error) {
                console.error("حدث خطأ: " + error);
            }
        });
    });

    $("#reser_button").on("click", function() {
        $("#expenseCard").addClass("hidden");
        $("#messageCard").addClass("hidden");
    });
});


$(document).ready(function() {
    $('#records').on('click', '.delete-button', function() {
        var id = $(this).data('id');
        var source = $(this).data('source');

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
                    url: 'req/delete-expenses.php',
                    type: 'POST',
                    data: {
                        id: id,
                        source: source
                    },
                    success: function(response) {
                        // Assuming the response from the server contains a success flag
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم !',
                                text: 'تم حذف المصروف بنجاح.',
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
                                'حدث خطأ أثناء الحذف.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'خطأ!',
                            'حدث خطأ أثناء الحذف.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});

