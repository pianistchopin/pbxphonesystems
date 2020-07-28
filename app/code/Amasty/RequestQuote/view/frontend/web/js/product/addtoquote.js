define([
    'jquery'
], function ($) {

    $.widget('mage.addtoquote', {
        options: {},

        _create: function () {
            if (this.options.addUrl) {
                this.productPageInit();
            } else if (this.options.isCategoryPage) {
                this.productCategoryInit();
            }
        },

        productPageInit: function () {
            var addToForm = $(this.element).parents('form'),
                addUrl = this.options.addUrl,
                formUrl = addToForm.attr('action');

            this.addSubmitListener(addToForm, addUrl, formUrl);
        },

        productCategoryInit: function () {
            var addToQuoteButton = $(this.element),
                actionsWrapper = addToQuoteButton.parents('.product-item-actions'),
                addToForm = actionsWrapper.find('form'),
                formUrl,
                addUrl;

            if (addToForm.length) {
                formUrl = addToForm.attr('action');
                actionsWrapper.addClass('amquote-action-wrap');
                addToQuoteButton.appendTo(addToForm);
                addToQuoteButton.show();
                addUrl = formUrl.replace('checkout', 'amasty_quote');

                this.addSubmitListener(addToForm, addUrl, formUrl);
            }
        },

        addSubmitListener: function (addToForm, addUrl, formUrl) {
            var self = this;

            addToForm.find('button[type="submit"]').on('click', function (e) {
                e.preventDefault();
                if ($(this).attr('data-amquote-js')) {
                    addToForm.attr('action', addUrl)
                        .attr('data-amquote-js', 'addtoquote')
                        .attr('data-amquote-logged', self.options.loggedIn);
                } else {
                    addToForm.attr('action', formUrl).attr('data-amquote-js', '');
                }
                addToForm.trigger('submit');
            });
        }
    });

    return $.mage.addtoquote;
});
