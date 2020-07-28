/*jshint browser:true jquery:true*/

define(['jquery'], function ($) {
    'use strict';

   var amstockstatusRenderer = {
        configurableStatus: null,
        spanElement: null,
        infoLink: null,
        defaultInfoLink: false,
        options: {},
        defaultContents: [],
        priceAlert: null,
        defaultPriceAlert: '',

        init: function (options) {
            this.options = options;
            this.spanElement = $('.stock').first();
            this.infoLink = $('.amstockstatus-info-link');
            if (this.spanElement.length && this.infoLink.length == 0) {
                this.infoLink = $(this.options.info_block).first();
                this.spanElement.after(this.infoLink.hide());
            } else {
                this.defaultInfoLink = true;
            }
            this.priceAlert = $('.alert.price').length ?
                $('.alert.price') :
                $('#form-validate-price').parent();
            if (this.priceAlert.length) {
                this.defaultPriceAlert = this.priceAlert.html();
            }
            this.dropdowns   = $('select.super-attribute-select, select.swatch-select');

            this._initialization();
        },

        /**
         * remove stock alert block
         */
        _hideStockAlert: function () {
            $('.amstockstatus-stockalert').remove();
        },

        _reloadDefaultContent: function (key) {
            if (this.spanElement.length
                && !this.spanElement.hasClass('ampreorder-observed')
            ) {
                this.spanElement.html(this.configurableStatus);
            }
            $('.box-tocart').show();
            if (this.priceAlert.length) {
                this.showPriceAlert(this.defaultPriceAlert);
            }
        },

        showStockAlert: function (code) {
            $('<div/>', {
                class: 'amstockstatus-stockalert',
                title: 'Subscribe to back in stock notification',
                rel: 'external',
                html: code
            }).appendTo('.product-add-form');

            $('#form-validate-stock').mage('validation');
        },

        /*
         * configure statuses at product page
         */
        onConfigure: function (key) {
            var keyCheck = '',
                selectedKey = '';

            this.dropdowns   = $('select.super-attribute-select, select.swatch-select, .swatch-attribute-options:has(.swatch-option)');
            this._hideStockAlert();
            if (null == this.configurableStatus && this.spanElement.length) {
                this.configurableStatus = this.spanElement.html();
            }

            //get current selected key
            this.settingsForKey = $('select.super-attribute-select, div.swatch-option.selected, select.swatch-select');
            if (this.settingsForKey.length) {
                for (var i = 0; i < this.settingsForKey.length; i++) {
                    if (parseInt(this.settingsForKey[i].value) > 0) {
                        selectedKey += this.settingsForKey[i].value + ',';
                    }

                    if (parseInt($(this.settingsForKey[i]).attr('option-id')) > 0) {
                        selectedKey += $(this.settingsForKey[i]).attr('option-id') + ',';
                    }
                }
            }
            var trimSelectedKey = selectedKey.substr(0, selectedKey.length - 1);
            var countKeys = selectedKey.split(",").length - 1;

            /*reload main status*/
            if ('undefined' !== typeof(this.options[trimSelectedKey])) {
                this._reloadContent(trimSelectedKey);
            } else {
                this._reloadDefaultContent(trimSelectedKey);
            }

            /*add statuses to dropdown*/
            var settings = this.dropdowns;
            for (var i = 0; i < settings.length; i++) {
                if (!settings[i].options) {
			        continue;
	        	}
                for (var x = 0; x < settings[i].options.length; x++) {
                    if (!settings[i].options[x].value) continue;

                    if (countKeys === i + 1) {
                        var keyCheckParts = trimSelectedKey.split(',');
                        keyCheckParts[keyCheckParts.length - 1] = settings[i].options[x].value;
                        keyCheck = keyCheckParts.join(',');

                    } else {
                        if (countKeys < i + 1) {
                            keyCheck = selectedKey + settings[i].options[x].value;
                        }
                    }

                    if ('undefined' !== typeof(this.options[keyCheck]) && this.options[keyCheck]) {
                        var status = this.options[keyCheck]['custom_status_text'];
                        if (status) {
                            status = status.replace(/<(?:.|\n)*?>/gm, ''); // replace html tags
                            if (settings[i].options[x].textContent.indexOf(status) === -1) {
                                if ('undefined' == typeof(this.defaultContents[i + '-' + x])) {
					                this.defaultContents[i + '-' + x] = settings[i].options[x].textContent;
				                }
                                settings[i].options[x].textContent = this.defaultContents[i + '-' + x] + ' (' + status + ')';
                            }
                        } else if (this.defaultContents[i + '-' + x]) {
                            settings[i].options[x].textContent = this.defaultContents[i + '-' + x];
                        }
                    }
                }
            }

        },
        /*
         * reload default stock status after select option
         */
        _reloadContent: function (key) {
            if ('undefined' !== typeof(this.options.changeConfigurableStatus)
                && this.options.changeConfigurableStatus
                && this.spanElement.length
                && !this.spanElement.hasClass('ampreorder-observed')
            ) {
                if (this.options[key] && this.options[key]['custom_status']) {
                    this.infoLink.show();
                    if (this.options[key]['custom_status_icon_only'] == 1) {
                        this.spanElement.html(this.options[key]['custom_status_icon']);
                    } else {
                        this.spanElement.html(this.options[key]['custom_status']);
                    }
                } else {
                    if (this.defaultInfoLink) {
                        this.infoLink.show();
                    } else {
                        this.infoLink.hide();
                    }
                    this.spanElement.html(this.configurableStatus);
                }
            }

            if ('undefined' !== typeof(this.options[key])
                && this.options[key]
                && 0 == this.options[key]['is_in_stock']
            ) {
                $('.box-tocart').each(function (index,elem) {
                    $(elem).hide();
                });
                if (this.options[key]['stockalert']) {
                    this.showStockAlert(this.options[key]['stockalert']);
                }
            } else {
                $('.box-tocart').each(function (index,elem) {
                    $(elem).show();
                });
            }

            if ('undefined' !== typeof(this.options[key]) &&
                this.options[key] &&
                this.options[key]['pricealert'] &&
                this.priceAlert.length
            ) {
                this.showPriceAlert(this.options[key]['pricealert']);
            }
        },

       showPriceAlert: function (code) {
           this.priceAlert.html(code);
       },

        _initialization: function () {
	        var me = this;

            $(document).on('configurable.initialized', function() {
                me.onConfigure();
            });

            $('body').on( {
                    'click': function(){setTimeout(function() { me.onConfigure(); }, 300);}
                },
                'div.swatch-option, select.super-attribute-select, select.swatch-select'
            );

            $('body').on( {
                    'change': function(){setTimeout(function() { me.onConfigure(); }, 300);}
                },
                'select.super-attribute-select, select.swatch-select'
            );
        }
    };

    return amstockstatusRenderer;
});
