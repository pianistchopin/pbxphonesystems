<?php
/**
 * @category   Webkul
 * @package    Webkul_MultiEbayStoreMageConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

class Getmagechildcategory extends Categories
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * ValidateTest constructor.
     *
     * @param Action\Context $context
     * @param JsonFactory    $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
    
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('multiebaystoremageconnect/*/');

            return;
        }

        $childcategory = $this
                        ->_categoryFactory
                        ->create()->getCollection()
                        ->addFieldToFilter(
                            'parent_id',
                            ['eq' => $data['cat_id']]
                        )->load();

        $categoryList = [];
        foreach ($childcategory as $category) {
            $category = $this->_categoryFactory
                                ->create()
                                ->load($category->getEntityId());
            $categoryList[] = [
                                'value' => $category->getEntityId(),
                                'lable' => $category->getName(),
                            ];
        }
        $categoryList = [
                        'totalRecords' => count($childcategory),
                        'items' => $categoryList,
                    ];

        return $this->_resultJsonFactory->create()->setData($categoryList);
    }
}
