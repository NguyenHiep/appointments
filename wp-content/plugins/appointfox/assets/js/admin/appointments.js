(function ($) {
    $(document).ready(function () {

        $('#appointfox-modal').appendTo('body');

        var tableAppointments = $('#table_appointments').DataTable({
            lengthChange: true,
            // dom: '<"col-md-12"<"col-md-6"l><""f><t>ip>',
            dom: 'lBfrtip',
            buttons: [{
                extend: 'copy',
                footer: false,
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                footer: false,
                title: 'AppointFox - Appointments',
                filename: 'Excel-Appointments',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                footer: false,
                filename: 'PDF-Appointments',
                title: 'AppointFox - Appointments',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function (doc) {
                    doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                footer: false,
                title: 'AppointFox - Appointments',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                //                    autoPrint: false,
                customize: function (win) {
                    $(win.document.body)
                        .css('font-size', '12pt');

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
            ],
            //            responsive: true,
            'aProcessing': true,
            'aServerSide': true,
            'oLanguage': {
                'sEmptyTable': 'No record(s) found'
            },
            'ajax': {
                'url': afx_dt.ajax_url,
                'data': {
                    'action': 'afx-appointments-table',
                    '_ajax_nonce': afx_dt.get_table_appointments_nonce
                }
            },
            //            "initComplete": function (settings, json) {
            //                initAjaxModal();
            //                initDeleteButton();
            //            },
            'columns': [{
                'data': null,
                defaultContent: '',
                orderable: false
            },
            {
                'data': 'start_datetime'
            },
            {
                'data': 'service'
            },
            {
                'data': 'customer'
            },
            {
                'data': 'staff'
            },
            {
                'data': 'status'
            },
            {
                'data': null,
                bSortable: false,
                bSearchable: false
            }
            ],
            order: [1, 'asc'],
            'createdRow': function (row, data, index) {
                $('td:eq(0)', row).attr('data-title', ' ');
                $('td:eq(1)', row).attr('data-title', 'Date/Time');
                $('td:eq(2)', row).attr('data-title', 'Service');
                $('td:eq(3)', row).attr('data-title', 'Customer');
                $('td:eq(4)', row).attr('data-title', 'Staff');
                $('td:eq(5)', row).attr('data-title', 'Status');
                $('td:eq(6)', row).attr('data-title', 'Action');

                // delete checkbox
                var checkbox = '';
                checkbox += '<input type="checkbox" name="DeleteCheck[]" value="' + data.id + '" class="recordsToDelete" onclick="javascript:fnRowSelected(this);"></input>';
                $('td:eq(0)', row).html(checkbox);
                $('td:eq(0)', row).addClass('text-center');

                // view link
                var viewLink = '';
                viewLink += '<a href="' + afx_dt.ajax_url + '?action=afx-appointments-view&id=' + data.id + '&_ajax_nonce=' + afx_dt.view_nonce + '" data-toggle="mainmodal">' + data.full_name + '</a>';
                $('td:eq(1)', row).html(viewLink);

                // edit and delete button
                var buttons = '';
                buttons += '<a data-tooltip="tooltip" title="Edit" class="btn btn-link btn-xs" href="' + afx_dt.ajax_url + '?action=afx-appointments-edit&id=' + data.id + '&_ajax_nonce=' + afx_dt.edit_nonce + '" data-toggle="mainmodal"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                //                buttons += ' <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-tooltip="tooltip" title="Delete" data-target="#modalDeleteAppointment" data-id="' + data.id + '" data-name="' + data.full_name + '"><i class="fa fa-trash-o"></i> Delete</button>';
                var url = afx_dt.ajax_url + '?action=afx-appointments-delete-process&_ajax_nonce=' + afx_dt.delete_nonce;
                buttons += ' <button type="button" class="btn btn-link btn-xs btn-confirm-delete" data-url="' + url + '" data-id="' + data.id + '" data-name="' + data.full_name + '"><i class="fa fa-trash-o"></i> Delete</button>';

                $('td:eq(6)', row).html(buttons);
                $('td:eq(6)', row).addClass('td-action center hidden-print');
            }
        });

        $('#table_appointments').on('draw.dt', function () {
            initAjaxModal();
            initDeleteButton();
            //            $('[data-tooltip="tooltip"]').tooltip();
        });

        //        tableAppointments.buttons().container()
        //                .appendTo('#table_appointments_wrapper .col-sm-6:eq(0)');



        //        initAjaxModal();
        //        $('[data-tooltip="tooltip"]').tooltip();
    });
})(jQuery);