define(
    [   'jquery',
        'uiComponent',
        'mage/url',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        $,
        Component,
        url,
        rendererList
    ) { 
        'use strict';
        rendererList.push(
            {
                type: 'paywisepayment',
                component: 'Paywise_Payment/js/view/payment/method-renderer/paywisepayment'
            }
        );
        return Component.extend({});
    }
);