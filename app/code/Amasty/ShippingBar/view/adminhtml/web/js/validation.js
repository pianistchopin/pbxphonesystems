require([
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'
], function (validator, $t) {
    'use strict';

    validator.addRule(
        'validate-length-of-numbers-after-comma',
        function (value) {
            return /^\d+(\.\d{0,2})?$/.test(value);
        },
        $t('The field should contain no more than 2 decimal places.')
    );

    validator.addRule(
        'validate-no-html-tags',
        function (value) {
            return !/<+\w+((\s+\w+(\s*=\s*(?:".*?"|'.*?'|[^'">\s]+))?)+\s*|\s*)?\/?>/gm.test(value);
        },
        $t('The field should contain no html tags.')
    );
});
