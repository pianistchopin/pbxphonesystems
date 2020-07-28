define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _Rebuild: function () {
                this._super();
                if (typeof this.options.jsonConfig.original_products != 'undefined') {
                    this._CrossOutofstockOptions();
                }
            },

            _Rewind: function (controls) {
                this._super(controls);
                controls.find('div[option-id], option[option-id]').removeClass('am-disabled');
            },

            _CrossOutofstockOptions: function () {
                var $widget = this,
                    controls = $widget.element.find('.' + $widget.options.classes.attributeClass + '[attribute-id]'),
                    selected = controls.filter('[option-selected]');

                // done if nothing selected
                if (selected.length <= 0) {
                    return;
                }

                // Crossed-out out of stock options
                controls.each(function () {
                    var $this = $(this),
                        id = $this.attr('attribute-id'),
                        products = $widget._CalcProducts(id);

                    if (selected.length === 1 && selected.first().attr('attribute-id') === id) {
                        return;
                    }

                    $this.find('[option-id]').each(function () {
                        var $element = $(this),
                            option = $element.attr('option-id');

                        if (!$widget.optionsMap.hasOwnProperty(id) ||
                            !$widget.optionsMap[id].hasOwnProperty(option)
                        ) {
                            return true;
                        }

                        if (_.intersection(products, $widget.options.jsonConfig.original_products[id][option]).length <= 0) {
                            $element.addClass('am-disabled');
                        }
                    });
                });
            },

            /**
             * disable Amasty_Conf cross options
             * @private
             */
            _addOutOfStockLabels: function () {
            }
        });

        return $.mage.SwatchRenderer;
    }
});
