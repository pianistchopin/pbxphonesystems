define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
    'mage/translate',
    'mage/dropdown'
], function (Component, customerData, $, ko, _) {
    'use strict';

    var popupInitialized = false,
        addToQuoteCalls = 0,
        quoteCart;

    quoteCart = $('[data-block=\'quotecart\']');

    /**
     * @return {Boolean}
     */
    function initPopup() {
        if (quoteCart.data('mageSidebar')) {
            quoteCart.sidebar('update');
        }

        if (!$('[data-role=product-item]').length) {
            return false;
        }
        quoteCart.trigger('contentUpdated');

        if (popupInitialized) {
            return false;
        }
        popupInitialized = true;
        quoteCart.sidebar({
            'targetElement': 'div.block.block-quotecart',
            'url': {
                'checkout': window.amasty_quote_cart.checkoutUrl,
                'update': window.amasty_quote_cart.updateItemQtyUrl,
                'remove': window.amasty_quote_cart.removeItemUrl,
                'loginUrl': window.amasty_quote_cart.customerLoginUrl,
                'isRedirectRequired': window.amasty_quote_cart.isRedirectRequired
            },
            'button': {
                'checkout': '#top-quotecart-button',
                'remove': '#quote-cart a.action.delete',
                'close': '#btn-quotecart-close'
            },
            'showcart': {
                'parent': 'span.counter',
                'qty': 'span.counter-number',
                'label': 'span.counter-label'
            },
            'minicart': {
                'list': '#quote-cart',
                'content': '#quotecart-content-wrapper',
                'qty': 'div.items-total',
                'subtotal': 'div.subtotal span.price',
                'maxItemsVisible': window.amasty_quote_cart.minicartMaxItemsVisible
            },
            'item': {
                'qty': ':input.cart-item-qty',
                'button': ':button.update-cart-item'
            },
            'confirmMessage': $.mage.__('Are you sure you would like to remove this item from the quote cart?')
        });
    }

    quoteCart.on('dropdowndialogopen', function () {
        initPopup();
    });

    return Component.extend({
        shoppingCartUrl: '#',
        maxItemsToDisplay: window.amasty_quote_cart.maxItemsToDisplay,
        quoteCart: {},

        initialize: function () {
            var self = this,
                cartData = customerData.get('quotecart');

            this.subTotal = cartData;

            this.update(cartData());
            cartData.subscribe(function (updatedCart) {
                addToQuoteCalls--;
                this.isLoading(addToQuoteCalls > 0);
                popupInitialized = false;
                this.update(updatedCart);
                initPopup();
            }, this);
            $('[data-block="quotecart"]').on('contentLoading', function () {
                addToQuoteCalls++;
                self.isLoading(true);
            });

            if (cartData()['website_id'] !== window.amasty_quote_cart.websiteId) {
                customerData.reload(['quotecart'], false);
            }

            return this._super();
        },

        isLoading: ko.observable(false),

        initPopup: initPopup,

        /**
         * Close mini shopping cart.
         */
        closeMinicart: function () {
            $('[data-block="quotecart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
        },

        /**
         * @return {Boolean}
         */
        closeSidebar: function () {
            var minicart = $('[data-block="quotecart"]');

            minicart.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                minicart.find('[data-role="dropdownDialog"]').dropdownDialog('close');
            });

            return true;
        },

        /**
         * @param {String} productType
         * @return {*|String}
         */
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        },

        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            _.each(updatedCart, function (value, key) {
                if (!this.quoteCart.hasOwnProperty(key)) {
                    this.quoteCart[key] = ko.observable();
                }
                this.quoteCart[key](value);
            }, this);
        },

        /**
         * Get cart param by name.
         * @param {String} name
         * @returns {*}
         */
        getCartParam: function (name) {
            if (!_.isUndefined(name)) {
                if (!this.quoteCart.hasOwnProperty(name)) {
                    this.quoteCart[name] = ko.observable();
                }
            }

            return this.quoteCart[name]();
        },

        /**
         * Returns array of cart items, limited by 'maxItemsToDisplay' setting
         * @returns []
         */
        getCartItems: function () {
            var items = this.getCartParam('items') || [];

            items = items.slice(parseInt(-this.maxItemsToDisplay, 10));

            return items;
        },

        /**
         * Returns count of cart line items
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            var items = this.getCartParam('items') || [];

            return parseInt(items.length, 10);
        }
    });
});