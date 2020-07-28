define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            options: {
                quoteCartSelector: '',
                addToQuoteButtonSelector: '[data-amquote-js="addto-button"]',
                addToQuoteButtonTextWhileAdding: '',
                addToQuoteButtonDisabledClass: 'disabled',
                addToQuoteButtonTextDefault: ''
            },

            /**
             * Handler for the form 'submit' event
             *
             * @param {Object} form
             */
            submitForm: function (form) {
                var isAddToQuote = form.attr('data-amquote-js'),
                    self = this,
                    isLogged = form.attr('data-amquote-logged') === '1';

                if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                    postSubmitForm(form, false);
                } else if (isAddToQuote) {
                    if (isLogged) {
                        self.ajaxSubmitQuote(form);
                    } else {
                        form.append(
                            $('<input/>').attr('type', 'hidden')
                                .attr('name', 'return_url_quote_added')
                                .val('amasty_quote/cart')
                        );
                        self.postSubmitForm(form, true);
                    }
                } else {
                    self.ajaxSubmit(form);
                }
            },

            /**
             * @param {String} form
             * @param quote
             */
            postSubmitForm: function (form, quote) {
                this.element.off('submit');
                if (quote) {
                    this.disableAddToQuoteButton(form);
                } else {
                    // disable 'Add to Cart' button
                    var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                    addToCartButton.prop('disabled', true);
                    addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                }
                form.submit();
            },

            /**
             * @param {String} form
             */
            ajaxSubmitQuote: function (form) {
                var self = this;

                $(self.options.quoteCartSelector).trigger('contentLoading');
                self.disableAddToQuoteButton(form);

                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    type: 'post',
                    dataType: 'json',

                    beforeSend: function () {
                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStart);
                        }
                    },

                    success: function (res) {
                        var eventData,
                            parameters;

                        $(document).trigger('ajax:addToQuote', {
                            'sku': form.data().productSku,
                            'form': form,
                            'response': res
                        });

                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStop);
                        }

                        if (res.backUrl) {
                            eventData = {
                                'form': form,
                                'redirectParameters': []
                            };
                            // trigger global event, so other modules will be able add parameters to redirect url
                            $('body').trigger('catalogCategoryAddToQuoteRedirect', eventData);

                            if (eventData.redirectParameters.length > 0) {
                                parameters = res.backUrl.split('#');
                                parameters.push(eventData.redirectParameters.join('&'));
                                res.backUrl = parameters.join('#');
                            }
                            window.location = res.backUrl;

                            return;
                        }

                        if (res.messages) {
                            $(self.options.messagesSelector).html(res.messages);
                        }

                        if (res.quotecart) {
                            $(self.options.quoteCartSelector).replaceWith(res.quotecart);
                            $(self.options.quoteCartSelector).trigger('contentUpdated');
                        }

                        if (res.product && res.product.statusText) {
                            $(self.options.productStatusSelector)
                                .removeClass('available')
                                .addClass('unavailable')
                                .find('span')
                                .html(res.product.statusText);
                        }
                        self.enableAddToQuoteButton(form);
                    },

                    error: function (exp) {
                        self.enableAddToQuoteButton(form);
                    }
                });
            },

            /**
             * @param {String} form
             */
            disableAddToQuoteButton: function (form) {
                var addToQuoteButtonTextWhileAdding = this.options.addToQuoteButtonTextWhileAdding || $t('Adding...'),
                    addToQuoteButton = $(form).find(this.options.addToQuoteButtonSelector);

                addToQuoteButton.addClass(this.options.addToQuoteButtonDisabledClass);
                addToQuoteButton.find('span').text(addToQuoteButtonTextWhileAdding);
                addToQuoteButton.attr('title', addToQuoteButtonTextWhileAdding);
            },

            /**
             * @param {String} form
             */
            enableAddToQuoteButton: function (form) {
                var self = this,
                    addToQuoteButtonTextDefault = self.options.addToQuoteButtonTextDefault || $t('Add to Quote'),
                    addToQuoteButton = $(form).find(self.options.addToQuoteButtonSelector);

                addToQuoteButton.removeClass(self.options.addToQuoteButtonDisabledClass);
                addToQuoteButton.find('span').text(addToQuoteButtonTextDefault);
                addToQuoteButton.attr('title', addToQuoteButtonTextDefault);
                addToQuoteButton.blur();
            }
        });

        return $.mage.catalogAddToCart;
    }
});
