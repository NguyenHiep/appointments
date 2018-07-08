var app = new Vue({
    el: '#app',
    data: {
        currency: afx_vars.currency,
        selected_service: 0,
        // data: [],
        isShowWelcome: true,
        isShowFormService: false,
        isLoading: false,
        modeFormCategory: 'add',
        modeFormService: 'add',
        categories: [],
        services: [],
        staffs: [{
                id: '1',
                full_name: 'Zharfan Mazli'
            },
            {
                id: '2',
                full_name: 'Suhaida Hasnan'
            }
        ],
        formCategory: {
            id: '',
            name: ''
        },
        formService: {
            staffs: []
        },
        money: {
            decimal: '.',
            thousands: ',',
            prefix: '',
            suffix: '',
            precision: 2,
            masked: false /* doesn't work with directive */
        }
    },
    created: function () {
        this.isLoading = true;

        axios.get(afx_vars.ajax_url + '?action=afx-services-list&_ajax_nonce=' + afx_vars.get_list_nonce)
            .then(function (response) {
                app.services = response.data.services;
                app.categories = response.data.categories;
                app.staffs = response.data.staffs;
                app.isLoading = false;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    methods: {
        displayDuration: function (duration) {
            var temp = parseInt(duration);

            switch (temp) {
                case (60 * 5):
                    return '5 min';
                    break;
                case (60 * 10):
                    return '10 min';
                    break;
                case (60 * 15):
                    return '15 min';
                    break;
                case (60 * 30):
                    return '30 min';
                    break;
                case (60 * 60):
                    return '1 hour';
                    break;
                case (60 * 60 * 2):
                    return '2 hours';
                    break;
                case (60 * 60 * 3):
                    return '3 hours';
                    break;
                case (60 * 60 * 4):
                    return '4 hours';
                    break;
                case (60 * 60 * 5):
                    return '5 hours';
                    break;
                case (60 * 60 * 24):
                    return 'Daily';
                    break;
                default:
                    return '';
                    break;
            }
        },
        showFormService: function (service) {
            this.isShowWelcome = false;
            this.isShowFormService = true;
            this.clearFormErrors();

            if (service !== undefined) {
                this.selected_service = service.id;

                this.modeFormService = 'edit';
                this.formService = JSON.parse(JSON.stringify(service));
                jQuery('#ServiceStaffs').val(this.formService.staffs).trigger('change');
                jQuery('.color-field').wpColorPicker('color', this.formService.color);

                if (this.formService.category_id == 0) {
                    this.formService.category_id = '';
                }

            } else {
                this.selected_service = 0;
                this.modeFormService = 'add';
                this.formService = {
                    'duration': 300,
                    'access': 'Public',
                    'color': '#FFFFFF',
                    'price': 0.0,
                    'category_id': '',
                    'staffs': []
                };

                jQuery('#ServiceStaffs').val(null).trigger('change');

                jQuery('.color-field').wpColorPicker('color', '#ffffff');
            }


        },
        hideFormService: function () {
            this.isShowFormService = false;
            this.isShowWelcome = true;
            this.selected_service = 0;
        },
        clearFormErrors: function () {
            jQuery('#FormCategory').find('.text-danger').remove();
            jQuery('#FormCategory').find('.form-group').removeClass('has-error');
            jQuery('#FormCategory').find('.form-control').removeClass('form-error');

            jQuery('#FormService').find('.text-danger').remove();
            jQuery('#FormService').find('.form-group').removeClass('has-error');
            jQuery('#FormService').find('.form-control').removeClass('form-error');
        },
        showFormCategory: function (category) {
            this.clearFormErrors();

            if (category !== undefined) {
                this.modeFormCategory = 'edit';
                this.formCategory.id = category.id;
                this.formCategory.name = category.name;
            } else {
                this.modeFormCategory = 'add';
                this.formCategory.id = '';
                this.formCategory.name = '';
            }

            jQuery('#ModalFormCategory').modal('show');
        },
        displayFormsErrors: function (response, formId) {
            jQuery.each(response.data.errors, function () {
                var element = jQuery('#' + formId + camelize(this.field));

                element.addClass('form-error');
                element.parent().addClass('has-error');

                var _insert = jQuery(document.createElement('span')).insertAfter(element);
                _insert.addClass('help-block text-danger').text(this.msg);

            });
        },
        saveService: function () {
            // Convert object to Form Data
            var input = new FormData();

            for (var key in this.formService) {
                if (key == 'staffs') {
                    input.append('data[Service][' + key + ']', jQuery("#ServiceStaffs").val());
                } else {
                    input.append('data[Service][' + key + ']', this.formService[key]);
                }
            }

            axios.post(afx_vars.ajax_url + '?action=afx-services-save&_ajax_nonce=' + afx_vars.save_service_nonce, input)
                .then(function (response) {
                    if (response.data.success) {

                        // update record in data object
                        var id = app.formService.id;

                        if (id !== undefined) {
                            var item = app.services.filter(function (item) {
                                return item.id == id;
                            }).pop();

                            for (var field in app.formService) {
                                if (field == 'staffs') {
                                    item[field] = jQuery("#ServiceStaffs").val();
                                } else {
                                    item[field] = app.formService[field];
                                }
                            }

                            item = app.formService;
                        } else {
                            app.formService.id = response.data.insert_id;
                            app.formService.staffs = jQuery("#ServiceStaffs").val();
                            var newService = JSON.parse(JSON.stringify(app.formService));
                            app.services.push(newService);
                        }

                        app.isShowWelcome = true;
                        app.isShowFormService = false;
                        app.selected_service = 0;

                        toastr.success(afx_vars.labels.service_saved);

                    } else {
                        app.clearFormErrors();
                        app.displayFormsErrors(response, 'Service');
                    }

                    Ladda.stopAll();
                });
        },
        saveCategory: function () {

            // Convert object to Form Data
            var input = new FormData();

            for (var key in this.formCategory) {
                input.append('data[Category][' + key + ']', this.formCategory[key]);
            }

            axios.post(afx_vars.ajax_url + '?action=afx-categories-save&_ajax_nonce=' + afx_vars.save_category_nonce, input)
                .then(function (response) {
                    if (response.data.success) {
                        // this.changePage(this.pagination.current_page);
                        jQuery('#ModalFormCategory').modal('hide');

                        // update record in data object
                        var id = app.formCategory.id;

                        if (id !== '') {
                            var item = app.categories.filter(function (item) {
                                return item.id == id;
                            }).pop();

                            item.name = app.formCategory.name;
                        } else {
                            app.categories.push({
                                id: response.data.insert_id,
                                name: app.formCategory.name
                            });

                            app.formService.category_id = response.data.insert_id;
                        }
                        toastr.success(afx_vars.labels.category_saved);

                    } else {
                        app.clearFormErrors();
                        app.displayFormsErrors(response, 'Category');
                    }

                    Ladda.stopAll();
                });
        },
        deleteCategory: function (category, index) {
            var id = category.id;

            swal({
                title: afx_vars.labels.are_you_sure+'?',
                html: afx_vars.labels.you_wont_able_revert+'!<br />'+afx_vars.labels.category+' <b>' + category.name + '</b> '+afx_vars.labels.and_all_services_deleted,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: afx_vars.labels.yes_delete_it+'!'
            }).then(function () {

                    jQuery.ajax({
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
                                afx_vars.labels.deleted+'!',
                                afx_vars.labels.the_category_deleted+'.',
                                'success'
                            );

                            app.categories.splice(index, 1);
                        },
                        dataType: 'json'
                    });

                },
                function (dismiss) {});
        },
        deleteService: function () {

            var id = this.formService.id;

            swal({
                title: afx_vars.labels.are_you_sure+'?',
                html: afx_vars.labels.you_wont_able_revert+'!<br />'+afx_vars.labels.service+' <b>' + this.formService.title + '</b>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: afx_vars.labels.yes_delete_it+'!'
            }).then(function () {
                jQuery.ajax({
                    type: 'POST',
                    url: afx_vars.ajax_url,
                    data: {
                        action: 'afx-services-delete',
                        _ajax_nonce: afx_vars.delete_service_nonce,
                        id: id
                    },
                    success: function () {
                        // prompt success
                        swal(
                            afx_vars.labels.deleted+'!',
                            afx_vars.labels.the_service_deleted+'.',
                            'success'
                        );

                        // app.services.splice(index, 1);
                        jQuery.each(app.services, function (i, el) {
                            if (this.id == id) {
                                app.services.splice(i, 1);
                            }
                        });

                        app.isShowWelcome = true;
                        app.isShowFormService = false;
                        app.selected_service = 0;

                        Ladda.stopAll();
                    },
                    dataType: 'json'
                });

            }, function (dismiss) {
                Ladda.stopAll();
            });
        }
    }
});

(function ($) {

    $(document).ready(function () {
        $('.color-field').wpColorPicker({
            change: function (event, ui) {
                var theColor = ui.color.toString();
                app.formService.color = theColor;
            },
            width: 200
        });

        Ladda.bind('.ladda-button');

        jQuery(".el-select2").select2({
            width: '100%'
        });
    });

})(jQuery);