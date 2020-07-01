/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var Ves_Builder = function () {
    this.builder = null;
    this.callback = {};
    this.currentCol = null;
    this.currentWidget = null;
    this.currentShortcode = ''; 
}
var turnoffTinyMCEs = [];
var getContentTinyMCEs = [];
var getTinyMCEFields = [];
var VesBuilder = null;
var VesCallBack = {};
var VesCurrentCol = null;
var VesCurrentWidget = null;
var VesCurrentShortcode = "";

/*get widget type from shortcode*/
if(typeof(getWidgetTypeByShortcode) !== 'function') {
    function getWidgetTypeByShortcode(short_code) {
        var widgetType = "";
        if (short_code.indexOf('{{widget') != -1) {
            
            short_code.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                if (match[1] == 'type') {
                    widgetType = match[2];
                }
            });
        }
        return widgetType;
    }
}

define([
    "jquery",
    "wysiwygAdapter",
    'Magento_Ui/js/modal/alert',
    "Ves_BaseWidget/js/jquery/ui/jquery-ui.min",
    "mage/translate",
    "mage/mage",
    "mage/validation",
    "mage/adminhtml/events",
    "prototype",
    'Magento_Ui/js/modal/modal'
], function(jQuery, wysiwyg, alert){

    var WysiwygWidgetTools = {
        getDivHtml: function(id, html) {
            if (!html) html = '';
            return '<div id="' + id + '">' + html + '</div>';
        },

        onAjaxSuccess: function(transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON()
                if (response.error) {
                    throw response;
                } else if (response.ajaxExpired && response.ajaxRedirect) {
                    setLocation(response.ajaxRedirect);
                }
            }
        },

        dialogOpened : false,

        getMaxZIndex: function() {
            var max = 0, i;
            var cn = document.body.childNodes;
            for (i = 0; i < cn.length; i++) {
                var el = cn[i];
                var zIndex = el.nodeType == 1 ? parseInt(el.style.zIndex, 10) || 0 : 0;
                if (zIndex < 10000) {
                    max = Math.max(max, zIndex);
                }
            }
            return max + 10;
        },

        openDialog: function(widgetUrl, objbuilder, callback, col ) {

            if (this.dialogOpened) {
                return
            }
            var objbuilder = objbuilder !=null?objbuilder:null;
            var callback = callback != null?callback:null;
            var col = col != null?col:null;

            if ($('widget_window') && typeof(Windows) != 'undefined') {
                Windows.focus('widget_window');
                return;
            }
            var oThis = this;

            VesBuilder = new Ves_Builder();
            VesBuilder.currentShortcode = "";
            VesBuilder.currentWidget = null;
            VesBuilder.builder = objbuilder;
            VesBuilder.callback['widget'] = callback;
            VesBuilder.currentCol = col;

            this.dialogWindow = jQuery('<div/>').modal({
                title: jQuery.mage.__('Insert Widget...'),
                type: 'slide',
                buttons: [],
                opened: function () {
                    var dialog = jQuery(this).addClass('loading magento_message')
                    new Ajax.Updater($(this), widgetUrl, {evalScripts: true, onComplete: function () {
                            dialog.removeClass('loading');
                        }
                    });
                },
                closed: function (e, modal) {
                    modal.modal.remove();
                    oThis.dialogOpened = false;
                }
            });
            this.dialogOpened = true;
            this.dialogWindow.modal('openModal');
        },
        openFormDialog: function(widgetUrl, widget, widget_shortcode, objbuilder, callback, col ) {
            if (this.dialogOpened) {
                return
            }
            var oThis = this;
            var widget_shortcode = widget_shortcode!=null?widget_shortcode:"";
            var widget = widget != null?widget:null;
            var objbuilder = objbuilder !=null?objbuilder:null;
            var callback = callback != null?callback:null;
            var col = col != null?col:null;

            VesBuilder = new Ves_Builder();
            VesBuilder.currentShortcode = widget_shortcode;
            VesBuilder.currentWidget = widget;
            VesBuilder.builder = objbuilder;
            VesBuilder.callback['widget'] = callback;
            VesBuilder.currentCol = col;


            if ($('widget_window') && typeof(Windows) != 'undefined') {
                Windows.focus('widget_window');
                return;
            }

            this.dialogWindow = jQuery('<div/>').modal({
                title: jQuery.mage.__('Insert Widget...'),
                type: 'slide',
                buttons: [],
                opened: function () {
                    var dialog = jQuery(this).addClass('loading magento_message')
                    new Ajax.Updater($(this), widgetUrl, {evalScripts: true, onComplete: function () {
                            dialog.removeClass('loading');
                        }
                    });
                },
                closed: function (e, modal) {
                    modal.modal.remove();
                    oThis.dialogOpened = false;
                }
            });
            this.dialogOpened = true;
            this.dialogWindow.modal('openModal');
        }
    };

    var WysiwygWidget = {};
    WysiwygWidget.Widget = Class.create();
    WysiwygWidget.Widget.prototype = {

        initialize: function(formEl, widgetEl, widgetOptionsEl, optionsSourceUrl, widgetTargetId) {
            $(formEl).insert({bottom: WysiwygWidgetTools.getDivHtml(widgetOptionsEl)});
            jQuery('#' + formEl).mage('validation', {
                ignore: ".skip-submit",
                errorClass: 'mage-error'
            });
            this.formEl = formEl;
            this.widgetEl = $(widgetEl);
            this.widgetOptionsEl = $(widgetOptionsEl);
            this.optionsUrl = optionsSourceUrl;
            this.optionValues = new Hash({});
            this.widgetTargetId = widgetTargetId;

            this.buildWidgetForm(widgetEl);

            if(typeof(jQuery) != "undefined") {
                jQuery(this.widgetEl).on("change", this.loadOptions.bind(this) );
            } else {
                Event.observe(this.widgetEl, "change", this.loadOptions.bind(this));
            }

            //Event.observe(this.widgetEl, "change", this.loadOptions.bind(this));

            this.initOptionValue();
        },
        buildWidgetForm: function(widgetEl) {
            var widgets_list = jQuery("#wpo-widgetslist").html();
            if(widgets_list) {
                jQuery("#base_fieldset .widgets_list").remove();
                jQuery("#base_fieldset .field-select_widget_type").hide();
                jQuery("#base_fieldset").append(widgets_list);

                //Call actioin filter widgets
                this.widgetsFillter();
                this.widgetsAction(widgetEl);

            }
        },
        widgetsFillter:  function(){ //jQuery Function
            if(typeof(jQuery) == "undefined")
                return;

            jQuery("#base_fieldset .showinform").show();
            
            jQuery(".backtolist", "#base_fieldset").click( function(){
               
                jQuery("#base_fieldset .widgets-filter").toggle();
                jQuery("#base_fieldset .widgets_list").toggle();
            });

            jQuery("#searchwidgets", "#base_fieldset").keypress( function( event ){
                if ( event.which == 13 ) {
                    event.preventDefault();
                }
                var $this = this;
                
                setTimeout( function(){
                    if( jQuery.trim(jQuery("#searchwidgets", "#base_fieldset").val()) !="" ) {
                        jQuery("#base_fieldset .widgets_list .wpo-wg-button").hide(); 
                        jQuery( "#base_fieldset div.widget-title:contains("+jQuery("#searchwidgets", "#base_fieldset").val()+")" ).parent().parent().show();
                    }else { 
                        jQuery("#base_fieldset .widgets_list .wpo-wg-button").show();
                    }

                 }, 300 );

            } );

            jQuery( '#filterbygroups .filter-option' , "#base_fieldset").click( function(){
                jQuery( '#filterbygroups .filter-option' ,"#base_fieldset").removeClass( 'active' );
                jQuery(this).addClass( 'active' );
                if( jQuery(this).data('option') == 'all' ) {
                    jQuery("#base_fieldset .widgets_list .wpo-wg-button").show();  
                }else {
                    jQuery("#base_fieldset .widgets_list .wpo-wg-button").hide();  
                    jQuery('#base_fieldset [data-group='+jQuery(this).data('option')+']').show();
                }   
                return false; 
            } );

            jQuery( '#filterbygroups .filter-option[data-option="all"]' , "#base_fieldset").trigger("click");
        },
        widgetsAction : function(widgetEl ){
            if(typeof(jQuery) == "undefined")
                return;
            var $wwidgets = jQuery("#base_fieldset");
            var $obj = this;
            jQuery(".wpo-wg-button > div", $wwidgets ).click( function(){
                var widget_type = jQuery(this).data("widgettype");
                var options = $$('select#'+widgetEl+' option');
                var len = options.length;
                $obj.widgetEl.value = widget_type;
                for (var i = 0; i < len; i++) {
                    if(options[i].value == widget_type) {
                        options[i].selected = true;
                    }
                }
                jQuery('select#'+widgetEl).trigger("change");
            } );
        },
        getOptionsContainerId: function() {
            return this.widgetOptionsEl.id + '_' + this.widgetEl.value.gsub(/\//, '_');
        },

        switchOptionsContainer: function(containerId) {
            $$('#' + this.widgetOptionsEl.id + ' div[id^=' + this.widgetOptionsEl.id + ']').each(function(e) {
                this.disableOptionsContainer(e.id);
            }.bind(this));
            if(containerId != undefined) {
                this.enableOptionsContainer(containerId);
            }
            this._showWidgetDescription();
        },

        enableOptionsContainer: function(containerId) {
            $$('#' + containerId + ' .widget-option').each(function(e) {
                e.removeClassName('skip-submit');
                if (e.hasClassName('obligatory')) {
                    e.removeClassName('obligatory');
                    e.addClassName('required-entry');
                }
            });
            $(containerId).removeClassName('no-display');
        },

        disableOptionsContainer: function(containerId) {
            if ($(containerId).hasClassName('no-display')) {
                return;
            }
            $$('#' + containerId + ' .widget-option').each(function(e) {
                // Avoid submitting fields of unactive container
                if (!e.hasClassName('skip-submit')) {
                    e.addClassName('skip-submit');
                }
                // Form validation workaround for unactive container
                if (e.hasClassName('required-entry')) {
                    e.removeClassName('required-entry');
                    e.addClassName('obligatory');
                }
            });
            $(containerId).addClassName('no-display');
        },

        // Assign widget options values when existing widget selected in WYSIWYG
        initOptionValues: function() {

            if (!this.wysiwygExists()) {
                return false;
            }

            var e = this.getWysiwygNode();
            if (e != undefined && e.id) {
                var widgetCode = Base64.idDecode(e.id);
                if (widgetCode.indexOf('{{widget') != -1) {
                    this.optionValues = new Hash({});
                    widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                        if (match[1] == 'type') {
                            this.widgetEl.value = match[2];
                        } else {
                            this.optionValues.set(match[1], match[2]);
                        }
                    }.bind(this));

                    this.loadOptions();
                }
            }
        },
        // Assign widget options values when existing widget selected in WYSIWYG
        initOptionValue: function(widgetCode) {
            var widgetCode = VesBuilder.currentShortcode!=null?VesBuilder.currentShortcode:"";
            if (widgetCode.indexOf('{{widget') != -1) {
                    this.optionValues = new Hash({});
                    widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                        if (match[1] == 'type') {
                            this.widgetEl.value = match[2];
                        } else {
                            this.optionValues.set(match[1], match[2]);
                        }
                    }.bind(this));

                    this.loadOptions();
            } else {
                jQuery("#base_fieldset .widgets-filter").show();
                jQuery("#base_fieldset .widgets_list").show();
            }
        },
        loadOptions: function() {
            if (!this.widgetEl.value) {
                this.switchOptionsContainer();
                return;
            }

            var optionsContainerId = this.getOptionsContainerId();
            if ($(optionsContainerId) != undefined) {
                this.switchOptionsContainer(optionsContainerId);
                return;
            }

            this._showWidgetDescription();

            var params = {widget_type: this.widgetEl.value, values: this.optionValues};
            new Ajax.Request(this.optionsUrl,
                {
                    parameters: {widget: Object.toJSON(params)},
                    onSuccess: function(transport) {
                        try {
                            WysiwygWidgetTools.onAjaxSuccess(transport);
                            this.switchOptionsContainer();
                            if ($(optionsContainerId) == undefined) {
                                this.widgetOptionsEl.insert({bottom: WysiwygWidgetTools.getDivHtml(optionsContainerId, transport.responseText)});

                                if(typeof(jQuery) != "undefined") { 
                                    if(jQuery("#base_fieldset .showinform").length > 0) {
                                        jQuery("#base_fieldset .widgets-filter").hide();
                                        jQuery("#base_fieldset .widgets_list").hide();
                                    }
                                }
                
                            } else {
                                this.switchOptionsContainer(optionsContainerId);
                            }
                        } catch(e) {
                            alert({
                                content: e.message
                            });
                        }
                    }.bind(this)
                }
            );
        },

        _showWidgetDescription: function() {
            var noteCnt = this.widgetEl.next().down('small');
            var descrCnt = $('widget-description-' + this.widgetEl.selectedIndex);
            if(noteCnt != undefined) {
                var description = (descrCnt != undefined ? descrCnt.innerHTML : '');
                noteCnt.update(description);

                if(typeof(jQuery) != "undefined") {
                    jQuery("#base_fieldset .widget-info").html(descrCnt.innerHTML);
                }
            }
        },

        validateField: function() {
            jQuery(this.widgetEl).valid();
        },

        insertWidget: function() {
            var validationResult,
                $form = jQuery('#' + this.formEl),
                formElements,
                i,
                params,
                editor,
                activeNode;

            if(jQuery("#"+this.formEl+" .admin__field-control .wysiwyg-editor").length > 0){
                if(jQuery("#"+this.formEl+" .admin__field-control .action-show-hide.texteditor-enabled").length > 0) {
                    jQuery("#"+this.formEl+" .admin__field-control .action-show-hide.texteditor-enabled").trigger("click");
                }
                
            }
            jQuery.data($form[0], 'validator', null);

            $form.validate({
                /**
                 * Ignores elements with .skip-submit, .no-display ancestor elements
                 */
                ignore: function () {
                    return jQuery(this).closest('.skip-submit, .no-display').length;
                },
                errorClass: 'mage-error'
            });
            
            validationResult = $form.valid();

            if (validationResult) {
                var formElements = [];
                var i = 0;
                Form.getElements($(this.formEl)).each(function(e) {
                    if(!e.hasClassName('skip-submit')) {
                        formElements[i] = e;
                        i++;
                    }
                });

                // Add as_is flag to parameters if wysiwyg editor doesn't exist
                //var params = Form.serializeElements(formElements);
                params = Form.serializeElements(formElements);

                editor = wysiwyg.activeEditor();
                if(editor && getContentTinyMCEs.length > 0) {
                    var field_name = "";
                    
                    for(i = 0; i < getContentTinyMCEs.length; i++) {
                        if(typeof getContentTinyMCEs[i] == "function" && typeof getTinyMCEFields[i] == "function") {
                            field_name = getTinyMCEFields[i]();
                            params[field_name] = getContentTinyMCEs[i]();
                            //params = params + "&"+field_name+"=" + getContentTinyMCEs[i]();
                        }
                        
                    }
                    getContentTinyMCEs = [];
                    getTinyMCEFields = [];
                }

                //if (!this.wysiwygExists()) {
                params = params + '&as_is=1';
                //}

                new Ajax.Request($(this.formEl).action,
                    {
                        parameters: params,
                        onComplete: function(transport) {
                            try {
                                editor = wysiwyg.activeEditor();
                                WysiwygWidgetTools.onAjaxSuccess(transport);
                                WysiwygWidgetTools.dialogWindow.modal('closeModal');

                                if (editor) {
                                    editor.focus();
                                    activeNode = widgetTools.getActiveSelectedNode();

                                    if (activeNode) {
                                        editor.selection.select(activeNode);
                                        editor.selection.setContent(transport.responseText);
                                    } else if (this.bMark) {
                                        wysiwyg.activeEditor().selection.moveToBookmark(this.bMark);
                                    }
                                }

                                if (!activeNode) {
                                    this.updateContent(transport.responseText);
                                }
                            } catch(e) {
                                alert({
                                    content: e.message
                                });
                            }
                        }.bind(this)
                    });
            }
        },

        updateContent: function(content) {
            if(typeof(VesBuilder.callback['widget']) != "undefined" && typeof VesBuilder.callback['widget'] == "function") {
               VesBuilder.callback['widget'].call( VesBuilder.builder, VesBuilder.currentCol, VesBuilder.currentWidget, content  );
            }else if (this.wysiwygExists()) {
                this.getWysiwyg().execCommand("mceInsertContent", false, content);
            } else {
                var textarea = document.getElementById(this.widgetTargetId);
                updateElementAtCursor(textarea, content);
                varienGlobalEvents.fireEvent('tinymceChange');
            }
        },

        wysiwygExists: function() {
            return typeof wysiwyg != 'undefined' && wysiwyg.get(this.widgetTargetId);
        },

        getWysiwyg: function() {
            return wysiwyg.activeEditor();
        },

        getWysiwygNode: function () {
            return widgetTools.getActiveSelectedNode() || wysiwyg.activeEditor().selection.getNode();
        }
    }

    WysiwygWidget.chooser = Class.create();
    WysiwygWidget.chooser.prototype = {

        // HTML element A, on which click event fired when choose a selection
        chooserId: null,

        // Source URL for Ajax requests
        chooserUrl: null,

        // Chooser config
        config: null,

        // Chooser dialog window
        dialogWindow: null,

        // Chooser content for dialog window
        dialogContent: null,

        overlayShowEffectOptions: null,
        overlayHideEffectOptions: null,

        initialize: function(chooserId, chooserUrl, config) {
            this.chooserId = chooserId;
            this.chooserUrl = chooserUrl;
            this.config = config;
        },

        getResponseContainerId: function() {
            return 'responseCnt' + this.chooserId;
        },

        getChooserControl: function() {
            return $(this.chooserId + 'control');
        },

        getElement: function() {
            return $(this.chooserId + 'value');
        },

        getElementLabel: function() {
            return $(this.chooserId + 'label');
        },

        open: function() {
            $(this.getResponseContainerId()).show();
        },

        close: function() {
            $(this.getResponseContainerId()).hide();
            this.closeDialogWindow();
        },

        choose: function(event) {
            // Open dialog window with previously loaded dialog content
            if (this.dialogContent) {
                this.openDialogWindow(this.dialogContent);
                return;
            }
            // Show or hide chooser content if it was already loaded
            var responseContainerId = this.getResponseContainerId();

            // Otherwise load content from server
            new Ajax.Request(this.chooserUrl,
                {
                    parameters: {element_value: this.getElementValue(), element_label: this.getElementLabelText()},
                    onSuccess: function(transport) {
                        try {
                            WysiwygWidgetTools.onAjaxSuccess(transport);
                            this.dialogContent = WysiwygWidgetTools.getDivHtml(responseContainerId, transport.responseText);
                            this.openDialogWindow(this.dialogContent);
                        } catch(e) {
                            alert({
                                content: e.message
                            });
                        }
                    }.bind(this)
                }
            );
        },

        openDialogWindow: function (content) {
            this.dialogWindow = jQuery('<div/>').modal({
                title: this.config.buttons.open,
                type: 'slide',
                buttons: [],
                opened: function () {
                    jQuery(this).addClass('magento_message');
                },
                closed: function (e, modal) {
                    modal.modal.remove();
                    this.dialogWindow = null;
                }
            });

            this.dialogWindow.modal('openModal').append(content);
        },

        closeDialogWindow: function () {
            this.dialogWindow.modal('closeModal').remove();
        },

        getElementValue: function(value) {
            return this.getElement().value;
        },

        getElementLabelText: function(value) {
            return this.getElementLabel().innerHTML;
        },

        setElementValue: function(value) {
            this.getElement().value = value;
        },

        setElementLabel: function(value) {
            this.getElementLabel().innerHTML = value;
        }
    };

    window.WysiwygWidget = WysiwygWidget;
    window.WysiwygWidgetTools = WysiwygWidgetTools;
});