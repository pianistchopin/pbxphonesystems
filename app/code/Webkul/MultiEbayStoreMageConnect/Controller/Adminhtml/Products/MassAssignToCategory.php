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

class MassAssignToCategory extends Products
{

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var ProductmapRepositoryInterface
     */
    protected $_productMapRepository;

    /**
     * @param Context                                         $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param ProductmapRepositoryInterface                   $productMapRepository
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        ProductmapRepositoryInterface $productMapRepository
    ) {
        $this->_productRepository = $productRepository;
        $this->_productMapRepository = $productMapRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $collection =  $this->_productMapRepository
                    ->getCollectionByIds($params['productEntityIds']);
        $prodMapUpdate = 0;
        foreach ($collection as $proMap) {
            $catId = $this->getRequest()->getParam('magecate');
            $pro = $this->_productRepository->getById($proMap->getMagentoProId());
            $pro->setCategoryIds([$catId]);
            $this->_productRepository->save($pro, true);
            $proMap->setMageCatId($catId)->save();
            ++$prodMapUpdate;
        }
        $this->messageManager->addSuccess(
            __("A total of %1 record(s) have been updated.", $prodMapUpdate)
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
