(function ($) {
    $(document).ready(function () {

        function initBtnArrowClick() {
            $('.btn-arrow').click(function (e) {
                e.preventDefault();

//            var gotomonth = $(this).data('gotomonth');
                var year = $(this).data('year');
                var month = $(this).data('month');
//            alert(gotomonth);

                $.ajax({
                    type: "GET",
                    url: afx_dt.ajax_url,
                    data: {
                        action: 'afx-front-calendar',
                        _ajax_nonce: afx_dt.get_calendar_nonce,
//                    gotomonth: gotomonth,
                        year: year,
                        month: month
                    },
                    beforeSend: function () {
                        $('.nav-title').hide();
                        $('.nav-loading').show();
                    },
                    complete: function () {
                        $('.nav-loading').hide();
                    },
                    success: function (html) {
                        $("#div-bookme-calendar").replaceWith(html);
                        initBtnArrowClick();
                    }
                });
            });
        }

        initBtnArrowClick();
    });
})(jQuery);

