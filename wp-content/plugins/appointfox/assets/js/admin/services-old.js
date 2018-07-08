(function ($) {

    $(document).ready(function () {

        // reload services list
        updateServicesList();

        // reload Category Drop Down List
        updateCategoriesDDL();

        $('#btnAddService').click(function (e) {
            e.preventDefault();
            $('#PanelWelcome').hide();
            $('#PanelLoading').show();

            $('#ServiceId').val('');
            $('#ServiceTitle').val('');
            $('#ServiceAccess').val('Public');
            $('#ServiceDuration').val(300);
            $('#ServicePrice').val(0.0);
            $('#ServiceColor').val('');
            $('#ServiceCategoryId').val('');
            $('#ServiceInfo').val('');

            $('#btnDeleteService').hide();
            $('#PanelLoading').hide();
            $('#PanelFormService').show();
        });

        $('#btnAddCategory, .btn-category-add').click(function (e) {
            e.preventDefault();
            $('#ModalFormCategoryTitle').html('Create a new Category...');
            $('#CategoryId').val('');
            $('#CategoryName').val('');
            $('#ModalFormCategory').modal('show');
        });

        $('#btnCancelSaveService').click(function (e) {
            e.preventDefault();
            $('#PanelFormService').hide();
            $('#PanelWelcome').show();
        });

        $('.color-field').wpColorPicker({
            width: 200
        });

        $('#FormCategory').ajaxForm({
            dataType: 'json',
            beforeSubmit: function (formData, jqForm, options) {
                jqForm.find('.text-danger').remove();
                jqForm.find('.form-group').removeClass('has-error');
                jqForm.find('.form-control').removeClass('form-error');
            },
            success: function (data) {
                if (data.success) {
                    $('#ModalFormCategory').modal('hide');
                    $('#CategoryName').val('');

                    // reload services list
                    updateServicesList();

                    // prompt success
                    // swal(
                    //     'Success!',
                    //     'Category successfully saved.',
                    //     'success'
                    // );
                    // $.notify('Category successfully saved', 'success');
                    // alert('test');
                    // $('.bookme-wrap').notify(
                    //     'Category successfully saved', {
                    //         position: 'left middle',
                    //         className: 'success',
                    //         arrowShow: false
                    //     }
                    // );

                    toastr.success('Category successfully saved');



                    // reload categories drop down list
                    updateCategoriesDDL(data.insert_id);
                } else {

                    $.each(data.errors, function () {
                        var element = $('#Category' + camelize(this.field));
                        element.addClass('form-error');
                        element.parent().addClass('has-error');

                        var _insert = $(document.createElement('span')).insertAfter(element);
                        _insert.addClass('help-block text-danger').text(this.msg);

                    });
                }
                Ladda.stopAll();
            }
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
                        'Success!',
                        'Service successfully saved.',
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
    });
})(jQuery);