<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

/**
 * Class Save
 */
class Save extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractSave
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Shiprestriction::rule';

    protected $dataPersistorKey = \Amasty\Shiprestriction\Model\ConstantsInterface::DATA_PERSISTOR_FORM;

    protected function prepareData(&$data)
    {
        if (isset($data['rule_id'])) {
            unset($data['rule_id']);
        }

        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }

        unset($data['rule']);
    }
}
