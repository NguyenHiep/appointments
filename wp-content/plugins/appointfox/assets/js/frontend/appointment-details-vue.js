var $ = jQuery.noConflict();

var appAppointFoxDetails = new Vue({
    el: '#AppAppointFoxDetails',
    data: {
        isLoadingCheckPayment: false,
        isPaid: false
    },
    mounted: function () {
        this.isPaid = parseInt($('#AppointmentIsPaid').val());
    }
});