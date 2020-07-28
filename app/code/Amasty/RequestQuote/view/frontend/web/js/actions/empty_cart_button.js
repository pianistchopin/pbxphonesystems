define([
    'jquery'
], function ($) {
    var clearButtton = $('#empty_cart_button');

    return function () {
        clearButtton.show();
        clearButtton.on('click', function(event) {
            clearButtton.closest('form').off('submit');
        });
    }
});
