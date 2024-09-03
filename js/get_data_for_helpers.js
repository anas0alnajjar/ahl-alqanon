$(document).ready(function() {
    $.ajax({
        url: "req/get_active_cases.php", 
        type: "post", 
        dataType: "json",
        success: function(response) {
            
            var rows = "";
            var countCases = response.length; 
            $.each(response, function(index, cases) {
                rows += "<tr>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<div class='flex items-center'>";
                rows += "<a href='case-view.php?id=" + cases.case_id + "' class='text-gray-600 text-sm font-medium hover:text-blue-500 ml-2 truncate'>" + cases.case_title + "</a>";
                rows += "</div>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<span class='text-[13px] font-medium text-gray-400'>" + cases.client_name + "</span>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<span class='inline-block p-1 rounded bg-emerald-500/10 text-emerald-500 font-medium text-[12px] leading-none'>" + cases.source + "</span>";
                rows += "</td>";
                rows += "</tr>";
            });
            $("#active-cases tbody").html(rows);
            $("#count-cases").text(countCases); 
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });
});


$(document).ready(function() {
    $.ajax({
        url: "req/get_sessions.php", 
        type: "post", 
        dataType: "json",
        success: function(response) {
            
            var rows = "";

            $.each(response, function(index, session) {
                rows += "<tr>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<div class='flex items-center'>";
                rows += "<a href='case-view.php?id=" + session.case_id + "' class='text-gray-600 text-sm font-medium hover:text-blue-500 ml-2 truncate'>" + session.case_title + "</a>";
                rows += "</div>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50' style='direction: rtl;'>";
                rows += "<span class='text-[13px] font-medium text-gray-400'>" + session.session_date + " الساعة " + session.session_hour + "</span>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50' style='direction: rtl;'>";
                rows += "<span class='text-[13px] font-medium text-gray-400'> " + session.client_first_name + ' ' +  session.client_last_name + "</span>";
                rows += "</td>";
                rows += "</tr>";
            });
            $("#sessions-cases tbody").html(rows);
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });
});

$(document).ready(function() {
    $.ajax({
        url: "req/get_client_date.php", 
        type: "post", 
        dataType: "json",
        success: function(response) {
            
            var rows = "";

            $.each(response, function(index, client) {
                rows += "<tr>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<div class='flex items-center'>";
                rows += "<a href='#' class='text-gray-600 text-sm font-medium hover:text-blue-500 ml-2 truncate'>" + client.first_name + " " + client.last_name + "</a>";
                rows += "</div>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50' style='direction: rtl;'>";
                rows += "<span class='text-[13px] font-medium text-rose-500'>" + client.upcoming_sessions_count + "</span>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<span class='inline-block p-1 rounded bg-blue-500/10 text-sky-100 font-medium text-[12px] leading-none'>" + client.outstanding_cost.toLocaleString('en-US') + "</span>";
                rows += "</td>";
                rows += "</tr>";
            });
            $("#clientDate tbody").html(rows);
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });
});


$(document).ready(function() {
    // استرجاع القضايا من PHP
    $.ajax({
        url: "req/get_cases.php",
        method: "POST",
        success: function(response) {
            var jsonData = JSON.parse(response);
            var caseSelect = $("#case-select");
            jsonData.cases.forEach(caseData => {
                var option = $("<option>", {
                    value: caseData.case_id,
                    text: caseData.case_title
                });
                caseSelect.append(option);
            });
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });

    // معالجة حدث تغيير القضية
    $("#case-select").on("change", function() {
        var selectedCaseId = $(this).val();
        if (selectedCaseId) {
            $.ajax({
                url: "req/chart_data.php",
                method: "POST",
                data: { case_id: selectedCaseId },
                success: function(response) {
                    var jsonData = JSON.parse(response);

                    // تحضير البيانات لـ Chart.js
                    const chartLabels = [""];
                    const totalPaidData = [jsonData.total_paid];
                    const totalExpData = [jsonData.total_exp];
                    const differenceData = [jsonData.total_paid - jsonData.total_exp];

                    // رسم المخطط البياني 
                    var chartCanvas = $("#case-chart")[0].getContext('2d'); // الحصول على سياق الرسم
                    if (window.chart) {
                        window.chart.destroy(); //  تدمير المخطط السابق 
                    }
                    window.chart = new Chart(chartCanvas, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [
                                {
                                    label: 'إجمالي المبالغ المدفوعة',
                                    data: totalPaidData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'إجمالي المصاريف',
                                    data: totalExpData,
                                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                    borderColor: 'rgba(255, 159, 64, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'الفرق',
                                    data: differenceData,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }
                            ]
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
                                    text: `راقب مدفوعات القضية - ${jsonData.case_title}`, 
                                    font: {
                                        size: 18 
                                    }
                                },

                                legend: {
                                    position: 'bottom', // موضع legenda
                                    labels: {
                                        font: {
                                            size: 14 // حجم الخط 
                                        }
                                // ... (باقي إعدادات المخطط)
                            }
                        }
                    }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("حدث خطأ: " + error);
                }
            });
        } else {
            //  لا يوجد اختيار 
        }
    });
});

function getRandomColor() {
    // Generate a random hex color code
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

function getContrastYIQ(hexcolor) {
    hexcolor = hexcolor.replace('#', '');
    var r = parseInt(hexcolor.substring(0, 2), 16);
    var g = parseInt(hexcolor.substring(2, 4), 16);
    var b = parseInt(hexcolor.substring(4, 6), 16);
    var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    return (yiq >= 128) ? 'black' : 'white';
}

function display_events() {
    var events = [];

    $.ajax({
        url: 'req/display_event.php',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                var result = response.data;
                var eventColor = getRandomColor();
                var textColor = getContrastYIQ(eventColor);

                $.each(result, function(i, item) {
                    events.push({
                        id: item.id,  // استخدام المفتاح 'id'
                        title: item.title,
                        start: item.start,
                        end: item.end,
                        color: eventColor,
                        textColor: textColor
                    });
                });
                
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    defaultView: 'month',
                    timeZone: 'local',
                    editable: true,
                    selectable: true,
                    selectHelper: true,
                    isRTL: true, // إضافة دعم اللغة العربية
                    locale: 'ar', // استخدام اللغة العربية
                    select: function(start, end) {
                        $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
                        $('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
                        $('#event_entry_modal').modal('show');
                    },
                    events: events,
                    eventRender: function(event, element) {
                        if (!element.find('.delete-event-btn').length && event.id.startsWith("E")) { // شرط حذف فقط للأحداث من جدول events
                            var delete_button = $('<button type="button" class="delete-event-btn">&times;</button>');
                            element.append(delete_button);

                            element.on('mouseenter', function() {
                                delete_button.show();
                            });

                            element.on('mouseleave', function() {
                                delete_button.hide();
                            });

                            delete_button.click(function() {
                                Swal.fire({
                                    title: 'هل أنت متأكد؟',
                                    text: "سيتم حذف الجلسة نهائياً!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'نعم, قم بالحذف!',
                                    cancelButtonText: 'إلغاء'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        delete_event(event.id);
                                    }
                                });
                            });
                        }
                    }
                });
            } else {
                // Swal.fire({
                //     icon: 'error',
                //     title: 'خطأ!',
                //     text: 'خطأ في جلب البيانات.',
                //     showConfirmButton: false,
                //     timer: 2000, 
                //     willClose: function() {
                //         location.reload();
                //     }
                // });
            }
        },
        error: function(xhr, status) {
            // Swal.fire({
            //     icon: 'error',
            //     title: 'خطأ!',
            //     text: 'خطأ في جلب البيانات.',
            //     showConfirmButton: false,
            //     timer: 2000, 
            //     willClose: function() {
            //         // location.reload();
            //     }
            // });
        }
    });
}

function save_event() {
    var event_data = {
        event_name: $('#event_name').val(),
        event_start_date: $('#event_start_date').val(),
        event_end_date: $('#event_end_date').val(),
        lawer_name: $('#lawer_name').val(),
        client_name: $('#client_name').val()
    };

    if (event_data.event_name === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد اسم للحدث',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (event_data.event_start_date === '' || event_data.event_end_date === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد تاريخ للحدث',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    if (event_data.lawer_name === '' && event_data.client_name === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد محامي أو عميل',
            confirmButtonColor: '#dc3545'
        });
        return; // إيقاف التنفيذ
    }
    

    $.ajax({
        url: 'req/save_event.php',
        type: 'POST',
        data: event_data,
        success: function(response) {
            console.log(response); // اطبع الرد المرجعي للتحقق
            if (response) {
                $('#calendar').fullCalendar('refetchEvents');
                $('#event_entry_modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم الحفظ بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        location.reload();
                    }
                });
            } else {
                $('#error-message').text(response.msg).show();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في معالجة البيانات.',
                    showConfirmButton: true,

                });
            }
        },
        error: function(xhr, status) {
            $('#error-message').text('خطأ في حفظ الحدث!').show();
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'خطأ في حفظ الحدث.',
                showConfirmButton: true,
            });
        }
    });
}

function delete_event(event_id) {
    $.ajax({
        url: 'req/delete_event.php',
        type: 'POST',
        data: { event_id: event_id.substring(1) }, // إزالة البادئة عند الإرسال
        success: function(response) {
            if (response) { // التحقق مما إذا كان هناك رد من السيرفر
                $('#calendar').fullCalendar('removeEvents', event_id);
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف!',
                    text: 'تم حذف الحدث بنجاح.',
                    showConfirmButton: false,
                    timer: 2000, 
                    willClose: function() {
                        location.reload();
                    }
                });
            } else {
                console.log('response');
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في حذف الحدث.',
                    showConfirmButton: true,
                });
            }
        },
        error: function(xhr, status) {
            console.log('response');
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'خطأ في حذف الحدث.',
                showConfirmButton: true,
            });
        }
    });
}


$(document).ready(function() {
    display_events();
});
