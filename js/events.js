let recordsPerPage = 5;

function setRecordsPerPage(num) {
    recordsPerPage = num;
    $('#recordsPerPage').text(num);
    fetchEventsSessions(1); // إعادة جلب البيانات من الصفحة الأولى
}

function fetchEventsSessions(page = 1) {
    var start_date = $('#start_date').val();
    var condition = $('#condition').val();
    var search_text = $('#search_text').val();
    var filter_type = $('#filter_type').val();

    $.ajax({
        url: 'req/fetch_events_sessions.php',
        type: 'POST',
        data: {
            start_date: start_date,
            condition: condition,
            search_text: search_text,
            filter_type: filter_type,
            page: page,
            records_per_page: recordsPerPage // إرسال عدد السجلات المعروضة
        },
        success: function (data) {
            var parsedData = JSON.parse(data);
            if (filter_type === 'sessions') {
                $('#timeline-sessions').html(parsedData.sessions).parent().removeClass('col-md-6').addClass('col-md-12').show();
                $('#timeline-events').html('').parent().hide();
            } else if (filter_type === 'events') {
                $('#timeline-events').html(parsedData.events).parent().removeClass('col-md-6').addClass('col-md-12').show();
                $('#timeline-sessions').html('').parent().hide();
            } else {
                $('#timeline-sessions').html(parsedData.sessions).parent().removeClass('col-md-12').addClass('col-md-6').show();
                $('#timeline-events').html(parsedData.events).parent().removeClass('col-md-12').addClass('col-md-6').show();
            }
            renderPagination(parsedData.total_pages_sessions, parsedData.total_pages_events, parsedData.current_page, filter_type);
        }
    });
}

function renderPagination(totalPagesSessions, totalPagesEvents, currentPage, filterType) {
    let paginationHtml = '';
    let totalPages = filterType === 'sessions' ? totalPagesSessions : totalPagesEvents;

    if (totalPages > 1) {
        paginationHtml += '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="fetchEventsSessions(${i})">${i}</a></li>`;
        }
        paginationHtml += '</ul></nav>';
    }

    $('#pagination').html(paginationHtml);
}

$(document).ready(function () {
    $('#filterBtn').click(function () {
        fetchEventsSessions();
    });

    $('#defaultBtn').click(function () {
        $('#start_date').val('');
        $('#condition').val('equals');
        $('#search_text').val('');
        $('#filter_type').val('all');
        fetchEventsSessions();
    });

    $('#search_text').on('keyup', function () {
        fetchEventsSessions();
    });

    $('#filter_type').on('change', function () {
        fetchEventsSessions();
    });

    // جلب البيانات عند تحميل الصفحة لأول مرة
    fetchEventsSessions();
});




function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function updateTimeLabels() {
    $('.time-label').each(function () {
        var color = getRandomColor();
        $(this).css('background-color', color);
        var r = parseInt(color.substr(1, 2), 16);
        var g = parseInt(color.substr(3, 2), 16);
        var b = parseInt(color.substr(5, 2), 16);
        var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        var textColor = (yiq >= 128) ? 'black' : 'white';
        $(this).css('color', textColor);
    });
}

$(document).ajaxComplete(function () {
    updateTimeLabels();
});

document.getElementById('floatingButton').addEventListener('click', function () {
    const filterOptions = document.getElementById('filterOptions');
    const floatingButtonIcon = document.querySelector('#floatingButton i');
    if (filterOptions.style.display === 'none' || filterOptions.style.display === '') {
        filterOptions.style.display = 'flex';
        floatingButtonIcon.classList.remove('fa-plus');
        floatingButtonIcon.classList.add('fa-times');
    } else {
        filterOptions.style.display = 'none';
        floatingButtonIcon.classList.remove('fa-times');
        floatingButtonIcon.classList.add('fa-plus');
    }
});

document.getElementById('addEventOption').addEventListener('click', function () {
    var myModal = new bootstrap.Modal(document.getElementById('event_entry_modal'));
    myModal.show();
});
document.getElementById('addSessionOption').addEventListener('click', function () {
    var myModal = new bootstrap.Modal(document.getElementById('sessionModal'));
    myModal.show();
});


function save_event() {
    console.log("Starting save_event function");
    var event_data = {
        event_name: $('#event_name').val(),
        event_start_date: $('#event_start_date').val(),
        event_end_date: $('#event_end_date').val(),
        lawer_name: $('#lawer_name').val(),
        client_name: $('#client_name').val()
    };

    console.log("Event Data:", event_data);

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
        beforeSend: function () {
            console.log("Sending AJAX request with data:", event_data);
        },
        success: function (response) {
            console.log("AJAX request succeeded:", response);
            if (response.status) {
                var newEvent = {
                    id: response.event_id,
                    title: event_data.event_name,
                    start: event_data.event_start_date,
                    end: event_data.event_end_date
                };
                $('#event_entry_modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم الحفظ بنجاح.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function () {
                        fetchEventsSessions();
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
        error: function (xhr, status) {
            console.log("AJAX request failed:", status);
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


$(document).ready(function () {
    $('.close').click(function () {
        $('#event_entry_modal').modal('hide');
    });
});

$(document).ready(function () {
    $('.close').click(function () {
        $('#editEventModal').modal('hide');
    });
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


$('#closeEditModal,.btn-close,#closeModal,.close-details, .close').click(function () {
    $('#editSessionModal,#sessionModal,.details').modal('hide');
});

document.getElementById("sessionForm").addEventListener("submit", function (event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch('req/save_session.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (error) {
                throw new Error('Invalid JSON: ' + text);
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم حفظ البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 2000,
                    willClose: function () {
                        fetchEventsSessions();
                        document.getElementById("sessionForm").reset();
                        $('#sessionModal').modal('hide');
                    }
                });

            } else {
                Swal.fire({
                    title: 'خطأ!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'خطأ!',
                text: 'حدث خطأ غير متوقع: ' + error.message,
                icon: 'error',
                confirmButtonText: 'موافق'
            });
            console.error('Error:', error);
        });
});


$(document).ready(function () {
    $(document).on('click', '.delete-button', function () {
        var sessionId = $(this).data('id');
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
                                fetchEventsSessions();
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
        var eventId = $(this).data('id');
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
                        fetchEventsSessions();
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


$(document).on('click', '.edit-button', function () {
    var sessionId = $(this).data('id');
    $.ajax({
        url: 'req/get_session.php',
        type: 'GET',
        data: { id: sessionId },
        success: function (response) {
            if (response.success) {
                var session = response.session;
                $('#editSessionId').val(session.sessions_id);
                $('#editSessionNumber').val(session.session_number);
                $('#editCaseId').val(session.case_id);
                $('#assistant_lawyer').val(session.assistant_lawyer);
                $('#editSessionDateGregorian').val(session.session_date);
                $('#editSessionDateHijri').val(session.session_date_hjri);
                $('#editSessionHour').val(session.session_hour);
                $('#editNotes').val(session.notes);

                $('#editSessionModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: response.message
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
            });
        }
    });
});

// التحقق من أن جميع الحقول ممتلئة
function validateForm() {
    var isValid = true;
    $('#editSessionForm').find('input[required], select[required]').each(function () {
        if ($(this).val() === '') {
            isValid = false;
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    return isValid;
}

$('#editSessionForm').submit(function (e) {
    e.preventDefault();
    if (!validateForm()) {
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
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: 'تم تحديث البيانات بنجاح.',
                    showConfirmButton: false,
                    timer: 3000,
                    willClose: function () {
                        fetchEventsSessions();
                        $('#editSessionModal').modal('hide');
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
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
            });
        }
    });
});


$(document).on('click', '.edit-event-btn', function () {
    var eventId = $(this).data('id');
    $.ajax({
        url: 'req/get_event.php',
        type: 'GET',
        data: { id: eventId },
        success: function (response) {
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
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
            });
        }
    });
});

// التحقق من أن جميع الحقول ممتلئة
function validateEditEventForm() {
    var isValid = true;
    $('#editEventForm').find('input[required], select[required]').each(function () {
        if ($(this).val() === '') {
            isValid = false;
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    return isValid;
}

$('#editEventForm').submit(function (e) {
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
        success: function (response) {
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
                    willClose: function () {
                        $('#editEventModal').modal('hide');
                        fetchEventsSessions();
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
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى.'
            });
        }
    });
});

$('#saveEditEventButton').click(function () {
    $('#editEventForm').submit();
});
