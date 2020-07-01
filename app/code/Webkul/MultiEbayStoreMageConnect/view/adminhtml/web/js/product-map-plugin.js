/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($,$t,alert) {
    'use strict';
    var id,popup;
    $.widget('mage.productImportScript', {
        _create: function () {
            var self = this;
            $(this.options.importProductSelector).click(function (e) {
            id = $('#entity_id').val();
            var subcat=$(this);
            e.preventDefault();
            $.ajax({
                url: self.options.importAjaxUrl,
                data: {
                    form_key: window.FORM_KEY,
                    'id' : id
                },
                type: 'POST',
                dataType:'JSON',
                showLoader: true,
                success: function (ebayPro) {
                    if (ebayPro.error_msg==false) {
                        var msg='Total '+ebayPro.data.length +' products imported in your store from eBay. Now run profiler to create these product(s) in your store.';
                        $('<div />').html(msg)
                            .modal({
                                title: $.mage.__('Attention'),
                                autoOpen: true,
                                buttons: [{
                                 text: 'OK',
                                    attr: {
                                        'data-action': 'cancel'
                                    },
                                    'class': 'action-primary',
                                    click: function () {
                                            this.closeModal();
                                        }
                                }]
                            });
                    } else {
                        $('<div />').html(ebayPro.error_msg)
                            .modal({
                                title: $.mage.__('Attention'),
                                autoOpen: true,
                                buttons: [{
                                 text: 'OK',
                                    attr: {
                                        'data-action': 'cancel'
                                    },
                                    'class': 'action-primary',
                                    click: function () {
                                            this.closeModal();
                                        }
                                }]
                            });
                    }
                },
                error: function (error) {
                    console.log(error);
                }
                });
            });
            $(self.options.profilerSelector).click(function (e) {
                var width = '1100';
                var height = '400';
                var scroller = 1;
                var screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
                var screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
                var outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
                var outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
                var left = parseInt(screenX + ((outerWidth - width) / 2), 10);
                var top = parseInt(screenY + ((outerHeight - height) / 2.5), 10);
                
                var settings = (
                    'width=' + width +
                    ',height=' + height +
                    ',left=' + left +
                    ',top=' + top +
                    ',scrollbars=' + scroller
                    );
               popup = window.open(self.options.profilerAjaxUrl,'',settings);
               popup.onunload = self.afterChildClose;
            });
        },
        afterChildClose:function () {
            if (popup.location != "about:blank") {
                $('button[title="Reset Filter"]').trigger('click');
            }
        }
    });
    return $.mage.productImportScript;
});
