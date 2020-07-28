define([
    'Magento_Ui/js/form/element/select'
], function (Element) {
    'use strict';

    return Element.extend({
        noticeStorage: '',

        initialize: function () {
            this._super();

            this.noticeStorage = this.additionalInfo();

            return this;
        },

        initObservable: function () {
            this._super();

            this.observe('additionalInfo');

            return this;
        },

        hideInfo: function () {
            this.additionalInfo(false);
        },

        showInfo: function () {
            this.additionalInfo(this.noticeStorage);
        }
    });
});
