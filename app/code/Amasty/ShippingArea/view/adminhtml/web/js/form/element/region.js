define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function (_, registry, multiselect) {
    'use strict';

    return multiselect.extend({
        defaults: {
            conditionValue: 0,
            imports: {
                updateByCountry: '${ $.parentName }.country_set:value',
                conditionChanged: '${$.parentName}.state_condition:value',
                checkVisibility: '${$.parentName}.state_condition:visible'
            },
            listens: {
                visible: 'checkVisibility setPreview'
            },
            modules: {
                countrySet: '${$.parentName}.country_set'
            }
        },

        initialize: function () {
            this._super();

            //Link text input to region field to show if no ones available for selected country
            //Should not use as attribute in XML because of field type:
            //if this is set by attribute text input will be created instead of textarea
            this.customEntry = 'state_set';
            this.customName = this.parentName + '.' + this.customEntry;
        },

        /**
         * @param {String} value
         */
        updateByCountry: function (value) {
            if (value.length > 1 || value.length === 0) {
                return this.hide();
            }
        },

        conditionChanged: function (value) {
            this.conditionValue = value;
            this.checkVisibility(this.visible());
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function (value, field) {
            var countryField = this.countrySet();

            if (value.length === 1) {
                value = value.shift();
            }

            if (countryField) {
                this._super(value, field);
            }
        },

        isOptionsCanBeVisible: function () {
            return this.options().length > 0 && this.conditionValue != 0;
        },

        isInputCanBeVisible: function () {
            return this.options().length === 0 && this.conditionValue != 0;
        },

        checkVisibility: function (isVisible) {
            var isOptions = this.isOptionsCanBeVisible(),
                isInput = this.isInputCanBeVisible();

            if (!isOptions && !isInput) {
                this.toggleInput(false);
                if (isVisible) {
                    this.visible(false);
                }
                return this;
            }

            if (isVisible && !isOptions) {
                this.visible(false);
                if (isInput) {
                    this.toggleInput(true);
                }
            }

            return this;
        },

        /**
         * Change visibility for input.
         *
         * @param {Boolean} isVisible
         */
        toggleInput: function (isVisible) {
            this._super(this.isInputCanBeVisible() && isVisible);
        }
    });
});
