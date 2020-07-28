/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedTerms = config.selectedTerms,
            terms = $H(selectedTerms),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('related_terms').value = Object.toJSON(terms);


        function registerTerms(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    terms.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                terms.unset(element.value);
            }
            $('related_terms').value = Object.toJSON(terms);
            grid.reloadParams = {
                'related[]': terms.keys()
            };
        }

        function termRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                terms.set(element.checkboxElement.value, element.value);
                $('related_terms').value = Object.toJSON(terms);
            }
        }

        function termRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = termRowClick;
        gridJsObject.initRowCallback = termRowInit;
        gridJsObject.checkboxCheckCallback = registerTerms;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                termRowInit(gridJsObject, row);
            });
        }
    };
});
