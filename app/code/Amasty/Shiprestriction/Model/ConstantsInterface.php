<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Model;

interface ConstantsInterface
{
    const REGISTRY_KEY = 'current_amasty_shiprestriction_rule';
    const SECTION_KEY = 'amshiprestriction';
    const DATA_PERSISTOR_FORM = 'amasty_shiprestriction_form_data';

    const FIELDS = [
        'stores',
        'cust_groups',
        'methods',
        'days',
        'discount_id',
        'discount_id_disable'
    ];
}
