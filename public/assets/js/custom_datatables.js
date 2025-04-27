// Custom DataTables initialization
$(document).ready(function() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('.datatable-button-html5-columns')) {
        $('.datatable-button-html5-columns').DataTable().destroy();
    }

    // Basic initialization
    $('.datatable-button-html5-columns').DataTable({
        buttons: {
            dom: {
                button: {
                    className: 'btn btn-light'
                }
            },
            buttons: [
                {
                    extend: 'copyHtml5',
                    className: 'btn btn-light'
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-light'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-light'
                }
            ]
        },
        columnDefs: [
            {
                targets: 'no-sort',
                orderable: false
            }
        ]
    });

    // Initialize select2 for length menu styling
    if ($().select2) {
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true,
            width: 'auto'
        });
    }
});
