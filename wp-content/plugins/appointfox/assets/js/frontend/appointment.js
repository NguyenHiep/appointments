var $ = jQuery.noConflict();
var price = '0.01';
var appointmentId = '0';

$(document).ready(function () {

    //Initialize tooltips
    // $('.appointfox-tbs .nav-tabs > li a[title]').tooltip();

    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);

        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $('.next-step').click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        $active.removeClass('active');
        $active.addClass('disabled');
        $active.next().removeClass('disabled');
        nextTab($active);
    });

    $('.prev-step').click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        $active.removeClass('active');
        $active.addClass('disabled');
        prevTab($active);
    });

    // If sandbox mode use USD currency
    if (afx_vars.paypal_env == 'sandbox') {
        afx_vars.currency = 'USD';
    }

    if (afx_vars.payment_method == 'PayPal') {
        // Render the PayPal button
        paypal.Button.render({

                // Set your environment

                env: afx_vars.paypal_env, // sandbox | production

                // Specify the style of the button

                style: {
                    fundingicons: false,
                    label: 'pay',
                    size: 'medium', // small | medium | large | responsive
                    shape: 'rect', // pill | rect
                    color: 'gold' // gold | blue | silver | black
                },

                // PayPal Client IDs - replace with your own
                // Create a PayPal app: https://developer.paypal.com/developer/applications/create

                client: {
                    sandbox: afx_vars.paypal_sandbox_clientid,
                    production: afx_vars.paypal_prod_clientid
                },

                // Wait for the PayPal button to be clicked

                payment: function (data, actions) {
                    return actions.payment.create({
                        payment: {
                            transactions: [{
                                amount: {
                                    total: price,
                                    currency: afx_vars.currency
                                }
                            }]
                        },
                        experience: {
                            input_fields: {
                                no_shipping: 1
                            }
                        }
                    });
                },

                // Wait for the payment to be authorized by the customer

                onAuthorize: function (data, actions) {
                    return actions.payment.execute().then(function () {
                        console.log('Payment complete!');

                        appAppointFox.isLoadingCheckPayment = true;

                        axios.post(afx_vars.ajax_url + '?action=afx-ajax-checkpayment&_ajax_nonce=' + afx_vars.check_payment_nonce, {
                            paymentID: data.paymentID,
                            payerID: data.payerID,
                            paymentToken: data.paymentToken,
                            pid: appointmentId
                        }).then(function (response) {
                            if (response.data.success) {
                                swal(
                                    afx_vars.labels.payment_paid+'!',
                                    afx_vars.labels.thank_you+'!',
                                    'success'
                                );

                                appAppointFox.formAppointment.is_paid = true;
                            }

                            appAppointFox.isLoadingCheckPayment = false;
                        });
                    });
                }
            },
            '#paypal-button-container');
    }

    $('.swal2-success-circular-line-left').css('background', afx_vars.background_color)
    $('.swal2-success-fix').css('background', afx_vars.background_color)
    $('.swal2-success-circular-line-right').css('background', afx_vars.background_color)

    Ladda.bind('.ladda-button');
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}

function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}