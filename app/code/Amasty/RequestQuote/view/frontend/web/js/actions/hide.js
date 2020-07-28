define([
    'jquery'
], function ($) {
    function hideActions() {
        $('[href^="#hide-element-"]').each(function (index, elem) {
            $(elem).hide();
            if ($(elem).attr('href').match(/\d+/) && $(elem).attr('href').match(/\d+/)[0]) {
                $('.action.delete[data-cart-item="' + $(elem).attr('href').match(/\d+/)[0] + '"]').hide();
            }
        });
    }

    return function () {
        $('[data-block=\'minicart\']').on('contentUpdated', function() {
            hideActions();
        });
    }
});
