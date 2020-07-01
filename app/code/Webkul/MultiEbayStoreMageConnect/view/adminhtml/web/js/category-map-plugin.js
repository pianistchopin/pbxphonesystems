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
    'Magento_Ui/js/modal/alert',
], function ($,$t,alert) {
    'use strict';
    $.widget('mage.categoryMap', {
        _create: function () {
            var self = this;
            var loaderPath= self.options.loaderPath;
            var loader = $('<img />',{'src':loaderPath,'class':'loader','style':'margin-left:5px;'});
            $(self.options.formSelector).on('change',self.options.mageCategorySelector,function () {
                var cat_id=$(this).val();
                if (cat_id=="") {
                    $('<div />').html("please select category")
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
                    return false;
                }
                var subcat=$(this);
                subcat.after(loader.clone());
                subcat.nextAll('.mage_category').remove();
                if ($('.mage_category').length>1) {
                    subcat.attr('style','margin-top: 10px;min-width:300px;');
                }
                
                $.ajax({
                    url: self.options.getMageChildCategoryAjaxUrl,
                    data: {form_key: window.FORM_KEY,cat_id:cat_id},
                    type: 'POST',
                    dataType:'JSON',
                    success: function (magecat) {
                        if (magecat.totalRecords) {
                            var select=$('<select/>',{'class':'required-entry mage_category _required select admin__control-select','style':'margin-top: 10px;min-width:300px;','id':'mage_category_'+$('.mage_category').length})
                                            .append($('<option />')
                                                .val('')
                                                .text("Select Sub Category"));
                            $(magecat.items).each(function (i,cat) {
                                select.append($('<option />').val(cat.value).text(cat.lable));
                            });
                            $('.mage_category:last').next().after(select);
                        } else {
                            subcat.attr('style',subcat.attr('style')+'border-color:green;')
                                  .attr('name','leaf_mage_category');
                        }
                        subcat.next('img').remove();
                    },
                    error: function (error) {
                        $('<div />').html(error)
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
                });
            });

            $(self.options.formSelector).on('change',self.options.ebayCategorySelector,function () {
                var cat_id=$(this).val();
                if (cat_id=="") {
                    $('<div />').html("please select category")
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
                    return false;
                }
                var subcat=$(this);
                subcat.after(loader);
                subcat.nextAll('.ebay_category').remove();
                if ($('.ebay_category').length>1) {
                    subcat.attr('style','margin-top: 10px;min-width:250px;')
                }
                
                $.ajax({
                    url: self.options.getEbayChildCategoryAjaxUrl,
                    data: {form_key: window.FORM_KEY,cat_id:cat_id},
                    type: 'POST',
                    dataType:'JSON',
                    success: function (ebayCat) {
                        if (ebayCat.totalRecords) {
                            var select=$('<select/>',{'class':'required-entry ebay_category _required select admin__control-select','style':'margin-top:10px; min-width: 250px;','id':'ebay_category_'+$('.ebay_category').length})
                                            .append($('<option />')
                                                .val('')
                                                .text("Select Sub Category"));
                            $(ebayCat.items).each(function (i,cat) {
                                select.append($('<option />').val(cat.ebay_cat_id).text(cat.ebay_cat_name));
                            });
                            $('.ebay_category:last').next().after(select);
                        } else {
                            subcat.attr('style',subcat.attr('style')+'border-color:green;')
                                  .attr('name','leaf_ebay_category');
                        }
                        subcat.next('img').remove();
                    },
                    error:function (error) {
                        $('<div />').html(error)
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
                        subcat.next('img').remove();
                    }
                });
            });

            $(self.options.formSelector).on('click',self.options.saveMapSelector,function () {
                var ebayLeafCate = $('select[name="leaf_ebay_category"]').val();
                var mageLeafCate = $('select[name="leaf_mage_category"]').val();
                var ebayRootCate = $('select[name="ebay_category"]').val();
                var mageRootCate = $('select[name="mage_category"]').val();
                var id = $('#entity_id').val();
                if (ebayLeafCate && mageLeafCate) {
                    $.ajax({
                        url: self.options.saveMappingAjaxUrl,
                        data: {
                            form_key: window.FORM_KEY,
                            'ebayLeafCate' : ebayLeafCate,
                            'mageLeafCate' : mageLeafCate,
                            'ebayRootCate' : ebayRootCate,
                            "mageRootCate" : mageRootCate,
                            'id' : id
                        },
                        type: 'POST',
                        dataType:'JSON',
                        showLoader: true,
                        success: function (response) {
                            if (response.status) {
                                $('button[title="Reset Filter"]').trigger('click');
                            }
                            setTimeout(function () {
                                $('<div />').html(response.msg)
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
                            },3000);
                        },
                        error:function () {
                            $('<div />').html('Something went wrong')
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
                    });
                } else {
                    alert('Please select the category');
                }

            });
           
        }
    });
    return $.mage.categoryMap;
});
