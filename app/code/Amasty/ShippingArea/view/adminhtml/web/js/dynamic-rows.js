define([
    'underscore',
    'uiLayout',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'mage/translate'
], function (_, layout, dynamicRows, $t) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            toTableLabel: $t('Switch to table input'),
            toRawLabel: $t('Switch to textarea input (for mass insert)'),
            tableVisible: true,
            error: '',
            rawInputObservable: false,
            rawConfig: {
                name: '${ $.name }_raw',
                component: 'Amasty_ShippingArea/js/dynamic-rows/textarea',
                template: 'Amasty_ShippingArea/textarea',
                recordsProvider: '${ $.name }',
                provider: '${ $.provider }',
                visible: false
            },
            listens: {
                tableVisible: 'setVisible'
            },
            modules: {
                rawInput: '${ $.rawConfig.name }'
            }
        },

        initialize: function () {
            this._super();

            this.rawInput(function (rawInput) {
                this.rawInputObservable(rawInput);
            }.bind(this));

            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('tableVisible error rawInputObservable');

            return this;
        },

        /**
         * Init Raw module
         *
         * @returns {Object} Chainable.
         */
        initModules: function () {
            this._super();
            layout([this.rawConfig]);

            return this;
        },

        /**
         * Set visibility to dynamic-rows child
         *
         * @param {Boolean} state
         */
        setVisible: function (state) {
            this._super();

            if (!this.visible()) {
                this.rawInput(function (component) {
                    component.setVisible(false);
                });
            } else {
                this.rawInput(function (component) {
                    component.setVisible(!this.tableVisible());
                }.bind(this));
            }
        },

        switchToRaw: function () {
            var dataArray = [],
                elementData = '';
            _.each(this.recordData(), function (elem) {
                if (elem[this.deleteProperty] !== this.deleteValue && elem['zip_from']) {
                    elementData = elem['zip_from'];
                    if (elem['zip_to']) {
                        elementData += '-' + elem['zip_to'];
                    }
                    dataArray.push(elementData);
                }
            }.bind(this));
            var resultData = dataArray.join(',');
            this.rawInput().default = resultData;
            this.rawInput().initialValue = resultData;
            this.rawInput().value(resultData);
            this.rawInput().setDifferedFromDefault();

            this.tableVisible(false);
            this.rawInput().visible(true);
            this.rawInput().focused(true);
        }
    });
});
