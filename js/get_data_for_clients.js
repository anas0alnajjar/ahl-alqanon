
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


// تحويل اللون من hex إلى rgba
function hexToRGBA(hex, opacity) {
    let r = 0, g = 0, b = 0;
    if (hex.length == 7) {
        r = parseInt(hex.substring(1, 3), 16);
        g = parseInt(hex.substring(3, 5), 16);
        b = parseInt(hex.substring(5, 7), 16);
    }
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}


$(document).ready(function() {
    // جلب القضايا من PHP
    $.ajax({
        url: "req/get_cases.php",
        method: "POST",
        success: function(response) {
            var jsonData = JSON.parse(response);
            var caseFilter = $("#case-filter");
            jsonData.cases.forEach(caseData => {
                var option = $("<option>", {
                    value: caseData.case_id,
                    text: caseData.case_title
                });
                caseFilter.append(option);
            });

            // عرض كل القضايا بشكل افتراضي
            fetchChartData("ALL");
        },
        error: function(xhr, status, error) {
            console.error("حدث خطأ: " + error);
        }
    });

    // معالجة حدث تغيير القضية
    $("#case-filter").on("change", function() {
        var selectedCaseId = $(this).val();
        fetchChartData(selectedCaseId);
    });

    function fetchChartData(caseId) {
        $.ajax({
            url: "req/chart_data.php",
            method: "POST",
            data: { case_id: caseId },
            success: function(response) {
                var jsonData = JSON.parse(response);
                console.log("Received data:", jsonData);

                // تحضير البيانات لـ Chart.js
                const sessions = jsonData.sessions;
                const groupedByMonth = {};
                const caseTitles = [...new Set(sessions.map(session => session.case_title))];
                
                sessions.forEach(session => {
                    const month = session.session_date.substring(0, 7); // استخراج السنة والشهر
                    if (!groupedByMonth[month]) {
                        groupedByMonth[month] = {};
                    }
                    if (!groupedByMonth[month][session.case_title]) {
                        groupedByMonth[month][session.case_title] = 0;
                    }
                    groupedByMonth[month][session.case_title] += session.count;
                });

                const chartLabels = Object.keys(groupedByMonth);
                const datasets = caseTitles.map(title => ({
                    label: title,
                    data: chartLabels.map(month => groupedByMonth[month][title] || 0),
                    backgroundColor: getRandomColor(0.5),
                    borderColor: getRandomColor(1),
                    borderWidth: 1
                }));

                // رسم المخطط البياني
                var chartCanvas = $("#case-chart")[0].getContext('2d');
                if (window.myChart) {
                    window.myChart.destroy();
                }
                window.myChart = new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: chartLabels, // استخدام الأشهر كـ label
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'الأشهر',
                                    font: {
                                        size: 16
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(200, 200, 200, 0.7)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'عدد الجلسات',
                                    font: {
                                        size: 16
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(200, 200, 200, 0.7)'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `العدد: ${context.raw}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'عدد الجلسات لكل شهر',
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
                        },
                        onClick: (event, elements, chart) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const month = chart.data.labels[index];
                                showDetailsByDay(month);
                            }
                        }
                    }
                });

                // عرض التفاصيل حسب الأيام عند النقر على شريط الشهر
                function showDetailsByDay(month) {
                    const filteredSessions = sessions.filter(session => session.session_date.startsWith(month));
                    const groupedByDay = {};
                    filteredSessions.forEach(session => {
                        const day = session.session_date;
                        if (!groupedByDay[day]) {
                            groupedByDay[day] = {};
                        }
                        if (!groupedByDay[day][session.case_title]) {
                            groupedByDay[day][session.case_title] = 0;
                        }
                        groupedByDay[day][session.case_title] += session.count;
                    });

                    const filteredLabels = Object.keys(groupedByDay);
                    const datasets = caseTitles.map(title => ({
                        label: title,
                        data: filteredLabels.map(date => groupedByDay[date][title] || 0),
                        backgroundColor: getRandomColor(0.4),
                        borderColor: getRandomColor(1),
                        borderWidth: 1
                    }));

                    window.myChart.data.labels = filteredLabels;
                    window.myChart.data.datasets = datasets;
                    window.myChart.options.scales.x.title.text = 'الأيام';
                    window.myChart.options.plugins.title.text = 'عدد الجلسات لكل شهر';
                    window.myChart.update();
                }
            },
            error: function(xhr, status, error) {
                console.error("حدث خطأ: " + error);
            }
        });
    }
});

// وظيفة لتوليد ألوان عشوائية بزيادة الشفافية



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
    // تعريف دالة display_events
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
                            textColor: getContrastYIQ(getRandomColor())
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
                        eventRender: function(event, element) {
                            if (!element.find('.delete-event-btn').length && event.id.startsWith("E")) {
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
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'خطأ في جلب البيانات.',
                        showConfirmButton: false,
                        timer: 2000,
                        willClose: function() {
                            // location.reload();
                        }
                    });
                }
            },
            error: function(xhr, status) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في جلب البيانات.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function() {
                        // location.reload();
                    }
                });
            }
        });
    }

    // جلب المحامين
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

    // مستمع لتغيير قيمة المحامين
    $('#lawyers-events').change(function() {
        display_events();
    });

    // عرض الأحداث عند تحميل الصفحة لأول مرة
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
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'خطأ في حذف الحدث.',
                    showConfirmButton: true,
                });
            }
        },
        error: function(xhr, status) {
            
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'خطأ في حذف الحدث.',
                showConfirmButton: true,
            });
        }
    });
}




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




