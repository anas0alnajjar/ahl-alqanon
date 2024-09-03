
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
                rows += "<span class='text-[13px] font-medium text-gray-400'><a href='clients.php?search=" + session.client_first_name + ' ' +  session.client_last_name + "'>" + session.client_first_name + ' ' +  session.client_last_name + "</a></span>";
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
                var firstName = client.first_name ? client.first_name : "غير متوفر";
                var lastName = client.last_name ? client.last_name : "غير متوفر";
                var upcomingSessionsCount = client.upcoming_sessions_count !== null ? client.upcoming_sessions_count : "غير متوفر";
                var amountPaid = client.amount_paid !== null ? client.amount_paid : "غير متوفر";
                var paymentDate = client.payment_date !== null ? client.payment_date : "غير متوفر";

                // تجاهل الصفوف التي تحقق الشروط المحددة
                if (upcomingSessionsCount === 0 && amountPaid === "غير متوفر" && paymentDate === "غير متوفر") {
                    return true; // متابعة الحلقة دون إضافة الصف
                }

                rows += "<tr style='text-wrap:nowrap;'>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<div class='flex items-center'>";
                rows += "<a href='clients.php?search=" + firstName + " " + lastName + "' class='text-gray-600 text-sm font-medium hover:text-blue-500 ml-2 truncate'>" + firstName + " " + lastName + "</a>";
                rows += "</div>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50' style='direction: rtl;'>";
                rows += "<span class='text-[13px] font-medium text-rose-500'>" + upcomingSessionsCount + "</span>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<span class='text-[13px] font-medium text-gray-700'>" + (amountPaid !== "غير متوفر" ? amountPaid.toLocaleString('en-US') : amountPaid) + "</span>";
                rows += "</td>";
                rows += "<td class='py-2 px-4 border-b border-b-gray-50'>";
                rows += "<span class='text-[13px] font-medium text-gray-700'>" + paymentDate + "</span>";
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
        url: "req/get_offices.php",
        method: "POST",
        success: function(response) {
            var jsonData = JSON.parse(response);
            var officeSelect = $("#office-select");
            jsonData.offices.forEach(officeData => {
                var option = $("<option>", {
                    value: officeData.office_id,
                    text: officeData.office_name
                });
                officeSelect.append(option);
            });
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });

    // معالجة حدث تغيير القضية
    $("#office-select").on("change", function() {
        var selectedOfficeId = $(this).val();
        if (selectedOfficeId) {
            $.ajax({
                url: "req/chart_data.php",
                method: "POST",
                data: { office_id: selectedOfficeId },
                success: function(response) {
                    var jsonData = JSON.parse(response);

                    // تحضير البيانات لـ Chart.js
                    const chartLabels = [""];
                    const totalPaidData = [jsonData.total_paid];
                    const totalExpData = [jsonData.total_exp];
                    const differenceData = [jsonData.total_paid - jsonData.total_exp];

                    // رسم المخطط البياني 
                    var chartCanvas = $("#office-chart")[0].getContext('2d'); // الحصول على سياق الرسم
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
                                    text: `راقب أرباح المكتب - ${jsonData.office_name}`, 
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

$(document).ready(function() {
    function display_events() {
        var events = [];
        var selectedLawyer = $('#lawyers-events').val();

        $.ajax({
            url: 'req/display_event.php',
            method: 'POST',
            data: {
                lawyer_id: selectedLawyer
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    var result = response.data;

                    $.each(result, function(i, item) {
                        var event = {
                            id: item.id,
                            title: item.title,
                            start: item.start + (item.session_hour ? 'T' + item.session_hour : ''),
                            end: item.end ? item.end + (item.session_hour ? 'T' + item.session_hour : '') : null,
                            color: getRandomColor(),
                            textColor: getContrastYIQ(getRandomColor()),
                            type: item.type
                        };

                        events.push(event);
                    });

                    $('#calendar').fullCalendar('destroy');
                    $('#calendar').fullCalendar({
                        isRTL: true,
                        locale: 'ar-dz',
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay,list'
                        },
                        defaultView: 'month',
                        timeZone: 'local',
                        editable: true,
                        selectable: true,
                        selectHelper: true,
                        dayClick: function(start) {
                            var formattedStartDate = moment(start).locale('en').format('YYYY-MM-DD HH:mm:ss');
                            var formattedEndDate = '';

                            $('#event_start_date').val(formattedStartDate);
                            $('#event_end_date').val(formattedEndDate);

                            $('#event_entry_modal').modal('show');
                        },
                        select: function(start, end) {
                            var formattedStartDate = moment(start).locale('en').format('YYYY-MM-DD HH:mm:ss');
                            var formattedEndDate = moment(end).locale('en').format('YYYY-MM-DD HH:mm:ss');

                            $('#event_start_date').val(formattedStartDate);
                            $('#event_end_date').val(formattedEndDate);

                            $('#event_entry_modal').modal('show');
                        },
                        events: events,
                        eventClick: function(event, jsEvent, view) {
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
                                    var eventId = event.id.substring(1); // إزالة الحرف التمييزي
                                    delete_event(eventId, event.type);
                                }
                            });
                        },
                        eventRender: function(event, element) {
                            var delete_button = $('<button type="button" class="delete-event-btn"><i class="fa fa-times"></i></button>');
                            element.append(delete_button);

                            delete_button.on('click', function(e) {
                                e.stopPropagation(); // منع تفعيل eventClick
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
                                        var eventId = event.id.substring(1); // إزالة الحرف التمييزي
                                        delete_event(eventId, event.type);
                                    }
                                });
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'خطأ في جلب البيانات.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function(xhr, status) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في جلب البيانات.',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    function delete_event(event_id, event_type) {
        $.ajax({
            url: 'req/delete_event.php',
            method: 'POST',
            data: {
                id: event_id,
                type: event_type
            },
            dataType: 'json', // تأكد من استخدام JSON لتحليل الرد
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف!',
                        text: 'تم حذف الحدث بنجاح.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        display_events(); // تحديث الأحداث بعد الحذف
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء حذف الحدث: ' + response.message,
                        footer: response.errorInfo ? response.errorInfo[2] : ''
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء حذف الحدث.',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    $.ajax({
        url: "req/get_lawyers.php",
        method: "POST",
        success: function(response) {
            var jsonData = JSON.parse(response);
            var lawyerSelect = $("#lawyers-events");
            jsonData.lawyers.forEach(lawyersData => {
                var option = $("<option>", {
                    value: lawyersData.lawyer_id,
                    text: lawyersData.lawyer_name
                });
                lawyerSelect.append(option);
            });
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });

    $('#lawyers-events').change(function() {
        display_events();
    });

    display_events();
});






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
        return;
    }
    if (event_data.event_start_date === '' || event_data.event_end_date === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد تاريخ للحدث',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    if (event_data.lawer_name === '' && event_data.client_name === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يجب تحديد محامي أو عميل',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    $.ajax({
        url: 'req/save_event.php',
        type: 'POST',
        data: event_data,
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                var newEvent = {
                    id: response.event_id,
                    title: event_data.event_name,
                    start: event_data.event_start_date,
                    end: event_data.event_end_date,
                    color: getRandomColor(),
                    textColor: getContrastYIQ(getRandomColor())
                };

                $('#calendar').fullCalendar('renderEvent', newEvent, true);
                $('#event_entry_modal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم الحفظ بنجاح.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function() {
                        // إعادة تعيين قيم الفورم إلى القيم الافتراضية
                        $('#event_form')[0].reset();
                        //  display_events();
                        
                    }
                });

            } else {
                $('#error-message').text(response.msg).show();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في معالجة البيانات.',
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status) {
            $('#error-message').text('خطأ في حفظ الحدث!').show();
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'خطأ في حفظ الحدث.',
                showConfirmButton: true
            });
        }
    });
}



// function delete_event(event_id) {
//     $.ajax({
//         url: 'req/delete_event.php',
//         type: 'POST',
//         data: { event_id: event_id.substring(1) }, // إزالة البادئة عند الإرسال
//         success: function(response) {
//             if (response) { // التحقق مما إذا كان هناك رد من السيرفر
//                 $('#calendar').fullCalendar('removeEvents', event_id);
//                 Swal.fire({
//                     icon: 'success',
//                     title: 'تم الحذف!',
//                     text: 'تم حذف الحدث بنجاح.',
//                     showConfirmButton: false,
//                     timer: 2000, 
//                     willClose: function() {
//                         location.reload();
//                     }
//                 });
//             } else {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'خطأ!',
//                     text: 'خطأ في حذف الحدث.',
//                     showConfirmButton: true,
//                 });
//             }
//         },
//         error: function(xhr, status) {
            
//             Swal.fire({
//                 icon: 'error',
//                 title: 'خطأ!',
//                 text: 'خطأ في حذف الحدث.',
//                 showConfirmButton: true,
//             });
//         }
//     });
// }




// تعريف اللغة المحلية بشكل صحيح
var arDzLocale = {
    code: 'ar-dz',
    week: {
        dow: 0, // Sunday is the first day of the week.
        doy: 4 // The week that contains Jan 1st is the first week of the year.
    },
    direction: 'rtl',
    buttonText: {
        prev: 'السابق',
        next: 'التالي',
        today: 'اليوم',
        month: 'شهر',
        week: 'أسبوع',
        day: 'يوم',
        list: 'أجندة'
    },
    weekText: 'أسبوع',
    allDayText: 'اليوم كله',
    moreLinkText: 'أخرى',
    noEventsText: 'أي أحداث لعرض'
};

// إضافة اللغة المحلية إلى FullCalendar
$.fullCalendar.locale('ar-dz', arDzLocale);

// في دالة display_events، استخدم FullCalendar بشكل عادي
$('#calendar').fullCalendar({
    isRTL: true,
    locale: 'ar-dz', // استخدام اللغة المحلية هنا
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,list'
    },
    // باقي الإعدادات الأخرى لـ FullCalendar
    // ...
});




