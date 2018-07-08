(function ($) {
    $(document).ready(function () {

        $('#appointfox-modal').appendTo('body');

        var tableCustomers = $('#table_customers').DataTable({
            language: {
                url: "http://cdn.datatables.net/plug-ins/1.10.16/i18n/"+afx_dt.locale_display+".json"
                // url: afx_dt.plugin_url+"assets/js/datatable/i18n/"+afx_dt.locale_display+".lang"
            },
            lengthChange: true,
            // dom: '<"col-md-12"<"col-md-6"l><""f><t>ip>',
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'copy',
                    footer: false,
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'excel',
                    footer: false,
                    title: 'AppointFox - '+afx_dt.labels.customers,
                    filename: 'Excel-Customers',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'pdf',
                    footer: false,
                    filename: 'PDF-Customers',
                    title: 'AppointFox - '+afx_dt.labels.customers,
                    exportOptions: {
                        columns: [1, 2]
                    },
                    customize: function (doc) {
                        doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    footer: false,
                    title: 'AppointFox - '+afx_dt.labels.customers,
                    exportOptions: {
                        columns: [1, 2]
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
            // 'oLanguage': {
            //     'sEmptyTable': afx_dt.labels.notfound
            // },
            'ajax': {
                'url': afx_dt.ajax_url,
                'data': {
                    'action': 'afx-customers-table',
                    '_ajax_nonce': afx_dt.get_table_customers_nonce
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
                    'data': 'full_name'
                },
                {
                    'data': 'email'
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
                $('td:eq(1)', row).attr('data-title', 'Fullname');
                $('td:eq(2)', row).attr('data-title', 'Email');
                $('td:eq(3)', row).attr('data-title', 'Action');

                // delete checkbox
                var checkbox = '';
                checkbox += '<input type="checkbox" name="DeleteCheck[]" value="' + data.id + '" class="recordsToDelete" onclick="javascript:fnRowSelected(this);"></input>';
                $('td:eq(0)', row).html(checkbox);
                $('td:eq(0)', row).addClass('text-center');

                // view link
                var viewLink = '';
                viewLink += '<a href="' + afx_dt.ajax_url + '?action=afx-customers-view&id=' + data.id + '&_ajax_nonce=' + afx_dt.view_nonce + '" data-toggle="mainmodal">' + data.full_name + '</a>';
                $('td:eq(1)', row).html(viewLink);

                // edit and delete button
                var buttons = '';
                buttons += '<a data-tooltip="tooltip" title="Edit" class="btn btn-link btn-xs" href="' + afx_dt.ajax_url + '?action=afx-customers-edit&id=' + data.id + '&_ajax_nonce=' + afx_dt.edit_nonce + '" data-toggle="mainmodal"><i class="fa fa-pencil-square-o"></i> '+afx_dt.labels.edit+'</a>';
                //                buttons += ' <button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-tooltip="tooltip" title="Delete" data-target="#modalDeleteCustomer" data-id="' + data.id + '" data-name="' + data.full_name + '"><i class="fa fa-trash-o"></i> Delete</button>';
                var url = afx_dt.ajax_url + '?action=afx-customers-delete-process&_ajax_nonce=' + afx_dt.delete_nonce;
                buttons += ' <button type="button" class="btn btn-link btn-xs btn-confirm-delete" data-url="' + url + '" data-id="' + data.id + '" data-name="' + data.full_name + '"><i class="fa fa-trash-o"></i> '+afx_dt.labels.delete+'</button>';

                $('td:eq(3)', row).html(buttons);
                $('td:eq(3)', row).addClass('td-action center hidden-print');
            }
        });

        $('#table_customers').on('draw.dt', function () {
            initAjaxModal();
            initDeleteButton('customers');
            //            $('[data-tooltip="tooltip"]').tooltip();
        });

        //        tableCustomers.buttons().container()
        //                .appendTo('#table_customers_wrapper .col-sm-6:eq(0)');



        //        initAjaxModal();
        //        $('[data-tooltip="tooltip"]').tooltip();
    });
})(jQuery);