(function ($) {
    $(document).ready(function () {

        $('#appointfox-modal').appendTo('body');

        var tablePayments = $('#table_payments').DataTable({
            language: {
                url: "http://cdn.datatables.net/plug-ins/1.10.16/i18n/"+afx_vars.locale_display+".json"
                // url: afx_dt.plugin_url+"assets/js/datatable/i18n/"+afx_dt.locale_display+".lang"
            },
            lengthChange: true,
            // dom: '<"col-md-12"<"col-md-6"l><""f><t>ip>',
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'copy',
                    footer: false,
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'excel',
                    footer: false,
                    title: 'AppointFox - '+afx_vars.labels.payments,
                    filename: 'Excel-Payments',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    footer: false,
                    filename: 'PDF-Payments',
                    title: 'AppointFox - '+afx_vars.labels.payments,
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    },
                    customize: function (doc) {
                        doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    footer: false,
                    title: 'AppointFox - '+afx_vars.labels.payments,
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
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
            //     'sEmptyTable': afx_vars.labels.notfound
            // },
            'ajax': {
                'url': afx_vars.ajax_url,
                'data': {
                    'action': 'afx-payments-table',
                    '_ajax_nonce': afx_vars.get_table_payments_nonce
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
                    'data': 'created'
                },
                {
                    'data': 'customer_name'
                },
                {
                    'data': 'service_name'
                },
                {
                    'data': 'appointment_datetime'
                },
                {
                    'data': 'amount'
                },
                {
                    'data': 'payment_type'
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
            order: [1, 'desc'],
            'createdRow': function (row, data, index) {
                $('td:eq(0)', row).attr('data-title', ' ');
                $('td:eq(1)', row).attr('data-title', afx_vars.labels.date);
                $('td:eq(2)', row).attr('data-title', afx_vars.labels.customer);
                $('td:eq(3)', row).attr('data-title', afx_vars.labels.service);
                $('td:eq(4)', row).attr('data-title', afx_vars.labels.appointment_date);
                $('td:eq(5)', row).attr('data-title', afx_vars.labels.amount);
                $('td:eq(6)', row).attr('data-title', afx_vars.labels.method);
                $('td:eq(7)', row).attr('data-title', afx_vars.labels.status);
                $('td:eq(8)', row).attr('data-title', afx_vars.labels.action);

                // delete checkbox
                var checkbox = '';
                checkbox += '<input type="checkbox" name="DeleteCheck[]" value="' + data.id + '" class="recordsToDelete" onclick="javascript:fnRowSelected(this);"></input>';
                $('td:eq(0)', row).html(checkbox);
                $('td:eq(0)', row).addClass('text-center');

                // date
                var datecol = '';
                datecol += moment(data.created).format('D-MMM-YYYY h:mma');
                $('td:eq(1)', row).html(datecol);

                // view link
                var viewLink = '';
                viewLink += '<a href="' + afx_vars.ajax_url + '?action=afx-payments-view&id=' + data.id + '&_ajax_nonce=' + afx_vars.view_nonce + '" data-toggle="mainmodal">' + data.customer_name + '</a>';
                $('td:eq(2)', row).html(viewLink);

                // appointment date/time
                var appointment_datecol = '';
                appointment_datecol += moment(data.appointment_datetime).format('D-MMM-YYYY h:mma');
                $('td:eq(4)', row).html(appointment_datecol);

                // status
                var status = '';
                if (data.status == 'Paid') {
                    status += '<span class="label label-success">' + data.status + '</span>';
                } else {
                    status += '<span class="label label-default">' + data.status + '</span>';
                }
                $('td:eq(7)', row).html(status);

                // amount
                var amount = '';
                amount += afx_vars.currency + data.amount;
                $('td:eq(5)', row).html(amount);

                // edit and delete button
                var buttons = '';
                // buttons += '<a data-tooltip="tooltip" title="Edit" class="btn btn-link btn-xs" href="' + afx_vars.ajax_url + '?action=afx-payments-edit&id=' + data.id + '&_ajax_nonce=' + afx_vars.edit_nonce + '" data-toggle="mainmodal"><i class="fa fa-pencil-square-o"></i> Edit</a>';
                var url = afx_vars.ajax_url + '?action=afx-payments-delete-process&_ajax_nonce=' + afx_vars.delete_nonce;
                buttons += ' <button type="button" class="btn btn-link btn-xs btn-confirm-delete" data-url="' + url + '" data-id="' + data.id + '" data-name="' + data.customer_name + '"><i class="fa fa-trash-o"></i> '+afx_vars.labels.delete+'</button>';

                $('td:eq(8)', row).html(buttons);
                $('td:eq(8)', row).addClass('td-action center hidden-print');
            }
        });

        $.fn.dataTable.moment('D-MMM-YYYY h:mma');

        $('#table_payments').on('draw.dt', function () {
            initAjaxModal();
            initDeleteButton('payments');
        });
    });
})(jQuery);