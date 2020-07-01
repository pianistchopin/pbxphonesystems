/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define([
    "jquery",
    "mage/translate",
    'Magento_Ui/js/modal/alert'
], function ($, $t, alertt) {
    "use strict";
    var popup;
    $.widget('ebayConnect.authorizeEbayUser', {
        _create: function () {
            var self = this;
            var globalSite;var notificationStatus;

            // re auth click event
            $('body').on('click','#save-btn-reauth', function () {
                $('.wk-mp-design-ebay').removeClass('block-hide');
                $('.verfied-ebay-user-container').addClass('block-hide');
            });
            $('body').on('click', '.ebay-authorize', function () {
                globalSite = $('#global_site').val();
                var postalCode = $('#shop_postal_code').val();
                var storeName = $('#store_name').val();
                var attributeSetId = $('#attribute_set_id').val();

                if (globalSite && postalCode && storeName) {
                    console.log(self.options.sessionUrl);
                    $.ajax({
                        url : self.options.sessionUrl,
                        data: {
                            form_key: window.FORM_KEY,
                        },
                        type : 'post',
                        showLoader : true,
                        datatype : 'json',
                        success : function (result) {

                            if (result.error == 0) {
                                var rupatams = encodeURIComponent("globalSite="+globalSite+"&storeName="+storeName+"&postalCode="+postalCode+"&sessid="+result.sessionId+"&attributeset_id="+attributeSetId);
                                var AuthUrl = self.options.auth_url+"&runame="+result.ruName+"&SessID="+encodeURIComponent(result.sessionId)+"&ruparams="+rupatams;
                                var width = '700';
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
                                console.log(AuthUrl);
                                popup = window.open(AuthUrl, "eBay User Authorization", settings);
                                var timer = setInterval(function () {
                                    if (popup.closed) {
                                        clearInterval(timer);
                                        $('#save').removeAttr('disabled');
                                        $('#save').click();
                                    }
                                }, 1000);
                            } else {
                                alertt({
                                   title: 'Error',
                                   content: result.error,
                                   actions: {
                                       always: function (){}
                                   }
                               });
                            }
                        }
                    });
                } else {
                    alertt({
                           title: 'Error',
                           content: 'Please fill all required Fields.',
                           actions: {
                               always: function (){}
                           }
                    });
                }
            });
        }

    });
    return $.ebayConnect.authorizeEbayUser;
});
