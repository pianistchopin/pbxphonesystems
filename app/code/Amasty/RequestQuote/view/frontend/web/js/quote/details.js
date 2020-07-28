define([
    'ko',
    'underscore',
    'uiComponent',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/checkout-data'
], function (ko, _, Component, storage, urlBuilder, totalsService, checkoutData) {
    'use strict';

    return Component.extend({
        remark: ko.observable(window.checkoutConfig.quoteData['remarks']),
        checkDelay: 2000,
        remarkCheckTimeout: 0,
        isLoading: ko.observable(false),

        initObservable: function () {
            var self = this;
            this.remark.subscribe(function (value) {
                clearTimeout(self.remarkCheckTimeout);
                self.isLoading(true);

                self.remarkCheckTimeout = setTimeout(function () {
                    storage.put(
                        self._getUrl(),
                        JSON.stringify({'remark': value}),
                        false
                    ).always(function () {
                        self.isLoading(false);
                    });
                }, self.checkDelay);
            });
            if (this.notLoggedIn()) {
                this.firstname = ko.observable(this.getDefaultValue('firstname'));
                this.firstname.subscribe(function (value) {
                    var shippingData = checkoutData.getShippingAddressFromData();
                    if (shippingData === null) {
                        shippingData = {};
                    }
                    shippingData.firstname = value;
                    checkoutData.setShippingAddressFromData(shippingData);
                });
                this.lastname = ko.observable(this.getDefaultValue('lastname'));
                this.lastname.subscribe(function (value) {
                    var shippingData = checkoutData.getShippingAddressFromData();
                    if (shippingData === null) {
                        shippingData = {};
                    }
                    shippingData.lastname = value;
                    checkoutData.setShippingAddressFromData(shippingData);
                });
            }
            this._super();
            return this;
        },

        getDefaultValue: function (key) {
            return checkoutData.getShippingAddressFromData()
                ? checkoutData.getShippingAddressFromData()[key]
                : '';
        },

        _getUrl: function () {
            return urlBuilder.createUrl('/amasty_quote/updateRemark', {});
        },

        notLoggedIn: function () {
            return !window.checkoutConfig.isCustomerLoggedIn;
        }
    });
});
