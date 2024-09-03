<script>
document.addEventListener("DOMContentLoaded", function() {
    var pages = <?php echo $permissions_json; ?>;

    for (var page_name in pages) {
        var perms = pages[page_name];

        // صلاحيات القراءة
        if (perms['read'] == 1) {
            document.querySelectorAll('.' + page_name).forEach(function(elem) {
                elem.classList.remove('hidden-by-default');
            });
        } else {
            document.querySelectorAll('.' + page_name).forEach(function(elem) {
                elem.classList.add('hidden-by-default');
            });
        }

        // صلاحيات الكتابة
        if (perms['write'] == 1) {
            document.querySelectorAll('.' + page_name + '-write').forEach(function(elem) {
                elem.disabled = false;
                elem.classList.remove('hidden-by-default');
            });
        } else {
            document.querySelectorAll('.' + page_name + '-write').forEach(function(elem) {
                elem.disabled = true;
                elem.classList.add('hidden-by-default');
            });
        }

        // صلاحيات الإضافة
        if (perms['add'] == 1) {
            document.querySelectorAll('.' + page_name + '-add').forEach(function(elem) {
                elem.disabled = false;
                elem.classList.remove('hidden-by-default');
            });
        } else {
            document.querySelectorAll('.' + page_name + '-add').forEach(function(elem) {
                elem.disabled = true;
                elem.classList.add('hidden-by-default');
            });
        }

        // صلاحيات الحذف
        if (perms['delete'] == 1) {
            document.querySelectorAll('.' + page_name + '-delete').forEach(function(elem) {
                elem.disabled = false;
                elem.classList.remove('hidden-by-default');
            });
        } else {
            document.querySelectorAll('.' + page_name + '-delete').forEach(function(elem) {
                elem.disabled = true;
                elem.classList.add('hidden-by-default');
            });
        }

        // تعطيل الحقول داخل الكولابس الخاص بالجلسات إذا كانت صلاحيات التعديل 0
        if (page_name === 'sessions' && perms['write'] == 0) {
            document.querySelectorAll('#sessions-info input, #sessions-info select, #sessions-info textarea').forEach(function(elem) {
                elem.disabled = true;
            });
        }
      
        if (page_name === 'expenses_sessions' && perms['write'] == 0) {
            document.querySelectorAll('#dynamic_cards_expenses input, #dynamic_cards_expenses select, #dynamic_cards_expenses textarea').forEach(function(elem) {
                elem.disabled = true;
            });
        }
       
        if (page_name === 'payments' && perms['write'] == 0) {
            document.querySelectorAll('#dynamic_cards_payment input, #dynamic_cards_payment select, #dynamic_cards_payment textarea').forEach(function(elem) {
                elem.disabled = true;
            });
        }
        if (page_name === 'offices' && perms['write'] == 0) {
            document.querySelectorAll('#editForm input, #editForm select, #editForm textarea').forEach(function(elem) {
                elem.disabled = true;
            });
        }
        if (page_name === 'roles' && perms['write'] == 0) {
            document.querySelectorAll('#roleFormEdit input, #roleFormEdit select, #roleFormEdit textarea, #roleFormEdit checkbox').forEach(function(elem) {
                elem.disabled = true;
            });
        }
        
                if (page_name === 'message_customization' && perms['write'] == 0) {
            const selectors = [
                '#EditmessageForm input, #EditmessageForm select, #EditmessageForm textarea',
                '#EditmessageFormDues input, #EditmessageFormDues select, #EditmessageFormDues textarea',
                '#EditmessageFormDues2 input, #EditmessageFormDues2 select, #EditmessageFormDues2 textarea',
                '#EditmessageForm2 input, #EditmessageForm2 select, #EditmessageForm2 textarea'
            ];

            selectors.forEach(function(selector) {
                document.querySelectorAll(selector).forEach(function(elem) {
                    elem.disabled = true;
                });
            });
        }

    }

    // إخفاء القائمة المنسدلة إذا كانت جميع العناصر مخفية
    document.querySelectorAll('.dropdown').forEach(function(dropdown) {
        var dropdownItems = dropdown.querySelectorAll('.dropdown-menu .nav-link, .dropdown-menu .dropdown-item');
        var allHidden = true;
        dropdownItems.forEach(function(item) {
            if (window.getComputedStyle(item).display !== 'none') {
                allHidden = false;
            }
        });
        if (allHidden) {
            dropdown.classList.add('hidden-by-default');
        } else {
            dropdown.classList.remove('hidden-by-default');
        }
    });
});
</script>