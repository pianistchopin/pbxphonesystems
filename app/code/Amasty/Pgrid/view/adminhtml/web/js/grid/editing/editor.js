/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'mage/translate',
    'uiCollection'
], function (_, utils, layout, $t, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            isMultiEditing: true,
            fieldTmpl: 'Amasty_Pgrid/ui/grid/editing/field',
            headerButtonsTmpl: 'Amasty_Pgrid/ui/grid/editing/header-buttons',
            successMsg: $t('You have successfully saved your edits.'),
            saveData: {},
            templates: {
                fields: {
                    base: {

                        parent: '${ $.$data.editor.name }',
                        name: '${ $.$data.productId }_${ $.$data.column.index }',
                        provider: '${ $.parent }',
                        dataScope: 'rowsData.${ $.$data.rowIndex }.${ $.$data.column.index }',
                        isEditor: true,
                        reloadOnFocused: true,
                        modules: {
                            parentObject: '${ $.parent }'
                        }

                    },
                    text: {
                        component: 'Magento_Ui/js/form/element/abstract',
                        template: 'Amasty_Pgrid/ui/form/element/input'
                    },
                    price: {
                        component: 'Amasty_Pgrid/js/form/element/price',
                        template: 'Amasty_Pgrid/ui/form/element/input',
                        dataScope: 'rowsData.${ $.$data.rowIndex }.amasty_${ $.$data.column.index }'
                    },
                    date: {
                        component: 'Magento_Ui/js/form/element/date',
                        template: 'ui/form/element/date',
                        dateFormat: 'MMM d, y h:mm:ss a',
                        reloadOnFocused: false,
                        reloadOnUpdate: true
                    },
                    select: {
                        component: 'Magento_Ui/js/form/element/select',
                        template: 'Amasty_Pgrid/ui/form/element/select',
                        options: '${ JSON.stringify($.$data.column.options) }',
                        reloadOnUpdate: true
                    },
                    multiselect: {
                        component: 'Magento_Ui/js/form/element/multiselect',
                        options: '${ JSON.stringify($.$data.column.options) }',
                        template: 'Amasty_Pgrid/ui/form/element/multiselect',
                    },
                    textarea: {
                        component: 'Magento_Ui/js/form/element/textarea',
                        template: 'Amasty_Pgrid/ui/form/element/textarea',
                    }
                }
            },
            cellConfig: {
                component: 'Amasty_Pgrid/js/grid/editing/cell',
                name: '${ $.name }_cell',
                model: '${ $.name }',
                columnsProvider: '${ $.columnsProvider }'
            },
            clientConfig: {
                component: 'Magento_Ui/js/grid/editing/client',
                name: '${ $.name }_client'
            },

            imports: {
                rowsData: '${ $.dataProvider }:data.items',
                filters: '${ $.dataProvider }:params.filters'
            },
            listens: {
                saveData: 'updateSaveState',
                elems: 'updateSaveState',
                '${ $.dataProvider }:params.paging.pageSize': 'onPagingSizeChanged'
            },
            modules: {
                columns: '${ $.columnsProvider }',
                client: '${ $.clientConfig.name }',
                source: '${ $.dataProvider }',
                cell: '${ $.cellConfig.name }'
            }
        },
        onPagingSizeChanged: function () {
            if (this.cell()) {
                this.cell().initCells();
            }
        },
        onInputKeyUp: function () {
            if ((this.editorType == 'textarea' && event.ctrlKey && event.which == 13) ||
                (this.editorType != 'textarea' && event.which == 13)
            ) {
                this.focused(false);
            }
            return true;
        },
        onFieldUpdated: function (hasChanged) {
            var saveData = this.parentObject().saveData;

            if (_.has(this.parentObject().saveData, this.fieldId)) {
                delete saveData[this.fieldId];
            }

            if (hasChanged) {
                saveData[this.fieldId] = {
                    'entityId': this.productId,
                    'value': typeof(this.value()) != 'object' || this.value().length > 0 ? this.value() : null,
                    'colIndex': this.colIndex,
                    'rowIndex': this.rowIndex
                };
            }

            this.parentObject().saveData = saveData;

            if (!this.parentObject().isMultiEditing && hasChanged) {
                this.parentObject().save();
            }
        },
        save: function () {
            var valid = true;

            var data = this.source().get('params');
            data.amastyItems = {};
            data.store_id = this.filters.store_id;

            var editor = this;
            
            _.each(_.values(this.saveData), function (item) {
                if (valid && editor.getField(item.entityId, item.colIndex).validate().valid) {
                    if (!_.has(data.amastyItems, item.entityId)) {
                        data.amastyItems[item.entityId] = {};
                    }

                    var newValue = undefined == item.value? '': item.value;
                    data.amastyItems[item.entityId][item.colIndex] = newValue;
                } else {
                    valid = false;
                }

            });

            if (valid && this.client().busy !== true) {
                this.client().busy = true;

                this.columns('showLoader');

                editor.clearMessages();

                this.client()
                    .save(data)
                    .done(this.onDataSaved)
                    .fail(this.onSaveError);
            }
        },
        initialize: function () {

            _.bindAll(this, 'onDataSaved', 'onSaveError', 'clearSaveData', 'clearMessages');

            this._super();

            layout([this.clientConfig]);
            layout([this.cellConfig]);

            this.source().on('reloaded', this.clearSaveData);

            return this;
        },
        updateSaveState: function () {
            var editor = this;

            var hasActive = false;
            _.each(editor.elems(), function (elem) {
                if (elem.visible() && !hasActive && editor.isMultiEditing) {
                    hasActive = true;
                }
            });

            this.hasActive(hasActive);

            this.canSave(hasActive || _.keys(this.saveData).length > 0);
        },
        initObservable: function () {
            this._super()
                .track([
                    'saveData',
                    'rowsData'
                ])
                .observe({
                    canSave: false,
                    hasActive: false,
                    messages: []
                });

                return this;
        },
        startEdit: function (rowIndex, colIndex) {
            return this.edit(rowIndex, colIndex);
        },
        getId: function (rowIndex, colIndex) {
            return rowIndex + "_" + colIndex;
        },
        edit: function (rowIndex, colIndex) {

            if (this.isEditableColumn(colIndex)) {
                var field = this.getField(this.rowsData[rowIndex].entity_id, colIndex);

                if (!field) {
                    this.initField(rowIndex, colIndex);
                } else {
                    field.visible(true);
                    field.value(this.rowsData[rowIndex][colIndex]);
                }
            }

            return this;
        },
        initField: function (rowIndex, colIndex) {

            var field = this.buildField(rowIndex, colIndex);
            layout([field]);
            return this;
        },
        buildField: function (rowIndex, colIndex) {
            var fields = this.templates.fields,
                column = this.columns().getChild(colIndex),
                field  = column.editor,
                rowData = this.rowsData[rowIndex];

            if (_.isObject(field) && field.editorType) {
                field = utils.extend({}, fields[field.editorType], field);
            } else if (_.isString(field)) {
                field = fields[field];
            }

            field = utils.extend({}, fields.base, field);

            var ret = utils.template(field, {
                editor: this,
                column: column,
                productId: rowData.entity_id,
                rowIndex: rowIndex
            }, true, true);
            //

            ret.fieldId = this.getId(rowData.entity_id, colIndex);
            ret.productId = rowData.entity_id;
            ret.colIndex = colIndex;
            ret.onKeyUp = this.onInputKeyUp;

            return ret;
        },
        initElement: function (field) {
            var editor = this;
            if (field.reloadOnUpdate) {
                field.on('update', this.onFieldUpdated.bind(field));
            }

            if (field.reloadOnFocused) {
                field.on('focused', function (focused) {
                    if (!focused) {
                        editor.onFieldUpdated.bind(field)(true);
                    }
                });
            }

            field.focused(true);
        },
        getField: function (productId, colIndex) {
            return this.elems.findWhere({
                fieldId: this.getId(productId, colIndex)
            });
        },
        hasEditorColumn: function (colIndex) {
            var column = this.columns().getChild(colIndex);
            return column.ampgrid && column.ampgrid.has_editor;
        },
        isEditableColumn: function (colIndex) {
            var column = this.columns().getChild(colIndex);
            return column.ampgrid && column.ampgrid.editable;
        },
        isEditable: function (productId, colIndex) {
            var elem = this.getField(productId, colIndex);
            var visible = !elem || elem.visible() !== false;

            return this.columns().getChild(colIndex) &&
                this.columns().getChild(colIndex).editor && visible;
        },
        /**
         * Handles successful save request.
         */
        onDataSaved: function (data) {
            if (data.ajaxExpired) {
                document.location.href = data.ajaxRedirect;
            } else {
                var msg = {
                    type: 'success',
                    message: this.successMsg
                };

                this.addMessage(msg);


                this.source().onReload(data.grid);
            }

            this.client().busy = false;

        },

        clearSaveData: function () {
            var editor = this;

            _.each(editor.elems(), function (elem) {
                elem.visible(false);
            })

            this.saveData = {};

            return this;
        },
        /**
         * Handles failed save request.
         *
         * @param {(Array|Object)} errors - List of errors or a single error object.
         */
        onSaveError: function (errors) {
            this.addMessage(errors)
                .columns('hideLoader');
            this.client().busy = false;
        },
        addMessage: function (message) {
            var messages = this.messages();

            Array.isArray(message) ?
                messages.push.apply(messages, message) :
                messages.push(message);

            this.messages(messages);

            return this;
        },
        clearMessages: function () {
            this.messages.removeAll();

            return this;
        },
        hasMessages: function () {
            return this.messages().length;
        },
        cancel: function () {
            this.clearSaveData()
                .clearMessages();

            return this;
        }
    })
});
