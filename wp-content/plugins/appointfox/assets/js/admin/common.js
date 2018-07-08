var $ = jQuery.noConflict();

function secondsToString(seconds) {
    var numdays = Math.floor(seconds / 86400);
    var numhours = Math.floor((seconds % 86400) / 3600);
    var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
    var numseconds = ((seconds % 86400) % 3600) % 60;

    // return numdays + " days " + numhours + " hours " + numminutes + " minutes " + numseconds + " seconds";
    if (numhours && numminutes) {
        return numhours + ' hours ' + numminutes + ' minutes';
    }

    if (numminutes) {
        return numminutes + ' minutes';
    }

    if (numhours) {
        if (numhours > 1) {
            return numhours + ' hours';
        } else {
            return numhours + ' hour';
        }
    }

    return '';
}

function initAjaxModal() {
    $('[data-toggle="mainmodal"]').one('click', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');

        if (url.indexOf('#') === 0) {
            $('#mainmodal').modal('open');
        } else {
            var loadingContent = $('#mainmodal_loading').html();
            $('#mainmodal').html(loadingContent);

            $('#mainmodal').modal('show');

            $.get(url, function (data) {
                //                $('#mainmodal').modal();
                $('#mainmodal').html(data);

            }).success(function () {
                //                NProgress.done();
            }).fail(function (data) {
                //                $('#redirect').html(data.responseText);
            });
        }
    });
}

function camelize(string) {
    var a = string.split('_'),
        i;
    s = [];
    for (i = 0; i < a.length; i++) {
        s.push(a[i].charAt(0).toUpperCase() + a[i].substring(1));
    }
    s = s.join('');
    return s;
}

function fnRowSelected(row) {
    var s = $(row).is(':checked');
    if (s == true) {
        $(row).closest('tr').addClass('selected');
    } else {
        $(row).closest('tr').removeClass('selected');
    }
}

function initDeleteButton(tablename) {

    if (tablename === undefined) {
        tablename = 'staffs';
    }

    $('.btn-confirm-delete').on('click', function () {
        var id = $(this).data('id');
        var url = $(this).data('url');
        var name = $(this).data('name');

        swal({
            title: afx_common.labels.are_you_sure+'?',
            html: afx_common.labels.you_wont_able_revert+'!<br />'+afx_common.labels.record+': <b>' + name + '</b>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: afx_common.labels.yes_delete_it+'!'
        }).then(function () {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: id
                },
                success: function () {
                    $('#table_' + tablename).DataTable().ajax.reload();

                    // prompt success
                    swal(
                        afx_common.labels.deleted+'!',
                        afx_common.labels.record_deleted+'.',
                        'success'
                    );
                },
                dataType: 'json'
            });


        }, function (dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            //            if (dismiss === 'cancel') {
            //                swal(
            //                        'Cancelled',
            //                        'Your imaginary file is safe :)',
            //                        'error'
            //                        );
            //            }
        });
    });
}


function initEventsFormService() {
    (function ($) {
        $('.color-field').wpColorPicker({
            width: 200
        });

        $('#btnAddCategory, .btn-category-add').click(function (e) {
            e.preventDefault();
            $('#ModalFormCategoryTitle').html(afx_common.labels.create_new_category+'...');
            $('#CategoryId').val('');
            $('#CategoryName').val('');
            $('#ModalFormCategory').modal('show');
        });

        $('#btnCancelSaveService').click(function (e) {
            e.preventDefault();
            $('#PanelFormService').hide();
            $('#PanelWelcome').show();
            $('.btn-service-edit').removeClass('active');
        });

        $('#btnDeleteService').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var title = $(this).data('title');

            swal({
                title: 'Are you sure?',
                html: afx_common.labels.you_wont_able_revert+'!<br />'+afx_common.labels.service+': <b>' + title + '</b>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: afx_common.labels.yes_delete_it+'!'
            }).then(function () {
                $.ajax({
                    type: 'POST',
                    url: afx_vars.ajax_url,
                    data: {
                        id: id,
                        action: 'afx-services-delete',
                        _ajax_nonce: afx_vars.delete_service_nonce
                    },
                    success: function () {
                        updateServicesList();

                        $('#PanelFormService').hide();
                        $('#PanelWelcome').show();

                        // prompt success
                        swal(
                            afx_common.labels.deleted+'!',
                            afx_common.labels.record_deleted+'.',
                            'success'
                        );
                    },
                    dataType: 'json'
                });


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
            });
        });

        $('#FormService').ajaxForm({
            dataType: 'json',
            beforeSubmit: function (formData, jqForm, options) {
                jqForm.find('.text-danger').remove();
                jqForm.find('.form-group').removeClass('has-error');
                jqForm.find('.form-control').removeClass('form-error');
            },
            success: function (data) {
                if (data.success) {
                    $('#PanelFormService').hide();
                    $('#PanelWelcome').show();

                    // reload services list
                    updateServicesList();

                    // prompt success
                    swal(
                        afx_common.labels.success+'!',
                        afx_common.labels.service_saved+'.',
                        'success'
                    );

                } else {

                    $.each(data.errors, function () {
                        var element = $('#Service' + camelize(this.field));
                        element.addClass('form-error');
                        element.parent().addClass('has-error');

                        var _insert = $(document.createElement('span')).insertAfter(element);
                        _insert.addClass('help-block text-danger').text(this.msg);

                    });
                }
                Ladda.stopAll();
            }
        });

        Ladda.bind('button[type=submit]');
    })(jQuery);
}

function updateCategoriesDDL(selected_id) {
    (function ($) {
        if (selected_id === undefined) {
            selected_id = '';
        }

        $.ajax({
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'afx-categories-list',
                _ajax_nonce: afx_vars.get_list_nonce
            },
            url: afx_vars.ajax_url,
            success: function (result) {
                $('#ServiceCategoryId').html('');
                $('#ServiceCategoryId')
                    .append($('<option></option>')
                        .attr('value', ' ')
                        .text('(none)'));

                $.each(result.data, function (key, field) {
                    if (field.id == selected_id) {
                        $('#ServiceCategoryId')
                            .append($('<option selected></option>')
                                .attr('value', field.id)
                                .text(field.name));
                    } else {
                        $('#ServiceCategoryId')
                            .append($('<option></option>')
                                .attr('value', field.id)
                                .text(field.name));
                    }
                });
            }
        });
    })(jQuery);
}

/**
 * Update ListServices
 */
function updateServicesList() {
    (function ($) {
        $('#PanelServicesList').hide();
        $('#PanelLoading').show();

        $.ajax({
            type: 'GET',
            data: {
                action: 'afx-services-list',
                _ajax_nonce: afx_vars.get_list_nonce
            },
            url: afx_vars.ajax_url,
            success: function (result) {
                $('#PanelServicesList').html(result);
                $('#PanelLoading').hide();
                $('#PanelServicesList').show();

                $('.category-name').hover(
                    function () {
                        // $(this).append($("<span> ***</span>"));
                        $(this).children('.panel-category-action').css('display', 'inline-block');
                    },
                    function () {
                        // $(this).find("span:last").remove();
                        $(this).children('.panel-category-action').hide();
                    });

                $('.btn-category-edit').click(function (e) {
                    e.preventDefault();
                    $('#ModalFormCategoryTitle').html('Edit Category');
                    $('#CategoryId').val($(this).data('id'));
                    $('#CategoryName').val($(this).data('name'));
                    $('#ModalFormCategory').modal('show');
                });

                $('.btn-service-edit').click(function (e) {
                    e.preventDefault();
                    // clear all active buttons

                    $('.btn-service-edit').removeClass('active');

                    $(this).addClass('active');
                    var id = $(this).data('id');

                    $('#PanelWelcome').hide();
                    $('#PanelFormService').hide();
                    $('#PanelLoadingFormService').show();

                    $.ajax({
                        type: 'GET',
                        url: afx_vars.ajax_url,
                        data: {
                            action: 'afx-services-edit',
                            _ajax_nonce: afx_vars.edit_service_nonce,
                            id: id
                        },
                        success: function (result) {
                            $('#PanelLoadingFormService').hide();
                            $('#PanelFormService').html(result);

                            initEventsFormService();

                            $('#PanelFormService').show();

                        }
                    });

                });

                $('.btn-category-delete').click(function (e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var name = $(this).data('name');

                    swal({
                        title: afx_common.labels.are_you_sure+'?',
                        html: afx_common.labels.you_wont_able_revert+'!<br />'+afx_common.labels.category+' <b>' + name + '</b> '+afx_common.labels.and_all_services_deleted,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: afx_common.labels.yes_delete_it+'!'
                    }).then(function () {
                        $.ajax({
                            type: 'POST',
                            url: afx_vars.ajax_url,
                            data: {
                                action: 'afx-categories-delete',
                                _ajax_nonce: afx_vars.delete_category_nonce,
                                id: id
                            },
                            success: function () {
                                // prompt success
                                swal(
                                    afx_common.labels.deleted+'!',
                                    afx_common.labels.the_category_deleted+'.',
                                    'success'
                                );

                                updateServicesList();
                            },
                            dataType: 'json'
                        });

                    }, function (dismiss) {});
                });
            }
        });
    })(jQuery);
}

(function ($) {
    $(document).ready(function () {
        $('#checkAll').on('click', function () {
            $('.recordsToDelete').prop('checked', this.checked);

            if (this.checked) {
                $('.recordsToDelete').closest('tr').addClass('selected');
            } else {
                $('.recordsToDelete').closest('tr').removeClass('selected');
            }
        });

        $('#btnBulkDelete').on('click', function () {

            //        var atLeastOneIsChecked = $('input:checkbox:checked').length > 0;

            var selectedIDs = $('input:checkbox:checked').map(function () {
                if ($(this).val() != 'all') {
                    return $(this).val();
                }
            }).get();

            if (selectedIDs.length === 0) {
                swal(
                    afx_common.labels.oops+'...',
                    afx_common.labels.no_record_selected+'!',
                    'error'
                );

                Ladda.stopAll();
                return false;
            }

            var url = $(this).data('url');
            var tablename = $(this).data('tablename');

            if (tablename == undefined) {
                tablename = 'staffs';
            }

            swal({
                title: afx_common.labels.are_you_sure+'?',
                html: afx_common.labels.you_wont_able_revert+'!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: afx_common.labels.yes_delete_it+'!'
            }).then(function () {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        ids: selectedIDs
                    },
                    success: function (data) {
                        if (data.success) {
                            $('#table_' + tablename).DataTable().ajax.reload();

                            // prompt success
                            swal(
                                afx_common.labels.deleted+'!',
                                afx_common.labels.records_deleted+'.',
                                'success'
                            );
                        } else {
                            console.log(data.errors);
                        }

                        Ladda.stopAll();
                    },
                    dataType: 'json'
                });


            }, function (dismiss) {
                Ladda.stopAll();
            });
        });
    });


})(jQuery);