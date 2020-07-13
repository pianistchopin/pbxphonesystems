<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Ebay\Template\Shipping;

/**
 * Class \Ess\M2ePro\Model\Ebay\Template\Shipping\ChangeProcessor
 */
class ChangeProcessor extends \Ess\M2ePro\Model\Ebay\Template\ChangeProcessor\AbstractModel
{
    const INSTRUCTION_INITIATOR = 'template_shipping_change_processor';

    //########################################

    protected function getInstructionInitiator()
    {
        return self::INSTRUCTION_INITIATOR;
    }

    // ---------------------------------------

    protected function getInstructionsData(\Ess\M2ePro\Model\Template\Diff\AbstractModel $diff, $status)
    {
        /** @var \Ess\M2ePro\Model\Ebay\Template\Shipping\Diff $diff */

        $data = [];

        if ($diff->isShippingDifferent()) {
            $priority = 5;

            if ($status == \Ess\M2ePro\Model\Listing\Product::STATUS_LISTED) {
                $priority = 30;
            }

            $data[] = [
                'type'      => self::INSTRUCTION_TYPE_SHIPPING_DATA_CHANGED,
                'priority'  => $priority,
            ];
        }

        return $data;
    }

    //########################################
}
