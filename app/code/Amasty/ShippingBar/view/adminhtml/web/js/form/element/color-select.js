define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'jquery/colorpicker/js/colorpicker',
], function ($, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            elementTmpl: 'Amasty_ShippingBar/form/element/color-select',
            color: false
        },

        bindColorPicker: function(element) {
            $(element).ColorPicker({
                color: this.value(),
                onChange: function (hsb, hex) {
                    this.value('#' + hex);
                }.bind(this)
            });
        },

        initObservable: function () {
            this._super().observe('color');

            this.value.subscribe(function () {
               this.color(this.inverseColor(this.value()));
            }.bind(this));

            return this;
        },

        inverseColor: function (color) {
            color = color.replace('#', '');
            return (0xFFFFFF - ("0x" + color))
                .toString(16)
                .padStart(6, "0")
                .toUpperCase().
                replace('', '#');
        },
    });
});
