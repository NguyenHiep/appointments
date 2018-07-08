Vue.component("date-picker", VueBootstrapDatetimePicker.default);

var app = new Vue({
  el: "#app",
  data: {
    currency: afx_vars.currency,
    payment_method: afx_vars.payment_method,
    disabledDates: [],
    // daysOfWeekDisabled: [],
    // enabledDates: [],
    isShowDatePicker: true,
    isLoadingDatePicker: false,
    isShowEditAppointmentNote: false,
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
    },
    modeFormAppointment: "add",
    categories: [],
    services: [],
    staffs: [],
    // staffs_list: [],
    defaultStaffLabel: afx_vars.labels.choose_service_first+"...",
    defaultStartTimeLabel: afx_vars.labels.choose_time+"...",
    staffs_services: [],
    customers: [],
    times: [],
    eventEdited: null,
    formAppointment: {
      id: "",
      service_id: "",
      staff_id: "",
      start_date: "",
      start_time: "",
      customer_id: "",
      note: "",
      price: "0.00",
      is_paid: false
    },
    viewAppointment: {
      id: "",
      service_title: "",
      staff_name: "",
      start_date: "",
      start_time: "",
      end_time: "",
      customer_name: "",
      price: "",
      is_paid: false,
      note: "",
      new_note: ""
    },
    formCustomer: {
      full_name: "",
      email: "",
      phone: "",
      info: ""
    },
    money: {
      decimal: ".",
      thousands: ",",
      prefix: afx_vars.currency,
      suffix: "",
      precision: 2,
      masked: false /* doesn't work with directive */
    }
  },
  created: function() {
    axios
      .get(
        afx_vars.ajax_url +
          "?action=afx-calendar-selectslist&_ajax_nonce=" +
          afx_vars.get_list_nonce
      )
      .then(function(response) {
        app.categories = response.data.categories;
        app.services = response.data.services;
        // app.staffs = response.data.staffs;
        // app.staffs_services = response.data.staffs_services;
        app.customers = response.data.customers;
      })
      .catch(function(error) {
        console.log(error);
      });
  },
  methods: {
    refreshDatePicker: function() {
      this.isLoadingDatePicker = true;
      this.isShowDatePicker = false;
      this.times = [];

      if (app.formAppointment.service_id == "") {
        return false;
      }

      if (app.formAppointment.staff_id == "") {
        return false;
      }

      axios
        .get(
          afx_vars.ajax_url +
            "?action=afx-calendar-disableddateslist&service_id=" +
            app.formAppointment.service_id +
            "&staff_id=" +
            app.formAppointment.staff_id +
            "&_ajax_nonce=" +
            afx_vars.get_list_nonce
        )
        .then(function(response) {
          app.disabledDates = response.data.disabled_dates;
          app.daysOfWeekDisabled = response.data.daysOfWeekDisabled;
          app.enabledDates = response.data.enabledDates;

          // app.disabledDates = ['2017/12/26', '2017/12/27'];

          if (app.daysOfWeekDisabled.length == 0) {
            app.daysOfWeekDisabled = false;
          }

          if (app.disabledDates.length == 0) {
            app.disabledDates = false;
          }

          app.dateTimePickerConfig = {
            inline: true,
            format: "YYYY-MM-DD",
            disabledDates: app.disabledDates,
            daysOfWeekDisabled: app.daysOfWeekDisabled,
            enabledDates: app.enabledDates,
            locale: afx_vars.locale
          };

          app.isLoadingDatePicker = false;
          app.isShowDatePicker = true;

          if (app.eventEdited == null) {
            app.formAppointment.start_date = null;
          }
        })
        .catch(function(error) {
          console.log(error);
        });

      // jQuery('#datetimepicker').data('DateTimePicker').disabledDates([{ moment("2017-11-29")}]);

      // jQuery('#datetimepicker').datetimepicker({
      //     inline: true,
      //     format: 'MMMM D, YYYY',
      //     minDate: new Date(),
      //     disabledDates: ['2017/11/28', '2017/11/29']
      // }).on('dp.show', function() {
      //     alert('datetimepicker shown');
      //     // this.isShowDatePicker = true;
      //     this.isLoadingDatePicker = false;
      // });

      // jQuery('#datetimepicker').data('DateTimePicker').hide();
      // jQuery('#datetimepicker').data('DateTimePicker').show();

      // setTimeout(function(){ alert('Hello'); }, 3000);
    },
    showStaffs: function() {
      // console.log(this.formAppointment.service_id);

      // if (this.formAppointment.service_id == '') {
      //     app.defaultStaffLabel = 'Choose service first...';
      // } else {
      //     app.defaultStaffLabel = 'Loading...';
      // }

      app.defaultStaffLabel = afx_vars.labels.loading+"...";
      this.formAppointment.staff_id = "";
      this.formAppointment.start_date = "";
      this.formAppointment.start_time = "";

      // set default price
      var self = this;
      if (self.modeFormAppointment == "add") {
        for (var i = 0; i < self.services.length; i++) {
          if (self.services[i].id == self.formAppointment.service_id) {
            self.formAppointment.price = self.services[i].price;
          }
        }
      }

      axios
        .get(
          afx_vars.ajax_url +
            "?action=afx-calendar-staffslist&service_id=" +
            app.formAppointment.service_id +
            "&_ajax_nonce=" +
            afx_vars.get_list_nonce
        )
        .then(function(response) {
          app.staffs = response.data.staffs;

          if (app.staffs.length > 0) {
            app.defaultStaffLabel = afx_vars.labels.choose_staff+"...";
          } else {
            app.defaultStaffLabel = "N/A";
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    // isStaffServices: function (staff_id, service_id) {
    //     console.log('staff_id: ' + staff_id);
    //     console.log('service_id: ' + service_id);

    //     jQuery.each(this.staffs_services, function (value, key) {
    //         if (key === staff_id) {
    //             console.log('services: ' + value);
    //         }
    //     });

    //     // for (var key in this.staffs_services) {
    //     //     if (key === staff_id) {
    //     //         for (var key2 in this.staffs_services[key]) {
    //     //             if (key2 == service_id) {
    //     //                 return true;
    //     //             }
    //     //         }
    //     //     }
    //     // }

    //     return false;
    // },
    inArray: function(needle, haystack) {
      var length = haystack.length;
      for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
      }
      return false;
    },
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
    showAddAppointmentForm: function() {
      this.eventEdited = null;
      this.showAppointmentForm();
    },
    showAppointmentView: function(appointment) {
      // console.log(appointment);

      var self = this;

      this.eventEdited = appointment;

      var duration = secondsToString(appointment.duration);

      this.viewAppointment.service_title =
        appointment.service_title +
        " - " +
        self.currency +
        appointment.price +
        " @ " +
        duration;
      this.viewAppointment.staff_name = appointment.staff_name;
      this.viewAppointment.start_date = appointment.start.format(
        "MMMM Do YYYY"
      );

      this.viewAppointment.start_time =
        appointment.start.format("h:mm a") +
        " - " +
        appointment.end.format("h:mm a");

      if (afx_vars.time_format == "24 hour") {
        this.viewAppointment.start_time =
          appointment.start.format("HH:mm") +
          " - " +
          appointment.end.format("HH:mm");
      }

      this.viewAppointment.customer_name = appointment.customer_name;
      this.viewAppointment.customer_phone = appointment.customer_phone;
      this.viewAppointment.customer_email = appointment.customer_email;
      this.viewAppointment.note = appointment.note;
      this.viewAppointment.price = appointment.price;
      this.viewAppointment.is_paid = parseInt(appointment.is_paid);
      this.viewAppointment.id = appointment.id;

      jQuery("#ModalViewAppointment").modal("show");
    },
    showAppointmentForm: function() {
      var self = this;
      appointment = this.eventEdited;

      this.formAppointment.id = "";
      this.formAppointment.service_id = "";
      this.formAppointment.staff_id = "";
      this.formAppointment.start_date = "";
      this.formAppointment.start_time = "";
      this.formAppointment.customer_id = "";
      this.formAppointment.note = "";
      this.formAppointment.price = "0.00";
      this.formAppointment.is_paid = 0;

      this.staffs = [];
      this.times = [];

      if (appointment !== null) {
        this.eventEdited = appointment;

        this.modeFormAppointment = "edit";
        this.formAppointment.id = appointment.id;
        this.formAppointment.service_id = appointment.service_id;
        this.formAppointment.customer_id = appointment.customer_id;
        this.formAppointment.note = appointment.note;
        this.formAppointment.price = appointment.price;
        this.formAppointment.is_paid = parseInt(appointment.is_paid);

        self.showStaffs();

        Vue.nextTick(function() {
          self.formAppointment.staff_id = appointment.staff_id;
        });

        Vue.nextTick(function() {
          self.refreshDatePicker();
        });

        Vue.nextTick(function() {
          self.formAppointment.start_date = appointment.start.format(
            "YYYY-MM-DD"
          );
        });

        // Vue.nextTick(function () {
        // self.showTimes();
        // });

        Vue.nextTick(function() {
          // if (afx_vars.time_format == '24 hour') {
          // self.formAppointment.start_time = appointment.start.format('h:mma');
          // } else {
          // self.formAppointment.start_time = appointment.start.format('h:mma');
          // }
        });
      } else {
        this.modeFormAppointment = "add";
        this.defaultStartTimeLabel = afx_vars.labels.choose_time+"...";
        this.eventEdited = null;
      }

      jQuery("#ModalViewAppointment").modal("hide");
      jQuery("#ModalFormAppointment").modal("show");
    },
    showTimes: function() {
      var self = this;

      if (app.formAppointment.service_id == "") {
        return false;
      }

      if (app.formAppointment.staff_id == "") {
        return false;
      }

      if (app.formAppointment.start_date == null) {
        return false;
      }

      app.formAppointment.start_time = "";
      app.defaultStartTimeLabel = afx_vars.labels.loading+"...";

      axios
        .get(
          afx_vars.ajax_url +
            "?action=afx-calendar-gettimes&appointment_id=" +
            app.formAppointment.id +
            "&service_id=" +
            app.formAppointment.service_id +
            "&staff_id=" +
            app.formAppointment.staff_id +
            "&start_date=" +
            app.formAppointment.start_date +
            "&_ajax_nonce=" +
            afx_vars.get_list_nonce
        )
        .then(function(response) {
          // app.disabledDates = response.data.disabled_dates;

          self.times = response.data.times;
          // self.times = ['10:00am', '10:30am', '11:00am'];

          // Convert time format if 24 hour set
          if (afx_vars.time_format == "24 hour") {
            var newTimes = [];
            jQuery.each(self.times, function(i, value) {
              var m = moment("2018-1-1 " + value, "YYYY-MM-DD h:mma");
              newTimes.push(m.format("HH:mm"));
            });

            self.times = newTimes;
          }

          if (self.eventEdited) {
            self.formAppointment.start_time = self.eventEdited.start.format(
              "h:mma"
            );

            if (afx_vars.time_format == "24 hour") {
              self.formAppointment.start_time = self.eventEdited.start.format(
                "HH:mm"
              );
            }

            self.defaultStartTimeLabel = afx_vars.labels.choose_time+"...";
          } else {
            self.defaultStartTimeLabel = afx_vars.labels.choose_time+"...";
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    hideLoading: function() {
      this.isLoadingDatePicker = false;
      this.isShowDatePicker = true;
    },
    saveAppointment: function() {
      var self = this;

      // Convert time format if 24 hour set
      if (afx_vars.time_format == "24 hour") {
        var oldTime = self.formAppointment.start_time;
        var m = moment("2018-1-1 " + oldTime, "YYYY-MM-DD HH:mm");
        self.formAppointment.start_time = m.format("h:mma");
      }

      axios
        .post(
          afx_vars.ajax_url +
            "?action=afx-appointments-save&_ajax_nonce=" +
            afx_vars.save_appointment_nonce,
          self.formAppointment
        )
        .then(function(response) {
          if (response.data.success) {
            var appointment = response.data.appointment;
            // self.formService.category_id = response.data.insert_id;
            toastr.success(afx_vars.labels.appointment_saved);

            // get customer name
            var customer_name = "";

            for (var i = 0, len = self.customers.length; i < len; i++) {
              if (self.customers[i].id == self.formAppointment.customer_id) {
                customer_name = self.customers[i].full_name;
                customer_phone = self.customers[i].phone;
                customer_email = self.customers[i].email;
              }
            }

            // console.log(customer_name);

            // get service name
            var service_title = "";
            var service_color = "";
            var service_duration = "";

            for (var i = 0, len = self.services.length; i < len; i++) {
              if (self.services[i].id == self.formAppointment.service_id) {
                service_title = self.services[i].title;
                service_color = self.services[i].color;
                service_duration = self.services[i].duration;
                service_price = self.services[i].price;
              }
            }

            var event = [
              {
                id: appointment.id,
                service_id: appointment.service_id,
                service_title: service_title,
                price: appointment.price,
                is_paid: appointment.is_paid,
                duration: service_duration,
                staff_id: appointment.staff_id,
                staff_name: appointment.staff_name,
                customer_id: appointment.customer_id,
                customer_name: customer_name,
                customer_phone: customer_phone,
                customer_email: customer_email,
                note: appointment.note,
                title: customer_name + ": " + service_title,
                start: appointment.start_datetime,
                end: appointment.end_datetime,
                resource: self.formAppointment.staff_id,
                color: service_color,
                textColor: "black"
              }
            ];

            // console.log(event);

            // jQuery('#fullcalendar').fullCalendar('removeEvents', [eventEdited]);
            if (response.data.mode == "update") {
              // self.eventEdited.service_id = appointment.service_id;
              // self.eventEdited.staff_id = appointment.staff_id;
              // self.eventEdited.customer_id = appointment.customer_id;
              // self.eventEdited.note = appointment.note;
              // self.eventEdited.title = customer_name + ': ' + service_title;
              // self.eventEdited.start = appointment.start_datetime;
              // self.eventEdited.end = appointment.end_datetime;
              // self.eventEdited.resource = appointment.staff_id;
              // self.eventEdited.color = service_color;

              // console.log(self.eventEdited);

              jQuery("#fullcalendar").fullCalendar(
                "removeEvents",
                self.eventEdited.id
              );
            }

            jQuery("#fullcalendar").fullCalendar("addEventSource", event);
            // jQuery('#fullcalendar').fullCalendar('refetchEvents');

            jQuery("#ModalFormAppointment").modal("hide");
          } else {
            // self.clearFormErrors();
            self.displayFormsErrors(response, "FormAppointment");
          }

          Ladda.stopAll();
        });
    },
    deleteAppointment: function() {
      var self = this;

      swal({
        title: afx_vars.labels.are_you_sure+"?",
        html:
          afx_vars.labels.you_wont_able_revert+"!<br />"+afx_vars.labels.customer+": <b>" +
          self.eventEdited.customer_name,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: afx_vars.labels.yes_cancel_it+"!",
        cancelButtonText: afx_vars.labels.no_keep_it+"!"
      }).then(
        function() {
          axios
            .post(
              afx_vars.ajax_url +
                "?action=afx-appointments-delete-process2&_ajax_nonce=" +
                afx_vars.delete_appointment_nonce,
              {
                id: self.eventEdited.id
              }
            )
            .then(function(response) {
              if (response.data.success) {
                // prompt success
                swal(
                  afx_vars.labels.cancelled+"!",
                  afx_vars.labels.appointment_cancelled+".",
                  "success"
                );

                jQuery("#fullcalendar").fullCalendar(
                  "removeEvents",
                  self.eventEdited.id
                );

                Ladda.stopAll();

                jQuery("#ModalViewAppointment").modal("hide");
              }
            })
            .catch(function(error) {
              console.log(error);
            });
        },
        function(dismiss) {
          Ladda.stopAll();
        }
      );
    },
    saveCustomer: function() {
      // Convert object to Form Data
      var input = new FormData();

      for (var key in this.formCustomer) {
        input.append("data[Customer][" + key + "]", this.formCustomer[key]);
      }

      axios
        .post(
          afx_vars.ajax_url +
            "?action=afx-customers-save&_ajax_nonce=" +
            afx_vars.save_customer_nonce,
          input
        )
        .then(function(response) {
          if (response.data.success) {
            jQuery("#ModalFormCustomer").modal("hide");
            jQuery("body").addClass("modal-open");

            app.customers.push({
              id: response.data.insert_id,
              full_name: app.formCustomer.full_name,
              email: app.formCustomer.email,
              phone: app.formCustomer.phone,
              info: app.formCustomer.info
            });

            app.formAppointment.customer_id = response.data.insert_id;

            // prompt success
            toastr.success(afx_vars.labels.customer_saved);
          } else {
            app.displayFormsErrors(response, "Customer");
          }

          Ladda.stopAll();
        });
    },
    cancelCustomer: function() {
      jQuery("#ModalFormCustomer").modal("hide");
      jQuery("body").addClass("modal-open");
    },
    displayFormsErrors: function(response, formId) {
      jQuery("#Form" + formId)
        .find(".text-danger")
        .remove();
      jQuery("#Form" + formId)
        .find(".form-group")
        .removeClass("has-error");
      jQuery("#Form" + formId)
        .find(".form-control")
        .removeClass("form-error");

      jQuery.each(response.errors, function() {
        var element = jQuery("#" + formId + camelize(this.field));

        element.addClass("form-error");
        element.parent().addClass("has-error");

        var _insert = jQuery(document.createElement("span")).insertAfter(
          element
        );
        _insert.addClass("help-block text-danger").text(this.msg);
      });
    },
    showEditAppointmentNote: function() {
      this.isShowEditAppointmentNote = true;
      this.viewAppointment.new_note = this.viewAppointment.note;
    },
    cancelSaveAppointmentNote: function() {
      this.isShowEditAppointmentNote = false;
    },
    saveAppointmentNote: function() {
      // save note
      var self = this;

      axios
        .post(
          afx_vars.ajax_url +
            "?action=afx-appointments-savenote&_ajax_nonce=" +
            afx_vars.save_appointment_nonce,
          {
            id: self.viewAppointment.id,
            note: self.viewAppointment.new_note
          }
        )
        .then(function(response) {
          if (response.data.success) {
            toastr.success(afx_vars.labels.note_saved);

            // update existing note
            self.viewAppointment.note = self.viewAppointment.new_note;
            self.eventEdited.note = self.viewAppointment.new_note;

            Ladda.stopAll();

            self.isShowEditAppointmentNote = false;
          }
        });
    },
    markPaid: function() {
      // Mark payment paid
      var self = this;

      axios
        .post(
          afx_vars.ajax_url +
            "?action=afx-appointments-markpaid&_ajax_nonce=" +
            afx_vars.save_appointment_nonce,
          {
            id: self.viewAppointment.id
          }
        )
        .then(function(response) {
          if (response.data.success) {
            toastr.success(afx_vars.labels.appointment_paid);

            // update existing note
            self.viewAppointment.is_paid = "1";
            self.eventEdited.is_paid = "1";

            Ladda.stopAll();

            self.isShowEditAppointmentNote = false;
          }
        });
    }
  }
});
