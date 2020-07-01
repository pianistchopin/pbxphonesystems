define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'jquery/ui'
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator
    ) {
        'use strict';
        /**
         *
         *
         */
        return Component.extend({
            defaults: {
                template: 'ProVu_UploadOrder/ponumber-step'
            },
            //add here your logic to display step,
            isVisible: ko.observable(false),
            /**
             *
             * @returns {*}
             */
            initialize: async function () {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    //step code will be used as step content id in the component template
                    'ponumber_step_code',
                    //step alias
                    null,
                    //step title value
                    'Purchase Order Number',
                    //observable property with logic when display step or hide step
                    this.isVisible,
                    _.bind(this.navigate, this),
                    /**
                     * sort order value
                     * 'sort order value' < 10: step displays before shipping step;
                     * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                     * 'sort order value' > 20 : step displays after payment step
                     */
                    15
                );
                return this;
            },
            /**
             * The navigate() method is responsible for navigation between checkout step
             * during checkout. You can add custom logic, for example some conditions
             * for switching to your custom step
             */
            navigate: function () {
            },
            /**
             * @returns void
             */
            navigateToNextStep: function () {
                var ponumber = $("#ponumber").val();

                $.ajax({
                    url: "/ponumber/save/index",
                    type: "POST",
                    data: {
                        'ponumber':ponumber,
                    },
                    cache: false,
                    success: function(result){
                        console.log("Passed");
                    }
                });
                stepNavigator.next();
            },
            getCartItems: function () {
                return window.checkoutConfig.quoteItemData;
            }
        });
    }
);