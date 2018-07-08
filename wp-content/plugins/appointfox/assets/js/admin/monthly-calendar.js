function loadMonthlyCalendar(calendar_id) {
    jQuery.ajax({
            method: 'GET',
            url: afx_vars.ajax_url,
            data: {
                action: 'afx-calendars-monthlycalendar',
                _ajax_nonce: afx_vars.get_calendar_nonce,
                calendar_id: calendar_id,
                month: moment().format('M'),
                year: moment().format('YYYY')
            }
        })
        .done(function (response) {
            jQuery('#MonthlyCalendar').html(response);
            initDayActiveButton();
            intBtnArrow();
        });
}

function intBtnArrow() {
    jQuery('.btn-arrow').click(function (e) {
        e.preventDefault();
        jQuery('#LoadingMonthlyCalendar').show();
        var calendar_id = jQuery('#FrmCalendarEditId').val();
        jQuery.ajax({
                method: 'GET',
                url: afx_vars.ajax_url,
                data: {
                    action: 'afx-calendars-monthlycalendar',
                    _ajax_nonce: afx_vars.get_calendar_nonce,
                    calendar_id: calendar_id,
                    month: $(this).data('month'),
                    year: $(this).data('year')
                }
            })
            .done(function (response) {
                jQuery('#MonthlyCalendar').html(response);
                jQuery('#LoadingMonthlyCalendar').hide();
                initDayActiveButton();
                intBtnArrow();
            });
    });
}

function tagHour(value) {
    if (value == '') {
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
        if (timeTemp != 'Invalid date') {
            tags += timeTemp + ', ';
        }
    }

    tags = jQuery.trim(tags);

    if (tags[tags.length - 1] == ',') {
        tags = tags.substring(0, tags.length - 1);
    }

    return tags;
}

function initDayActiveButton() {
    $('.day-active').click(function (e) {
        e.preventDefault();
        $(this).webuiPopover('show');
    });

    $('.day-active').webuiPopover({
        // title: 'Title',
        // content: 'Content',
        content: $('#PopupHour').html(),
        placement: 'auto-top',
        animation: 'pop',
        width: 300,
        height: 220,
        cache: false,
        trigger: 'manual',
        type: 'html',
        onShow: function (e) {
            var clickedElement = $(e).data('trigger-element');
            var day = clickedElement.data('day');
            var hour = clickedElement.data('hour');

            $(e).find('#PopupHourTime').focus();

            $(e).find('#PopupHourDayData').val(day);
            $(e).find('#PopupHourTime').val(hour);

            var formatDay = moment(day, 'YYYY-MM-DD');

            $(e).find('#PopupHourDay').html(formatDay.format('MMMM D, YYYY'));
            $(e).find('#PopupHourDayName').html(formatDay.format('dddd'));

            Ladda.bind('.ladda-button');

            $(e).find('#PopupHourBtnCancel').click(function (j) {
                j.preventDefault();
                WebuiPopovers.hideAll();
            });

            $(e).find('#PopupHourBtnSet').click(function (v) {
                v.preventDefault();

                var newDay = $(e).find('#PopupHourDayData').val();
                var newHour = $(e).find('#PopupHourTime').val();
                var calendar_id = $('#FrmCalendarEditId').val();

                var hours = tagHour(newHour);

                jQuery.ajax({
                        method: 'POST',
                        url: afx_vars.ajax_url,
                        data: {
                            action: 'afx-calendars-daysave',
                            _ajax_nonce: afx_vars.save_calendar_nonce,
                            calendar_id: calendar_id,
                            day: newDay,
                            hour: hours
                        }
                    })
                    .done(function (response) {
                        clickedElement.find('p').html(hours);
                        clickedElement.data('hour', hours);
                        Ladda.stopAll();
                        WebuiPopovers.hideAll();
                    });
            });
        }
    });
}

// (function ($) {
//     $(document).ready(function () {

//     });
// })(jQuery);