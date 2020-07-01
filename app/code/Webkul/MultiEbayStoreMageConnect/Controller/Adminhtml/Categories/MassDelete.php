<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategorymapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

class MassDelete extends Categories
{
    /**
     * @var EbaycategorymapRepositoryInterface
     */
    protected $_ebayCategoryMapRepository;

    /**
     * @param Context                            $context
     * @param EbaycategorymapRepositoryInterface $ebayCategoryMapRepository
     */
    public function __construct(
        Context $context,
        EbaycategorymapRepositoryInterface $ebayCategoryMapRepository
    ) {
    
        $this->_ebayCategoryMapRepository = $ebayCategoryMapRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $collection = $this->_ebayCategoryMapRepository
                ->getCollectionByIds($params['cateEntityIds']);
        $catRecordDeleted = 0;
        foreach ($collection as $categoryMap) {
            $categoryMap->setId($categoryMap->getEntityId());
            $categoryMap->delete();
            ++$catRecordDeleted;
        }
        $this->messageManager->addSuccess(
            __("A total of %1 record(s) have been deleted.", $catRecordDeleted)
        );

        return $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        )->setPath(
            '*/ebayaccount/edit',
            [
                'id'=>$params['rule_id'],
                'active_tab' => 'mapcategory'
            ]
        );
    }
}
