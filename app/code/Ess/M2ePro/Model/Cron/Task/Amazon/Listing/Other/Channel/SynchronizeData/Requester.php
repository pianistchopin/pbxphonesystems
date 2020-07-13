<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Cron\Task\Amazon\Listing\Other\Channel\SynchronizeData;

/**
 * Class \Ess\M2ePro\Model\Cron\Task\Amazon\Listing\Other\Channel\SynchronizeData\Requester
 */
class Requester extends \Ess\M2ePro\Model\Amazon\Connector\Inventory\Get\ItemsRequester
{
    //########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Cron_Task_Amazon_Listing_Other_Channel_SynchronizeData_ProcessingRunner';
    }

    //########################################
}
