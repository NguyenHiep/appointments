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
                        total: '10',
                        currency: 'USD'
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

            axios.post(afx_vars.ajax_url + '?action=afx-ajax-checkpayment&_ajax_nonce=' + afx_vars.check_payment_nonce, {
                paymentID: data.paymentID,
                payerID: data.payerID,
                token: data.paymentToken,
                pid: appointmentId
            }).then(function (response) {
                if (response.data.success) {
                    swal(
                        'Payment Paid!',
                        'Thank you!',
                        'success'
                    );
                }

                appAppointFox.formAppointment.is_paid = true;
            });
        });
    }
},
'#paypal-button-container');