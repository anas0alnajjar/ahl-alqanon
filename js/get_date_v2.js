
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
    moment.updateLocale('ar-dz', {
        months: [
            "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
            "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
        ],
        monthsShort: [
            "ينا", "فبر", "مار", "أبر", "ماي", "يون",
            "يول", "أغس", "سبت", "أكت", "نوف", "ديس"
        ]
    });

    function getAspectRatio() {
        return $(window).width() < 768 ? 1.35 : 3.0;
    }

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
                            color: '#272c3f',
                            textColor: '#cfccc0',
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
                        aspectRatio: getAspectRatio(),
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
                        eventClick: function(event) {
                            if (event.type === 'event') {
                                $.ajax({
                                    url: 'req/get_event.php',
                                    type: 'GET',
                                    data: { id: event.id.substring(1) },
                                    success: function(response) {
                                        try {
                                            response = JSON.parse(response);
                                        } catch (e) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'خطأ',
                                                text: 'فشل في تحليل استجابة الخادم.'
                                            });
                                            return;
                                        }

                                        if (response.success) {
                                            var event = response.event;
                                            $('#editEventId').val(event.event_id);
                                            $('#editEventName').val(event.event_name);
                                            $('#editEventStartDate').val(event.event_start_date);
                                            $('#editEventEndDate').val(event.event_end_date);
                                            $('#editLawyerName').val(event.lawyer_id);
                                            $('#editClientName').val(event.client_id);

                                            $('#editEventModal').modal('show');
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'خطأ',
                                                text: response.message
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'خطأ',
                                            text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
                                        });
                                    }
                                });
                            } else if (event.type === 'session') {
                                $.ajax({
                                    url: 'req/get_session.php',
                                    type: 'GET',
                                    data: { id: event.id.substring(1) },
                                    success: function(response) {
                                        try {
                                            // إذا كان response كائن JSON بالفعل، فلا داعي لتحليله مرة أخرى
                                            if (typeof response === 'string') {
                                                response = JSON.parse(response);
                                            }
                                            if (response.success) {
                                                var session = response.session;
                                                $('#editSessionId').val(session.sessions_id || '');
                                                $('#editSessionNumber').val(session.session_number || '');
                                                $('#editCaseId').val(session.case_id || '');
                                                $('#assistant_lawyer').val(session.assistant_lawyer || '');
                                                $('#editSessionDateGregorian').val(session.session_date || '');
                                                $('#editSessionDateHijri').val(session.session_date_hjri || '');
                                                $('#editSessionHour').val(session.session_hour || '');
                                                $('#editNotes').val(session.notes || '');
                                
                                                $('#editSessionModal').modal('show');
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'خطأ',
                                                    text: response.message
                                                });
                                            }
                                        } catch (e) {
                                            console.error("Parsing error:", e, response);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'خطأ',
                                                text: 'فشل في تحليل استجابة الخادم.'
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'خطأ',
                                            text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
                                        });
                                    }
                                });
                                
                            }
                        },
                        eventDrop: function(event, delta, revertFunc) {
                            Swal.fire({
                                title: 'هل أنت متأكد؟',
                                text: "هل تريد تحديث تاريخ هذا الحدث؟",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'نعم, قم بالتحديث!',
                                cancelButtonText: 'إلغاء'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    update_event_date(event);
                                } else {
                                    revertFunc();
                                }
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

    function update_event_date(event) {
        var startDate = moment(event.start).format('YYYY-MM-DDTHH:mm').replace(/[^0-9\-:]/g, 'T');
        var endDate = event.end ? moment(event.end).format('YYYY-MM-DDTHH:mm').replace(/[^0-9\-:]/g, 'T') : null;
        var hijriDate = moment(startDate, 'YYYY-MM-DDTHH:mm').format('iYYYY-iMM-iDD').replace(/[٠-٩]/g, function(d) {
            return '٠١٢٣٤٥٦٧٨٩'.indexOf(d);
        });
    
        hijriDate = hijriDate.replace(/[٠-٩]/g, function(d) {
            return '0123456789'.charAt('٠١٢٣٤٥٦٧٨٩'.indexOf(d));
        });
    
        var event_data = {
            id: event.id.substring(1),
            type: event.type,
            start_date: startDate,
            end_date: endDate,
            hijri_date: hijriDate
        };
    
        console.log(event_data);
    
        $.ajax({
            url: 'req/update_event_date.php',
            method: 'POST',
            data: event_data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث!',
                        text: 'تم تحديث تاريخ الحدث بنجاح.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء تحديث تاريخ الحدث: ' + response.message,
                        footer: response.errorInfo ? response.errorInfo[2] : ''
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء تحديث تاريخ الحدث.',
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

    $(window).resize(function() {
        $('#calendar').fullCalendar('option', 'aspectRatio', getAspectRatio());
    });

    $('#saveEditEventButton').click(function() {
        $('#editEventForm').submit();
    });

    $('#editEventForm').submit(function(e) {
        e.preventDefault();
        if (!validateEditEventForm()) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'يرجى تعبئة جميع الحقول المطلوبة'
            });
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: 'req/update_event.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'فشل في تحليل استجابة الخادم.'
                    });
                    return;
                }

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم تحديث البيانات بنجاح.',
                        showConfirmButton: false,
                        timer: 3000,
                        willClose: function() {
                            $('#editEventModal').modal('hide');
                            display_events();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
                });
            }
        });
    });

    $('#saveEditSessionButton').click(function() {
        $('#editSessionForm').submit();
    });

    $('#editSessionForm').submit(function(e) {
        e.preventDefault();
        if (!validateEditSessionForm()) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'يرجى تعبئة جميع الحقول المطلوبة'
            });
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: 'req/update_session.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    // إذا كانت الاستجابة تأتي كسلسلة نصية، نقوم بتحليلها كـ JSON
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
        
                    // التحقق من نجاح الاستجابة
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ بنجاح!',
                            text: 'تم تحديث البيانات بنجاح.',
                            showConfirmButton: false,
                            timer: 3000,
                            willClose: function() {
                                $('#editSessionModal').modal('hide');
                                display_events();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: response.message
                        });
                    }
                } catch (e) {
                    console.error("Parsing error:", e, response); // لإظهار رسالة الخطأ في وحدة التحكم
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'فشل في تحليل استجابة الخادم.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
                });
            }
        });
    });
    function validateEditEventForm() {
        var isValid = true;
        $('#editEventForm').find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }

    function validateEditSessionForm() {
        var isValid = true;
        $('#editSessionForm').find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }
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



$('#closeEditModal,.btn-close,#closeModal,.close-details, .close').click(function () {
    $('#editSessionModal,#sessionModal,#editEventModal, #agendaModal, #dateRangeModal').modal('hide');
});


document.addEventListener("DOMContentLoaded", function () {
    function convertToHijri(gregorianDate) {
        if (gregorianDate) {
            return moment(gregorianDate, 'YYYY-MM-DD').format('iYYYY-iMM-iDD');
        }
        return '';
    }

    function convertToGregorian(hijriDate) {
        if (hijriDate) {
            return moment(hijriDate, 'iYYYY-iMM-iDD').format('YYYY-MM-DD');
        }
        return '';
    }

    function attachDateChangeEvents() {
        var gregorianInputs = document.querySelectorAll('.geo-date-input');
        var hijriInputs = document.querySelectorAll('.hijri-date-input');

        gregorianInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                var hijriField = input.closest('form').querySelector('.hijri-date-input');
                hijriField.value = convertToHijri(input.value);
            });
        });

        hijriInputs.forEach(function (input) {
            $(input).hijriDatePicker({
                locale: "ar-sa",
                format: "DD-MM-YYYY",
                hijriFormat: "iYYYY-iMM-iDD",
                dayViewHeaderFormat: "MMMM YYYY",
                hijriDayViewHeaderFormat: "iMMMM iYYYY",
                showSwitcher: true,
                allowInputToggle: true,
                useCurrent: false,
                isRTL: true,
                viewMode: 'days',
                keepOpen: false,
                hijri: true,
                debug: false,
                showClear: true,
                showClose: true
            }).on('dp.change', function (e) {
                var gregorianField = input.closest('form').querySelector('.geo-date-input');
                gregorianField.value = convertToGregorian(e.date.format('iYYYY-iMM-iDD'));
            });

            input.addEventListener('change', function () {
                var gregorianField = input.closest('form').querySelector('.geo-date-input');
                gregorianField.value = convertToGregorian(input.value);
            });
        });
    }

    $('#sessionModal, #editSessionModal').on('shown.bs.modal', function () {
        attachDateChangeEvents();
    });

    // For dynamically loaded modals (e.g., via AJAX), reattach the date change events
    $(document).ajaxComplete(function () {
        attachDateChangeEvents();
    });
});


$(document).ready(function () {
    $(document).on('click', '.delete-button', function () {
        var sessionId = $('#editSessionId').val();
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
                    url: 'req/delete_session_v2.php',
                    type: 'POST',
                    data: { id: sessionId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'تم الحذف!',
                                'تم حذف الجلسة بنجاح.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'خطأ!',
                                response.error || 'حدث خطأ أثناء حذف الجلسة.',
                                'error'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMessage;
                        try {
                            errorMessage = JSON.parse(xhr.responseText).error;
                        } catch (e) {
                            errorMessage = 'حدث خطأ في الاتصال بالخادم.';
                        }
                        Swal.fire(
                            'خطأ!',
                            errorMessage + ': ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });
});

$(document).ready(function () {
    $(document).on('click', '.delete-event-btn', function () {
        var eventId = $('#editEventId').val();
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
                delete_event(eventId);
            }
        });
    });
});

function delete_event(event_id) {
    $.ajax({
        url: 'req/delete_event_v2.php',
        type: 'POST',
        data: { event_id: event_id },
        success: function (response) {
            if (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف!',
                    text: 'تم حذف الحدث بنجاح.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function () {
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
        error: function (xhr, status) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'خطأ في حذف الحدث.',
                showConfirmButton: true,
            });
        }
    });
}



document.addEventListener('DOMContentLoaded', (event) => {
    const pdfBtn = document.querySelector('#download-pdf');
    const openDateRangePicker = document.querySelector('#openDateRangePicker');
    const dateRangeModal = document.querySelector('#dateRangeModal');
    const dateRangeForm = document.querySelector('#dateRangeForm');
    const agendaModal = document.querySelector('#agendaModal');
    const agendaContent = document.querySelector('#agendaContent');

    // فتح المودال عند النقر على زر اختيار المدة الزمنية
    openDateRangePicker.addEventListener('click', () => {
        $('#dateRangeModal').modal('show');
    });

    // توليد الأجندة عند إرسال النموذج
    dateRangeForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const startDate = document.querySelector('#startDate').value;
        const endDate = document.querySelector('#endDate').value;

        // استدعاء AJAX لجلب البيانات بناءً على المدة الزمنية المحددة
        $.ajax({
            url: 'get_agenda.php',
            method: 'POST',
            data: {
                startDate: startDate,
                endDate: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    let agendaHtml = '<h2>الأجندة من ' + startDate + ' إلى ' + endDate + '</h2>';
                    agendaHtml += '<hr class="mt-2">';
        
                    if (response.data.sessions.length > 0) {
                        agendaHtml += '<h3 class="text-center mt-2">الجلسات</h3>';
                        agendaHtml += '<ul>';
                        response.data.sessions.forEach(session => {
                            const formattedDate = moment(session.start).format('(YYYY-MM-DD)');
                            const formattedTime = moment(session.start + ' ' + session.session_hour, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A');
                            agendaHtml += '<li class="event-session session"><strong>' + session.title + '</strong> - ' + formattedDate + ' ' + formattedTime + '</li>';
                        });
                        agendaHtml += '</ul>';
                    }
        
                    if (response.data.events.length > 0) {
                        agendaHtml += '<h3 class="text-center mt-2">الأحداث</h3>';
                        agendaHtml += '<ul>';
                        response.data.events.forEach(event => {
                            const formattedDate = moment(event.start).format('(YYYY-MM-DD)');
                            const formattedTime = moment(event.start + ' ' + event.session_hour, 'YYYY-MM-DD HH:mm:ss').format('hh:mm A');
                            agendaHtml += '<li class="event-session event"><strong>' + event.title + '</strong> - ' + formattedDate + ' ' + formattedTime + '</li>';
                        });
                        agendaHtml += '</ul>';
                    }
        
                    agendaContent.innerHTML = agendaHtml;
        
                    // عرض المودال
                    $('#dateRangeModal').modal('hide');
                    $('#agendaModal').modal('show');
                } else {
                    alert('لا توجد بيانات للأجندة في المدة المحددة.');
                }
            },
            error: function(xhr, status, error) {
                console.error('حدث خطأ أثناء جلب البيانات:', error);
            }
        });
        
    });

    // تنزيل الأجندة كـ PDF
    pdfBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const opt = {
            margin:       0.5,
            filename:     'agenda.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 1, logging: true, dpi: 192, letterRendering: true, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(agendaContent).save();
    });
});