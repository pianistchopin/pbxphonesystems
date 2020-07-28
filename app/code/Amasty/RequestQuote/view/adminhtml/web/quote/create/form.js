define([
    'jquery',
    'Amasty_RequestQuote/quote/edit/scripts'
], function (jQuery) {
    'use strict';

    var $el = jQuery('#edit_form'),
        config,
        baseUrl,
        order;

    if (!$el.length || !$el.data('order-config')) {
        return;
    }

    config = $el.data('order-config');
    baseUrl = $el.data('load-base-url');

    var quote = new AmAdminQuote(config);
    quote.setLoadBaseUrl(baseUrl);
    window.quote = quote;
});
