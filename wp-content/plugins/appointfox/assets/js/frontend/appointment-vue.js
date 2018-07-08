var $ = jQuery.noConflict();

Vue.component("date-picker", VueBootstrapDatetimePicker.default);

var appAppointFox = new Vue({
    el: "#AppAppointFox",
    data: {
        background_color: afx_vars.background_color,
        font_color: afx_vars.font_color,
        font_size: afx_vars.font_size,
        currency: afx_vars.currency,
        payment_method: afx_vars.payment_method,
        services: [],
        categories: [],
        selected_service: 0,
        isLoading: false,
        isLoadingCheckPayment: false,
        isShowFormServices: true,
        isShowFormDatePicker: false,
        isLoadingDatePicker: false,
        defaultStartTimeLabel: afx_vars.labels.choose_time+"...",
        times: [],
        isShowDatePicker: false,
        formAppointment: {
            service: [],
            start_date: "",
            start_time: "",
            start_time_display: "",
            start_datetime: "",
            end_datetime: "",
            customer_id: "",
            customer_name: "",
            customer_phone: "",
            customer_email: "",
            insert_id: null,
            is_paid: false
        },
        dateTimePickerConfig: {
            inline: true,
            // format: 'MMMM D, YYYY'
            // format: 'YYYY-MM-DD HH:mm'
            format: "YYYY-MM-DD",
            disabledDates: false,
            daysOfWeekDisabled: false,
            enabledDates: false,
            useCurrent: false,
            locale: afx_vars.locale
        }
    },
    mounted: function() {
        Ladda.bind(".ladda-button");
    },
    created: function() {
        var self = this;
        self.isLoading = true;
        axios
            .get(
                afx_vars.ajax_url +
                    "?action=afx-ajax-getformdata&_ajax_nonce=" +
                    afx_vars.get_formdata_nonce
            )
            .then(function(response) {
                self.services = response.data.services;
                self.categories = response.data.categories;

                self.isLoading = false;
            })
            .catch(function(error) {
                console.log(error);
            });
    },
    methods: {
        displayDuration: function(duration) {
            var temp = parseInt(duration);

            switch (temp) {
                case 60 * 5:
                    return "5 min";
                    break;
                case 60 * 10:
                    return "10 min";
                    break;
                case 60 * 15:
                    return "15 min";
                    break;
                case 60 * 30:
                    return "30 min";
                    break;
                case 60 * 60:
                    return "1 hour";
                    break;
                case 60 * 60 * 2:
                    return "2 hours";
                    break;
                case 60 * 60 * 3:
                    return "3 hours";
                    break;
                case 60 * 60 * 4:
                    return "4 hours";
                    break;
                case 60 * 60 * 5:
                    return "5 hours";
                    break;
                case 60 * 60 * 24:
                    return "Daily";
                    break;
                default:
                    return "";
                    break;
            }
        },
        formatDate: function(value) {
            var m = moment(value);
            return m.format("MMMM D, YYYY");
        },
        showDatePicker: function(service) {
            // Scroll to Service
            $("html, body").animate(
                { scrollTop: $("#FormDateTime").offset().top },
                2000
            );

            var self = this;
            self.formAppointment.service = service;
            self.formAppointment.start_date = null;
            self.selected_service = service.id;

            self.isShowFormServices = false;
            self.isShowFormDatePicker = true;

            self.isLoadingDatePicker = true;
            self.isShowDatePicker = false;
            self.times = [];

            axios
                .get(
                    afx_vars.ajax_url +
                        "?action=afx-ajax-disableddateslist&service_id=" +
                        self.formAppointment.service.id +
                        "&_ajax_nonce=" +
                        afx_vars.get_formdata_nonce
                )
                .then(function(response) {
                    self.disabledDates = response.data.disabled_dates;
                    self.daysOfWeekDisabled = response.data.daysOfWeekDisabled;
                    self.enabledDates = response.data.enabledDates;

                    if (self.daysOfWeekDisabled.length == 0) {
                        self.daysOfWeekDisabled = false;
                    }

                    if (self.disabledDates.length == 0) {
                        self.disabledDates = false;
                    }

                    if (afx_vars.week_start_on == "Monday") {
                        moment.updateLocale(self.dateTimePickerConfig.locale, {
                            week: {
                                dow: 1
                            }
                        });
                        self.dateTimePickerConfig = {
                            inline: true,
                            format: "YYYY-MM-DD",
                            disabledDates: self.disabledDates,
                            daysOfWeekDisabled: self.daysOfWeekDisabled,
                            enabledDates: self.enabledDates,
                            minDate: moment().startOf("day"),
                            locale: self.dateTimePickerConfig.locale
                        };
                    } else {
                        moment.updateLocale(self.dateTimePickerConfig.locale, {
                            week: {
                                dow: 0
                            }
                        });
                        self.dateTimePickerConfig = {
                            inline: true,
                            format: "YYYY-MM-DD",
                            disabledDates: self.disabledDates,
                            daysOfWeekDisabled: self.daysOfWeekDisabled,
                            enabledDates: self.enabledDates,
                            minDate: moment().startOf("day"),
                            locale: self.dateTimePickerConfig.locale
                        };
                    }

                    self.isLoadingDatePicker = false;
                    self.isShowDatePicker = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        showServices: function() {
            var self = this;
            self.isShowFormDatePicker = false;
            self.isShowFormServices = true;
        },
        showTimes: function() {
            var self = this;

            if (self.formAppointment.service.id == "") {
                return false;
            }

            if (self.formAppointment.start_date == null) {
                return false;
            }

            self.formAppointment.start_time = "";
            self.defaultStartTimeLabel = afx_vars.labels.loading+"...";

            axios
                .get(
                    afx_vars.ajax_url +
                        "?action=afx-ajax-gettimes&service_id=" +
                        self.formAppointment.service.id +
                        "&start_date=" +
                        self.formAppointment.start_date +
                        "&_ajax_nonce=" +
                        afx_vars.get_formdata_nonce
                )
                .then(function(response) {
                    self.times = response.data.times;

                    // Convert time format if 24 hour set
                    if (afx_vars.time_format == "24 hour") {
                        var newTimes = [];
                        jQuery.each(self.times, function(i, value) {
                            var m = moment(
                                "2018-1-1 " + value,
                                "YYYY-MM-DD h:mma"
                            );
                            newTimes.push(m.format("HH:mm"));
                        });

                        self.times = newTimes;
                    }

                    var count = Object.keys(self.times).length;

                    if (count == 0) {
                        self.defaultStartTimeLabel =
                            afx_vars.labels.not_available;
                    } else {
                        self.defaultStartTimeLabel = afx_vars.labels.choose_time+"...";
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        showFormCustomer: function() {
            var self = this;

            if (self.formAppointment.start_date == null) {
                swal("Oops!", afx_vars.labels.choose_your_date+"!", "error");
                return false;
            }

            if (self.formAppointment.start_time == "") {
                swal("Oops!", afx_vars.labels.choose_your_time+"!", "error");
                return false;
            }

            // show next tab
            var $active = $(".wizard .nav-tabs li.active");
            $active.removeClass("active");
            $active.addClass("disabled");
            $active.next().removeClass("disabled");
            nextTab($active);
        },
        saveAppointment: function() {
            var self = this;

            if (self.formAppointment.customer_name == "") {
                swal("Oops!", afx_vars.labels.enter_your_name+"!", "error").then(
                    function() {
                        Ladda.stopAll();
                    },
                    function(dismiss) {}
                );
                return false;
            }

            if (self.formAppointment.customer_email == "") {
                swal("Oops!", afx_vars.labels.enter_your_email+"!", "error").then(
                    function() {
                        Ladda.stopAll();
                    },
                    function(dismiss) {}
                );
                return false;
            }

            self.formAppointment.start_time_display =
                self.formAppointment.start_time;

            // Convert time format if 24 hour set
            if (afx_vars.time_format == "24 hour") {
                var oldTime = self.formAppointment.start_time;
                var m = moment("2018-1-1 " + oldTime, "YYYY-MM-DD HH:mm");
                self.formAppointment.start_time = m.format("h:mma");
            }

            // save appointment to db
            axios
                .post(
                    afx_vars.ajax_url +
                        "?action=afx-ajax-saveappointment&_ajax_nonce=" +
                        afx_vars.save_appointment_nonce,
                    {
                        appointment: self.formAppointment
                    }
                )
                .then(function(response) {
                    Ladda.stopAll();

                    if (response.data.success) {
                        // remove select start_time in the list
                        jQuery.each(self.times, function(i, value) {
                            if (value == self.formAppointment.start_time) {
                                delete self.times[i];
                                $("#FormAppointmentStartTime option[value='" + value + "']").remove();
                            }
                        });

                        // get insert_id
                        self.formAppointment.insert_id =
                            response.data.insert_id;
                        price = self.formAppointment.service.price;
                        appointmentId = response.data.insert_id;

                        // disable your info tab
                        var $active = $(".wizard .nav-tabs li.active");
                        $active.prev().addClass("disabled");
                        $active.addClass("disabled");
                        $active.removeClass("active");
                        $active.next().removeClass("disabled");
                        nextTab($active);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    Ladda.stopAll();
                });
        },
        resetForm: function() {
            var self = this;
            var $active = $(".wizard .nav-tabs li.active");
            $active.removeClass("active");
            $active.addClass("disabled");
            $active
                .prev()
                .prev()
                .removeClass("disabled");
            $active
                .prev()
                .prev()
                .find('a[data-toggle="tab"]')
                .click();

            self.isShowFormServices = true;
            self.isShowFormDatePicker = false;
            self.selected_service = 0;
            self.formAppointment.is_paid = false;
        },
        backToAppointment: function() {
            var $active = $(".wizard .nav-tabs li.active");
            $active.removeClass("active");
            $active.addClass("disabled");
            $active.prev().removeClass("disabled");
            $active
                .prev()
                .find('a[data-toggle="tab"]')
                .click();
        },
        hideLoading: function() {
            // this.isLoadingDatePicker = false;
            // this.isShowDatePicker = true;
        }
    }
});
