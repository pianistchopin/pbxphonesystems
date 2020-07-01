<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Webkul\MultiEbayStoreMageConnect\Api\ProductmapRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Products;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassDelete extends Products
{
    /**
     * @var ProductmapRepositoryInterface
     */
    protected $_productMapRepository;

    /**
     * @param Context                       $context
     * @param ProductmapRepositoryInterface $productMapRepositoryInterface
     */
    public function __construct(
        Context $context,
        ProductmapRepositoryInterface $productMapRepository,
        CollectionFactory $productCollectionFactory
    ) {
        $this->_productMapRepository = $productMapRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $collection = $this->_productMapRepository
                    ->getCollectionByIds($params['productEntityIds']);
        $productMapCount = 0;
        $deletedIdsArr = [];
        foreach ($collection as $productMap) {
            array_push($deletedIdsArr, $productMap->getId());
            $productMap->delete();
            ++$productMapCount;
        }
        $mageProducts = $this->_productCollectionFactory
            ->create()
            ->addFieldToFilter(
                'entity_id',
                ['in' => $deletedIdsArr]
            );
        foreach ($mageProducts as $mageProduct) {
            $mageProduct->delete();
        }
        $this->messageManager->addSuccess(
            __("A total of %1 record(s) have been deleted.", $productMapCount)
        );

        return $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        )->setPath(
            '*/ebayaccount/edit',
            [
                'id'=>$params['rule_id'],
                'active_tab' => 'mapproduct'
            ]
        );
    }
}
