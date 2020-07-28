define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            conditions: [],
            listens: {
                value: 'conditionsVisibility setDifferedFromDefault',
                visible: 'conditionsVisibility setPreview'
            },
            modules: {
                countryCondition: '${$.parentName}.country_condition'
            }
        },
        conditionsVisibility: function () {
            this.countryCondition(function (conditionElement) {
                if (this.visible() && this.value() != 2 && conditionElement.value() == 1) {
                    _.each(this.conditions, function (index) {
                        registry.get(index, function (element) {
                            element.visible(true);
                        });
                    });
                } else {
                    _.each(this.conditions, function (index) {
                        registry.get(index, function (element) {
                            element.value(0);
                            element.visible(false);
                        });
                    });
                }
            }.bind(this));
        }
    });
});
