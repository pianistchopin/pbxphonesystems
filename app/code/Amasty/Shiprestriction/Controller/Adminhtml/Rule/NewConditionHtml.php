<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

/**
 * Class for getting html of selected Condition.
 */
class NewConditionHtml extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractCondition
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Shiprestriction::rule';

    public function execute()
    {
        $this->newConditions('conditions');
    }
}
