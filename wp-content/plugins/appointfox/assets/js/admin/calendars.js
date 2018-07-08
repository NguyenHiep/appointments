var calendars = [];

var List = Vue.extend({
    template: '#list',
    data: function () {
        return {
            loading: false,
            calendars: [],
            searchKey: ''
        };
    },
    created: function () {
        var self = this;
        this.loading = true;
        axios.get(afx_vars.ajax_url + '?action=afx-calendars-list&_ajax_nonce=' + afx_vars.get_list_nonce)
            .then(function (response) {
                self.calendars = response.data.calendars;
                self.loading = false;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    computed: {
        filteredCalendars: function () {
            var self = this;
            return self.calendars.filter(function (calendar) {
                return calendar.name.toLowerCase().indexOf(self.searchKey.toLowerCase()) != -1;
            });
        }
    },
    filters: {
        truncate: function (value) {
            var length = 20;
            return value.length > length ? value.substring(0, length - 3) + '...' : value;
        }
    },
    methods: {
        deleteCalendar: function (calendar, index) {
            var id = calendar.id;
            var self = this;

            swal({
                title: 'Are you sure?',
                html: 'You won\'t be able to revert this!<br />Calendar <b>' + calendar.name,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {

                    jQuery.ajax({
                        type: 'POST',
                        url: afx_vars.ajax_url,
                        data: {
                            action: 'afx-calendars-delete',
                            _ajax_nonce: afx_vars.delete_calendar_nonce,
                            id: id
                        },
                        success: function () {
                            // prompt success
                            swal(
                                'Deleted!',
                                'The calendar has been deleted.',
                                'success'
                            );

                            self.calendars.splice(index, 1);
                        },
                        dataType: 'json'
                    });

                },
                function (dismiss) {});
        }
    }
});

var Add = Vue.extend({
    template: '#add',
    data: function () {
        return {
            loading: false,
            staffs: null,
            frmCalendar: {
                id: '',
                name: '',
                staffs: []
            }
        };
    },
    mounted: function () {
        Ladda.bind('.ladda-button');
    },
    methods: {
        clearFormErrors: function () {
            jQuery('#FormCalendarAdd').find('.text-danger').remove();
            jQuery('#FormCalendarAdd').find('.form-group').removeClass('has-error');
            jQuery('#FormCalendarAdd').find('.form-control').removeClass('form-error');
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
        saveCalendar: function () {

            var self = this;

            app.isLoading = true;

            axios.post(afx_vars.ajax_url + '?action=afx-calendars-save&_ajax_nonce=' + afx_vars.save_calendar_nonce, self.frmCalendar)
                .then(function (response) {
                    if (response.data.success) {
                        // self.formService.category_id = response.data.insert_id;
                        toastr.success('Calendar successfully saved');
                        // router.go(-1);
                        router.push({
                            name: 'settings',
                            params: {
                                id: response.data.insert_id
                            }
                        });

                    } else {
                        self.clearFormErrors();
                        self.displayFormsErrors(response, 'CalendarAdd');
                    }

                    Ladda.stopAll();
                });
        },
    }
});

var Edit = Vue.extend({
    template: '#edit',
    props: ['id'],
    data: function () {
        return {
            loading: false,
            frmCalendar: null
        };
    },
    mounted: function () {
        Ladda.bind('.ladda-button');
    },
    created: function () {
        var self = this;
        self.loading = true;
        axios.get(afx_vars.ajax_url + '?action=afx-calendars-get&id=' + this.id + '&_ajax_nonce=' + afx_vars.get_calendar_nonce)
            .then(function (response) {
                self.frmCalendar = response.data.calendar;
                // self.frmCalendar.push({
                //     staffs: response.data.staffs
                // });
                self.loading = false;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    methods: {
        clearFormErrors: function () {
            jQuery('#FormCalendarEdit').find('.text-danger').remove();
            jQuery('#FormCalendarEdit').find('.form-group').removeClass('has-error');
            jQuery('#FormCalendarEdit').find('.form-control').removeClass('form-error');
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
        saveCalendar: function () {

            var self = this;

            axios.post(afx_vars.ajax_url + '?action=afx-calendars-save&_ajax_nonce=' + afx_vars.save_calendar_nonce, self.frmCalendar)
                .then(function (response) {
                    if (response.data.success) {
                        // self.formService.category_id = response.data.insert_id;
                        toastr.success('Calendar successfully saved');

                        router.go(-1);

                    } else {
                        this.clearFormErrors();
                        this.displayFormsErrors(response, 'CalendarEdit');
                    }

                    Ladda.stopAll();
                });
        },
    }
});

var Settings = Vue.extend({
    template: '#settings',
    props: ['id'],
    data: function () {
        return {
            loading: false,
            isShow_hour_sunday: true,
            isShow_hour_monday: true,
            isShow_hour_tuesday: true,
            isShow_hour_wednesday: true,
            isShow_hour_thursday: true,
            isShow_hour_friday: true,
            isShow_hour_saturday: true,
            frmCalendar: {
                id: '',
                name: '',
                staffs: [],
                hour_sunday: '',
                hour_sunday_text: '',
                hour_monday: '',
                hour_monday_text: '',
                hour_tuesday: '',
                hour_tuesday_text: '',
                hour_wednesday: '',
                hour_wednesday_text: '',
                hour_thursday: '',
                hour_thursday_text: '',
                hour_friday: '',
                hour_friday_text: '',
                hour_saturday: '',
                hour_saturday_text: ''
            },
            formPopupHour: {
                day: '',
                time: '',
            },
            hourPlaceholder: 'Closed - Enter availability like: 9.00am-12.30pm, 1.30pm-6pm',
        };
    },
    mounted: function () {
        Ladda.bind('.ladda-button');
    },
    created: function () {
        var self = this;
        self.loading = true;
        axios.get(afx_vars.ajax_url + '?action=afx-calendars-get&id=' + this.id + '&_ajax_nonce=' + afx_vars.get_calendar_nonce)
            .then(function (response) {
                self.frmCalendar = response.data.calendar;

                self.frmCalendar['hour_sunday_text'] = self.tagHour(self.frmCalendar.hour_sunday);
                self.frmCalendar['hour_monday_text'] = self.tagHour(self.frmCalendar.hour_monday);
                self.frmCalendar['hour_tuesday_text'] = self.tagHour(self.frmCalendar.hour_tuesday);
                self.frmCalendar['hour_wednesday_text'] = self.tagHour(self.frmCalendar.hour_wednesday);
                self.frmCalendar['hour_thursday_text'] = self.tagHour(self.frmCalendar.hour_thursday);
                self.frmCalendar['hour_friday_text'] = self.tagHour(self.frmCalendar.hour_friday);
                self.frmCalendar['hour_saturday_text'] = self.tagHour(self.frmCalendar.hour_saturday);

                // load monthly calendar
                loadMonthlyCalendar(self.id);

                self.loading = false;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    methods: {
        blurHour: function (e) {
            if (this.frmCalendar[e] == '') {
                this.frmCalendar[e] = 'Closed';
            } else {
                // cleaning...
                this.frmCalendar[e] = this.frmCalendar[e].toLowerCase().replace('closed', '');
                this.frmCalendar[e] = this.frmCalendar[e].toLowerCase().replace(';', '');

                if (this.frmCalendar[e][this.frmCalendar[e].length - 1] == ',') {
                    this.frmCalendar[e] = this.frmCalendar[e].substring(0, this.frmCalendar[e].length - 1);
                }
            }
            this['isShow_' + e] = true;

            var hour = this.tagHour(this.frmCalendar[e]);
            var hour2 = this.tagHour2(this.frmCalendar[e]);
            this.frmCalendar[e + '_text'] = hour;
            this.frmCalendar[e] = hour2;
        },
        tagHour: function (value) {
            if (value == 'Closed') {
                return '<span class="label label-default">Closed</span>';
            }

            var hours = value.split(',');
            var tags = '';
            var hour = '';
            for (var i = 0, count = hours.length; i < count; i++) {
                var time = hours[i].split('-');
                var timeTemp = '';
                if (time.length == 1) {
                    var momentTime = moment(time[0], ['h:mma', 'ha']);
                    timeTemp = momentTime.format('h:mma');
                } else {
                    var momentTime1 = moment(time[0], ['h:mma', 'ha']);
                    var momentTime2 = moment(time[1], ['h:mma', 'ha']);
                    timeTemp = momentTime1.format('h:mma') + '-' + momentTime2.format('h:mma');
                }
                // tags += '<span class="label label-default">' + hours[i] + '</span> ';
                tags += '<span class="label label-default">' + timeTemp + '</span> ';
            }
            return tags;
        },
        tagHour2: function (value) {
            if (value == 'Closed') {
                return 'Closed';
            }

            var hours = value.split(',');
            var tags = '';
            var hour = '';
            for (var i = 0, count = hours.length; i < count; i++) {
                var time = hours[i].split('-');
                var timeTemp = '';
                if (time.length == 1) {
                    var momentTime = moment(time[0], ['h:mma', 'ha']);
                    timeTemp = momentTime.format('h:mma');
                } else {
                    var momentTime1 = moment(time[0], ['h:mma', 'ha']);
                    var momentTime2 = moment(time[1], ['h:mma', 'ha']);
                    timeTemp = momentTime1.format('h:mma') + '-' + momentTime2.format('h:mma');
                }
                // tags += '<span class="label label-default">' + hours[i] + '</span> ';
                tags += timeTemp + ',';
            }

            if (tags[tags.length - 1] == ',') {
                tags = tags.substring(0, tags.length - 1);
            }

            return tags;
        },
        focusHour: function (e) {
            if (this.frmCalendar[e] == 'Closed') {
                this.frmCalendar[e] = '';
            }
            this['isShow_' + e] = false;
        },
        showInput: function (e) {
            this['isShow_' + e] = false;
            var self = this;
            Vue.nextTick(function () {
                self.$refs[e].focus();
            });
        },
        checkHour: function (e) {
            var input = this.frmCalendar[e].toLowerCase();

            if (input == 'closed') {
                return false;
            }

            if (input == '') {
                return false;
            }

            var pattern = new RegExp('/closed|am|pm|[0-9]|[-]|[:]|[,]/gi');
            var result = pattern.test(input);

            if (!result) {
                return true;
            }
            return false;
        },
        clearFormErrors: function () {
            jQuery('#FormCalendarEdit').find('.text-danger').remove();
            jQuery('#FormCalendarEdit').find('.form-group').removeClass('has-error');
            jQuery('#FormCalendarEdit').find('.form-control').removeClass('form-error');
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
        saveCalendar: function () {

            var self = this;

            axios.post(afx_vars.ajax_url + '?action=afx-calendars-save&_ajax_nonce=' + afx_vars.save_calendar_nonce, self.frmCalendar)
                .then(function (response) {
                    if (response.data.success) {
                        // self.formService.category_id = response.data.insert_id;
                        toastr.success('Calendar successfully saved');

                        // router.go(-1);
                        // router.push({
                        //     name: 'index'
                        // });

                        // load monthly calendar
                        loadMonthlyCalendar(self.id);

                    } else {
                        this.clearFormErrors();
                        this.displayFormsErrors(response, 'CalendarEdit');
                    }

                    Ladda.stopAll();
                });
        },
    }
});

var router = new VueRouter({
    routes: [{
            path: '/',
            component: List,
            name: 'index'
        },
        {
            path: '/add',
            component: Add,
            name: 'add'
        },
        {
            path: '/edit/:id',
            component: Edit,
            name: 'edit',
            props: true
        },
        {
            path: '/settings/:id',
            component: Settings,
            name: 'settings',
            props: true
        }
    ]
});

var app = new Vue({
    el: '#app',
    router: router
});

// (function ($) {

//     $(document).ready(function () {
//         Ladda.bind('.ladda-button');
//     });

// })(jQuery);