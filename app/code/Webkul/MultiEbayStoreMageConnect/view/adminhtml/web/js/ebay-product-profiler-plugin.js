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
    var skipCount,total;
    $.widget('mage.ebayToProductProfiler', {
        _create: function () {
            var self = this;
            skipCount = 0;
            var total = self.options.ebayProductCount;
            var ruleId = self.options.ruleId;
            var productList = JSON.parse(self.options.productJson);
            if (total > 0) {
                importProduct(1,productList[0]);
            }
            function importProduct(count,product)
            {
                count = count;
                $.ajax({
                    type: 'get',
                    url:self.options.importUrl,
                    async: true,
                    dataType: 'json',
                    data : { count:count,
                    'ruleId' :ruleId,
                    'product':product },
                    success:function (data) {
                        if (data['error'] == 1) {
                            var mageProDetail = data['product_sku'] == '' ? '' : '(Store Product SKU - '+data['product_sku']+' )';
                            $.each(data['error_list'], function (index, value) {
                                $(".fieldset .wk-mu-error-msg-container").append(
                                    $('<div />').addClass('message message-'+value['type']+' '+value['type'])
                                                .text(value['message'] + mageProDetail)
                                );
                            });

                            skipCount++;
                        }
                        var width = (100/total)*count;
                        $(self.options.progressBarSelector).animate({width: width+"%"},'slow', function () {
                            if (count == total) {
                                finishImporting(count,productList[count-1], skipCount);
                            } else {
                                count++;
                                $(self.options.currentSelector).text(count);
                                importProduct(count,productList[count-1]);
                            }
                        });
                    }
                });
            }
            function finishImporting(count, product, skipCount)
            {
                var total = count-skipCount;
                $(self.options.fieldsetSelector).append($('<div />')
                                .addClass('wk-mu-success wk-mu-box')
                                .text('Total '+ total +' product(s) imported on eBay.'))
                              .append($('<div />')
                                    .addClass('wk-mu-note wk-mu-box')
                                    .text('Finished Execution'))
                              .append($('<a/>').attr('href',self.options.backUrl)
                                .append($('<button/>').addClass('wk-back-button primary')
                                    .append($('<span/>').addClass('button')
                                        .append($('<span/>').text('Back')))));
                $(self.options.infoBarSelector).text('Completed');
            }
        }
    });
    return $.mage.ebayToProductProfiler;
});
