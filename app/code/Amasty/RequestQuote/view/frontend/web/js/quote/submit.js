define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var form = $('.amasty-quote-update');
        $(element).click(function (event) {
            event.preventDefault();
            var detailsForm = $('[data-form-js="am-details-form"]');

            if (form.valid() && detailsForm.valid()) {
                $('<input />').attr('type', 'hidden')
                    .attr('name', 'remarks')
                    .attr('value', detailsForm.find('[name="quote_remark"]').val())
                    .appendTo(form);
                $('<input />').attr('type', 'hidden')
                    .attr('name', 'update_cart_action')
                    .attr('value', 'submit')
                    .appendTo(form);
                $('<input />').attr('type', 'hidden')
                    .attr('name', 'email')
                    .attr('value', detailsForm.find('[name="username"]').val())
                    .appendTo(form);
                $('<input />').attr('type', 'hidden')
                    .attr('name', 'first_name')
                    .attr('value', detailsForm.find('[name="first_name"]').val())
                    .appendTo(form);
                $('<input />').attr('type', 'hidden')
                    .attr('name', 'last_name')
                    .attr('value', detailsForm.find('[name="last_name"]').val())
                    .appendTo(form);

                $(element).attr('disabled', true);

                form.submit();
            }
        });
    };
});
