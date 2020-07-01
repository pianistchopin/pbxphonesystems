<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Ebayorder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MultiEbayStoreMageConnect\Api\OrdermapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Ebayorder;

class MassDelete extends Ebayorder
{
    /**
     * OrdermapRepositoryInterface
     */
    protected $_orderMapRepository;

    /**
     * @param Context                     $context
     * @param OrdermapRepositoryInterface $orderMapRepository
     */
    public function __construct(
        Context $context,
        OrdermapRepositoryInterface $orderMapRepository
    ) {
        $this->_orderMapRepository = $orderMapRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $orderColl = $this->_orderMapRepository
                    ->getCollectionByIds($params['orderEntityIds']);

        $orderDeleted = 0;
        foreach ($orderColl->getItems() as $orderMap) {
            $orderMap->delete();
            ++$orderDeleted;
        }
        $this->messageManager->addSuccess(
            __("A total of %1 record(s) have been deleted.", $orderDeleted)
        );

        return $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        )->setPath(
            '*/ebayaccount/edit',
            [
                'id'=>$params['rule_id'],
                'active_tab' => 'maporder'
            ]
        );
    }
}
