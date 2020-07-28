define([
    'uiRegistry',
    'Magento_Ui/js/form/element/ui-select'
], function (registry, multiselect) {
    'use strict';

    return multiselect.extend({
        defaults: {
            conditionValue: 0,
            imports: {
                conditionChanged: '${$.parentName}.country_condition:value'
            },
            listens: {
                visible: 'conditionsVisibility'
            },
            modules: {
                regionCondition: '${$.parentName}.state_condition'
            }
        },
        initialize: function () {
            this._super();
            this.conditionsVisibility();
            return this;
        },

        conditionChanged: function (value) {
            this.conditionValue = value;
            this.conditionsVisibility();
        },

        onUpdate: function () {
            this._super();
            this.conditionsVisibility();

            return this;
        },
        conditionsVisibility: function () {
            if (this.visible() && this.value().length === 1 && this.conditionValue == 1) {
                this.regionCondition(function (element) {
                    element.setVisible(true);
                    element.conditionsVisibility();
                });
            } else {
                this.regionCondition(function (element) {
                    element.value(0);
                    element.setVisible(false);
                    element.conditionsVisibility();
                });
            }
        }
    });
});
