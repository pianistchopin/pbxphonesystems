define([
    'jquery'
], function ($) {
    $.widget('mage.addnote', {
        _create: function () {
            this.element.on('click', this._addNote);
            if ($.trim(this.element.parent().find('.textarea').text())) {
                this.element.trigger('click');
            }
        },

        _addNote: function () {
            $(this).parent().find('.textarea').show();
            $(this).hide();
        }
    });

    return $.mage.addnote;
});
