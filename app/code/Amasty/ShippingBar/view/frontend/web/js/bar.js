define([
    'jquery',
    'uiComponent',
    'mage/storage',
    'Magento_Customer/js/customer-data'
], function ($, Element, storage, customerData) {
    'use strict';

    return Element.extend({
        defaults: {
            isVisible: false,
            additionalClass: false,
            helpFlag: false,
            contentVisibility: true,
            goalLeft: false,
            template: 'Amasty_ShippingBar/bar'
        },

        initialize: function () {
            this._super();
            this.setGoalLeft();

            if (this.barBackground) {
                this.isVisible(true);
            }
        },

        initObservable: function () {
            this._super().observe('isVisible additionalClass helpFlag contentVisibility goalLeft');

            return this;
        },

        setGoalLeft: function () {
            var cart = customerData.get('cart');

            this.goalLeft(this.goal - cart().subtotalAmount);

            cart.subscribe(function (cart) {
                this.goalLeft(this.goal - cart.subtotalAmount);
            }.bind(this))
        },

        isCarVisible: function () {
            return this.isCarVisibleValue === "1" || !this.contentVisibility();
        },

        hasTermContent: function () {
            return this.labels.terms_message && this.labels.terms_message.length > 0;
        },

        initMoving: function () {
            var container = $(".amasty-shipbar.container");

            if (this.position === "15") {
                var defaultTop = container.position().top;

                if ($(window).scrollTop() > defaultTop) {
                    this.additionalClass('fixed top');
                }

                $(document).scroll(function (e) {
                    if ($(window).scrollTop() > defaultTop) {
                        this.additionalClass('fixed top');
                    } else {
                        this.additionalClass(null);
                    }
                }.bind(this));
            }

            if (this.position === "25") {
                var defaultBottom = $(document).height() - container.position().top,
                    containerHeight = container.height();

                if ($(window).height() + $(window).scrollTop() < $(document).height() - defaultBottom) {
                    this.additionalClass('fixed bottom');
                }

                $(document).scroll(function (e) {
                    var additionalClass = this.additionalClass(),
                        height =  0;
                    if (typeof additionalClass === 'string' && additionalClass.indexOf('fixed') > -1) {
                        height = containerHeight;
                    }
                    if ($(window).height() + $(window).scrollTop() < $(document).height() - defaultBottom + height) {
                        this.additionalClass('fixed bottom');
                    } else {
                        this.additionalClass(null);
                    }
                }.bind(this));
            }
        },

        haveAction: function () {
            return this.actionClickable === "1" && this.actionLink && this.actionLink.length > 0;
        },

        isCloseable: function () {
            return this.closeable === "1";
        },

        haveCustomStyle: function () {
            return this.customStyle && this.customStyle.length > 0;
        },

        getLabelContent: function () {
            var text = this.getRawLabelContent();

            text = text.replace('{{ruleGoal}}', this.addExtraColor(this.goal));
            text = text.replace('{{ruleGoalLeft}}', this.addExtraColor(this.goalLeft()));

            return text;
        },

        getFontSize: function () {
            return this.textSize + 'px';
        },

        getRawLabelContent: function () {
            if (this.helpFlag()) {
                return this.labels.terms_message;
            }

            if (this.goalLeft() == this.goal) {
                return this.labels.init_message;
            }

            if (this.goalLeft() <= 0) {
                return this.labels.achieved_message;
            }

            if (this.goalLeft() < this.goal) {
                return this.labels.progress_message;
            }

            return this.labels.init_message;
        },

        addExtraColor: function (data) {
            return "<b style=\"color: " + this.extraColor + "\">" + this.currencySymbol + (Math.round(data * 100) / 100).toFixed(2) + "</b>";
        },
    });
});
