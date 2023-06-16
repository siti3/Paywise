define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Paywise_Payment/js/action/set-payment-method-action'
    ],
    function (ko, $, Component, setPaymentMethodAction) {
        'use strict';
        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Paywise_Payment/payment/paywisepayment'
            },
            afterPlaceOrder: function () {
                setPaymentMethodAction(this.messageContainer);
                return false;
            }
        });
    }
);