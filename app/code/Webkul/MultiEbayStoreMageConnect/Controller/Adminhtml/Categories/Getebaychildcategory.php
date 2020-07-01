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
use Webkul\MultiEbayStoreMageConnect\Api\EbaycategoryRepositoryInterface;
use Webkul\MultiEbayStoreMageConnect\Controller\Adminhtml\Categories;

class Getebaychildcategory extends Categories
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var EbaycategoryRepositoryInterface
     */
    protected $_ebayCategoryRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory                         $resultJsonFactory
     * @param EbaycategoryRepositoryInterface     $ebayCategoryRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        EbaycategoryRepositoryInterface $ebayCategoryRepository
    ) {
    
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_ebayCategoryRepository = $ebayCategoryRepository;
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
        $childcategory = $this->_ebayCategoryRepository
                        ->getCollectionByeBayCateParentId($data['cat_id']);

        return $this->_resultJsonFactory
                            ->create()
                            ->setData($childcategory->toArray());
    }
}
