define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, url, fullScreenLoader) {
        'use strict';
        return function (messageContainer) {
            // console.log('-----------------------------------');
            // var currency = window.currency;
            // var terminalId = window.terminalId;
            // var api = window.api;
            // var disableAddressParam = window.disableAddressParam;
            // var enableDynamicBillingDescriptor = window.enableDynamicBillingDescriptor;
            // var color = window.color;
            // var backgroundColor = window.backgroundColor;
            // var btntxtcolor = window.btntxtcolor;
            // var url = api+'?terminalId='+terminalId+'&trackId=Demo-12345988120&currency='+currency+'&amount=1.00&disableAddressParam='+disableAddressParam+'&color='+color+'&bgcolor='+backgroundColor+'&btntxtcolor='+btntxtcolor+'&enableDynamicBillingDescriptor='+enableDynamicBillingDescriptor+'';
            $.mage.redirect(
                url.build('paymentgetway/index/PaywiseInterface')
            ); //url is your url
        };
    }
);