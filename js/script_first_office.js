
const table = new gridjs.Grid({
    columns: [
        {
            id: 'table_id', 
            name: 'ID', 
            minWidth: '80px',
            cellRenderer: (cell, row) => {
                return h('button', {
                    className: 'py-2 mb-4 px-4 border rounded-md text-white bg-blue-600',
                    onClick: () => alert(`Editing "${row.cells[0].data}" "${row.cells[1].data}"`)
                  }, 'Edit');
            }
        },
        { id: 'national_id', name: 'الرقم الوطني', minWidth: '200px' },
        { id: 'father_name', name: 'اسم الأب', minWidth: '150px' },
        { id: 'mother_name', name: 'اسم الأم', minWidth: '150px' },
        { id: 'primary_recipient', name: 'المستفيد الأساسي', minWidth: '200px' },
        { id: 'primary_recipient_national_id', name: 'الرقم الوطني للمستفيد الأساسي', minWidth: '200px' },
        { id: 'secondary_recipient', name: 'المستفيد البديل', minWidth: '200px' },
        { id: 'secondary_recipient_national_id', name: 'الرقم الوطني للمستفيد البديل', minWidth: '200px' },
        { id: 'date_of_registration', name: 'تاريخ التسجيل', minWidth: '150px' },
        { id: 'project', name: 'المشروع', minWidth: '150px' }
    ],
    server: {
        url: 'req/fetch_adults.php',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        then: data => data.map(row => [
            row.table_id,
            row.national_id,
            row.father_name,
            row.mother_name,
            row.primary_recipient,
            row.primary_recipient_national_id,
            row.secondary_recipient,
            row.secondary_recipient_national_id,
            row.date_of_registration,
            row.project
        ])
        
    },
    search: {
        enabled: true,
        placeholder: 'ابحث هنا...'
    },
    pagination: {
        enabled: true,
        limit: 10,
        summary: true,
        buttonsCount: 5
    },
    sort: true,
    resizable: true,
    fixedHeader: true,
    height: '400px',
    width: '100%',
    // autoWidth: true,
    language: {
        search: {
            placeholder: 'ابحث هنا...'
        },
        pagination: {
            previous: 'السابق',
            next: 'التالي',
            showing: 'عرض',
            results: () => 'نتائج',
            to: 'إلى',
            of: 'من',
            ofTotal: 'من مجموع'
        },
        sort: {
            sortAsc: 'ترتيب تصاعدي',
            sortDesc: 'ترتيب تنازلي'
        },
        loading: 'جار التحميل...',
        noRecordsFound: 'لم يتم العثور على سجلات',
        error: 'حدث خطأ أثناء جلب البيانات'
    },
    style: {
        table: {

            'white-space': 'nowrap'

        }
    }
}).render(document.getElementById('adults-container'));



const table1 = new gridjs.Grid({
    columns: [
        {
            id: 'tableId', 
            name: 'ID', 
            minWidth: '80px',
            cellRenderer: (cell, row) => {
                return h('button', {
                    className: 'py-2 mb-4 px-4 border rounded-md text-white bg-blue-600',
                    onClick: () => alert(`Editing "${row.cells[0].data}" "${row.cells[1].data}"`)
                  }, 'Edit');
            }
        },
        { id: 'national_id', name: 'الرقم الوطني', minWidth: '200px' },
        { id: 'name', name: 'اسم الطفل', minWidth: '150px' },
        { id: 'project', name: 'المشروع', minWidth: '150px' }
    ],
    server: {
        url: 'req/fetch_children.php',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        then: data => data.map(row => [
            row.tableId,
            row.national_id,
            row.name,
            row.project
        ])
        
    },
    search: {
        enabled: true,
        placeholder: 'ابحث هنا...'
    },
    pagination: {
        enabled: true,
        limit: 10,
        summary: true,
        buttonsCount: 5
    },
    sort: true,
    resizable: true,
    fixedHeader: true,
    height: '400px',
    width: '100%',
    // autoWidth: true,
    language: {
        search: {
            placeholder: 'ابحث هنا...'
        },
        pagination: {
            previous: 'السابق',
            next: 'التالي',
            showing: 'عرض',
            results: () => 'نتائج',
            to: 'إلى',
            of: 'من',
            ofTotal: 'من مجموع'
        },
        sort: {
            sortAsc: 'ترتيب تصاعدي',
            sortDesc: 'ترتيب تنازلي'
        },
        loading: 'جار التحميل...',
        noRecordsFound: 'لم يتم العثور على سجلات',
        error: 'حدث خطأ أثناء جلب البيانات'
    },
    style: {
        table: {

            'white-space': 'nowrap'

        }
    }
}).render(document.getElementById('children-container'));


