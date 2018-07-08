var week_start = 0;
var moment_locale = "en";

if (afx_vars.week_start_on == "Monday") {
    moment.updateLocale(afx_vars.locale, {
        week: {
            dow: 1
        }
    });
} else {
    moment.updateLocale(afx_vars.locale, {
        week: {
            dow: 0
        }
    });
}

function loadAppointmentCalendar(resources, events) {
  jQuery("#fullcalendar").fullCalendar({
    lang: afx_vars.locale,
    firstDay: week_start,
    header: {
      left: "prev,next today",
      center: "title",
      right: "resourceAgendaDay,month,agendaWeek"
    },
    buttonText: {
      today: afx_vars.labels.today,
      month: afx_vars.labels.month,
      week: afx_vars.labels.week
    },
    height: 500,
    //            defaultDate: '2017-08-12',
    editable: false,
    eventLimit: true, // allow "more" link when too many events
    defaultView: "resourceAgendaDay",
    views: {
      resourceAgendaDay: {
        type: "resourceAgenda",
        duration: {
          days: 1
        },
        buttonText: afx_vars.labels.day
      }
    },
    allDaySlot: false, // Unsupported yet
    // unknownResourceTitle: 'Not Assigned',
    resources: resources,
    events: events,
    eventClick: function(event, element) {
      // event.title = 'CLICKED!';
      // $('#calendar').fullCalendar('updateEvent', event);
      // app.showAppointmentForm(event);
      app.showAppointmentView(event);
    }
  });
}

(function($) {
  $(document).ready(function() {
    var resources = [];
    var events = [];

    // load list
    axios
      .get(
        afx_vars.ajax_url +
          "?action=afx-appointmentscalendar-fullcalendardata&_ajax_nonce=" +
          afx_vars.get_list_nonce
      )
      .then(function(response) {
        var staffs = response.data.staffs;
        var appointments = response.data.appointments;

        if (staffs.length > 0) {
          for (var i = 0, len = staffs.length; i < len; i++) {
            resources.push({
              id: staffs[i]["id"],
              name: staffs[i]["full_name"]
            });
          }
        }

        if (appointments.length > 0) {
          for (var j = 0, len = appointments.length; j < len; j++) {
            events.push({
              id: appointments[j]["id"],
              service_id: appointments[j]["service_id"],
              service_title: appointments[j]["service_title"],
              price: appointments[j]["price"],
              is_paid: appointments[j]["is_paid"],
              duration: appointments[j]["duration"],
              staff_id: appointments[j]["staff_id"],
              staff_name: appointments[j]["staff_name"],
              customer_id: appointments[j]["customer_id"],
              customer_name: appointments[j]["customer_name"],
              customer_phone: appointments[j]["customer_phone"],
              customer_email: appointments[j]["customer_email"],
              note: appointments[j]["note"],
              title:
                appointments[j]["customer_name"] +
                ": " +
                appointments[j]["service_title"],
              start: appointments[j]["start_datetime"],
              end: appointments[j]["end_datetime"],
              resource: appointments[j]["staff_id"],
              color: appointments[j]["color"],
              textColor: "black"
            });
          }
        }

        loadAppointmentCalendar(resources, events);
      })
      .catch(function(error) {
        console.log(error);
      });

    // $('#datetimepicker').datetimepicker({
    //     inline: true,
    //     format: 'MMMM D, YYYY'
    // });

    // $('.modal').on('hidden.bs.modal', function (event) {
    //     $(this).removeClass('fv-modal-stack');
    //     $('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
    // });

    // $('.modal').on('shown.bs.modal', function (event) {

    //     // keep track of the number of open modals

    //     if (typeof ($('body').data('fv_open_modals')) == 'undefined') {
    //         $('body').data('fv_open_modals', 0);
    //     }

    //     // if the z-index of this modal has been set, ignore.

    //     if ($(this).hasClass('fv-modal-stack')) {
    //         return;
    //     }

    //     $(this).addClass('fv-modal-stack');

    //     $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);

    //     $(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));

    //     $('.modal-backdrop').not('.fv-modal-stack')
    //         .css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));

    //     $('.modal-backdrop').not('fv-modal-stack')
    //         .addClass('fv-modal-stack');

    // });

    $(document).on("show.bs.modal", ".modal", function(event) {
      //   var zIndex = 1040 + 10 * $(".modal:visible").length;
      //   $(this).css("z-index", zIndex);
      //   setTimeout(function() {
      //     $(".modal-backdrop")
      //       .not(".modal-stack")
      //       .css("z-index", zIndex - 1)
      //       .addClass("modal-stack");
      //   }, 0);
    });

    Ladda.bind(".ladda-button");
  });
})(jQuery);
